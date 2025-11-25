<?php
// Get followers and following lists
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once 'models/FollowerModel.php';
require_once 'models/UserModel.php';
global $pdo;
$followerModel = new FollowerModel($pdo);
$userModel = new UserModel($pdo);

$tab = $_GET['tab'] ?? 'followers';
$followers = $followerModel->getFollowers($_SESSION['user_id']);
$following = $followerModel->getFollowing($_SESSION['user_id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Followers</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/profile.css" />
  <style>
    .tabs {
      display: flex;
      gap: 1rem;
      margin: 2rem 0 1rem 0;
      border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }
    .tab {
      padding: 0.75rem 1.5rem;
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      border-bottom: 3px solid transparent;
      transition: all 0.2s;
    }
    .tab:hover {
      color: rgba(255, 255, 255, 0.9);
    }
    .tab.active {
      color: #fff;
      border-bottom-color: #4a9eff;
    }
    .user-list {
      display: grid;
      gap: 1rem;
      margin-top: 2rem;
    }
    .user-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.2s;
    }
    .user-card:hover {
      background: rgba(255, 255, 255, 0.08);
    }
    .user-info {
      flex: 1;
    }
    .user-info h3 {
      margin: 0 0 0.25rem 0;
      color: #fff;
    }
    .user-info p {
      margin: 0;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.9rem;
    }
    .user-actions {
      display: flex;
      gap: 0.5rem;
    }
    .btn-unfollow {
      background: rgba(255, 107, 107, 0.3);
      border: 1px solid #ff6b6b;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-unfollow:hover {
      background: #ff6b6b;
    }
    .empty {
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
      <a href="index.php?action=logout">Sign Out</a>
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
          <?= htmlspecialchars($_SESSION['username']) ?>
        </h2>
      </div>
    </section>

    <nav class="profile-pills">
      <a class="pill" href="index.php?action=profile">Profile</a>
      <a class="pill" href="index.php?action=activity">Activity</a>
      <a class="pill" href="index.php?action=reviews">Reviews</a>
      <a class="pill" href="index.php?action=listenList">Wishlist</a>
      <a class="pill" href="index.php?action=playlists">Playlists</a>
    </nav>

    <!-- Followers/Following Tabs -->
    <div class="tabs">
      <a href="index.php?action=followers&tab=followers" class="tab <?= $tab === 'followers' ? 'active' : '' ?>" style="text-decoration: none;">
        Followers (<?= count($followers) ?>)
      </a>
      <a href="index.php?action=followers&tab=following" class="tab <?= $tab === 'following' ? 'active' : '' ?>" style="text-decoration: none;">
        Following (<?= count($following) ?>)
      </a>
    </div>

    <!-- User List -->
    <section class="user-list">
      <?php if ($tab === 'followers'): ?>
        <?php if (empty($followers)): ?>
          <div class="empty">
            <p>No followers yet. Share your music taste to attract followers!</p>
          </div>
        <?php else: ?>
          <?php foreach ($followers as $user): ?>
            <div class="user-card">
              <div class="user-info">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <small style="color: rgba(255,255,255,0.65);">
                  Following since <?= date('M j, Y', strtotime($user['followed_at'])) ?>
                </small>
              </div>
              <div class="user-actions">
                <?php if ($followerModel->isFollowing($_SESSION['user_id'], $user['id'])): ?>
                  <form action="index.php?action=unfollowUser" method="POST" style="display: inline;">
                    <input type="hidden" name="following_id" value="<?= $user['id'] ?>" />
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                    <button type="submit" class="btn-unfollow">Unfollow</button>
                  </form>
                <?php else: ?>
                  <form action="index.php?action=followUser" method="POST" style="display: inline;">
                    <input type="hidden" name="following_id" value="<?= $user['id'] ?>" />
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                    <button type="submit" class="btn dark">Follow Back</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      <?php else: // Following tab ?>
        <?php if (empty($following)): ?>
          <div class="empty">
            <p>You're not following anyone yet. <a href="index.php?action=discover" style="color: #4a9eff;">Discover users</a></p>
          </div>
        <?php else: ?>
          <?php foreach ($following as $user): ?>
            <div class="user-card">
              <div class="user-info">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <small style="color: rgba(255,255,255,0.65);">
                  Following since <?= date('M j, Y', strtotime($user['followed_at'])) ?>
                </small>
              </div>
              <div class="user-actions">
                <form action="index.php?action=unfollowUser" method="POST" style="display: inline;">
                  <input type="hidden" name="following_id" value="<?= $user['id'] ?>" />
                  <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                  <button type="submit" class="btn-unfollow">Unfollow</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </section>

  </main>
</body>
</html>
