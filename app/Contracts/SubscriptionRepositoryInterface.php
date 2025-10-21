<?php

namespace App\Contracts;

use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubscriptionRepositoryInterface
{
    /**
     * Get all subscriptions with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find subscription by ID
     */
    public function findById(int $id): ?Subscription;

    /**
     * Find subscription by email
     */
    public function findByEmail(string $email): ?Subscription;

    /**
     * Create a new subscription
     */
    public function create(array $data): Subscription;

    /**
     * Update subscription
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete subscription
     */
    public function delete(int $id): bool;

    /**
     * Get active subscriptions
     */
    public function getActiveSubscriptions(int $perPage = 15): LengthAwarePaginator;
}