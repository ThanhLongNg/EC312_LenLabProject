<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIXING POSTS TABLE ===\n\n";

try {
    // Check if posts table exists
    if (Schema::hasTable('posts')) {
        echo "Posts table exists, checking structure...\n";
        
        // Get table info
        $tableInfo = DB::select("SHOW CREATE TABLE posts");
        echo "Current table structure:\n";
        echo $tableInfo[0]->{'Create Table'} . "\n\n";
        
        // Check if id column has AUTO_INCREMENT
        $columns = DB::select("SHOW COLUMNS FROM posts WHERE Field = 'id'");
        if (!empty($columns)) {
            $idColumn = $columns[0];
            echo "ID column info:\n";
            echo "Type: " . $idColumn->Type . "\n";
            echo "Null: " . $idColumn->Null . "\n";
            echo "Key: " . $idColumn->Key . "\n";
            echo "Default: " . $idColumn->Default . "\n";
            echo "Extra: " . $idColumn->Extra . "\n\n";
            
            // Fix AUTO_INCREMENT if missing
            if (strpos($idColumn->Extra, 'auto_increment') === false) {
                echo "Fixing AUTO_INCREMENT for id column...\n";
                DB::statement('ALTER TABLE posts MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
                
                // Reset AUTO_INCREMENT to start from 1
                $maxId = DB::table('posts')->max('id');
                $nextId = $maxId ? $maxId + 1 : 1;
                DB::statement("ALTER TABLE posts AUTO_INCREMENT = {$nextId}");
                
                echo "✅ Fixed AUTO_INCREMENT for posts table\n";
            } else {
                echo "✅ AUTO_INCREMENT is already set\n";
            }
        }
        
        // Test creating a post
        echo "\nTesting post creation...\n";
        $testId = DB::table('posts')->insertGetId([
            'title' => 'Test Post ' . time(),
            'slug' => 'test-post-' . time(),
            'category' => 'Test',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
            'is_published' => false,
            'published_at' => null,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        if ($testId) {
            echo "✅ Test post created with ID: {$testId}\n";
            
            // Clean up
            DB::table('posts')->where('id', $testId)->delete();
            echo "Test post cleaned up\n";
        } else {
            echo "❌ Failed to create test post\n";
        }
        
    } else {
        echo "❌ Posts table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FIX COMPLETED ===\n";