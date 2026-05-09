<?php

namespace App\Models\Sop;

use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Models\Payment\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopSubmission extends Model
{
    public const SERVICE_REVIEW = 'review';
    public const SERVICE_NEW    = 'new';

    protected $fillable = [
        'user_id',
        'module_id',
        'user_exam_id',
        'payment_id',
        'service_type',
        'intended_university',
        'country',
        'resume_path',
        'original_sop_path',
        'final_sop_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function userExam(): BelongsTo
    {
        return $this->belongsTo(UserExam::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function isReview(): bool
    {
        return $this->service_type === self::SERVICE_REVIEW;
    }
}
