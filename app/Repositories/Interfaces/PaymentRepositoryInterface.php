<?php

namespace App\Repositories\Interfaces;

use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByTransactionId(string $transactionId, string $method = 'bkash'): ?Payment;

    public function pendingForAdmin(): Collection;

    public function userHasPendingForModule(int $userId, int $moduleId): bool;
}
