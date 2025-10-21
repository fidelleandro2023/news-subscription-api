<?php

namespace Tests\Unit\Mail;

use App\Mail\NewsNotificationMail;
use App\Models\News;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsNotificationMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_notification_mail_can_be_constructed()
    {
        // Crear datos de prueba
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        $news = News::factory()->create([
            'title' => 'Test News Title',
            'summary' => 'Test news summary',
            'content' => 'Test news content'
        ]);

        // Crear el mailable
        $mailable = new NewsNotificationMail($news, $subscription);

        // Verificar que se construyó correctamente
        $this->assertEquals($news->id, $mailable->news->id);
        $this->assertEquals($subscription->id, $mailable->subscription->id);
    }

    public function test_news_notification_mail_has_correct_envelope()
    {
        // Crear datos de prueba
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        $news = News::factory()->create([
            'title' => 'Test News Title'
        ]);

        // Crear el mailable
        $mailable = new NewsNotificationMail($news, $subscription);
        $envelope = $mailable->envelope();

        // Verificar el envelope
        $this->assertEquals('Nueva noticia: Test News Title', $envelope->subject);
        $this->assertEquals('test@example.com', $envelope->to[0]->address);
    }

    public function test_news_notification_mail_has_correct_content()
    {
        // Crear datos de prueba
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        $news = News::factory()->create([
            'title' => 'Test News Title',
            'summary' => 'Test news summary',
            'content' => 'Test news content'
        ]);

        // Crear el mailable
        $mailable = new NewsNotificationMail($news, $subscription);
        $content = $mailable->content();

        // Verificar el contenido
        $this->assertEquals('emails.news-notification-simple', $content->view);
        $this->assertArrayHasKey('news', $content->with);
        $this->assertArrayHasKey('subscription', $content->with);
        $this->assertEquals($news->id, $content->with['news']->id);
        $this->assertEquals($subscription->id, $content->with['subscription']->id);
    }
}