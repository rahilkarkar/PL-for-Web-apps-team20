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

      <form class="signin-form" action="index.php?action=registerUser" method="post" id="registerForm" novalidate>
        <input
          type="text"
          name="username"
          id="username"
          placeholder="Username"
          class="signin-input"
          required
          autocomplete="username">
        <span class="error-msg" id="username-error"></span>

        <input
          type="email"
          name="email"
          id="email"
          placeholder="Email"
          class="signin-input"
          required
          autocomplete="email">
        <span class="error-msg" id="email-error"></span>

        <input
          type="password"
          name="password"
          id="password"
          placeholder="Password"
          class="signin-input"
          required
          autocomplete="new-password">
        <span class="error-msg" id="password-error"></span>

        <input
          type="password"
          name="confirm_password"
          id="confirm_password"
          placeholder="Confirm Password"
          class="signin-input"
          required
          autocomplete="new-password">
        <span class="error-msg" id="confirm-error"></span>

        <button type="submit" class="login-btn">CREATE ACCOUNT</button>

        <p class="signup-link">
          Already have an account? <a href="index.php?action=login">Sign In</a>
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
    .input-success {
      border: 2px solid #51cf66 !important;
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
        // Scroll to first error
        const firstError = document.querySelector('.input-error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  </script>
</body>
</html>
