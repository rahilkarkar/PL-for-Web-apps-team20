<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Profile</title>
  <link rel="stylesheet" href="public/css/profile.css" />
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
      <a href="#">Songs</a>
      <a href="#">Lists</a>
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
        <?php if (!empty($_SESSION['username'])): ?>
          <a href="index.php?action=settings" class="btn dark">Edit Profile</a>
        <?php else: ?>
          <a href="index.php?action=login" class="btn dark">Sign In</a>
        <?php endif; ?>
      </div>
    </section>

    <nav class="profile-pills">
      <a class="pill active" href="index.php?action=profile">Profile</a>
      <a class="pill" href="index.php?action=activity">Activity</a>
      <a class="pill" href="index.php?action=reviews">Reviews</a>
      <a class="pill" href="#">Wishlist</a>
      <a class="pill" href="#">Playlists</a>
    </nav>

    <section class="favorites">
      <div class="section-head">
        <h3>Favorite Music</h3>
        <div class="rule"></div>
      </div>

      <div class="album-grid">
        <div class="album large ph"></div>
        <div class="album large ph"></div>
        <div class="album large ph"></div>
        <div class="album large ph"></div>
      </div>
    </section>
  </main>
</body>
</html>
