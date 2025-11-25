<?php
/**
 * Route Testing Page
 * Quick way to test all routes
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Tester - JukeBoxed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
        }
        h1 { text-align: center; }
        .status {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .logged-in { color: #51cf66; }
        .logged-out { color: #ff6b6b; }
        .routes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        .route-link {
            display: block;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            transition: all 0.2s;
            text-align: center;
        }
        .route-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        .section-title {
            grid-column: 1 / -1;
            margin-top: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            color: #ffd700;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Route Tester</h1>

        <div class="status">
            <strong>Session Status:</strong>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <span class="logged-in">✅ Logged In as <?= htmlspecialchars($_SESSION['username']) ?> (ID: <?= $_SESSION['user_id'] ?>)</span>
            <?php else: ?>
                <span class="logged-out">❌ Not Logged In</span>
            <?php endif; ?>
        </div>

        <div class="routes">
            <div class="section-title"><h3>Public Pages</h3></div>
            <a href="index.php?action=home" class="route-link">🏠 Home</a>
            <a href="index.php?action=login" class="route-link">🔐 Login</a>
            <a href="index.php?action=register" class="route-link">📝 Register</a>
            <a href="index.php?action=songs" class="route-link">🎵 Songs</a>

            <div class="section-title"><h3>User Pages (Require Login)</h3></div>
            <a href="index.php?action=profile" class="route-link">👤 Profile</a>
            <a href="index.php?action=activity" class="route-link">📊 Activity</a>
            <a href="index.php?action=reviews" class="route-link">⭐ Reviews</a>
            <a href="index.php?action=settings" class="route-link">⚙️ Settings</a>
            <a href="index.php?action=listenList" class="route-link">📝 Wishlist</a>
            <a href="index.php?action=playlists" class="route-link">🎧 Playlists</a>

            <div class="section-title"><h3>API & Tools</h3></div>
            <a href="index.php?action=searchDemo" class="route-link">🔍 Search Demo (AJAX)</a>
            <a href="index.php?action=dbMonitor" class="route-link">🗄️ Database Monitor</a>
            <a href="index.php?action=dbReset" class="route-link">⚠️ Database Reset</a>

            <div class="section-title"><h3>Actions</h3></div>
            <a href="index.php?action=logout" class="route-link">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
