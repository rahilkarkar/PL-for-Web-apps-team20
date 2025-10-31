<?php
/**
 * Handles all review-related database operations
 * for the JukeBoxed app.
 */

class ReviewModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all reviews for a specific song
     */
    public function getReviewsBySongId($songId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.username, s.title as song_title, s.artist
            FROM reviews r
            JOIN jukeboxd_users u ON r.user_id = u.id
            JOIN songs s ON r.song_id = s.id
            WHERE r.song_id = :song_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['song_id' => $songId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all reviews by a specific user
     */
    public function getReviewsByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, s.title as song_title, s.artist, s.album
            FROM reviews r
            JOIN songs s ON r.song_id = s.id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all reviews (for activity feed, sorted/filtered)
     */
    public function getAllReviews($limit = 50, $sortBy = 'recent') {
        $orderClause = match($sortBy) {
            'rating' => 'r.rating DESC, r.created_at DESC',
            'oldest' => 'r.created_at ASC',
            default => 'r.created_at DESC' // 'recent'
        };

        $stmt = $this->pdo->prepare("
            SELECT r.*, u.username, s.title as song_title, s.artist, s.album
            FROM reviews r
            JOIN jukeboxd_users u ON r.user_id = u.id
            JOIN songs s ON r.song_id = s.id
            ORDER BY $orderClause
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new review
     */
    public function addReview($userId, $songId, $rating, $reviewText) {
        // Check if user already reviewed this song
        $existing = $this->getReviewByUserAndSong($userId, $songId);
        if ($existing) {
            return false; // User already reviewed this song
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO reviews (user_id, song_id, rating, review_text)
            VALUES (:user_id, :song_id, :rating, :review_text)
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'song_id' => $songId,
            'rating' => $rating,
            'review_text' => $reviewText
        ]);
    }

    /**
     * Update an existing review
     */
    public function updateReview($reviewId, $rating, $reviewText) {
        $stmt = $this->pdo->prepare("
            UPDATE reviews
            SET rating = :rating, review_text = :review_text, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute([
            'rating' => $rating,
            'review_text' => $reviewText,
            'id' => $reviewId
        ]);
    }

    /**
     * Delete a review
     */
    public function deleteReview($reviewId, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM reviews
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'id' => $reviewId,
            'user_id' => $userId
        ]);
    }

    /**
     * Get a specific review by user and song
     */
    public function getReviewByUserAndSong($userId, $songId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM reviews
            WHERE user_id = :user_id AND song_id = :song_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'song_id' => $songId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific review by ID
     */
    public function getReviewById($reviewId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.username, s.title as song_title, s.artist
            FROM reviews r
            JOIN jukeboxd_users u ON r.user_id = u.id
            JOIN songs s ON r.song_id = s.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $reviewId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
