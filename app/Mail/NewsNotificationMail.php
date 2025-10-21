<?php

namespace App\Mail;

use App\Models\News;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $news;
    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(News $news, Subscription $subscription)
    {
        $this->news = $news;
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->subscription->email,
            subject: 'Nueva noticia: ' . $this->news->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.news-notification-simple',
            with: [
                'news' => $this->news,
                'subscription' => $this->subscription,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
