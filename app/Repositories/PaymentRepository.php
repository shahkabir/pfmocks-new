<?php

namespace App\Repositories;

use App\Models\Payment\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function findByTransactionId(string $transactionId, string $method = 'bkash'): ?Payment
    {
        return $this->model->newQuery()
            ->where('transaction_id', $transactionId)
            ->where('payment_method', $method)
            ->first();
    }

    public function pendingForAdmin(): Collection
    {
        return $this->model->newQuery()
            ->with(['user:id,name,email,mobile', 'module:id,name,module_type,price_in_bdt', 'module.exam:id,name'])
            ->where('status', Payment::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function userHasPendingForModule(int $userId, int $moduleId): bool
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('status', Payment::STATUS_PENDING)
            ->exists();
    }
}
