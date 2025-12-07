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
      <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="color: #E8F0F2; margin: 0 0 8px; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px;">
          Create Your Account
        </h2>
        <p style="color: rgba(232, 240, 242, 0.6); margin: 0; font-size: 0.95rem;">
          Join JukeBoxed to discover and share music
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

      <form class="signin-form" action="index.php?action=registerUser" method="post" id="registerForm" novalidate>
        <div style="margin-bottom: 1.1rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Username
          </label>
          <input
            type="text"
            name="username"
            id="username"
            placeholder="Choose a username"
            class="signin-input"
            required
            autocomplete="username">
          <span class="error-msg" id="username-error"></span>
        </div>

        <div style="margin-bottom: 1.1rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Email Address
          </label>
          <input
            type="email"
            name="email"
            id="email"
            placeholder="Enter your email"
            class="signin-input"
            required
            autocomplete="email">
          <span class="error-msg" id="email-error"></span>
        </div>

        <div style="margin-bottom: 1.1rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Password
          </label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Create a password"
            class="signin-input"
            required
            autocomplete="new-password">
          <span class="error-msg" id="password-error"></span>
        </div>

        <div style="margin-bottom: 1.1rem;">
          <label style="display: block; margin-bottom: 10px; color: #E8F0F2; font-weight: 600; font-size: 0.95rem;">
            Confirm Password
          </label>
          <input
            type="password"
            name="confirm_password"
            id="confirm_password"
            placeholder="Confirm your password"
            class="signin-input"
            required
            autocomplete="new-password">
          <span class="error-msg" id="confirm-error"></span>
        </div>

        <button type="submit" class="login-btn">CREATE ACCOUNT</button>

        <p class="signup-link">
          Already have an account? <a href="index.php?action=login">Sign In</a>
        </p>
      </form>
    </div>
  </main>

  <style>
    .error-msg {
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
    .input-success {
      border-color: rgba(81, 207, 102, 0.8) !important;
      background: rgba(81, 207, 102, 0.05) !important;
    }
  </style>

  <script>
    // Register form validation with visual feedback
    const form = document.getElementById('registerForm');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');

    // Arrow function for password strength indicator (Sprint Requirement: Arrow Function)
    const checkPasswordStrength = (password) => {
      let strength = 0;
      if (password.length >= 6) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[^A-Za-z0-9]/.test(password)) strength++;
      return strength;
    };

    // Real-time username validation
    usernameInput.addEventListener('input', function() {
      const error = document.getElementById('username-error');
      const username = this.value.trim();

      if (username.length === 0) {
        error.textContent = '';
        this.classList.remove('input-error', 'input-success');
      } else if (username.length < 3) {
        error.textContent = 'Username must be at least 3 characters';
        this.classList.add('input-error');
        this.classList.remove('input-success');
      } else {
        error.textContent = '✓ Username looks good';
        error.style.color = '#51cf66';
        this.classList.remove('input-error');
        this.classList.add('input-success');
      }
    });

    // Real-time email validation
    emailInput.addEventListener('input', function() {
      const error = document.getElementById('email-error');
      const email = this.value.trim();
      const emailRegex = /^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/;

      if (email.length === 0) {
        error.textContent = '';
        this.classList.remove('input-error', 'input-success');
      } else if (!emailRegex.test(email)) {
        error.textContent = 'Please enter a valid email';
        this.classList.add('input-error');
        this.classList.remove('input-success');
      } else {
        error.textContent = '✓ Email format is valid';
        error.style.color = '#51cf66';
        this.classList.remove('input-error');
        this.classList.add('input-success');
      }
    });

    // Real-time password validation with strength indicator
    passwordInput.addEventListener('input', function() {
      const error = document.getElementById('password-error');
      const password = this.value;
      const strength = checkPasswordStrength(password);

      if (password.length === 0) {
        error.textContent = '';
        this.classList.remove('input-error', 'input-success');
        return;
      }

      if (password.length < 6 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
        error.textContent = 'Need: 6+ chars, 1 uppercase, 1 number';
        error.style.color = '#ff6b6b';
        this.classList.add('input-error');
        this.classList.remove('input-success');
      } else {
        const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];
        error.textContent = `✓ Password strength: ${strengthLabels[strength - 1]}`;
        error.style.color = '#51cf66';
        this.classList.remove('input-error');
        this.classList.add('input-success');
      }

      // Check confirm password match
      if (confirmInput.value.length > 0) {
        confirmInput.dispatchEvent(new Event('input'));
      }
    });

    // Real-time confirm password validation
    confirmInput.addEventListener('input', function() {
      const error = document.getElementById('confirm-error');
      const confirm = this.value;
      const password = passwordInput.value;

      if (confirm.length === 0) {
        error.textContent = '';
        this.classList.remove('input-error', 'input-success');
      } else if (confirm !== password) {
        error.textContent = 'Passwords do not match';
        error.style.color = '#ff6b6b';
        this.classList.add('input-error');
        this.classList.remove('input-success');
      } else {
        error.textContent = '✓ Passwords match';
        error.style.color = '#51cf66';
        this.classList.remove('input-error');
        this.classList.add('input-success');
      }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
      const username = usernameInput.value.trim();
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      const confirm = confirmInput.value;
      let isValid = true;

      // Validate all fields
      if (username.length < 3) {
        document.getElementById('username-error').textContent = 'Username must be at least 3 characters';
        document.getElementById('username-error').style.color = '#ff6b6b';
        usernameInput.classList.add('input-error');
        isValid = false;
      }

      const emailRegex = /^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/;
      if (!emailRegex.test(email)) {
        document.getElementById('email-error').textContent = 'Please enter a valid email';
        document.getElementById('email-error').style.color = '#ff6b6b';
        emailInput.classList.add('input-error');
        isValid = false;
      }

      if (password.length < 6 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
        document.getElementById('password-error').textContent = 'Password must have 6+ chars, 1 uppercase, 1 number';
        document.getElementById('password-error').style.color = '#ff6b6b';
        passwordInput.classList.add('input-error');
        isValid = false;
      }

      if (password !== confirm) {
        document.getElementById('confirm-error').textContent = 'Passwords must match';
        document.getElementById('confirm-error').style.color = '#ff6b6b';
        confirmInput.classList.add('input-error');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        const firstError = document.querySelector('.input-error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  </script>
</body>
</html>
