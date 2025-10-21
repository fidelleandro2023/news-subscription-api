<?php

namespace App\Repositories;

use App\Contracts\SubscriptionRepositoryInterface;
use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    protected $model;

    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }

    /**
     * Get all subscriptions with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find subscription by ID
     */
    public function findById(int $id): ?Subscription
    {
        return $this->model->find($id);
    }

    /**
     * Find subscription by email
     */
    public function findByEmail(string $email): ?Subscription
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create a new subscription
     */
    public function create(array $data): Subscription
    {
        return $this->model->create($data);
    }

    /**
     * Update subscription
     */
    public function update(int $id, array $data): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }
        
        return $subscription->update($data);
    }

    /**
     * Delete subscription
     */
    public function delete(int $id): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }
        
        return $subscription->delete();
    }

    /**
     * Get active subscriptions
     */
    public function getActiveSubscriptions(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('is_active', true)->orderBy('created_at', 'desc')->paginate($perPage);
    }
}