<?php
function updateProfile() {
    session_start();
    require_once "models/UserModel.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit();
    }

    $id = $_SESSION['user_id'];
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    $userModel = new UserModel();
    $userModel->updateUser($id, $username, $firstName, $lastName, $email, $bio);

    $_SESSION['username'] = $username;

    header("Location: index.php?action=profile");
    exit();
}
?>