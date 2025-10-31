<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JukeBoxed — Live Search Demo</title>
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/public/css/index.css" />
  <style>
    .search-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 2rem;
    }
    .search-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 2rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }
    .search-input {
      width: 100%;
      padding: 1rem;
      font-size: 1.1rem;
      border-radius: 8px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      background: rgba(0, 0, 0, 0.3);
      color: #fff;
      font-family: inherit;
    }
    .search-input:focus {
      outline: none;
      border-color: #4a9eff;
    }
    .results-container {
      min-height: 200px;
    }
    .result-item {
      background: rgba(255, 255, 255, 0.05);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      border-left: 4px solid #4a9eff;
    }
    .result-item h3 {
      margin: 0 0 0.5rem 0;
      color: #fff;
    }
    .result-item p {
      margin: 0.25rem 0;
      color: rgba(255, 255, 255, 0.7);
    }
    .loading {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.6);
    }
    .type-selector {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .type-btn {
      flex: 1;
      padding: 0.75rem;
      border: 2px solid rgba(255, 255, 255, 0.3);
      background: rgba(0, 0, 0, 0.3);
      color: rgba(255, 255, 255, 0.7);
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .type-btn:hover {
      background: rgba(255, 255, 255, 0.1);
    }
    .type-btn.active {
      background: #4a9eff;
      color: #fff;
      border-color: #4a9eff;
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
        <a href="index.php?action=profile">Profile</a>
        <a href="index.php?action=songs">Songs</a>
        <a href="index.php?action=listenList">Wishlist</a>
        <a href="index.php?action=logout">Sign Out</a>
      <?php else: ?>
        <a href="index.php?action=login">Sign In</a>
        <a href="index.php?action=register">Create Account</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <div class="search-container">
      <h1>Live Search Demo</h1>
      <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 2rem;">
        This demonstrates our JSON API endpoint. Type to search in real-time!
      </p>

      <div class="search-box">
        <div class="type-selector">
          <button class="type-btn active" data-type="songs">Songs</button>
          <button class="type-btn" data-type="reviews">Reviews</button>
          <button class="type-btn" data-type="all">All</button>
        </div>
        <input type="text" id="searchInput" class="search-input"
               placeholder="Search for songs, artists, or albums..."
               autocomplete="off" />
      </div>

      <div id="results" class="results-container">
        <p style="text-align: center; color: rgba(255, 255, 255, 0.5);">
          Start typing to search...
        </p>
      </div>
    </div>
  </main>

  <script>
    const searchInput = document.getElementById('searchInput');
    const resultsDiv = document.getElementById('results');
    const typeButtons = document.querySelectorAll('.type-btn');
    let currentType = 'songs';
    let searchTimeout;

    // Handle type selection
    typeButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        typeButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentType = btn.dataset.type;

        // Re-run search if there's a query
        if (searchInput.value.trim().length >= 2) {
          performSearch(searchInput.value.trim());
        }
      });
    });

    // Handle search input with debouncing
    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.trim();

      // Clear previous timeout
      clearTimeout(searchTimeout);

      if (query.length < 2) {
        resultsDiv.innerHTML = '<p style="text-align: center; color: rgba(255, 255, 255, 0.5);">Enter at least 2 characters to search...</p>';
        return;
      }

      // Show loading state
      resultsDiv.innerHTML = '<div class="loading">Searching...</div>';

      // Debounce search (wait 300ms after user stops typing)
      searchTimeout = setTimeout(() => {
        performSearch(query);
      }, 300);
    });

    async function performSearch(query) {
      try {
        const response = await fetch(`<?= $BASE_PATH ?>/api/search.php?q=${encodeURIComponent(query)}&type=${currentType}`);
        const data = await response.json();

        if (!data.success) {
          resultsDiv.innerHTML = `<p style="text-align: center; color: #ff6b6b;">${data.error}</p>`;
          return;
        }

        displayResults(data);
      } catch (error) {
        resultsDiv.innerHTML = `<p style="text-align: center; color: #ff6b6b;">Error: ${error.message}</p>`;
      }
    }

    function displayResults(data) {
      if (data.type === 'all') {
        displayAllResults(data);
        return;
      }

      const results = data.results || [];

      if (results.length === 0) {
        resultsDiv.innerHTML = '<p style="text-align: center; color: rgba(255, 255, 255, 0.5);">No results found.</p>';
        return;
      }

      let html = `<p style="margin-bottom: 1rem; color: rgba(255, 255, 255, 0.7);">Found ${data.count} ${data.type}</p>`;

      if (data.type === 'songs') {
        results.forEach(song => {
          html += `
            <div class="result-item">
              <h3>${escapeHtml(song.title)}</h3>
              <p><strong>Artist:</strong> ${escapeHtml(song.artist)}</p>
              ${song.album ? `<p><strong>Album:</strong> ${escapeHtml(song.album)}</p>` : ''}
              ${song.genre ? `<p><strong>Genre:</strong> ${escapeHtml(song.genre)}</p>` : ''}
              ${song.release_year ? `<p><strong>Year:</strong> ${song.release_year}</p>` : ''}
            </div>
          `;
        });
      } else if (data.type === 'reviews') {
        results.forEach(review => {
          const stars = '★'.repeat(review.rating);
          html += `
            <div class="result-item">
              <h3>${escapeHtml(review.song_title)} - ${escapeHtml(review.artist)}</h3>
              <p><strong>Review by:</strong> ${escapeHtml(review.username)} | <strong>Rating:</strong> ${stars}</p>
              <p>${escapeHtml(review.review_text)}</p>
            </div>
          `;
        });
      }

      resultsDiv.innerHTML = html;
    }

    function displayAllResults(data) {
      let html = '';

      if (data.songs.count > 0) {
        html += `<h3 style="margin-bottom: 1rem;">Songs (${data.songs.count})</h3>`;
        data.songs.results.forEach(song => {
          html += `
            <div class="result-item">
              <h3>${escapeHtml(song.title)}</h3>
              <p><strong>Artist:</strong> ${escapeHtml(song.artist)}</p>
              ${song.album ? `<p><strong>Album:</strong> ${escapeHtml(song.album)}</p>` : ''}
            </div>
          `;
        });
      }

      if (data.reviews.count > 0) {
        html += `<h3 style="margin: 2rem 0 1rem;">Reviews (${data.reviews.count})</h3>`;
        data.reviews.results.forEach(review => {
          const stars = '★'.repeat(review.rating);
          html += `
            <div class="result-item">
              <h3>${escapeHtml(review.song_title)} - ${escapeHtml(review.artist)}</h3>
              <p><strong>Review by:</strong> ${escapeHtml(review.username)} | <strong>Rating:</strong> ${stars}</p>
              <p>${escapeHtml(review.review_text)}</p>
            </div>
          `;
        });
      }

      if (html === '') {
        html = '<p style="text-align: center; color: rgba(255, 255, 255, 0.5);">No results found.</p>';
      }

      resultsDiv.innerHTML = html;
    }

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  </script>
</body>
</html>
