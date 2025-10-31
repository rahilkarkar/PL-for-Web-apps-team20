<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Activity</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/activity.css" />
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a class="active" href="index.php?action=profile">Profile</a>
        <a href="#">Songs</a>
        <a href="#">Lists</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
        <a href="#">Songs</a>
        <a href="#">Lists</a>
      <?php endif; ?>
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
      </div>
    </section>

    <nav class="profile-pills">
      <a class="pill" href="index.php?action=profile">Profile</a>
      <a class="pill active" href="index.php?action=activity">Activity</a>
      <a class="pill" href="index.php?action=reviews">Reviews</a>
      <a class="pill" href="#">Wishlist</a>
      <a class="pill" href="#">Playlists</a>
    </nav>

    <section class="activity-feed">
      <div class="section-head">
        <h3><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'USER' ?></h3>
        <span class="following-badge">FOLLOWING</span>
      </div>

      <!-- Static demo items from your original; later you can render from DB -->
      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> liked <strong>dahalnirusma's</strong> review of <strong>ORENJI</strong>
        </p>
        <time class="activity-time">6h</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> liked <strong>LILBABY's</strong> review of <strong>SOUR</strong>
        </p>
        <time class="activity-time">8h</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> rated <strong>HMU - Greek</strong> ★★★★
        </p>
        <time class="activity-time">1d</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> rated <strong>LADY - Avenoir</strong> ★★★★★
        </p>
        <time class="activity-time">1d</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> added <strong>Vie - Doja Cat</strong> into their wishlist
        </p>
        <time class="activity-time">1d</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> added <strong>Jealous Type - Doja Cat</strong> into playlist: <strong>Feel Good Songs</strong>
        </p>
        <time class="activity-time">1d</time>
      </article>

      <article class="activity-card">
        <p class="activity-text">
          <strong><?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'user' ?></strong> listened to <strong>Jealous Type - Doja Cat</strong>
        </p>
        <time class="activity-time">1d</time>
      </article>
    </section>
  </main>
</body>
</html>

