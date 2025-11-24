<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Playlists</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/playlist.css" />
  <style>
    .playlist-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .playlist-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      padding: 1rem;
      height: 220px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .playlist-preview {
      display: flex;
      height: 70%;
      gap: 0.3rem;
    }

    .preview-song {
      flex: 1;
      background: rgba(255,255,255,0.08);
      border-radius: 6px;
    }

    .playlist-title {
      font-weight: 700;
      color: white;
    }

    .create-playlist-box {
      height: 220px;
      border: 2px dashed rgba(255,255,255,0.3);
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 10px;
      font-size: 3rem;
      color: rgba(255,255,255,0.7);
      cursor: pointer;
      transition: 0.2s;
    }

    .create-playlist-box:hover {
      background: rgba(255,255,255,0.1);
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

<main class="container profile">

  <section class="profile-top">
    <div class="avatar"></div>
    <div class="user-meta">
      <h2 class="username"><?= htmlspecialchars($_SESSION['username']) ?></h2>
    </div>
  </section>

  <nav class="profile-pills">
    <a class="pill" href="index.php?action=profile">Profile</a>
    <a class="pill" href="index.php?action=activity">Activity</a>
    <a class="pill" href="index.php?action=reviews">Reviews</a>
    <a class="pill" href="index.php?action=listenList">Wishlist</a>
    <a class="pill active" href="index.php?action=playlists">Playlists</a>
  </nav>


  <!-- create and list playlists -->
  <section>
    <div class="section-head">
      <h3>Your Playlists</h3>
    </div>

    <div class="playlist-grid">

      <!-- create new playlist -->
      <div class="create-playlist-box" onclick="document.getElementById('createPlaylistForm').style.display='block'">
        +
      </div>

      <!-- user playlist -->
      <?php if (!empty($playlists)): ?>
        <?php foreach ($playlists as $pl): ?>
          <div class="playlist-card">
            <div class="playlist-preview">
              <?php if (!empty($pl['songs'])): ?>
                <?php foreach (array_slice($pl['songs'], 0, 4) as $song): ?>
                  <div class="preview-song"></div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="preview-song"></div>
              <?php endif; ?>
            </div>
            <div class="playlist-title"><?= htmlspecialchars($pl['name']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </section>
</main>


<!-- create playlist popup -->
<div id="createPlaylistForm" style="display:none; position:fixed; top:30%; left:50%; transform:translateX(-50%);
     background:#222; padding:2rem; border-radius:8px;">
  <form action="index.php?action=createPlaylist" method="POST">
    <h3>Create New Playlist</h3>
    <input type="text" name="playlist_name" placeholder="Playlist name" required>
    <button type="submit" class="btn dark">Create</button>
  </form>
</div>

</body>
</html>
