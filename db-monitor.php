<?php
/**
 * Database Monitoring Tool
 * Access at: index.php?action=dbMonitor (must be logged in)
 * Shows all tables and their contents for debugging
 */

session_start();

// Security: Only allow if logged in (you can add admin check later)
if (empty($_SESSION['user_id'])) {
    die('Access denied. Please log in first.');
}

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Monitor - JukeBoxed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 2rem;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .table-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .table-section h2 {
            margin-top: 0;
            color: #ffd700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            overflow: hidden;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(0, 0, 0, 0.5);
            font-weight: 600;
            color: #ffd700;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .count {
            color: #51cf66;
            font-weight: bold;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: white;
            text-decoration: none;
        }
        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .empty {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?action=profile" class="back-link">← Back to App</a>
        <h1>🔍 Database Monitor</h1>

        <?php
        // Function to display table contents
        function displayTable($pdo, $tableName, $displayName = null) {
            $displayName = $displayName ?? $tableName;

            try {
                $stmt = $pdo->query("SELECT * FROM $tableName");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo "<div class='table-section'>";
                echo "<h2>$displayName <span class='count'>(" . count($rows) . " rows)</span></h2>";

                if (empty($rows)) {
                    echo "<div class='empty'>No data in this table</div>";
                } else {
                    echo "<table>";
                    echo "<thead><tr>";
                    foreach (array_keys($rows[0]) as $column) {
                        echo "<th>" . htmlspecialchars($column) . "</th>";
                    }
                    echo "</tr></thead>";
                    echo "<tbody>";
                    foreach ($rows as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                }
                echo "</div>";
            } catch (PDOException $e) {
                echo "<div class='table-section'>";
                echo "<h2>$displayName</h2>";
                echo "<div class='empty' style='color: #ff6b6b;'>Error: Table not found or query failed</div>";
                echo "</div>";
            }
        }

        // Display all tables
        displayTable($pdo, 'jukeboxd_users', 'Users');
        displayTable($pdo, 'songs', 'Songs');
        displayTable($pdo, 'reviews', 'Reviews');
        displayTable($pdo, 'activity', 'Activity Feed');
        displayTable($pdo, 'playlists', 'Playlists');
        displayTable($pdo, 'playlist_songs', 'Playlist Songs');
        displayTable($pdo, 'listen_list', 'Listen List (Wishlist)');
        displayTable($pdo, 'followers', 'Followers');
        ?>

    </div>
</body>
</html>
