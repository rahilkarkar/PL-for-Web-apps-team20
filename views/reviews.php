<?php require_once __DIR__ . '/../includes/albumcover.php'; ?>

<?php

// Fetch user's reviews if logged in
$reviews = [];
$followingReviews = [];
$songs = [];
$errors = $_SESSION['review_errors'] ?? [];
unset($_SESSION['review_errors']);

if (!empty($_SESSION['user_id'])) {
    $reviews = $reviewModel->getReviewsByUserId($_SESSION['user_id']);
    $followingReviews = $reviewModel->getFollowingReviews($_SESSION['user_id']);
    $songs = $songModel->getAllSongs(100);
}

// Handle sorting
$sortBy = $_GET['sort'] ?? 'recent';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Reviews</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/reviews.css" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    .review-form {
      background: rgba(255, 255, 255, 0.05);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #fff;
      font-weight: 600;
    }
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border-radius: 4px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(0, 0, 0, 0.3);
      color: #fff;
      font-family: inherit;
    }
    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }
    .rating-input {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }
    .rating-input input[type="radio"] {
      margin: 0 0.2rem;
    }
    .error-message {
      background: rgba(255, 0, 0, 0.2);
      color: #ffcccc;
      padding: 0.75rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      border: 1px solid rgba(255, 0, 0, 0.3);
    }
    .success-message {
      background: rgba(0, 255, 0, 0.2);
      color: #ccffcc;
      padding: 0.75rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      border: 1px solid rgba(0, 255, 0, 0.3);
    }
    .no-reviews {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.6);
    }
  </style>
