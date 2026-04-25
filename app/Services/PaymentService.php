<?php

namespace App\Services;

use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Models\Payment\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private PaymentRepositoryInterface $payments) {}

    /**
     * Student submits payment details (bKash trxID etc.)
     * Creates a pending Payment record and flips the UserExam into payment_pending state.
     */
    public function submit(int $userId, array $data): Payment
    {
        $validated = Validator::make($data, [
            'module_id'      => ['required', 'integer', 'exists:modules,id'],
            'transaction_id' => ['required', 'string', 'min:4', 'max:80'],
            'sender_msisdn'  => ['nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', Rule::in(['bkash', 'nagad', 'rocket'])],
        ])->validate();

        $method = $validated['payment_method'] ?? 'bkash';

        // One pending payment per user/module at a time
        if ($this->payments->userHasPendingForModule($userId, $validated['module_id'])) {
            throw ValidationException::withMessages([
                'transaction_id' => ['You already have a pending payment for this module. Please wait for admin verification.'],
            ]);
        }

        // Prevent duplicate trxId submission (globally for this method)
        if ($this->payments->findByTransactionId($validated['transaction_id'], $method)) {
            throw ValidationException::withMessages([
                'transaction_id' => ['This transaction ID has already been submitted.'],
            ]);
        }

        $module = Module::findOrFail($validated['module_id']);

        // Recompute the referral discount server-side (don't trust the client's
        // displayed amount). If user has a pending invitation and this is their
        // first paid order, $finalAmount is the discounted price they actually pay.
        $gross   = (float) $module->price_in_bdt;
        $preview = app(ReferralService::class)->previewDiscount($userId, $gross);
        $finalAmount = $preview ? (float) $preview['final'] : $gross;

        return DB::transaction(function () use ($userId, $validated, $method, $module, $finalAmount) {

            // Create or update the UserExam row with payment_pending state
            $userExam = UserExam::updateOrCreate(
                ['user_id' => $userId, 'module_id' => $module->id],
                [
                    'type'         => $module->type ?? 'paid',
                    'price'        => $finalAmount,
                    'status'       => 'payment_pending',
                    'purchased_at' => now(),
                ]
            );

            return $this->payments->create([
                'user_id'        => $userId,
                'module_id'      => $module->id,
                'user_exam_id'   => $userExam->id,
                'payment_method' => $method,
                'amount'         => $finalAmount,
                'transaction_id' => $validated['transaction_id'],
                'sender_msisdn'  => $validated['sender_msisdn'] ?? null,
                'status'         => Payment::STATUS_PENDING,
            ]);
        });
    }

    /**
     * Admin approves: mark payment approved, flip UserExam to purchased,
     * and redeem any pending referral invitation (if this is the user's first paid order).
     */
    public function approve(int $paymentId, int $adminId, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($paymentId, $adminId, $note) {
            /** @var Payment $payment */
            $payment = $this->payments->find($paymentId);

            if ($payment->status === Payment::STATUS_APPROVED) {
                return $payment;
            }

            $payment->update([
                'status'      => Payment::STATUS_APPROVED,
                'admin_note'  => $note,
                'verified_by' => $adminId,
                'verified_at' => now(),
            ]);

            if ($payment->user_exam_id) {
                UserExam::where('id', $payment->user_exam_id)
                    ->update(['status' => 'purchased', 'purchased_at' => now()]);
            }

            // Redeem referral invitation (no-op if user has no pending claim
            // or this isn't their first paid order)
            app(\App\Services\ReferralService::class)->redeemForPayment($payment->fresh());

            return $payment->fresh();
        });
    }

    /**
     * Admin rejects: mark payment rejected, flip UserExam to cancelled
     * so the student sees the rejection and can retry later.
     */
    public function reject(int $paymentId, int $adminId, string $reason): Payment
    {
        return DB::transaction(function () use ($paymentId, $adminId, $reason) {
            /** @var Payment $payment */
            $payment = $this->payments->find($paymentId);

            if ($payment->status === Payment::STATUS_REJECTED) {
                return $payment;
            }

            $payment->update([
                'status'      => Payment::STATUS_REJECTED,
                'admin_note'  => $reason,
                'verified_by' => $adminId,
                'verified_at' => now(),
            ]);

            if ($payment->user_exam_id) {
                UserExam::where('id', $payment->user_exam_id)
                    ->update(['status' => 'cancelled']);
            }

            return $payment->fresh();
        });
    }
}
