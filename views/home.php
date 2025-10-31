<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Discover & Review Music</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/index.css" />
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a href="index.php?action=profile">Profile</a>
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
      <input type="search" placeholder="Search" />
      <button type="submit">🔍</button>
    </form>
  </header>

  <main class="container hero">
    <div class="hero-albums">
      <div class="album ph"></div>
      <div class="album ph"></div>
      <div class="album ph"></div>
      <div class="album ph"></div>
    </div>

    <div class="hero-copy">
      <h1>Don’t just listen to music, <span class="accent">review</span> it.</h1>
      <p>Share your favorite tunes and your unfiltered opinions with your friends.</p>

      <?php if (!empty($_SESSION['username'])): ?>
        <a class="cta" href="index.php?action=profile">Go to Profile</a>
      <?php else: ?>
        <a class="cta" href="index.php?action=login">Join Now</a>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>

