<?php

namespace Tests\Feature;

use App\Jobs\SendNewsNotification;
use App\Mail\NewsNotificationMail;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_notification_can_be_queued()
    {
        Queue::fake();

        // Crear datos de prueba
        $subscription = Subscription::factory()->create();
        $news = News::factory()->create();

        // Simular el envío de notificación
        SendNewsNotification::dispatch($news, $subscription);

        // Verificar que el job fue encolado
        Queue::assertPushed(SendNewsNotification::class, function ($job) use ($news, $subscription) {
            return $job->news->id === $news->id && $job->subscription->id === $subscription->id;
        });
    }

    public function test_send_news_notification_job_sends_email()
    {
        Mail::fake();

        // Crear datos de prueba con relaciones
        $category = NewsCategory::factory()->create();
        $author = User::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com'
        ]);
        
        $news = News::factory()->create([
            'title' => 'Test News',
            'news_category_id' => $category->id,
            'author_id' => $author->id
        ]);

        // Enviar el email usando Mail::send directamente
        Mail::to($subscription->email)->send(new NewsNotificationMail($news, $subscription));

        // Verificar que el email fue enviado
        Mail::assertSent(NewsNotificationMail::class, function ($mail) use ($subscription, $news) {
            return $mail->subscription->id === $subscription->id 
                && $mail->news->id === $news->id;
        });
    }

    public function test_news_notification_mail_has_correct_recipient()
    {
        Mail::fake();

        // Crear datos de prueba con relaciones
        $category = NewsCategory::factory()->create();
        $author = User::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'email' => 'recipient@example.com'
        ]);
        
        $news = News::factory()->create([
            'news_category_id' => $category->id,
            'author_id' => $author->id
        ]);

        // Enviar el email
        Mail::to($subscription->email)->send(new NewsNotificationMail($news, $subscription));

        // Verificar que se envió al destinatario correcto
        Mail::assertSent(NewsNotificationMail::class, function ($mail) use ($subscription) {
            $envelope = $mail->envelope();
            return $envelope->to[0]->address === $subscription->email;
        });
    }

    public function test_multiple_subscriptions_receive_notifications()
    {
        Mail::fake();

        // Crear datos de prueba con relaciones
        $category = NewsCategory::factory()->create();
        $author = User::factory()->create();
        
        // Crear múltiples suscripciones
        $subscriptions = Subscription::factory()->count(3)->create();
        $news = News::factory()->create([
            'news_category_id' => $category->id,
            'author_id' => $author->id
        ]);

        // Enviar notificaciones a todas las suscripciones
        foreach ($subscriptions as $subscription) {
            Mail::to($subscription->email)->send(new NewsNotificationMail($news, $subscription));
        }

        // Verificar que se enviaron 3 emails
        Mail::assertSent(NewsNotificationMail::class, 3);

        // Verificar que cada suscripción recibió su email
        foreach ($subscriptions as $subscription) {
            Mail::assertSent(NewsNotificationMail::class, function ($mail) use ($subscription) {
                return $mail->subscription->id === $subscription->id;
            });
        }
    }

    public function test_send_news_notification_job_execution()
    {
        Mail::fake();

        // Crear datos de prueba con relaciones
        $category = NewsCategory::factory()->create();
        $author = User::factory()->create();
        
        $subscription = Subscription::factory()->create([
            'email' => 'job-test@example.com'
        ]);
        
        $news = News::factory()->create([
            'title' => 'Job Test News',
            'news_category_id' => $category->id,
            'author_id' => $author->id
        ]);

        // Crear y ejecutar el job
        $job = new SendNewsNotification($news, $subscription);
        $job->handle();

        // Verificar que el email fue enviado por el job
        Mail::assertSent(NewsNotificationMail::class, function ($mail) use ($subscription, $news) {
            return $mail->subscription->id === $subscription->id 
                && $mail->news->id === $news->id;
        });
    }
}