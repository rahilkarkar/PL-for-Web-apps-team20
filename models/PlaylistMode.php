<?php
class PlaylistModel {

private $db;

public function __construct($db) {
    $this->db = $db;
}

// Get all playlists for a user
public function getUserPlaylists($userId) {
    $stmt = $this->db->prepare("
        SELECT * FROM playlists WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a playlist
public function createPlaylist($userId, $name) {
    $stmt = $this->db->prepare("
        INSERT INTO playlists (user_id, name) VALUES (?, ?)
    ");
    $stmt->execute([$userId, $name]);
    return $this->db->lastInsertId();
}

// Add song to playlist
public function addSongToPlaylist($playlistId, $songId) {
    $stmt = $this->db->prepare("
        INSERT INTO playlist_songs (playlist_id, song_id)
        VALUES (?, ?)
    ");
    return $stmt->execute([$playlistId, $songId]);
}

// Get a single playlist
public function getPlaylist($playlistId, $userId) {
    $stmt = $this->db->prepare("
        SELECT * FROM playlists
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$playlistId, $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all songs inside a playlist
public function getPlaylistSongs($playlistId) {
    $stmt = $this->db->prepare("
        SELECT s.* FROM songs s
        JOIN playlist_songs ps ON ps.song_id = s.id
        WHERE ps.playlist_id = ?
    ");
    $stmt->execute([$playlistId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Remove song from playlist
public function removeSongFromPlaylist($playlistId, $songId) {
    $stmt = $this->db->prepare("
        DELETE FROM playlist_songs
        WHERE playlist_id = ? AND song_id = ?
    ");
    return $stmt->execute([$playlistId, $songId]);
}
}

