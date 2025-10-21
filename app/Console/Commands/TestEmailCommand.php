<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeSubscriptionMail;
use App\Models\Subscription;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Create a test subscription
            $subscription = new Subscription();
            $subscription->name = 'Test User';
            $subscription->email = 'test@example.com';
            
            $this->info('Attempting to send test email...');
            
            // Send the email
            Mail::to('test@example.com')->send(new WelcomeSubscriptionMail($subscription));
            
            $this->info('Email sent successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error sending email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
