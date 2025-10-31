<?php
// Fetch user's reviews if logged in
$reviews = [];
$songs = [];
$errors = $_SESSION['review_errors'] ?? [];
unset($_SESSION['review_errors']);

if (!empty($_SESSION['user_id'])) {
    $reviews = $reviewModel->getReviewsByUserId($_SESSION['user_id']);
    $songs = $songModel->getAllSongs(100);
}

// Handle sorting
$sortBy = $_GET['sort'] ?? 'recent';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Reviews</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/reviews.css" />
  <style>
    .review-form {
      background: rgba(255, 255, 255, 0.05);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #fff;
      font-weight: 600;
    }
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border-radius: 4px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(0, 0, 0, 0.3);
      color: #fff;
      font-family: inherit;
    }
    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }
    .rating-input {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }
    .rating-input input[type="radio"] {
      margin: 0 0.2rem;
    }
    .error-message {
      background: rgba(255, 0, 0, 0.2);
      color: #ffcccc;
      padding: 0.75rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      border: 1px solid rgba(255, 0, 0, 0.3);
    }
    .success-message {
      background: rgba(0, 255, 0, 0.2);
      color: #ccffcc;
      padding: 0.75rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      border: 1px solid rgba(0, 255, 0, 0.3);
    }
    .no-reviews {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.6);
    }
  </style>
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a class="active" href="index.php?action=profile">Profile</a>
        <a href="index.php?action=songs">Songs</a>
        <a href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
        <a href="index.php?action=songs">Songs</a>
      <?php endif; ?>
    </nav>

    <form class="search" action="index.php" method="get">
      <input type="hidden" name="action" value="songs" />
      <input type="search" name="q" placeholder="Search" aria-label="Search music" />
      <button type="submit" aria-label="Submit search">🔍</button>
    </form>
  </header>

  <main class="container profile">
    <section class="profile-top">
      <div class="avatar"></div>
      <div class="user-meta">
        <h2 class="username">
          <?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest' ?>
        </h2>
      </div>
    </section>

    <nav class="profile-pills">
      <a class="pill" href="index.php?action=profile">Profile</a>
      <a class="pill" href="index.php?action=activity">Activity</a>
      <a class="pill active" href="index.php?action=reviews">Reviews</a>
      <a class="pill" href="index.php?action=listenList">Wishlist</a>
      <a class="pill" href="#">Playlists</a>
    </nav>

    <?php if (!empty($_SESSION['user_id'])): ?>

      <?php if (!empty($errors)): ?>
        <div class="error-message">
          <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
          Review submitted successfully!
        </div>
      <?php endif; ?>

      <!-- Review Submission Form -->
      <section class="review-form">
        <h3>Write a Review</h3>
        <form action="index.php?action=submitReview" method="POST">
          <div class="form-group">
            <label for="song_id">Select Song:</label>
            <select name="song_id" id="song_id" required>
              <option value="">-- Choose a song --</option>
              <?php foreach ($songs as $song): ?>
                <option value="<?= $song['id'] ?>">
                  <?= htmlspecialchars($song['title']) ?> - <?= htmlspecialchars($song['artist']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Rating:</label>
            <div class="rating-input">
              <input type="radio" name="rating" value="1" id="rating1" required />
              <label for="rating1">★</label>
              <input type="radio" name="rating" value="2" id="rating2" />
              <label for="rating2">★★</label>
              <input type="radio" name="rating" value="3" id="rating3" />
              <label for="rating3">★★★</label>
              <input type="radio" name="rating" value="4" id="rating4" />
              <label for="rating4">★★★★</label>
              <input type="radio" name="rating" value="5" id="rating5" />
              <label for="rating5">★★★★★</label>
            </div>
          </div>

          <div class="form-group">
            <label for="review_text">Your Review:</label>
            <textarea name="review_text" id="review_text" required
                      placeholder="Share your thoughts about this song..."></textarea>
          </div>

          <button type="submit" class="btn">Submit Review</button>
        </form>
      </section>

    <?php endif; ?>

    <section class="reviews-section">
      <div class="section-head">
        <h3>MY REVIEWS</h3>
        <div class="sort-controls">
          <label for="sort-select">Sort by</label>
          <select id="sort-select" name="sort" onchange="window.location.href='index.php?action=reviews&sort=' + this.value">
            <option value="recent" <?= $sortBy === 'recent' ? 'selected' : '' ?>>RECENT</option>
            <option value="rating" <?= $sortBy === 'rating' ? 'selected' : '' ?>>RATING</option>
          </select>
        </div>
      </div>

      <?php if (!empty($_SESSION['user_id']) && count($reviews) > 0): ?>
        <?php
        // Sort reviews based on selected option
        if ($sortBy === 'rating') {
            usort($reviews, function($a, $b) {
                return $b['rating'] - $a['rating'];
            });
        }

        // Display reviews using loop
        foreach ($reviews as $review):
            $stars = str_repeat('★', $review['rating']);
        ?>
          <article class="review-card">
            <div class="album-cover"></div>
            <div class="review-content">
              <div class="review-header">
                <h4 class="song-title"><?= strtoupper(htmlspecialchars($review['song_title'])) ?></h4>
                <p class="artist-name"><?= htmlspecialchars($review['artist']) ?></p>
              </div>
              <div class="rating"><?= $stars ?></div>
              <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
              <div style="display: flex; gap: 1rem;">
                <small style="color: rgba(255,255,255,0.5);">
                  <?= date('M j, Y', strtotime($review['created_at'])) ?>
                </small>
                <form action="index.php?action=deleteReview" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this review?')">
                  <input type="hidden" name="review_id" value="<?= $review['id'] ?>" />
                  <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-reviews">
          <p>No reviews yet. <?= empty($_SESSION['user_id']) ? 'Please log in to write reviews.' : 'Write your first review above!' ?></p>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
