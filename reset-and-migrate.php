<?php
/**
 * Database Reset & Migration Script
 * ----------------------------------
 * This script will DROP all tables and recreate them from migrations.sql
 * USE WITH CAUTION: All data will be lost!
 *
 * URL: http://localhost/PL-for-Web-apps-team20/reset-and-migrate.php
 */

session_start();

// Auto-detect environment and load appropriate config
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
    <title>JukeBoxed - Reset & Migrate Database</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #1a1a1a;
            color: #fff;
        }
        .container {
            background: #2a2a2a;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        h1 {
            color: #4a9eff;
            margin-top: 0;
        }
        .success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            color: #ccffcc;
        }
        .error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            color: #ffcccc;
        }
        .warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            color: #fff3cd;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #333;
            border-radius: 4px;
        }
        button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background: #c82333;
        }
        button.confirm {
            background: #28a745;
        }
        button.confirm:hover {
            background: #218838;
        }
        pre {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            color: #ccc;
        }
        .links {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #444;
        }
        .links a {
            color: #4a9eff;
            text-decoration: none;
            margin-right: 20px;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Database Reset & Migration</h1>

<?php

if (isset($_POST['confirm_reset'])) {
    echo "<h2>🗑️ Dropping All Tables...</h2>";

    try {
        // Get all tables
        $tables = $pdo->query("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
        ")->fetchAll(PDO::FETCH_COLUMN);

        echo "<div class='step'>";
        echo "<strong>Step 1:</strong> Dropping existing tables...<br>";

        if (empty($tables)) {
            echo "ℹ️ No tables to drop<br>";
        } else {
            foreach ($tables as $table) {
                try {
                    $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
                    echo "✅ Dropped table: $table<br>";
                } catch (PDOException $e) {
                    echo "⚠️ Error dropping $table: " . htmlspecialchars($e->getMessage()) . "<br>";
                }
            }
        }
        echo "</div>";

        // Now run migrations
        echo "<h2>📦 Running Migrations...</h2>";

        $sqlFile = __DIR__ . '/migrations.sql';

        if (!file_exists($sqlFile)) {
            throw new Exception("migrations.sql file not found!");
        }

        $sql = file_get_contents($sqlFile);

        if ($sql === false) {
            throw new Exception("Could not read migrations.sql file!");
        }

        echo "<div class='step'>";
        echo "<strong>Step 2:</strong> Reading migrations.sql...<br>";
        echo "✅ File found and loaded (" . strlen($sql) . " bytes)<br>";
        echo "</div>";

        echo "<div class='step'>";
        echo "<strong>Step 3:</strong> Creating tables...<br>";

        $candidates = array_map('trim', explode(';', $sql));
        $successCount = 0;
        $errors = [];

        foreach ($candidates as $candidate) {
            if (empty($candidate)) continue;

            // Remove comment lines
            $lines = preg_split('/\r\n|\r|\n/', $candidate);
            $cleanLines = [];
            foreach ($lines as $line) {
                $t = trim($line);
                if ($t === '' || preg_match('/^--/', $t)) continue;
                $cleanLines[] = $t;
            }

            $statement = trim(implode("\n", $cleanLines));
            if ($statement === '') continue;

            try {
                $pdo->exec($statement);
                $successCount++;

                // Show table name if it's a CREATE TABLE statement
                if (preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?([^\s(]+)/i', $statement, $matches)) {
                    echo "✅ Created table: {$matches[1]}<br>";
                }
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
                echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        }

        echo "✅ Executed $successCount SQL statements<br>";
        echo "</div>";

        // Verify tables
        echo "<div class='step'>";
        echo "<strong>Step 4:</strong> Verifying new schema...<br>";

        $newTables = $pdo->query("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
        ")->fetchAll(PDO::FETCH_COLUMN);

        echo "✅ Created " . count($newTables) . " tables:<br>";
        foreach ($newTables as $table) {
            // Get column count
            $cols = $pdo->query("
                SELECT COUNT(*)
                FROM information_schema.columns
                WHERE table_name = '$table'
            ")->fetchColumn();

            echo "  • <strong>$table</strong> ($cols columns)<br>";
        }
        echo "</div>";

        // Check for first_name and last_name columns
        echo "<div class='step'>";
        echo "<strong>Step 5:</strong> Verifying schema updates...<br>";

        $userColumns = $pdo->query("
            SELECT column_name, data_type
            FROM information_schema.columns
            WHERE table_name = 'jukeboxd_users'
            ORDER BY ordinal_position
        ")->fetchAll(PDO::FETCH_ASSOC);

        $hasFirstName = false;
        $hasLastName = false;

        foreach ($userColumns as $col) {
            if ($col['column_name'] === 'first_name') {
                $hasFirstName = true;
                echo "✅ Column 'first_name' exists ({$col['data_type']})<br>";
            }
            if ($col['column_name'] === 'last_name') {
                $hasLastName = true;
                echo "✅ Column 'last_name' exists ({$col['data_type']})<br>";
            }
        }

        if (!$hasFirstName || !$hasLastName) {
            echo "<div class='warning'>";
            echo "⚠️ Warning: first_name or last_name columns not found in jukeboxd_users table!<br>";
            echo "Check your migrations.sql file.";
            echo "</div>";
        }

        echo "<br><strong>All columns in jukeboxd_users:</strong><br>";
        foreach ($userColumns as $col) {
            echo "  • {$col['column_name']} ({$col['data_type']})<br>";
        }
        echo "</div>";

        // Check sample data
        echo "<div class='step'>";
        echo "<strong>Step 6:</strong> Checking sample data...<br>";

        try {
            $songCount = $pdo->query("SELECT COUNT(*) FROM songs")->fetchColumn();
            echo "✅ Songs table has $songCount records<br>";

            if ($songCount > 0) {
                $sampleSong = $pdo->query("SELECT title, artist FROM songs LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                echo "  Example: \"{$sampleSong['title']}\" by {$sampleSong['artist']}<br>";
            }
        } catch (PDOException $e) {
            echo "⚠️ Could not query songs table<br>";
        }

        echo "</div>";

        if (empty($errors)) {
            echo "<div class='success'>";
            echo "<h3>✅ Database Reset & Migration Complete!</h3>";
            echo "<p>All tables have been dropped and recreated with the latest schema.</p>";
            echo "<p>You can now test the application with the updated database structure.</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>⚠️ Migration completed with errors</h3>";
            echo "<p>Some statements failed. Check the errors above.</p>";
            echo "</div>";
        }

    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<h3>❌ Migration Failed</h3>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }

    echo "<div class='links'>";
    echo "<a href='index.php?action=dbMonitor'>📊 View Database Monitor</a>";
    echo "<a href='index.php'>← Go to Application</a>";
    echo "</div>";

} else {
    // Show warning form
    ?>

    <div class="warning">
        <h3>⚠️ WARNING - DATA LOSS</h3>
        <p><strong>This action will:</strong></p>
        <ul>
            <li>DROP all existing tables (users, songs, reviews, etc.)</li>
            <li>DELETE all data permanently</li>
            <li>CREATE fresh tables from migrations.sql</li>
            <li>Apply any schema changes (like first_name, last_name columns)</li>
        </ul>
        <p><strong style="color: #ff6b6b;">All user accounts, reviews, playlists, and wishlists will be lost!</strong></p>
    </div>

    <div class="step">
        <h3>📋 Current Database Status:</h3>
        <pre><?php
        try {
            $result = $pdo->query("SELECT current_database(), current_user")->fetch();
            echo "Database: " . htmlspecialchars($result['current_database']) . "\n";
            echo "User: " . htmlspecialchars($result['current_user']) . "\n\n";

            // List current tables
            $tables = $pdo->query("
                SELECT table_name
                FROM information_schema.tables
                WHERE table_schema = 'public'
                AND table_type = 'BASE TABLE'
                ORDER BY table_name
            ")->fetchAll(PDO::FETCH_COLUMN);

            if (empty($tables)) {
                echo "No tables found.\n";
            } else {
                echo "Existing tables (" . count($tables) . "):\n";
                foreach ($tables as $table) {
                    $count = $pdo->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
                    echo "  • $table ($count rows)\n";
                }
            }

            // Check if first_name/last_name exist
            echo "\nChecking jukeboxd_users schema:\n";
            $columns = $pdo->query("
                SELECT column_name
                FROM information_schema.columns
                WHERE table_name = 'jukeboxd_users'
            ")->fetchAll(PDO::FETCH_COLUMN);

            if (in_array('first_name', $columns)) {
                echo "  ✅ first_name column exists\n";
            } else {
                echo "  ❌ first_name column MISSING (needs migration)\n";
            }

            if (in_array('last_name', $columns)) {
                echo "  ✅ last_name column exists\n";
            } else {
                echo "  ❌ last_name column MISSING (needs migration)\n";
            }

        } catch (PDOException $e) {
            echo "❌ Could not connect to database\n";
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
        ?></pre>
    </div>

    <form method="POST" onsubmit="return confirm('⚠️ ARE YOU SURE? This will delete ALL data!\n\nType YES to confirm.');">
        <button type="submit" name="confirm_reset" value="1">
            🗑️ DROP ALL TABLES & RUN MIGRATIONS
        </button>
    </form>

    <div class="links">
        <a href="index.php?action=dbMonitor">📊 View Database Monitor</a>
        <a href="index.php">← Go to Application</a>
    </div>

    <?php
}
?>

    </div>
</body>
</html>
