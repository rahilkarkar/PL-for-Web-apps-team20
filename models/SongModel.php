<?php
/**
 * Handles all song and listen list related database operations
 * for the JukeBoxed app.
 */

class SongModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all songs
     */
    public function getAllSongs($limit = 50) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM songs
            ORDER BY title ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific song by ID
     */
    public function getSongById($songId) {
        $stmt = $this->pdo->prepare("SELECT * FROM songs WHERE id = :id");
        $stmt->execute(['id' => $songId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Search songs by title, artist, or album
     */
    public function searchSongs($query) {
        $searchTerm = "%$query%";
        $stmt = $this->pdo->prepare("
            SELECT * FROM songs
            WHERE LOWER(title) LIKE LOWER(:query)
               OR LOWER(artist) LIKE LOWER(:query)
               OR LOWER(album) LIKE LOWER(:query)
            ORDER BY title ASC
            LIMIT 20
        ");
        $stmt->execute(['query' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new song
     */
    public function addSong($title, $artist, $album = null, $releaseYear = null, $genre = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO songs (title, artist, album, release_year, genre)
            VALUES (:title, :artist, :album, :release_year, :genre)
            RETURNING id
        ");
        $stmt->execute([
            'title' => $title,
            'artist' => $artist,
            'album' => $album,
            'release_year' => $releaseYear,
            'genre' => $genre
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    /**
     * Get user's listen list
     */
    public function getListenList($userId) {
        $stmt = $this->pdo->prepare("
            SELECT s.*, ll.added_at
            FROM listen_list ll
            JOIN songs s ON ll.song_id = s.id
            WHERE ll.user_id = :user_id
            ORDER BY ll.added_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add song to listen list
     */
    public function addToListenList($userId, $songId) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO listen_list (user_id, song_id)
                VALUES (:user_id, :song_id)
            ");
            return $stmt->execute([
                'user_id' => $userId,
                'song_id' => $songId
            ]);
        } catch (PDOException $e) {
            // Handle duplicate entry (already in list)
            if ($e->getCode() == 23505) { // PostgreSQL unique violation
                return false;
            }
            throw $e;
        }
    }

    /**
     * Remove song from listen list
     */
    public function removeFromListenList($userId, $songId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM listen_list
            WHERE user_id = :user_id AND song_id = :song_id
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'song_id' => $songId
        ]);
    }

    /**
     * Check if song is in user's listen list
     */
    public function isInListenList($userId, $songId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM listen_list
            WHERE user_id = :user_id AND song_id = :song_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'song_id' => $songId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    /**
     * Get songs with their average ratings
     */
    public function getSongsWithRatings($limit = 20) {
        $stmt = $this->pdo->prepare("
            SELECT s.*,
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
            FROM songs s
            LEFT JOIN reviews r ON s.id = r.song_id
            GROUP BY s.id
            ORDER BY avg_rating DESC, review_count DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
