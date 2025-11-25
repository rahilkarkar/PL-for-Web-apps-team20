<?php
/**
 * Database Reset Tool
 * DANGER: This will DELETE ALL DATA!
 * Access at: index.php?action=dbReset
 */

session_start();

// Security: Require confirmation
$confirmed = isset($_POST['confirm']) && $_POST['confirm'] === 'DELETE_ALL_DATA';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Reset - JukeBoxed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
            color: #fff;
            padding: 2rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .warning {
            background: rgba(255, 107, 107, 0.3);
            border: 2px solid #ff6b6b;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning h2 {
            margin-top: 0;
            color: #fff;
        }
        ul {
            margin: 1rem 0;
        }
        li {
            margin: 0.5rem 0;
        }
        .confirm-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border: 2px solid #ff6b6b;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            margin: 0.5rem 0;
        }
        .buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        button {
            flex: 1;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-danger {
            background: #ff6b6b;
            color: #fff;
        }
        .btn-danger:hover {
            background: #ff5252;
        }
        .btn-safe {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .btn-safe:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .success {
            background: rgba(81, 207, 102, 0.3);
            border: 2px solid #51cf66;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            text-align: center;
        }
        a {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Database Reset</h1>

        <?php if ($confirmed): ?>
            <?php
            // Load database connection
            $isServer = (
                isset($_SERVER['HTTP_HOST']) &&
                strpos($_SERVER['HTTP_HOST'], 'cs4640.cs.virginia.edu') !== false
            );

            if ($isServer) {
                require_once 'config-server.php';
            } else {
                require_once 'config-local.php';
            }

            try {
                // Delete all data from tables (in correct order to avoid foreign key violations)
                $pdo->exec("DELETE FROM playlist_songs");
                $pdo->exec("DELETE FROM playlists");
                $pdo->exec("DELETE FROM activity");
                $pdo->exec("DELETE FROM followers");
                $pdo->exec("DELETE FROM listen_list");
                $pdo->exec("DELETE FROM reviews");
                $pdo->exec("DELETE FROM songs");
                $pdo->exec("DELETE FROM jukeboxd_users");

                // Reset sequences (PostgreSQL)
                $pdo->exec("ALTER SEQUENCE playlist_songs_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE playlists_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE activity_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE followers_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE listen_list_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE reviews_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE songs_id_seq RESTART WITH 1");
                $pdo->exec("ALTER SEQUENCE jukeboxd_users_id_seq RESTART WITH 1");

                // Re-insert sample songs
                $pdo->exec("
                    INSERT INTO songs (title, artist, album, release_year, genre) VALUES
                    ('COMFORT ME', 'Malcom Todd', 'Comfort Me', 2021, 'R&B'),
                    ('INTIMIDATED', 'Kaytranada, H.E.R', 'BUBBA', 2019, 'Electronic'),
                    ('HMU', 'Greek', 'HMU', 2020, 'Pop'),
                    ('LADY', 'Avenoir', 'Lady', 2021, 'Indie'),
                    ('Vie', 'Doja Cat', 'Planet Her', 2021, 'Pop'),
                    ('Jealous Type', 'Doja Cat', 'Planet Her', 2021, 'Pop'),
                    ('ORENJI', 'Various Artists', 'ORENJI', 2020, 'Electronic'),
                    ('Blinding Lights', 'The Weeknd', 'After Hours', 2020, 'Synth-pop'),
                    ('Levitating', 'Dua Lipa', 'Future Nostalgia', 2020, 'Disco-pop'),
                    ('Good Days', 'SZA', 'Good Days', 2020, 'R&B')
                ");

                echo "<div class='success'>";
                echo "<h2>✅ Database Reset Successfully!</h2>";
                echo "<p>All user data has been deleted and sample songs have been re-added.</p>";
                echo "<p><a href='index.php'>Go to Home Page</a> | <a href='index.php?action=dbMonitor'>View Database</a></p>";
                echo "</div>";

                // Destroy session
                session_destroy();
            } catch (PDOException $e) {
                echo "<div class='warning'>";
                echo "<h2>❌ Error</h2>";
                echo "<p>Failed to reset database: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "</div>";
            }
            ?>

        <?php else: ?>
            <div class="warning">
                <h2>⚠️ DANGER ZONE</h2>
                <p>This action will permanently delete:</p>
                <ul>
                    <li>All user accounts</li>
                    <li>All reviews</li>
                    <li>All playlists</li>
                    <li>All activity logs</li>
                    <li>All listen lists</li>
                    <li>All follower relationships</li>
                    <li>All songs (will re-add sample songs)</li>
                </ul>
                <p><strong>This action CANNOT be undone!</strong></p>
            </div>

            <div class="confirm-box">
                <form method="POST">
                    <label for="confirm"><strong>Type "DELETE_ALL_DATA" to confirm:</strong></label>
                    <input type="text" name="confirm" id="confirm" autocomplete="off" required>

                    <div class="buttons">
                        <a href="index.php" class="btn-safe" style="text-decoration: none; text-align: center; line-height: 3;">Cancel</a>
                        <button type="submit" class="btn-danger">🗑️ Delete Everything</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
