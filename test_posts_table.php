<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Post;
use Illuminate\Support\Facades\DB;

echo "=== POSTS TABLE TEST ===\n\n";

try {
    // Check if posts table exists
    if (Schema::hasTable('posts')) {
        echo "✅ Posts table exists\n";
        
        // Check table structure
        $columns = Schema::getColumnListing('posts');
        echo "Table columns: " . implode(', ', $columns) . "\n\n";
        
        // Check current posts count
        $count = Post::count();
        echo "Current posts count: {$count}\n\n";
        
        // Try to create a test post
        echo "Testing post creation...\n";
        
        $testPost = new Post([
            'title' => 'Test Post ' . time(),
            'slug' => 'test-post-' . time(),
            'category' => 'Test',
            'excerpt' => 'This is a test post excerpt',
            'content' => 'This is test content for the post.',
            'is_published' => false,
            'published_at' => null,
            'sort_order' => 0
        ]);
        
        echo "Post model created successfully\n";
        echo "Fillable fields: " . implode(', ', $testPost->getFillable()) . "\n\n";
        
        // Try to save
        $saved = $testPost->save();
        
        if ($saved) {
            echo "✅ Test post saved successfully!\n";
            echo "Post ID: {$testPost->id}\n";
            echo "Post title: {$testPost->title}\n";
            
            // Clean up - delete the test post
            $testPost->delete();
            echo "Test post cleaned up.\n";
        } else {
            echo "❌ Failed to save test post\n";
        }
        
    } else {
        echo "❌ Posts table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";