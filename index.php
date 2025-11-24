<?php
session_start();

/**
 * Team Members: Rahil Karkar, Ansh Pathapadu, Nirusma Dahal
 * Deployed URL: https://cs4640.cs.virginia.edu/uzu3gv/jukeboxd/
 */

// ---------------- DATABASE CONNECTION ----------------
// Auto-detect environment and load appropriate config
$isServer = (
    isset($_SERVER['HTTP_HOST']) &&
    strpos($_SERVER['HTTP_HOST'], 'cs4640.cs.virginia.edu') !== false
);

if ($isServer) {
    // Running on CS4640 server - use server config
    require_once 'config-server.php';
} else {
    // Running locally - use local config
    require_once 'config-local.php';
}

// $pdo is now available from the config file

// ---------------- BASE PATH HELPER ----------------
// Helper function to generate correct URLs for assets and links
function getBasePath() {
    // Get the directory of the current script
    $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /~abc3de/project/index.php
    $basePath = dirname($scriptName); // e.g., /~abc3de/project

    // Handle Windows paths
    $basePath = str_replace('\\', '/', $basePath);

    // Remove trailing slash, but keep root as '/'
    if ($basePath !== '/') {
        $basePath = rtrim($basePath, '/');
    }

    return $basePath;
}

// Make base path available globally
$BASE_PATH = getBasePath();

// Debug: Uncomment to see what base path is being generated
// echo "<!-- BASE_PATH: " . htmlspecialchars($BASE_PATH) . " -->\n";

// ---------------- LOAD MODELS ----------------
require_once 'models/UserModel.php';
require_once 'models/ReviewModel.php';
require_once 'models/SongModel.php';

$userModel = new UserModel($pdo);
$reviewModel = new ReviewModel($pdo);
$songModel = new SongModel($pdo);

// ---------------- ROUTING ----------------
$action = $_GET['action'] ?? 'home';

