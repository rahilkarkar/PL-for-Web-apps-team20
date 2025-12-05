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
      <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="color: #E8F0F2; margin: 0 0 8px; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px;">
          Welcome Back
        </h2>
        <p style="color: rgba(232, 240, 242, 0.6); margin: 0; font-size: 0.95rem;">
          Sign in to continue to JukeBoxed
        </p>
      </div>

      <?php if (!empty($errors)): ?>
        <div style="background: rgba(255, 107, 107, 0.15); border: 2px solid rgba(255, 107, 107, 0.4); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
          <ul style="color: #ff6b6b; margin: 0; padding-left: 20px; list-style-position: outside;">
            <?php foreach ($errors as $e): ?>
              <li style="margin-bottom: 6px; font-weight: 500;"><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="signin-form" action="index.php?action=authenticate" method="post" id="loginForm" novalidate>
        <div style="margin-bottom: 1.2rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Email Address
          </label>
          <input
            type="text"
            name="email"
            id="email"
            placeholder="Enter your email"
            class="signin-input"
            required
            autocomplete="username">
          <span class="error-msg" id="email-error"></span>
        </div>

        <div style="margin-bottom: 1.2rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Password
          </label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter your password"
            class="signin-input"
            required
            autocomplete="current-password">
          <span class="error-msg" id="password-error"></span>
        </div>

        <label style="display: flex; gap: 10px; align-items: center; margin: 12px 0 20px; color: #E8F0F2; font-size: 0.9rem; cursor: pointer;">
          <input type="checkbox" name="remember" style="cursor: pointer; width: 18px; height: 18px; accent-color: #7ab8d9;">
          <span>Remember me for 30 days</span>
        </label>

        <button type="submit" class="login-btn">LOG IN</button>

        <p class="signup-link">
          Don't have an account? <a href="index.php?action=register">Create one</a>
        </p>
      </form>
    </div>
  </main>

  <style>
    .error-msg {
      color: #ff6b6b;
      font-size: 0.875rem;
      display: block;
      margin-top: 8px;
      font-weight: 500;
      min-height: 20px;
    }
    .input-error {
      border-color: rgba(255, 107, 107, 0.8) !important;
      background: rgba(255, 107, 107, 0.05) !important;
    }
  </style>

  <script>
    // Login form validation (Sprint Requirement: Client-side validation)
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Validation object (Sprint Requirement: JavaScript Object)
    const validator = {
      emailRegex: /^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/,
      passwordRegex: /^(?=.*[A-Z])(?=.*[0-9]).{6,}$/,

      validateEmail: function(email) {
        return this.emailRegex.test(email);
      },

      validatePassword: function(password) {
        return this.passwordRegex.test(password);
      },

      showError: function(inputElement, errorElement, message) {
        errorElement.textContent = message;
        inputElement.classList.add('input-error');
      },

      clearError: function(inputElement, errorElement) {
        errorElement.textContent = '';
        inputElement.classList.remove('input-error');
      }
    };

    // Event listeners for real-time validation
    emailInput.addEventListener('blur', function() {
      const error = document.getElementById('email-error');
      if (!validator.validateEmail(this.value.trim())) {
        validator.showError(this, error, 'Please enter a valid email address');
      } else {
        validator.clearError(this, error);
      }
    });

    passwordInput.addEventListener('blur', function() {
      const error = document.getElementById('password-error');
      if (!validator.validatePassword(this.value)) {
        validator.showError(this, error, 'Password must be 6+ chars with 1 uppercase and 1 number');
      } else {
        validator.clearError(this, error);
      }
    });

    // Form submission validation
    loginForm.addEventListener('submit', function(e) {
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      const emailError = document.getElementById('email-error');
      const passwordError = document.getElementById('password-error');
      let isValid = true;

      // Validate email
      if (!validator.validateEmail(email)) {
        validator.showError(emailInput, emailError, 'Please enter a valid email address');
        isValid = false;
      } else {
        validator.clearError(emailInput, emailError);
      }

      // Validate password
      if (!validator.validatePassword(password)) {
        validator.showError(passwordInput, passwordError, 'Password must be 6+ chars with 1 uppercase and 1 number');
        isValid = false;
      } else {
        validator.clearError(passwordInput, passwordError);
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>

