<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Register</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/login.css" />
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
  <a href="index.php?action=login">Sign In</a>
  <a class="active" href="index.php?action=register">Create Account</a>
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
      <h2 style="text-align:center;color:#fff;">Create Your Account</h2>

      <?php if (!empty($errors)): ?>
        <ul style="color: #ffb4b4; margin-bottom:12px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form class="signin-form" action="index.php?action=registerUser" method="post" novalidate>
        <input 
          type="text" 
          name="username"
          placeholder="Username"
          class="signin-input"
          required
          autocomplete="username">

        <input 
          type="email" 
          name="email"
          placeholder="Email"
          class="signin-input"
          required
          autocomplete="email">

        <input 
          type="password" 
          name="password"
          placeholder="Password"
          class="signin-input"
          required
          autocomplete="new-password">

        <input 
          type="password" 
          name="confirm_password"
          placeholder="Confirm Password"
          class="signin-input"
          required
          autocomplete="new-password">

        <button type="submit" class="login-btn">CREATE ACCOUNT</button>

        <p class="signup-link">
          Already have an account? <a href="index.php?action=login">Sign In</a>
        </p>
      </form>
    </div>
  </main>
</body>
</html>