switch ($action) {

    // ---------- HOME ----------
    case 'home':
        include 'views/home.php';
        break;

    // ---------- LOGIN PAGE ----------
    case 'login':
        include 'views/login.php';
        break;

    // ---------- REGISTER PAGE ----------
    case 'register':
        include 'views/register.php';
        break;

    // ---------- AUTHENTICATE LOGIN ----------
    case 'authenticate':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $errors = [];

            // Validate inputs
            if (!preg_match("/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/", $email)) {
                $errors[] = "Invalid email format.";
            }
            if (!preg_match("/^(?=.*[A-Z])(?=.*[0-9]).{6,}$/", $password)) {
                $errors[] = "Password must contain at least 6 characters, one uppercase letter, and one number.";
            }

            // If valid, check credentials
            if (empty($errors)) {
                $user = $userModel->getUserByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];

                    // Optional "Remember Me" cookie
                    if (!empty($_POST['remember'])) {
                        setcookie("remember_user", $user['username'], time() + 86400 * 30, "/");
                    }

                    header('Location: index.php?action=profile');
                    exit;
                } else {
                    $errors[] = "Invalid email or password.";
                }
            }

            include 'views/login.php';
        }
        break;

    // ---------- REGISTER NEW USER ----------
    case 'registerUser':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $errors = [];

            // Validate form fields
            if (strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters.";
            }
            if (!preg_match("/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/", $email)) {
                $errors[] = "Invalid email format.";
            }
            if (!preg_match("/^(?=.*[A-Z])(?=.*[0-9]).{6,}$/", $password)) {
                $errors[] = "Password must contain at least 6 characters, one uppercase letter, and one number.";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            }

            // If valid, insert into database
            if (empty($errors)) {
                try {
                    $existing = $userModel->getUserByEmail($email);
                    if ($existing) {
                        $errors[] = "Email already registered.";
                    } else {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $userModel->addUser($username, $email, $hashed);

                        // Auto-login new user - fetch the created user to get ID
                        $newUser = $userModel->getUserByEmail($email);
                        $_SESSION['user_id'] = $newUser['id'];
                        $_SESSION['username'] = $username;
                        $_SESSION['email'] = $email;

                        header('Location: index.php?action=profile');
                        exit;
                    }
                } catch (PDOException $e) {
                    // Database error - likely tables don't exist
                    $errors[] = "Database error: " . $e->getMessage();
                    $errors[] = "Have you run migrations.sql on the server?";
                }
            }

            include 'views/register.php';
        }
        break;

    // ---------- LOGOUT ----------
    case 'logout':
        session_destroy();
        setcookie("remember_user", "", time() - 3600, "/");
        header('Location: index.php');
        exit;

    // ---------- USER PAGES ----------
    case 'profile':
        include 'views/profile.php';
        break;

    case 'activity':
        require_once "models/ActivityModel.php";
        $model = new ActivityModel();
        $activities = $model->getActivitiesForUser($_SESSION['user_id']);
        require "views/activity.php";
        break;
        

    case 'reviews':
        include 'views/reviews.php';
        break;

    case 'settings':
        include 'views/settings.php';
        break;

    // ---------- SONGS ----------
    case 'songs':
        // Get all songs or search results
        $songs = $songModel->getAllSongs(100);
        include 'views/songs.php';
        break;

    // ---------- SUBMIT REVIEW ----------
    case 'submitReview':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $songId = intval($_POST['song_id'] ?? 0);
            $rating = intval($_POST['rating'] ?? 0);
            $reviewText = trim($_POST['review_text'] ?? '');
            $errors = [];

            // Server-side validation
            if ($songId <= 0) {
                $errors[] = "Invalid song selected.";
            }
            if ($rating < 1 || $rating > 5) {
                $errors[] = "Rating must be between 1 and 5 stars.";
            }
            if (strlen($reviewText) < 3) {
                $errors[] = "Review must be at least 3 characters long.";
            }

            // If valid, add review
            if (empty($errors)) {
                $success = $reviewModel->addReview($_SESSION['user_id'], $songId, $rating, $reviewText);
                if ($success) {
                    header('Location: index.php?action=reviews&success=1');
                    exit;
                } else {
                    $errors[] = "You have already reviewed this song.";
                }
            }

            // If errors, go back to reviews page with errors
            $_SESSION['review_errors'] = $errors;
            header('Location: index.php?action=reviews');
            exit;
        }
        header('Location: index.php?action=reviews');
        exit;

    // ---------- DELETE REVIEW ----------
    case 'deleteReview':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $reviewId = intval($_POST['review_id'] ?? 0);
            if ($reviewId > 0) {
                $reviewModel->deleteReview($reviewId, $_SESSION['user_id']);
            }
        }
        header('Location: index.php?action=reviews');
        exit;

    // ---------- ADD TO LISTEN LIST ----------
    case 'addToListenList':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $songId = intval($_POST['song_id'] ?? 0);
            if ($songId > 0) {
                $songModel->addToListenList($_SESSION['user_id'], $songId);
            }
        }
        header('Location: ' . ($_POST['redirect'] ?? 'index.php?action=songs'));
        exit;

    // ---------- REMOVE FROM LISTEN LIST ----------
    case 'removeFromListenList':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $songId = intval($_POST['song_id'] ?? 0);
            if ($songId > 0) {
                $songModel->removeFromListenList($_SESSION['user_id'], $songId);
            }
        }
        header('Location: ' . ($_POST['redirect'] ?? 'index.php?action=listenList'));
        exit;

    // ---------- LISTEN LIST PAGE ----------
    case 'listenList':
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $listenList = $songModel->getListenList($_SESSION['user_id']);
        include 'views/listen_list.php';
        break;

    // ------ PLAYLIST PAGE ------
    case 'playlists':
        if (empty($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $playlists = $playlistModel->getUserPlaylists($_SESSION['user_id']);
        include 'views/playlist.php';
        break;
        
    case 'createPlaylist':
        if (!empty($_POST['playlist_name'])) {
            $playlistModel->createPlaylist($_SESSION['user_id'], $_POST['playlist_name']);
            if ($newPlaylistId) {
                // log the activity
                require_once "models/ActivityModel.php";
                $activityModel = new ActivityModel($pdo);
                $activityModel->add($_SESSION['user_id'], "created a new playlist: " . htmlspecialchars($_POST['playlist_name']));
            }
        }
        header("Location: index.php?action=playlists");
        exit;
        
    case 'addToPlaylist':
        if (!empty($_POST['playlist_id']) && !empty($_POST['song_id'])) {
            $playlistModel->addSongToPlaylist($_POST['playlist_id'], $_POST['song_id']);

             // Log activity
            require_once "models/ActivityModel.php";
            $activityModel = new ActivityModel($pdo);

            // Fetch song title and playlist name for log
            $song = $songModel->getSongById($_POST['song_id']);
            $playlist = $playlistModel->getPlaylist($_POST['playlist_id'], $_SESSION['user_id']);
            if ($song && $playlist) {
                $activityModel->add($_SESSION['user_id'], "added song '{$song['title']}' to playlist '{$playlist['name']}'");
            }
        }
        // return to songs page
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
        
    case 'createPlaylistAndAdd':
        if (!empty($_POST['playlist_name']) && !empty($_POST['song_id'])) {
            $newPlaylistId = $playlistModel->createPlaylist($_SESSION['user_id'], $_POST['playlist_name']);
            if ($newPlaylistId) {
                $playlistModel->addSongToPlaylist($newPlaylistId, $_POST['song_id']);
                
                // Log activity
                require_once "models/ActivityModel.php";
                $activityModel = new ActivityModel($pdo);

                $song = $songModel->getSongById($_POST['song_id']);
                if ($song) {
                    $activityModel->add($_SESSION['user_id'], "created a new playlist '{$_POST['playlist_name']}' and added song '{$song['title']}'");
                 }
            }
        }
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
        
    case 'viewPlaylist':
        if (!empty($_GET['id'])) {
            $playlist = $playlistModel->getPlaylist($_GET['id'], $_SESSION['user_id']);
            $playlistSongs = $playlistModel->getPlaylistSongs($_GET['id']);
            include 'views/playlistSongs.php';
        }
        break;

    // ---------- SEARCH DEMO (JSON API DEMONSTRATION) ----------
    case 'searchDemo':
        include 'views/search_demo.php';
        break;

    // ---------- DEFAULT ----------
    default:
        include 'views/home.php';
        break;

    

}
?>
