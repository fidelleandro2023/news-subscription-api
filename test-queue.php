<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsNotificationMail;
use App\Models\News;
use App\Models\Subscription;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Queue System...\n";
    
    // Get a test subscription
    $subscription = Subscription::where('is_active', true)->first();
    
    if (!$subscription) {
        echo "No active subscriptions found. Creating a test subscription...\n";
        $subscription = Subscription::create([
            'email' => 'test@example.com',
            'is_active' => true,
            'token' => \Illuminate\Support\Str::random(32)
        ]);
    }
    
    // Get or create a test news
    $news = News::where('is_published', true)->first();
    
    if (!$news) {
        echo "No published news found. Please create a published news item first.\n";
        exit(1);
    }
    
    echo "Found subscription: {$subscription->email}\n";
    echo "Found news: {$news->title}\n";
    
    // Test sending email via queue
    echo "Queuing email notification...\n";
    
    $mailable = new NewsNotificationMail($news, $subscription);
    Mail::queue($mailable);
    
    echo "Email queued successfully!\n";
    echo "Check the queue worker output to see if it processes correctly.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}