<?php
class ActivityModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getActivitiesForUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT activity_text, created_at
            FROM activity
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logActivity($user_id, $text) {
        $stmt = $this->db->prepare("
            INSERT INTO activity (user_id, activity_text)
            VALUES (?, ?)
        ");
        $stmt->execute([$user_id, $text]);
    }
}
