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

      <form class="signin-form" action="index.php?action=authenticate" method="post" id="loginForm" novalidate>
        <input
          type="text"
          name="email"
          id="email"
          placeholder="enter email"
          class="signin-input"
          required
          autocomplete="username">
        <span class="error-msg" id="email-error"></span>

        <input
          type="password"
          name="password"
          id="password"
          placeholder="enter password"
          class="signin-input"
          required
          autocomplete="current-password">
        <span class="error-msg" id="password-error"></span>

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

  <style>
    .error-msg {
      color: #ff6b6b;
      font-size: 0.85rem;
      display: block;
      margin-top: 0.25rem;
      margin-bottom: 0.5rem;
    }
    .input-error {
      border: 2px solid #ff6b6b !important;
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

