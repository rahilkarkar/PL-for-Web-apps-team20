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
require_once 'models/PlaylistMode.php';
require_once 'models/ActivityModel.php';
require_once 'models/FollowerModel.php';

$userModel = new UserModel($pdo);
$reviewModel = new ReviewModel($pdo);
$songModel = new SongModel($pdo);
$playlistModel = new PlaylistModel($pdo);
$activityModel = new ActivityModel($pdo);
$followerModel = new FollowerModel($pdo);

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
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $activities = $activityModel->getActivitiesForUser($_SESSION['user_id']);
        require "views/activity.php";
        break;
        

    case 'reviews':
        include 'views/reviews.php';
        break;

    case 'settings':
        include 'views/settings.php';
        break;

    case 'followers':
        include 'views/followers.php';
        break;

    case 'discover':
        include 'views/discover.php';
        break;

    // ---------- UPDATE PROFILE ----------
    case 'updateProfile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $firstName = trim($_POST['firstName'] ?? '');
            $lastName = trim($_POST['lastName'] ?? '');
            $errors = [];

            // Validation
            if (strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (strlen($bio) > 500) {
                $errors[] = "Bio must be less than 500 characters.";
            }

            // Check for duplicate username
            $existingUser = $userModel->getUserByUsername($username);
            if ($existingUser && $existingUser['id'] != $id) {
                $errors[] = "Username already taken.";
            }

            // Check for duplicate email
            $existingEmailUser = $userModel->getUserByEmail($email);
            if ($existingEmailUser && $existingEmailUser['id'] != $id) {
                $errors[] = "Email already registered.";
            }

            if (!empty($errors)) {
                $_SESSION['profile_errors'] = $errors;
                header("Location: index.php?action=settings");
                exit;
            }

            // Update user including first and last name
            $userModel->updateUser($id, $username, $email, $bio, $firstName, $lastName);
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            header("Location: index.php?action=settings&success=1");
            exit;
        }
        header("Location: index.php?action=settings");
        exit;

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
                    // Log activity for the review
                    $song = $songModel->getSongById($songId);
                    if ($song) {
                        $stars = str_repeat('★', $rating);
                        $activityModel->logActivity(
                            $_SESSION['user_id'],
                            "reviewed '{$song['title']}' by {$song['artist']} - {$stars}"
                        );
                    }

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
            $newPlaylistId = $playlistModel->createPlaylist($_SESSION['user_id'], $_POST['playlist_name']);
            if ($newPlaylistId) {
                // log the activity
                $activityModel->add($_SESSION['user_id'], "created a new playlist: " . htmlspecialchars($_POST['playlist_name']));
            }
        }
        header("Location: index.php?action=playlists");
        exit;
        
    case 'addToPlaylist':
        if (!empty($_POST['playlist_id']) && !empty($_POST['song_id'])) {
            $playlistModel->addSongToPlaylist($_POST['playlist_id'], $_POST['song_id']);

            // Log activity - fetch song title and playlist name
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

    case 'removeFromPlaylist':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $playlistId = intval($_POST['playlist_id'] ?? 0);
            $songId = intval($_POST['song_id'] ?? 0);

            if ($playlistId > 0 && $songId > 0) {
                $playlistModel->removeSongFromPlaylist($playlistId, $songId);

                // Log activity
                $playlist = $playlistModel->getPlaylist($playlistId, $_SESSION['user_id']);
                $song = $songModel->getSongById($songId);
                if ($playlist && $song) {
                    $activityModel->logActivity($_SESSION['user_id'], "removed '{$song['title']}' from playlist '{$playlist['name']}'");
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=playlists'));
        exit;

    // ---------- SEARCH DEMO (JSON API DEMONSTRATION) ----------
    case 'searchDemo':
        include 'views/search_demo.php';
        break;

    // ---------- DATABASE TOOLS (Development Only) ----------
    case 'dbMonitor':
        include 'db-monitor.php';
        exit;

    case 'dbReset':
        include 'db-reset.php';
        exit;

    // ---------- FOLLOW/UNFOLLOW ----------
    case 'followUser':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $followingId = intval($_POST['following_id'] ?? 0);
            if ($followingId > 0 && $followingId != $_SESSION['user_id']) {
                $success = $followerModel->followUser($_SESSION['user_id'], $followingId);
                if ($success) {
                    // Log activity
                    $followedUser = $userModel->getUserById($followingId);
                    if ($followedUser) {
                        $activityModel->logActivity($_SESSION['user_id'], "started following " . $followedUser['username']);
                    }
                }
            }
        }
        header('Location: ' . ($_POST['redirect'] ?? 'index.php?action=profile'));
        exit;

    case 'unfollowUser':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
            $followingId = intval($_POST['following_id'] ?? 0);
            if ($followingId > 0) {
                $followerModel->unfollowUser($_SESSION['user_id'], $followingId);
            }
        }
        header('Location: ' . ($_POST['redirect'] ?? 'index.php?action=profile'));
        exit;

    // ---------- DEFAULT ----------
    default:
        include 'views/home.php';
        break;

    // ---- ADDSONG VALIDATIONS ---

    case 'addSong':
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user_id'])) {
        $title = trim($_POST['title'] ?? '');
        $artist = trim($_POST['artist'] ?? '');
        $errors = [];

        // Basic validation
        if (strlen($title) < 2) {
            $errors[] = "Title must be at least 2 characters.";
        }
        if (strlen($artist) < 2) {
            $errors[] = "Artist must be at least 2 characters.";
        }

        if (empty($errors)) {
            // add the song
            $added = $songModel->addSong($title, $artist);

            if ($added) {
                // redirect to songs list
                header('Location: index.php?action=songs&success=1');
                exit;
            } else {
                // failure: redirect with error flag
                header('Location: index.php?action=songs&error=1');
                exit;
            }
        } else {
            // redirect with a generic error message
            header('Location: index.php?action=songs&error=validation');
            exit;
        }
    } else {
        // Not a POST request or user not logged in
        header('Location: index.php?action=songs');
        exit;
    }
    break;
}
?>