</head>
<body class="bg">
  <header class="site-header">
    <a class="logo" href="index.php">
      <span class="logo-ice">Juke</span><span class="logo-core">Boxed</span>
    </a>

    <nav class="main-nav">
      <?php if (!empty($_SESSION['username'])): ?>
        <a class="active" href="index.php?action=profile">Profile</a>
        <a href="index.php?action=songs">Songs</a>
        <a href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
        <a href="index.php?action=songs">Songs</a>
      <?php endif; ?>
    </nav>

    <form class="search" action="index.php" method="get">
      <input type="hidden" name="action" value="songs" />
      <input type="search" name="q" placeholder="Search" aria-label="Search music" />
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
      <a class="pill" href="index.php?action=listenList">Wishlist</a>
      <a class="pill" href="index.php?action=playlists">Playlists</a>
    </nav>

    <?php if (!empty($_SESSION['user_id'])): ?>

      <?php if (!empty($errors)): ?>
        <div class="error-message">
          <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
          Review submitted successfully!
        </div>
      <?php endif; ?>

      <!-- Review Submission Form -->
      <section class="review-form">
        <h3>Write a Review</h3>
        <form action="index.php?action=submitReview" method="POST">
          <div class="form-group">
            <label for="song_id">Select Song:</label>
            <select name="song_id" id="song_id" required>
              <option value="">-- Choose a song --</option>
              <?php foreach ($songs as $song): ?>
                <option value="<?= $song['id'] ?>">
                  <?= htmlspecialchars($song['title']) ?> - <?= htmlspecialchars($song['artist']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Rating:</label>
            <div class="rating-input">
              <input type="radio" name="rating" value="1" id="rating1" required />
              <label for="rating1">★</label>
              <input type="radio" name="rating" value="2" id="rating2" />
              <label for="rating2">★★</label>
              <input type="radio" name="rating" value="3" id="rating3" />
              <label for="rating3">★★★</label>
              <input type="radio" name="rating" value="4" id="rating4" />
              <label for="rating4">★★★★</label>
              <input type="radio" name="rating" value="5" id="rating5" />
              <label for="rating5">★★★★★</label>
            </div>
          </div>

          <div class="form-group">
            <label for="review_text">Your Review:</label>
            <textarea name="review_text" id="review_text" required
                      placeholder="Share your thoughts about this song..."></textarea>
          </div>

          <button type="submit" class="btn">Submit Review</button>
        </form>
      </section>

    <?php endif; ?>
    <nav class="review-tabs">
  <a href="index.php?action=reviews&tab=my" class="review-tab <?= ($_GET['tab'] ?? 'my') === 'my' ? 'active' : '' ?>">My Reviews</a>
  <a href="index.php?action=reviews&tab=following" class="review-tab <?= ($_GET['tab'] ?? 'my') === 'following' ? 'active' : '' ?>">Following</a>
</nav>

<section class="reviews-section">
  <?php $tab = $_GET['tab'] ?? 'my'; ?>
  <div class="section-head">
    <h3>
      <?= $tab === 'following' ? "FOLLOWING'S REVIEWS" : "MY REVIEWS" ?>
    </h3>
    <div class="sort-controls">
      <label for="sort-select">Sort by</label>
      <select id="sort-select" name="sort" onchange="window.location.href='index.php?action=reviews&sort=' + this.value">
        <option value="recent" <?= $sortBy === 'recent' ? 'selected' : '' ?>>RECENT</option>
        <option value="rating" <?= $sortBy === 'rating' ? 'selected' : '' ?>>RATING</option>
      </select>
    </div>
  </div>

  <?php $list = ($tab === 'following') ? $followingReviews : $reviews; ?>

  <?php if (!empty($_SESSION['user_id']) && count($list) > 0): ?>
    <?php foreach ($list as $review): ?>
      <?php $stars = str_repeat('★', $review['rating']); ?>
      <?php $bg = getRandomGradient($review['id']); ?>
      <article class="review-card">
        <div class="album-cover" style="background: <?= $bg ?>;"></div>
        <div class="review-content">
          <div class="review-header">
            <h4 class="song-title"><?= strtoupper(htmlspecialchars($review['song_title'])) ?></h4>
            <p class="artist-name"><?= htmlspecialchars($review['artist']) ?></p>

            <?php if ($tab === 'following'): ?>
              <p style="color: var(--subtle); font-size: .85rem;">
                Reviewed by <strong><?= htmlspecialchars($review['username']) ?></strong>
              </p>
            <?php endif; ?>
          </div>

          <div class="rating"><?= $stars ?></div>
          <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>

          <small style="color: rgba(255,255,255,0.7);">
            <?= date('M j, Y', strtotime($review['created_at'])) ?>
          </small>

          <?php if ($tab === 'my'): ?>
            <form action="index.php?action=deleteReview" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?')">
              <input type="hidden" name="review_id" value="<?= $review['id'] ?>" />
              <button type="submit" class="btn" style="padding: .25rem .5rem; font-size: .85rem;">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>

  <?php else: ?>
    <div class="no-reviews">
      <p>
        <?= $tab === 'following' 
            ? "People you follow haven't posted any reviews yet." 
            : "No reviews yet. Write your first review above!" ?>
      </p>
    </div>
  <?php endif; ?>
</section>


  <script>
    // jQuery implementation (Sprint Requirement: Use jQuery)
    $(document).ready(function() {
      // Star rating hover effect with jQuery
      $('.rating-input label').hover(
        function() {
          // Mouse enter: highlight this and all previous stars
          $(this).css('color', '#ffd700');
          $(this).prevAll('label').css('color', '#ffd700');
        },
        function() {
          // Mouse leave: reset colors for non-selected stars
          $('.rating-input label').css('color', 'rgba(255,255,255,0.7)');
          // Keep selected star highlighted
          $('.rating-input input:checked').next('label').css('color', '#ffd700')
            .prevAll('label').css('color', '#ffd700');
        }
      );

      // When a rating is selected, keep it highlighted
      $('.rating-input input[type="radio"]').on('change', function() {
        $('.rating-input label').css('color', 'rgba(255,255,255,0.7)');
        $(this).next('label').css('color', '#ffd700')
          .prevAll('label').css('color', '#ffd700');
      });

      // Form validation with jQuery (Sprint Requirement: Client-side validation)
      $('form[action*="submitReview"]').on('submit', function(e) {
        const songId = $('#song_id').val();
        const rating = $('input[name="rating"]:checked').val();
        const reviewText = $('#review_text').val().trim();
        let errors = [];

        if (!songId || songId === '') {
          errors.push('Please select a song');
        }
        if (!rating) {
          errors.push('Please select a rating');
        }
        if (reviewText.length < 3) {
          errors.push('Review must be at least 3 characters');
        }

        if (errors.length > 0) {
          e.preventDefault();
          // DOM manipulation with jQuery
          let errorHtml = '<div class="error-message" style="margin-top: 1rem;"><ul style="margin: 0; padding-left: 1.5rem;">';
          errors.forEach(function(err) {
            errorHtml += '<li>' + err + '</li>';
          });
          errorHtml += '</ul></div>';

          // Remove old errors and add new ones
          $('.review-form .error-message').remove();
          $('.review-form h3').after(errorHtml);

          // Smooth scroll to error
          $('html, body').animate({
            scrollTop: $('.error-message').offset().top - 100
          }, 500);
        }
      });

      // Filter reviews by rating with jQuery animation (Sprint Requirement: DOM Manipulation)
      $('<div class="filter-buttons" style="margin: 1rem 0;"></div>').insertAfter('.section-head');
      const filterButtons = `
        <button class="filter-btn active" data-filter="all">All Reviews</button>
        <button class="filter-btn" data-filter="5">5★ Only</button>
        <button class="filter-btn" data-filter="4">4★+</button>
        <button class="filter-btn" data-filter="3">3★+</button>
      `;
      $('.filter-buttons').html(filterButtons);

      // Style filter buttons
      $('.filter-btn').css({
        padding: '0.5rem 1rem',
        margin: '0 0.5rem 0.5rem 0',
        border: '1px solid rgba(255,255,255,0.3)',
        background: 'rgba(0,0,0,0.3)',
        color: 'rgba(255,255,255,0.7)',
        borderRadius: '4px',
        cursor: 'pointer',
        transition: 'all 0.2s'
      });

      $('.filter-btn.active').css({
        background: '#4a9eff',
        color: '#fff',
        borderColor: '#4a9eff'
      });

      // Filter functionality
      $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');

        // Update active button
        $('.filter-btn').removeClass('active').css({
          background: 'rgba(0,0,0,0.3)',
          color: 'rgba(255,255,255,0.7)',
          borderColor: 'rgba(255,255,255,0.3)'
        });
        $(this).addClass('active').css({
          background: '#4a9eff',
          color: '#fff',
          borderColor: '#4a9eff'
        });

        // Show/hide reviews with animation
        if (filter === 'all') {
          $('.review-card').fadeIn(300);
        } else {
          $('.review-card').each(function() {
            const stars = $(this).find('.rating').text().length;
            if (stars >= parseInt(filter)) {
              $(this).fadeIn(300);
            } else {
              $(this).fadeOut(300);
            }
          });
        }
      });

      // Character counter for review textarea
      const maxLength = 500;
      $('#review_text').after('<small style="color: rgba(255,255,255,0.7); display: block; margin-top: 0.25rem;">Characters: <span id="char-count">0</span>/' + maxLength + '</small>');

      $('#review_text').on('input', function() {
        const length = $(this).val().length;
        $('#char-count').text(length);
        if (length > maxLength) {
          $('#char-count').parent().css('color', '#ff6b6b');
        } else {
          $('#char-count').parent().css('color', 'rgba(255,255,255,0.5)');
        }
      });
    });
  </script>
</body>
</html>
