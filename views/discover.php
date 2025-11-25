<?php
// User discovery - find other users to follow
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once 'models/UserModel.php';
require_once 'models/FollowerModel.php';
global $pdo;
$userModel = new UserModel($pdo);
$followerModel = new FollowerModel($pdo);

// Get all users except current user
$allUsers = $userModel->getAllUsers();
$suggestedUsers = array_filter($allUsers, function($user) {
    return $user['id'] != $_SESSION['user_id'];
});
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Discover Users</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/profile.css" />
  <style>
    .discover-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
    .user-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    .user-card:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(74, 158, 255, 0.5);
      transform: translateY(-4px);
    }
    .user-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0 auto 1rem auto;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: bold;
      color: white;
    }
    .user-card h3 {
      margin: 0.5rem 0;
      color: #fff;
    }
    .user-card p {
      margin: 0.25rem 0;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.9rem;
    }
    .user-bio {
      margin: 1rem 0;
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.85rem;
      min-height: 40px;
    }
    .follow-btn {
      margin-top: 1rem;
      width: 100%;
    }
    .following-badge {
      background: rgba(81, 207, 102, 0.2);
      color: #51cf66;
      padding: 0.25rem 0.75rem;
      border-radius: 12px;
      font-size: 0.75rem;
      margin-top: 0.5rem;
      display: inline-block;
    }
    .empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 3rem;
      color: rgba(255, 255, 255, 0.5);
    }
  </style>
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <a href="index.php?action=profile">Profile</a>
      <a href="index.php?action=songs">Songs</a>
      <a href="index.php?action=listenList">Wishlist</a>
      <a class="active" href="index.php?action=discover">Discover</a>
      <a href="index.php?action=logout">Sign Out</a>
    </nav>

    <form class="search">
      <input type="search" placeholder="Search" aria-label="Search music" />
      <button type="submit" aria-label="Submit search">🔍</button>
    </form>
  </header>

  <main class="container">
    <section>
      <h1 style="margin-bottom: 0.5rem;">👥 Discover Users</h1>
      <p style="color: rgba(255,255,255,0.6); margin-bottom: 2rem;">Find other music lovers and follow them!</p>

      <div class="discover-grid">
        <?php if (empty($suggestedUsers)): ?>
          <div class="empty">
            <p>No users to discover yet. Invite your friends to join JukeBoxed!</p>
          </div>
        <?php else: ?>
          <?php foreach ($suggestedUsers as $user): ?>
            <?php
              $isFollowing = $followerModel->isFollowing($_SESSION['user_id'], $user['id']);
              $initial = strtoupper(substr($user['username'], 0, 1));
            ?>
            <div class="user-card">
              <div class="user-avatar"><?= $initial ?></div>
              <h3><?= htmlspecialchars($user['username']) ?></h3>
              <p><?= htmlspecialchars($user['email']) ?></p>

              <?php if (!empty($user['bio'])): ?>
                <div class="user-bio"><?= htmlspecialchars(substr($user['bio'], 0, 100)) ?><?= strlen($user['bio']) > 100 ? '...' : '' ?></div>
              <?php else: ?>
                <div class="user-bio" style="font-style: italic; opacity: 0.5;">No bio yet</div>
              <?php endif; ?>

              <?php if ($isFollowing): ?>
                <span class="following-badge">✓ Following</span>
                <form action="index.php?action=unfollowUser" method="POST">
                  <input type="hidden" name="following_id" value="<?= $user['id'] ?>" />
                  <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                  <button type="submit" class="btn dark follow-btn">Unfollow</button>
                </form>
              <?php else: ?>
                <form action="index.php?action=followUser" method="POST">
                  <input type="hidden" name="following_id" value="<?= $user['id'] ?>" />
                  <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                  <button type="submit" class="btn follow-btn">Follow</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>
