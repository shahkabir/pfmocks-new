<?php

namespace App\Models;

use App\Models\Question\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsvImport extends Model
{
    protected $fillable = [
        'question_id',
        'question_type',
        'original_filename',
        'stored_filename',
        'disk',
        'status',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'errors',
        'imported_by',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
