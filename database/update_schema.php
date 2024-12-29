<?php
require_once '../config/database.php';

try {
    // First check if columns exist
    $stmt = $pdo->query("SHOW COLUMNS FROM user_events");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Add columns only if they don't exist
    $updates = [];
    
    if (!in_array('status', $columns)) {
        $updates[] = "ADD COLUMN status ENUM('registered', 'waitlist', 'cancelled') DEFAULT 'registered'";
    }
    
    if (!in_array('registration_date', $columns)) {
        $updates[] = "ADD COLUMN registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    }
    
    if (!in_array('cancellation_date', $columns)) {
        $updates[] = "ADD COLUMN cancellation_date TIMESTAMP NULL";
    }
    
    // Execute ALTER TABLE only if there are columns to add
    if (!empty($updates)) {
        $sql = "ALTER TABLE user_events " . implode(", ", $updates);
        $pdo->exec($sql);
    }

    // Add indexes to events table if they don't exist
    $stmt = $pdo->query("SHOW INDEX FROM events");
    $indexes = $stmt->fetchAll(PDO::FETCH_COLUMN, 2); // Column 2 contains index names
    
    if (!in_array('idx_category', $indexes)) {
        $pdo->exec("CREATE INDEX idx_category ON events (category)");
    }
    
    if (!in_array('idx_date', $indexes)) {
        $pdo->exec("CREATE INDEX idx_date ON events (date)");
    }
    
    if (!in_array('idx_price', $indexes)) {
        $pdo->exec("CREATE INDEX idx_price ON events (price)");
    }

    echo "Database schema updated successfully!";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage();
}
?>
