<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Account Settings</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/settings.css" />
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
        <a href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
        <a href="index.php?action=songs">Songs</a>
        <a href="index.php?action=listenList">Wishlist</a>
      <?php endif; ?>
    </nav>

    <form class="search">
      <input type="search" placeholder="Search" aria-label="Search music" />
      <button type="submit" aria-label="Submit search">🔍</button>
    </form>
  </header>

  <main class="container settings">
    <div class="settings-header">
      <h2 class="page-title">Account Settings</h2>
    </div>
    
    <nav class="tabs">
      <button class="tab active" type="button">Profile</button>
      <button class="tab" type="button">Profile Picture</button>
      <button class="tab" type="button">Notifications</button>
    </nav>

    <section class="settings-content">
      <!-- Post to profile for now; you can later change to index.php?action=saveSettings -->
      <form class="settings-form" action="index.php?action=profile" method="post" novalidate>
        <h3 class="form-title">Profile</h3>
        
        <div class="form-group">
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username" 
            name="username"
            class="input-field"
            value="<?= !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'USER0329' ?>"
            required
            autocomplete="username">
        </div>

        <div class="form-row">
          <div class="form-group half">
            <label for="firstName">First Name</label>
            <input 
              type="text" 
              id="firstName" 
              name="firstName"
              class="input-field"
              placeholder="First name"
              autocomplete="given-name">
          </div>
          <div class="form-group half">
            <label for="lastName">Last Name</label>
            <input 
              type="text" 
              id="lastName" 
              name="lastName"
              class="input-field"
              placeholder="Last name"
              autocomplete="family-name">
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email"
            class="input-field"
            placeholder="your@email.com"
            required
            autocomplete="email">
        </div>

        <div class="form-group">
          <label for="bio">Bio</label>
          <textarea 
            id="bio" 
            name="bio"
            class="input-field textarea"
            placeholder="Tell us about yourself..."
            rows="5"></textarea>
        </div>
        
        <button type="submit" class="submit-btn">SUBMIT</button>
      </form>
    </section>
  </main>
</body>
</html>
