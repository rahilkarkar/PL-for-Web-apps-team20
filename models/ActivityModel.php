<?php
class ActivityModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getActivitiesForUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT activity_text, created_at
            FROM activity
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logActivity($user_id, $text) {
        $stmt = $this->pdo->prepare("
            INSERT INTO activity (user_id, activity_text)
            VALUES (?, ?)
        ");
        $stmt->execute([$user_id, $text]);
    }

    // Alias for compatibility with index.php
    public function add($user_id, $text) {
        return $this->logActivity($user_id, $text);
    }
}
