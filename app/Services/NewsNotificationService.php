<?php

namespace App\Services;

use App\Models\News;
use App\Models\Subscription;
use App\Mail\NewsNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsNotificationService
{
    /**
     * Send news notification to all active subscribers
     *
     * @param News $news
     * @return array
     */
    public function sendNewsNotification(News $news): array
    {
        $activeSubscriptions = Subscription::where('is_active', true)->get();
        $sentCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($activeSubscriptions as $subscription) {
            try {
                Mail::to($subscription->email)->send(new NewsNotificationMail($news, $subscription));
                $sentCount++;
                
                Log::info("News notification sent successfully", [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'subscriber_email' => $subscription->email
                ]);
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'email' => $subscription->email,
                    'error' => $e->getMessage()
                ];
                
                Log::error("Failed to send news notification", [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'subscriber_email' => $subscription->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'total_subscribers' => $activeSubscriptions->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'errors' => $errors
        ];
    }

    /**
     * Send news notification to specific subscriber
     *
     * @param News $news
     * @param Subscription $subscription
     * @return bool
     */
    public function sendNewsNotificationToSubscriber(News $news, Subscription $subscription): bool
    {
        try {
            if (!$subscription->is_active) {
                Log::warning("Attempted to send notification to inactive subscriber", [
                    'subscriber_email' => $subscription->email
                ]);
                return false;
            }

            Mail::to($subscription->email)->send(new NewsNotificationMail($news, $subscription));
            
            Log::info("News notification sent successfully to specific subscriber", [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'subscriber_email' => $subscription->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send news notification to specific subscriber", [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'subscriber_email' => $subscription->email,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Queue news notification for all active subscribers
     *
     * @param News $news
     * @return array
     */
    public function queueNewsNotification(News $news): array
    {
        $activeSubscriptions = Subscription::where('is_active', true)->get();
        $queuedCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($activeSubscriptions as $subscription) {
            try {
                Mail::to($subscription->email)->queue(new NewsNotificationMail($news, $subscription));
                $queuedCount++;
                
                Log::info("News notification queued successfully", [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'subscriber_email' => $subscription->email
                ]);
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'email' => $subscription->email,
                    'error' => $e->getMessage()
                ];
                
                Log::error("Failed to queue news notification", [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'subscriber_email' => $subscription->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'total_subscribers' => $activeSubscriptions->count(),
            'queued_count' => $queuedCount,
            'failed_count' => $failedCount,
            'errors' => $errors
        ];
    }
}