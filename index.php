<?php
session_start();

/**
 * JukeBoxed App - Front Controller
 * --------------------------------
 * Routes user requests via $_GET['action']
 * Handles authentication, registration, and sessions
 * Connects to PostgreSQL database (jukeboxed)
 */

// ---------------- DATABASE CONNECTION ----------------
$host = 'localhost';
$dbname = 'jukeboxed';
$user = 'anshpathapadu';  // ✅ Replace with your actual PostgreSQL username
$pass = '';                // leave blank if you don’t use a password (most macOS setups)
$dsn = "pgsql:host=$host;dbname=$dbname;options='--client_encoding=UTF8'";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ---------------- LOAD USER MODEL ----------------
require_once 'models/UserModel.php';
$userModel = new UserModel($pdo);

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
                $existing = $userModel->getUserByEmail($email);
                if ($existing) {
                    $errors[] = "Email already registered.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $userModel->addUser($username, $email, $hashed);

                    // Auto-login new user
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;

                    header('Location: index.php?action=profile');
                    exit;
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
        include 'views/activity.php';
        break;

    case 'reviews':
        include 'views/reviews.php';
        break;

    case 'settings':
        include 'views/settings.php';
        break;

    // ---------- DEFAULT ----------
    default:
        include 'views/home.php';
        break;
}
?>
