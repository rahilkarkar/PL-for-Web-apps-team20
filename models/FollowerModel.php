<?php
/**
 * Handles follower/following relationships
 */

class FollowerModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Follow a user
     */
    public function followUser($follower_id, $following_id) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO followers (follower_id, following_id)
                VALUES (:follower_id, :following_id)
            ");
            return $stmt->execute([
                'follower_id' => $follower_id,
                'following_id' => $following_id
            ]);
        } catch (PDOException $e) {
            // Handle duplicate (already following) or self-follow
            return false;
        }
    }

    /**
     * Unfollow a user
     */
    public function unfollowUser($follower_id, $following_id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM followers
            WHERE follower_id = :follower_id AND following_id = :following_id
        ");
        return $stmt->execute([
            'follower_id' => $follower_id,
            'following_id' => $following_id
        ]);
    }

    /**
     * Check if user1 follows user2
     */
    public function isFollowing($follower_id, $following_id) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM followers
            WHERE follower_id = :follower_id AND following_id = :following_id
        ");
        $stmt->execute([
            'follower_id' => $follower_id,
            'following_id' => $following_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    /**
     * Get list of users that this user follows
     */
    public function getFollowing($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.username, u.email, f.created_at as followed_at
            FROM followers f
            JOIN jukeboxd_users u ON f.following_id = u.id
            WHERE f.follower_id = :user_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get list of users that follow this user
     */
    public function getFollowers($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.username, u.email, f.created_at as followed_at
            FROM followers f
            JOIN jukeboxd_users u ON f.follower_id = u.id
            WHERE f.following_id = :user_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get follower/following counts
     */
    public function getCounts($user_id) {
        // Count followers
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM followers WHERE following_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        $followers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count following
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM followers WHERE follower_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        $following = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return [
            'followers' => $followers,
            'following' => $following
        ];
    }
}
