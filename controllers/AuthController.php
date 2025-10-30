<?php
require_once 'models/UserModel.php';

function showLoginForm() {
    include 'views/login.php';
}

function showRegisterForm() {
    include 'views/register.php';
}

function handleLogin() {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (empty($password)) {
        $errors[] = 'Password cannot be empty.';
    }

    if (empty($errors)) {
        $user = verifyCredentials($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (isset($_POST['remember'])) {
                setcookie('remember_user', $user['username'], time() + (86400 * 7));
            }

            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Incorrect email or password.';
        }
    }

    include 'views/login.php';
}

function handleRegistration() {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');
    $errors = [];

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = 'Username must be 3–20 characters, letters/numbers/underscores only.';
    }

    if (!preg_match('/^[\\w\\.-]+@[\\w\\.-]+\\.\\w{2,4}$/', $email)) {
        $errors[] = 'Invalid email address.';
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d).{8,}$/', $password)) {
        $errors[] = 'Password must include uppercase, lowercase, and number (min 8 chars).';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (findUserByEmail($email)) {
        $errors[] = 'Email already registered.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        registerUser($username, $email, $hash);
        header('Location: index.php?action=login');
        exit();
    }

    include 'views/register.php';
}

function logoutUser() {
    session_unset();
    session_destroy();
    setcookie('remember_user', '', time() - 3600);
    header('Location: index.php?action=login');
    exit();
}
?>
