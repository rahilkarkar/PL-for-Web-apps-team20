<?php
function updateProfile() {
    session_start();
    require_once "models/UserModel.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit();
    }

    $id = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    $errors = [];

    // basic validations
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($bio) > 500) {
        $errors[] = "Bio must be less than 500 characters.";
    }

    // instantiate model with PDO
    global $pdo; 
    $userModel = new UserModel($pdo);

    // check for duplicate username excluding current user
    $existingUser = $userModel->getUserByUsername($username);
    if ($existingUser && $existingUser['id'] != $id) {
        $errors[] = "Username already taken.";
    }

    // check for duplicate email excluding current user
    $existingEmailUser = $userModel->getUserByEmail($email);
    if ($existingEmailUser && $existingEmailUser['id'] != $id) {
        $errors[] = "Email already registered.";
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header("Location: index.php?action=profile");
        exit();
    }

    // if valid, update user
    $userModel->updateUser($id, $username, $email, $bio);

    $_SESSION['username'] = $username;

    header("Location: index.php?action=profile&success=1");
    exit();
}
?>