<?php
/**
 * UserModel.php
 * -------------------------------
 * Handles all user-related database operations
 * for the JukeBoxed app.
 */

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Fetch a user by their email address.
     * Used for login & registration validation.
     */
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM jukeboxd_users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a user by their ID.
     * Useful for profile editing later.
     */
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM jukeboxd_users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new user (during registration).
     */
    public function addUser($username, $email, $password) {
        $stmt = $this->pdo->prepare("
            INSERT INTO jukeboxd_users (username, email, password)
            VALUES (:username, :email, :password)
        ");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);
    }

    /**
     * Update user information (for Settings page).
     */
    public function updateUser($id, $username, $email, $bio = null) {
        $stmt = $this->pdo->prepare("
            UPDATE jukeboxd_users
            SET username = :username, email = :email, bio = :bio
            WHERE id = :id
        ");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'bio' => $bio,
            'id' => $id
        ]);
    }

    /**
     * Delete a user account (not required now but useful later).
     */
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM jukeboxd_users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Optional: Return all users (for testing/debugging or admin).
     */
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT id, username, email FROM jukeboxd_users ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
