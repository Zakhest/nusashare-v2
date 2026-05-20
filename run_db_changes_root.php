<?php

require 'app/Config/Paths.php';
require 'vendor/autoload.php';

$db = \Config\Database::connect();

try {
    echo "Adding columns to chapters table...\n";
    $db->query("ALTER TABLE chapters ADD COLUMN is_locked TINYINT(1) DEFAULT 0 AFTER status");
    $db->query("ALTER TABLE chapters ADD COLUMN price INT(11) DEFAULT 0 AFTER is_locked");
    echo "Columns added successfully.\n";
} catch (\Exception $e) {
    echo "Error adding columns: " . $e->getMessage() . "\n";
}

try {
    echo "Creating unlocked_chapters table...\n";
    $db->query("CREATE TABLE IF NOT EXISTS unlocked_chapters (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id CHAR(9) NOT NULL,
        chapter_id BIGINT(20) UNSIGNED NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_unlocked_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_unlocked_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Table created successfully.\n";
} catch (\Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}

echo "Done.\n";
