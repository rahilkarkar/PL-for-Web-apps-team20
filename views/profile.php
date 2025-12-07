<?php
// Get follower counts and following status
require_once 'models/FollowerModel.php';
require_once 'models/UserModel.php';
global $pdo;
$followerModel = new FollowerModel($pdo);
$userModel = new UserModel($pdo);

$viewingUserId = $_SESSION['user_id'] ?? null;
$followerCounts = $followerModel->getCounts($viewingUserId);
$currentUser = $viewingUserId ? $userModel->getUserById($viewingUserId) : null;
$isOwnProfile = true; // For now, always viewing own profile
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Profile</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/profile.css" />
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a class="active" href="index.php?action=profile">Profile</a>
        <a href="index.php?action=settings">Settings</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
      <?php endif; ?>
      <a href="index.php?action=songs">Songs</a>
      <a href="index.php?action=listenList">Wishlist</a>
    </nav>

    <form class="search">
      <input type="search" placeholder="Search" aria-label="Search music" />
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

        <!-- Display Full Name if Available -->
        <?php if (!empty($currentUser['first_name']) || !empty($currentUser['last_name'])): ?>
          <p style="margin: 0.25rem 0; color: rgba(255,255,255,0.7); font-size: 1.1rem;">
            <?= htmlspecialchars(trim(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''))) ?>
          </p>
        <?php endif; ?>

        <!-- Follower/Following Stats -->
        <?php if (!empty($_SESSION['username'])): ?>
          <div style="display: flex; gap: 2rem; margin: 0.5rem 0; color: rgba(255,255,255,0.8);">
            <a href="index.php?action=followers&tab=followers" style="color: rgba(255,255,255,0.8); text-decoration: none;">
              <span><strong><?= $followerCounts['followers'] ?></strong> Followers</span>
            </a>
            <a href="index.php?action=followers&tab=following" style="color: rgba(255,255,255,0.8); text-decoration: none;">
              <span><strong><?= $followerCounts['following'] ?></strong> Following</span>
            </a>
          </div>

          <?php if (!empty($currentUser['bio'])): ?>
            <p style="margin: 1rem 0; color: rgba(255,255,255,0.7); max-width: 600px;">
              <?= htmlspecialchars($currentUser['bio']) ?>
            </p>
          <?php endif; ?>

          <?php if ($isOwnProfile): ?>
            <a href="index.php?action=settings" class="btn dark">Edit Profile</a>
          <?php else: ?>
            <!-- Follow/Unfollow button -->
            <form action="index.php?action=followUser" method="POST" style="display: inline;">
              <input type="hidden" name="following_id" value="<?= $viewingUserId ?>" />
              <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
              <button type="submit" class="btn">Follow</button>
            </form>
          <?php endif; ?>
        <?php else: ?>
          <a href="index.php?action=login" class="btn dark">Sign In</a>
        <?php endif; ?>
      </div>
    </section>

    <nav class="profile-pills">
  <a class="pill active" href="index.php?action=profile">Profile</a>
  <a class="pill" href="index.php?action=activity">Activity</a>
  <a class="pill" href="index.php?action=reviews">Reviews</a>
  <a class="pill" href="index.php?action=listenList">Wishlist</a>
  <a class="pill" href="index.php?action=playlists">Playlists</a>
    </nav>

    <section class="favorites">
      <div class="section-head">
        <h3>Recent Reviews</h3>
        <div class="rule"></div>
      </div>

      <?php
      // Get user's recent reviews to display
      if (!empty($_SESSION['user_id'])):
        require_once 'models/ReviewModel.php';
        $reviewModel = new ReviewModel($pdo);
        $recentReviews = array_slice($reviewModel->getReviewsByUserId($_SESSION['user_id']), 0, 4);
      ?>

      <?php if (!empty($recentReviews)): ?>
        <div class="album-grid">
          <?php foreach ($recentReviews as $review): ?>
            <div class="album large" style="background: linear-gradient(135deg, rgba(74, 158, 255, 0.2), rgba(255, 107, 107, 0.2)); display: flex; flex-direction: column; justify-content: flex-end; padding: 1rem; position: relative;">
              <div style="position: absolute; top: 0.5rem; right: 0.5rem; color: #ffd700; font-size: 1.2rem;">
                <?= str_repeat('★', $review['rating']) ?>
              </div>
              <div style="font-weight: 700; color: #fff; font-size: 0.9rem; text-overflow: ellipsis; overflow: hidden;">
                <?= htmlspecialchars($review['song_title']) ?>
              </div>
              <div style="font-size: 0.8rem; color: rgba(255,255,255,0.7);">
                <?= htmlspecialchars($review['artist']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="album-grid">
          <div class="album large ph"></div>
          <div class="album large ph"></div>
          <div class="album large ph"></div>
          <div class="album large ph"></div>
        </div>
        <p style="text-align: center; color: rgba(255,255,255,0.6); margin-top: 1rem;">
          No reviews yet. <a href="index.php?action=reviews" style="color: #4a9eff;">Write your first review!</a>
        </p>
      <?php endif; ?>

      <?php else: ?>
        <div class="album-grid">
          <div class="album large ph"></div>
          <div class="album large ph"></div>
          <div class="album large ph"></div>
          <div class="album large ph"></div>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
