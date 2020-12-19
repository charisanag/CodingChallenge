<?php


namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Collection;

interface SubscriptionRepositoryInterface
{
    public function all(): Collection;
}