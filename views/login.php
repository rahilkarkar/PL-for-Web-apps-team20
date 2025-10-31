<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Sign In</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/login.css" />
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
  <a class="active" href="index.php?action=login">Sign In</a>
  <a href="index.php?action=register">Create Account</a>
  <a href="index.php?action=songs">Songs</a>
  <a href="index.php?action=listenList">Wishlist</a>
    </nav>

    <form class="search">
      <input type="search" placeholder="Search" aria-label="Search music" />
      <button type="submit" aria-label="Submit search">🔍</button>
    </form>
  </header>

  <main class="signin-content">
    <div class="signin-modal">
      <?php if (!empty($errors)): ?>
        <ul style="color: #ffb4b4; margin-bottom:12px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form class="signin-form" action="index.php?action=authenticate" method="post" novalidate>
        <input 
          type="text" 
          name="email"
          placeholder="enter email"
          class="signin-input"
          required
          autocomplete="username">

        <input 
          type="password" 
          name="password"
          placeholder="enter password"
          class="signin-input"
          required
          autocomplete="current-password">

        <label style="display:flex;gap:8px;align-items:center;margin:6px 0;">
          <input type="checkbox" name="remember"> Remember me
        </label>

        <button type="submit" class="login-btn">LOG IN</button>

        <p class="signup-link">
          Don't have an account? <a href="index.php?action=register">Create one</a>
        </p>
      </form>
    </div>
  </main>
</body>
</html>

