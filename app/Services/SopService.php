<?php

namespace App\Services;

use App\Models\Module\Module;
use App\Models\Sop\SopSubmission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SopService
{
    public function __construct(private PaymentService $payments) {}

    /**
     * Student purchases an SOP service:
     *   1. Stores resume + (optional) original SOP files
     *   2. Creates SopSubmission row
     *   3. Calls PaymentService::submit() which creates a pending Payment
     *      and a UserExam in `payment_pending` state
     *   4. Stitches payment_id back onto the submission
     *
     * Returns the SopSubmission with payment attached.
     */
    public function submit(int $userId, array $data, ?UploadedFile $resume, ?UploadedFile $originalSop): SopSubmission
    {
        $rules = [
            'module_id'           => 'required|integer|exists:modules,id',
            'service_type'        => 'required|in:review,new',
            'intended_university' => 'required|string|max:200',
            'country'             => 'required|string|max:100',
            'transaction_id'      => 'required|string|min:4|max:80',
            'sender_msisdn'       => 'nullable|string|max:20',
        ];

        $validator = validator($data, $rules);
        $validator->validate();

        if (!$resume) {
            throw ValidationException::withMessages([
                'resume' => ['Resume file is required.'],
            ]);
        }

        $module = Module::findOrFail($data['module_id']);
        if (!in_array($module->module_type, ['sop_review', 'sop_new'], true)) {
            throw ValidationException::withMessages([
                'module_id' => ['This module is not an SOP service.'],
            ]);
        }
        if ($data['service_type'] === 'review' && !$originalSop) {
            throw ValidationException::withMessages([
                'original_sop' => ['Existing SOP file is required for review service.'],
            ]);
        }

        return DB::transaction(function () use ($userId, $data, $module, $resume, $originalSop) {

            // Save the uploaded files into public/data/sop/<userId>/
            $userDir = "data/sop/{$userId}";
            $resumeName = uniqid('resume_') . '_' . Str::random(6) . '.' . $resume->getClientOriginalExtension();
            $resume->move(public_path($userDir), $resumeName);
            $resumePath = "{$userDir}/{$resumeName}";

            $originalSopPath = null;
            if ($originalSop) {
                $sopName = uniqid('sop_') . '_' . Str::random(6) . '.' . $originalSop->getClientOriginalExtension();
                $originalSop->move(public_path($userDir), $sopName);
                $originalSopPath = "{$userDir}/{$sopName}";
            }

            $submission = SopSubmission::create([
                'user_id'             => $userId,
                'module_id'           => $module->id,
                'service_type'        => $data['service_type'],
                'intended_university' => $data['intended_university'],
                'country'             => $data['country'],
                'resume_path'         => $resumePath,
                'original_sop_path'   => $originalSopPath,
            ]);

            // Reuse the existing payment flow — creates Payment + UserExam in pending state
            $payment = $this->payments->submit($userId, [
                'module_id'      => $module->id,
                'transaction_id' => $data['transaction_id'],
                'sender_msisdn'  => $data['sender_msisdn'] ?? null,
                'payment_method' => 'bkash',
            ]);

            $submission->update([
                'payment_id'   => $payment->id,
                'user_exam_id' => $payment->user_exam_id,
            ]);

            return $submission->fresh();
        });
    }

    /** Find one submission by user — used by download routes. */
    public function findForUser(int $submissionId, int $userId): ?SopSubmission
    {
        return SopSubmission::where('id', $submissionId)
            ->where('user_id', $userId)
            ->first();
    }
}
