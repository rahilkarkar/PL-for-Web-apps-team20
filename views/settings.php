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
        <a href="index.php?action=playlists">Playlists</a>
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
      <?php
      $errors = $_SESSION['profile_errors'] ?? [];
      unset($_SESSION['profile_errors']);
      $success = isset($_GET['success']);

      // Fetch current user data
      require_once 'models/UserModel.php';
      global $pdo;
      $userModel = new UserModel($pdo);
      $currentUser = $userModel->getUserById($_SESSION['user_id']);
      ?>

      <?php if (!empty($errors)): ?>
        <div style="background: rgba(255,0,0,0.2); color: #ffcccc; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
          <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div style="background: rgba(0,255,0,0.2); color: #ccffcc; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
          Profile updated successfully!
        </div>
      <?php endif; ?>

      <form class="settings-form" action="index.php?action=updateProfile" method="post" id="settingsForm" novalidate>
        <h3 class="form-title">Profile</h3>

        <div class="form-group">
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            class="input-field"
            value="<?= htmlspecialchars($currentUser['username'] ?? '') ?>"
            required
            autocomplete="username">
          <span class="error-msg" id="username-error"></span>
        </div>

        <div class="form-row">
          <div class="form-group half">
            <label for="firstName">First Name</label>
            <input
              type="text"
              id="firstName"
              name="firstName"
              class="input-field"
              value="<?= htmlspecialchars($currentUser['first_name'] ?? '') ?>"
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
              value="<?= htmlspecialchars($currentUser['last_name'] ?? '') ?>"
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
            value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>"
            required
            autocomplete="email">
          <span class="error-msg" id="email-error"></span>
        </div>

        <div class="form-group">
          <label for="bio">Bio</label>
          <textarea
            id="bio"
            name="bio"
            class="input-field textarea"
            placeholder="Tell us about yourself..."
            rows="5"><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
          <span class="error-msg" id="bio-error"></span>
          <small style="color: rgba(255,255,255,0.7);">Max 500 characters (<span id="bio-count">0</span>/500)</small>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
          SAVE CHANGES
        </button>
      </form>
    </section>
  </main>

  <style>
    .error-msg {
      color: #ff6b6b;
      font-size: 0.85rem;
      display: block;
      margin-top: 0.25rem;
    }
    .input-error {
      border-color: #ff6b6b !important;
    }
  </style>

  <script>
    // Client-side form validation with real-time feedback
    const form = document.getElementById('settingsForm');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const bioInput = document.getElementById('bio');
    const bioCount = document.getElementById('bio-count');

    // Arrow function for character counter (Sprint Requirement: Arrow Function)
    const updateBioCount = () => {
      const count = bioInput.value.length;
      bioCount.textContent = count;
      bioCount.style.color = count > 500 ? '#ff6b6b' : 'rgba(255,255,255,0.5)';
    };

    // Event listener for bio character count (Sprint Requirement: Event Listener)
    bioInput.addEventListener('input', updateBioCount);
    updateBioCount(); // Initialize count

    // Validation functions (Sprint Requirement: Multiple Functions)
    function validateUsername() {
      const username = usernameInput.value.trim();
      const error = document.getElementById('username-error');

      if (username.length < 3) {
        error.textContent = 'Username must be at least 3 characters';
        usernameInput.classList.add('input-error');
        return false;
      }

      error.textContent = '';
      usernameInput.classList.remove('input-error');
      return true;
    }

    function validateEmail() {
      const email = emailInput.value.trim();
      const error = document.getElementById('email-error');
      const emailRegex = /^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/;

      if (!emailRegex.test(email)) {
        error.textContent = 'Please enter a valid email address';
        emailInput.classList.add('input-error');
        return false;
      }

      error.textContent = '';
      emailInput.classList.remove('input-error');
      return true;
    }

    function validateBio() {
      const bio = bioInput.value.trim();
      const error = document.getElementById('bio-error');

      if (bio.length > 500) {
        error.textContent = 'Bio must be less than 500 characters';
        bioInput.classList.add('input-error');
        return false;
      }

      error.textContent = '';
      bioInput.classList.remove('input-error');
      return true;
    }

    // Real-time validation event listeners
    usernameInput.addEventListener('blur', validateUsername);
    emailInput.addEventListener('blur', validateEmail);
    bioInput.addEventListener('blur', validateBio);

    // Form submission validation (Sprint Requirement: Client-side Input Validation)
    form.addEventListener('submit', function(e) {
      console.log('Form submit event triggered');

      const isUsernameValid = validateUsername();
      const isEmailValid = validateEmail();
      const isBioValid = validateBio();

      console.log('Validation results:', {isUsernameValid, isEmailValid, isBioValid});

      if (!isUsernameValid || !isEmailValid || !isBioValid) {
        e.preventDefault();
        console.log('Form validation FAILED - preventing submission');

        // DOM manipulation: Scroll to first error (Sprint Requirement: DOM Manipulation)
        const firstError = document.querySelector('.input-error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstError.focus();
        }
      } else {
        console.log('Form validation PASSED - submitting to server...');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
      }
    });

    // Add click listener to submit button for additional debugging
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.addEventListener('click', function(e) {
      console.log('Submit button clicked!');
    });

    // Check if form and button are properly connected
    console.log('Settings form loaded');
    console.log('Form element:', form);
    console.log('Submit button:', submitBtn);
    console.log('Form action:', form.getAttribute('action'));
  </script>
</body>
</html>
