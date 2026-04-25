<?php

namespace App\Http\Controllers;

use App\Models\Module\Module;
use App\Services\PaymentService;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService  $payments,
        private ReferralService $referrals
    ) {}

    /**
     * Student submits bKash transaction id for a paid module.
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            $payment = $this->payments->submit(auth()->id(), $request->all());
        } catch (ValidationException $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->validator->errors()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Payment submitted. Admin will verify it shortly.',
            'payment' => [
                'id'             => $payment->id,
                'status'         => $payment->status,
                'transaction_id' => $payment->transaction_id,
            ],
        ]);
    }

    /**
     * Returns module pricing + referral preview for the Buy modal.
     * If the user has a pending referral invitation and this would be their
     * first paid order, returns gross/discount/final separately.
     */
    public function moduleInfo(int $moduleId): JsonResponse
    {
        $module = Module::with('exam:id,name')->findOrFail($moduleId);
        $gross  = (float) $module->price_in_bdt;

        $preview = $this->referrals->previewDiscount(auth()->id(), $gross);

        return response()->json([
            'module_id'    => $module->id,
            'module_name'  => $module->name,
            'exam_name'    => $module->exam?->name,
            'price_bdt'    => $gross,                                  // original
            'discount'     => $preview ? (float) $preview['discount'] : 0,
            'final_amount' => $preview ? (float) $preview['final']    : $gross,
            'has_referral' => $preview !== null,
            'referral_code'=> $preview ? $preview['invitation']->referral_code_used : null,
            'payee_msisdn' => config('payment.bkash_msisdn', '01962424219'),
        ]);
    }
}
