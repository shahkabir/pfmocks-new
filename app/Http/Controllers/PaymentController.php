<?php

namespace App\Http\Controllers;

use App\Models\Module\Module;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

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
     * Returns just the module price / currency / instructions — used by the Buy modal.
     */
    public function moduleInfo(int $moduleId): JsonResponse
    {
        $module = Module::with('exam:id,name')->findOrFail($moduleId);

        return response()->json([
            'module_id'    => $module->id,
            'module_name'  => $module->name,
            'exam_name'    => $module->exam?->name,
            'price_bdt'    => (float) $module->price_in_bdt,
            'payee_msisdn' => config('payment.bkash_msisdn', '01962424219'),
        ]);
    }
}
