<?php

namespace App\Observers;

use App\Models\News;
use App\Services\NewsNotificationService;
use Illuminate\Support\Facades\Log;

class NewsObserver
{
    protected $newsNotificationService;

    public function __construct(NewsNotificationService $newsNotificationService)
    {
        $this->newsNotificationService = $newsNotificationService;
    }

    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        // Send notifications only if the news is published upon creation
        if ($news->is_published && $news->published_at) {
            $this->sendNotifications($news, 'created');
        }
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        // Check if the news was just published (changed from unpublished to published)
        if ($news->is_published && $news->published_at && $news->wasChanged('is_published')) {
            $this->sendNotifications($news, 'published');
        }
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        Log::info("News deleted", [
            'news_id' => $news->id,
            'news_title' => $news->title
        ]);
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        Log::info("News restored", [
            'news_id' => $news->id,
            'news_title' => $news->title
        ]);
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        Log::info("News force deleted", [
            'news_id' => $news->id,
            'news_title' => $news->title
        ]);
    }

    /**
     * Send notifications to subscribers
     */
    private function sendNotifications(News $news, string $action): void
    {
        try {
            // Queue the notifications to avoid blocking the request
            $result = $this->newsNotificationService->queueNewsNotification($news);
            
            Log::info("News notifications queued", [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'action' => $action,
                'total_subscribers' => $result['total_subscribers'],
                'queued_count' => $result['queued_count'],
                'failed_count' => $result['failed_count']
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to queue news notifications", [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}
