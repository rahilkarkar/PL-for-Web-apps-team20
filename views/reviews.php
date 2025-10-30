<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Reviews</title>
  <link rel="stylesheet" href="public/css/reviews.css" />
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
      <a class="pill" href="index.php?action=activity">Activity</a>
      <a class="pill active" href="index.php?action=reviews">Reviews</a>
      <a class="pill" href="#">Wishlist</a>
      <a class="pill" href="#">Playlists</a>
    </nav>

    <section class="reviews-section">
      <div class="section-head">
        <h3>REVIEWS</h3>
        <div class="sort-controls">
          <label for="sort-select">Sort by</label>
          <select id="sort-select" name="sort">
            <option value="rating">RATING</option>
            <option value="date">DATE</option>
            <option value="likes">LIKES</option>
          </select>
        </div>
      </div>

      <!-- Static demo review cards preserved from your original markup -->
      <article class="review-card">
        <div class="album-cover"></div>
        <div class="review-content">
          <div class="review-header">
            <h4 class="song-title">COMFORT ME</h4>
            <p class="artist-name">Malcom Todd</p>
          </div>
          <div class="rating">★★★★</div>
          <p class="review-text">love this song</p>
          <button class="btn like" type="button">Like Review</button>
        </div>
      </article>

      <article class="review-card">
        <div class="album-cover"></div>
        <div class="review-content">
          <div class="review-header">
            <h4 class="song-title">INTIMIDATED</h4>
            <p class="artist-name">Kaytranada, H.E.R</p>
          </div>
          <div class="rating">★★★★★</div>
          <p class="review-text">song of the summer, perfect for car rides and beach visits</p>
          <button class="btn like" type="button">Like Review</button>
        </div>
      </article>
    </section>
  </main>
</body>
</html>
