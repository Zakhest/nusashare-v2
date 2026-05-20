<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RunDbChanges extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:lock-chapters';
    protected $description = 'Add locking fields to chapters and create unlocked_chapters table';
    protected $usage = 'db:lock-chapters';
    protected $arguments = [];
    protected $options = [];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        CLI::write("Describing users table...", 'yellow');
        try {
            $usersFields = $db->query("DESCRIBE users")->getResultArray();
            foreach ($usersFields as $field) {
                CLI::write($field['Field'] . ': ' . $field['Type']);
            }
        } catch (\Exception $e) {
            CLI::error($e->getMessage());
        }

        CLI::write("Describing chapters table...", 'yellow');
        try {
            $chaptersFields = $db->query("DESCRIBE chapters")->getResultArray();
            foreach ($chaptersFields as $field) {
                CLI::write($field['Field'] . ': ' . $field['Type']);
            }
        } catch (\Exception $e) {
            CLI::error($e->getMessage());
        }

        CLI::write("Creating unlocked_chapters table WITHOUT foreign keys for diagnostic...", 'yellow');
        try {
            $db->query("CREATE TABLE IF NOT EXISTS unlocked_chapters (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id CHAR(9) NOT NULL,
                chapter_id BIGINT(20) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            CLI::write("Table created successfully without FK.", 'green');
            
            CLI::write("Attempting to add user_id FK...", 'yellow');
            $db->query("ALTER TABLE unlocked_chapters ADD CONSTRAINT fk_unlocked_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE");
            CLI::write("User FK added.", 'green');

            CLI::write("Attempting to add chapter_id FK...", 'yellow');
            $db->query("ALTER TABLE unlocked_chapters ADD CONSTRAINT fk_unlocked_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE ON UPDATE CASCADE");
            CLI::write("Chapter FK added.", 'green');

        } catch (\Exception $e) {
            CLI::error("Error: " . $e->getMessage());
        }

        CLI::write("Done.", 'green');
    }
}
