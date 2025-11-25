<?php
// Get search query if present
$searchQuery = $_GET['q'] ?? '';
$songsToDisplay = [];

if (!empty($searchQuery)) {
    $songsToDisplay = $songModel->searchSongs($searchQuery);
} else {
    $songsToDisplay = $songs; // Already loaded from index.php
}

// Get user's listen list if logged in
$userListenList = [];
$userPlaylists = [];
if (!empty($_SESSION['user_id'])) {
    $listenListData = $songModel->getListenList($_SESSION['user_id']);
    foreach ($listenListData as $item) {
        $userListenList[] = $item['id'];
    }
    $userPlaylists = $playlistModel->getUserPlaylists($_SESSION['user_id']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Songs</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/profile.css" />
  <style>
    .songs-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
      padding: 1rem 0;
    }
    .song-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 1.5rem;
      transition: transform 0.2s;
    }
    .song-card:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.08);
    }
    .song-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #fff;
    }
    .song-artist {
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 0.5rem;
    }
    .song-info {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.5);
      margin-bottom: 1rem;
    }
    .song-actions {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    .song-actions form {
      flex: 1;
      min-width: 120px;
    }
    .btn-small {
      padding: 0.5rem 1rem;
      font-size: 0.85rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
      background: rgba(74, 158, 255, 0.3);
      color: #fff;
      font-weight: 600;
    }
    .btn-small:hover {
      background: rgba(74, 158, 255, 0.5);
      transform: translateY(-1px);
    }
    .btn-small.dark {
      background: rgba(118, 75, 162, 0.3);
    }
    .btn-small.dark:hover {
      background: rgba(118, 75, 162, 0.5);
    }
    .playlist-dropdown {
      background: rgba(0,0,0,0.85);
      border: 2px solid rgba(74, 158, 255, 0.4);
      padding: 1rem;
      margin-top: 0.5rem;
      border-radius: 8px;
      color: white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .playlist-dropdown select,
    .playlist-dropdown input {
      width: 100%;
      margin-bottom: 0.5rem;
      padding: 0.6rem;
      border-radius: 6px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.1);
      color: white;
      font-size: 0.9rem;
    }
    .playlist-dropdown select:focus,
    .playlist-dropdown input:focus {
      outline: none;
      border-color: rgba(74, 158, 255, 0.6);
    }
    .playlist-dropdown button {
      width: 100%;
      margin-top: 0.3rem;
    }
    .playlist-dropdown hr {
      opacity: 0.3;
      margin: 0.8rem 0;
      border-color: rgba(255,255,255,0.2);
    }
  </style>
  <script>
    function togglePlaylistMenu(id) {
      const box = document.getElementById("playlist-box-" + id);
      box.style.display = box.style.display === "block" ? "none" : "block";
    }
  </script>
</head>

<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a href="index.php?action=profile">Profile</a>
        <a class="active" href="index.php?action=songs">Songs</a>
        <a href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=playlists">Playlists</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
        <a class="active" href="index.php?action=songs">Songs</a>
      <?php endif; ?>
    </nav>

    <form class="search" action="index.php" method="get">
      <input type="hidden" name="action" value="songs" />
      <input type="search" name="q" placeholder="Search" aria-label="Search music"
             value="<?= htmlspecialchars($searchQuery) ?>" />
      <button type="submit" aria-label="Submit search">🔍</button>
    </form>
  </header>

  <main class="container">
    <section>
      <h2 style="margin-bottom: 1rem;">
        <?= !empty($searchQuery) ? 'Search Results for "' . htmlspecialchars($searchQuery) . '"' : 'All Songs' ?>
      </h2>

      <?php if (count($songsToDisplay) > 0): ?>
        <div class="songs-grid">
          <?php foreach ($songsToDisplay as $song): ?>
            <?php
              $inListenList = in_array($song['id'], $userListenList);
            ?>
            <article class="song-card">
              <h3 class="song-title"><?= htmlspecialchars($song['title']) ?></h3>
              <p class="song-artist"><?= htmlspecialchars($song['artist']) ?></p>
              <div class="song-info">
                <?php if (!empty($song['album'])): ?>
                  <div>Album: <?= htmlspecialchars($song['album']) ?></div>
                <?php endif; ?>
                <?php if (!empty($song['genre'])): ?>
                  <div>Genre: <?= htmlspecialchars($song['genre']) ?></div>
                <?php endif; ?>
                <?php if (!empty($song['release_year'])): ?>
                  <div>Year: <?= htmlspecialchars($song['release_year']) ?></div>
                <?php endif; ?>
              </div>

              <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="song-actions">
                  <?php if ($inListenList): ?>
                    <form action="index.php?action=removeFromListenList" method="POST">
                      <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
                      <input type="hidden" name="redirect" value="index.php?action=songs<?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" />
                      <button type="submit" class="btn btn-small">Remove from Wishlist</button>
                    </form>
                  <?php else: ?>
                    <form action="index.php?action=addToListenList" method="POST">
                      <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
                      <input type="hidden" name="redirect" value="index.php?action=songs<?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" />
                      <button type="submit" class="btn btn-small dark">Add to Wishlist</button>
                    </form>
                  <?php endif; ?>

                </div>

                <!-- add to playlist button -->
                <button class="btn-small" onclick="togglePlaylistMenu(<?= $song['id'] ?>); return false;"
                        style="width: 100%; margin-top: 0.5rem;">
                  + Add to Playlist
                </button>

                <div id="playlist-box-<?= $song['id'] ?>" class="playlist-dropdown" style="display:none;">

                    <?php if (!empty($userPlaylists)): ?>
                      <!-- Add to Existing Playlist -->
                      <form action="index.php?action=addToPlaylist" method="POST">
                        <select name="playlist_id">
                          <?php foreach ($userPlaylists as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                          <?php endforeach; ?>
                        </select>

                        <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
                        <button type="submit" class="btn-small dark">Add</button>
                      </form>

                      <hr>
                    <?php endif; ?>

                    <!-- Create New Playlist and Add -->
                    <form action="index.php?action=createPlaylistAndAdd" method="POST">
                      <input type="text" name="playlist_name" placeholder="New Playlist Name" required />
                      <input type="hidden" name="song_id" value="<?= $song['id'] ?>" />
                      <button type="submit" class="btn-small">Create & Add</button>
                    </form>
                  </div>
              <?php endif; ?>
            </article>

          <?php endforeach; ?>
        </div>

      <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
          <p>No songs found. <?= !empty($searchQuery) ? 'Try a different search term.' : '' ?></p>
        </div>
      <?php endif; ?>

    </section>
  </main>
</body>
</html>
