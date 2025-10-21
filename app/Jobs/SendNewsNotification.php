<?php

namespace App\Jobs;

use App\Mail\NewsNotificationMail;
use App\Models\News;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $news;
    public $subscription;

    /**
     * Create a new job instance.
     */
    public function __construct(News $news, Subscription $subscription)
    {
        $this->news = $news;
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->subscription->email)
            ->send(new NewsNotificationMail($this->news, $this->subscription));
    }
}