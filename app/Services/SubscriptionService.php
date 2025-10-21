<?php

namespace App\Services;

use App\Contracts\SubscriptionRepositoryInterface;
use App\Mail\WelcomeSubscriptionMail;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getAllSubscriptions($perPage = 15)
    {
        return $this->subscriptionRepository->getAllPaginated($perPage);
    }

    public function createSubscription(array $data)
    {
        // Verificar si el email ya existe
        $existingSubscription = $this->subscriptionRepository->findByEmail($data['email']);
        
        if ($existingSubscription) {
            throw new \Exception('El email ya está suscrito a nuestro boletín.');
        }

        // Agregar timestamp de suscripción
        $data['subscribed_at'] = now();

        // Crear la suscripción
        $subscription = $this->subscriptionRepository->create($data);

        // Enviar correo de bienvenida
        try {
            Mail::to($subscription->email)->send(new WelcomeSubscriptionMail($subscription));
        } catch (\Exception $e) {
            // Log del error pero no fallar la creación de la suscripción
            \Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
        }

        return $subscription;
    }

    public function updateSubscription($id, array $data)
    {
        return $this->subscriptionRepository->update((int) $id, $data);
    }

    public function deleteSubscription($id)
    {
        return $this->subscriptionRepository->delete((int) $id);
    }

    public function findSubscription($id)
    {
        return $this->subscriptionRepository->findById((int) $id);
    }

    public function getActiveSubscriptions($perPage = 15)
    {
        return $this->subscriptionRepository->getActiveSubscriptions($perPage);
    }

    public function findByEmail($email)
    {
        return $this->subscriptionRepository->findByEmail($email);
    }
}