<?php
/**
 * JSON Search API Endpoint
 * ------------------------
 * Returns search results in JSON format
 * Usage: api/search.php?q=searchterm&type=songs|reviews|all
 */

session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ---------------- DATABASE CONNECTION ----------------
// Auto-detect environment and load appropriate config
$isServer = (
    isset($_SERVER['HTTP_HOST']) &&
    strpos($_SERVER['HTTP_HOST'], 'cs4640.cs.virginia.edu') !== false
);

if ($isServer) {
    // Running on CS4640 server - use server config
    require_once '../config-server.php';
} else {
    // Running locally - use local config
    require_once '../config-local.php';
}

// Check connection succeeded
if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}

// Load models
require_once '../models/SongModel.php';
require_once '../models/ReviewModel.php';

$songModel = new SongModel($pdo);
$reviewModel = new ReviewModel($pdo);

// Get search query from GET parameter
$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'songs'; // Type: songs, reviews, or all

// Validate input
if (empty($query) || strlen($query) < 2) {
    echo json_encode([
        'success' => false,
        'error' => 'Search query must be at least 2 characters',
        'results' => []
    ]);
    exit;
}

// Perform search based on type
$results = [];

try {
    switch ($type) {
        case 'songs':
            $songs = $songModel->searchSongs($query);
            $results = [
                'success' => true,
                'query' => $query,
                'type' => 'songs',
                'count' => count($songs),
                'results' => $songs
            ];
            break;

        case 'reviews':
            // Search reviews by song title or artist
            $songs = $songModel->searchSongs($query);
            $reviewResults = [];

            foreach ($songs as $song) {
                $songReviews = $reviewModel->getReviewsBySongId($song['id']);
                foreach ($songReviews as $review) {
                    $reviewResults[] = $review;
                }
            }

            $results = [
                'success' => true,
                'query' => $query,
                'type' => 'reviews',
                'count' => count($reviewResults),
                'results' => $reviewResults
            ];
            break;

        case 'all':
            $songs = $songModel->searchSongs($query);
            $reviewResults = [];

            foreach ($songs as $song) {
                $songReviews = $reviewModel->getReviewsBySongId($song['id']);
                foreach ($songReviews as $review) {
                    $reviewResults[] = $review;
                }
            }

            $results = [
                'success' => true,
                'query' => $query,
                'type' => 'all',
                'songs' => [
                    'count' => count($songs),
                    'results' => $songs
                ],
                'reviews' => [
                    'count' => count($reviewResults),
                    'results' => $reviewResults
                ]
            ];
            break;

        default:
            $results = [
                'success' => false,
                'error' => 'Invalid search type. Use: songs, reviews, or all'
            ];
    }

    echo json_encode($results, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Search failed: ' . $e->getMessage()
    ]);
}
