<?php

namespace App\Models\Exam;

use App\Models\User;
use App\Models\Module\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UserExam extends Model
{
    protected $table = 'user_exams';

    protected $fillable = [
        'user_id',
        'module_id',
        'type',
        'price',
        'status', // free->free exams,purchased->initial purchase, payment_pending->payment verification pending, 
                //cancelled->exam cancelled, completed->student completed the exam
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
