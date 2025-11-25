<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — <?= htmlspecialchars($playlist['name'] ?? 'Playlist') ?></title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/profile.css" />
  <style>
    .playlist-header {
      background: linear-gradient(135deg, rgba(74, 158, 255, 0.2), rgba(118, 75, 162, 0.2));
      padding: 2rem;
      border-radius: 12px;
      margin-bottom: 2rem;
    }
    .playlist-header h1 {
      margin: 0 0 0.5rem 0;
      color: #fff;
    }
    .playlist-meta {
      color: rgba(255,255,255,0.7);
    }
    .songs-list {
      display: grid;
      gap: 1rem;
    }
    .song-item {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.2s;
    }
    .song-item:hover {
      background: rgba(255, 255, 255, 0.08);
    }
    .song-info h3 {
      margin: 0 0 0.25rem 0;
      color: #fff;
    }
    .song-info p {
      margin: 0;
      color: rgba(255,255,255,0.6);
      font-size: 0.9rem;
    }
    .song-actions {
      display: flex;
      gap: 0.5rem;
    }
    .btn-remove {
      background: rgba(255, 107, 107, 0.3);
      border: 1px solid #ff6b6b;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.9rem;
    }
    .btn-remove:hover {
      background: #ff6b6b;
    }
    .empty-playlist {
      text-align: center;
      padding: 3rem;
      color: rgba(255,255,255,0.7);
    }
    .back-btn {
      display: inline-block;
      margin-bottom: 1.5rem;
      padding: 0.5rem 1rem;
      background: rgba(255,255,255,0.1);
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: all 0.2s;
    }
    .back-btn:hover {
      background: rgba(255,255,255,0.2);
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
      <a class="active" href="index.php?action=playlists">Playlists</a>
      <a href="index.php?action=logout">Sign Out</a>
    </nav>
  </header>

  <main class="container">
    <a href="index.php?action=playlists" class="back-btn">← Back to Playlists</a>

    <div class="playlist-header">
      <h1><?= htmlspecialchars($playlist['name'] ?? 'Playlist') ?></h1>
      <p class="playlist-meta">
        <?= count($playlistSongs) ?> songs
        <?php if (!empty($playlist['created_at'])): ?>
          • Created <?= date('M j, Y', strtotime($playlist['created_at'])) ?>
        <?php endif; ?>
      </p>
    </div>

    <section class="songs-list">
      <?php if (empty($playlistSongs)): ?>
        <div class="empty-playlist">
          <p>This playlist is empty.</p>
          <p><a href="index.php?action=songs" style="color: #4a9eff;">Browse songs</a> to add some!</p>
        </div>
      <?php else: ?>
        <?php foreach ($playlistSongs as $song): ?>
          <div class="song-item">
            <div class="song-info">
              <h3><?= htmlspecialchars($song['title']) ?></h3>
              <p><?= htmlspecialchars($song['artist']) ?></p>
              <?php if (!empty($song['album'])): ?>
                <p style="font-size: 0.85rem; opacity: 0.7;"><?= htmlspecialchars($song['album']) ?></p>
              <?php endif; ?>
            </div>
            <div class="song-actions">
              <form action="index.php?action=removeFromPlaylist" method="POST" style="display: inline;">
                <input type="hidden" name="playlist_id" value="<?= $playlist['id'] ?>" />
                <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
                <button type="submit" class="btn-remove">Remove</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

  </main>
</body>
</html>
