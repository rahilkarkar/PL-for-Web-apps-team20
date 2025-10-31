<?php
// $listenList is already loaded from index.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Wishlist</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/reviews.css" />
  <style>
    .listen-list-section {
      padding: 2rem 0;
    }
    .song-item {
      background: rgba(255, 255, 255, 0.05);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .song-item:hover {
      background: rgba(255, 255, 255, 0.08);
    }
    .song-details h3 {
      margin: 0 0 0.5rem 0;
      color: #fff;
    }
    .song-details p {
      margin: 0.25rem 0;
      color: rgba(255, 255, 255, 0.7);
    }
    .song-meta {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.5);
    }
    .no-songs {
      text-align: center;
      padding: 3rem;
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
        <a href="index.php?action=profile">Profile</a>
        <a href="index.php?action=songs">Songs</a>
        <a class="active" href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
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
      <a class="pill" href="index.php?action=reviews">Reviews</a>
      <a class="pill active" href="index.php?action=listenList">Wishlist</a>
      <a class="pill" href="#">Playlists</a>
    </nav>

    <section class="listen-list-section">
      <h3>MY WISHLIST</h3>

      <?php if (count($listenList) > 0): ?>
        <?php foreach ($listenList as $song): ?>
          <article class="song-item">
            <div class="song-details">
              <h3><?= htmlspecialchars($song['title']) ?></h3>
              <p><?= htmlspecialchars($song['artist']) ?></p>
              <div class="song-meta">
                <?php if (!empty($song['album'])): ?>
                  Album: <?= htmlspecialchars($song['album']) ?>
                  <?php if (!empty($song['release_year'])): ?>
                    (<?= htmlspecialchars($song['release_year']) ?>)
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (!empty($song['genre'])): ?>
                  • Genre: <?= htmlspecialchars($song['genre']) ?>
                <?php endif; ?>
                <br />
                <small>Added: <?= date('M j, Y', strtotime($song['added_at'])) ?></small>
              </div>
            </div>
            <form action="index.php?action=removeFromListenList" method="POST"
                  onsubmit="return confirm('Remove this song from your wishlist?')">
              <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
              <input type="hidden" name="redirect" value="index.php?action=listenList" />
              <button type="submit" class="btn">Remove</button>
            </form>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-songs">
          <p>Your wishlist is empty.</p>
          <p><a href="index.php?action=songs" class="btn dark">Browse Songs</a></p>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
