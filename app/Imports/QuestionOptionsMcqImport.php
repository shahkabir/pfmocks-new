<?php

namespace App\Imports;

use App\Models\CsvImport;
use App\Models\Question\QuestionOptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionOptionsMcqImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public function __construct(
        private readonly int $questionId,
        private readonly int $importId
    ) {}

    public function chunkSize(): int
    {
        return config('csv_import.chunk_size', 100);
    }

    public function collection(Collection $rows): void
    {
        $chunkProcessed = 0;
        $chunkFailed    = 0;
        $chunkErrors    = [];

        // dd($rows->toArray());

        foreach ($rows as $rowNum => $row) {
            $row    = $row->toArray();
            $csvRow = $rowNum + 2; // +2 because row 1 is the header

            // ── 1. actual_question ───────────────────────────────────────────
            $actualQuestion = trim((string) ($row['actual_question'] ?? ''));
            if ($actualQuestion === '') {
                $chunkFailed++;
                $chunkErrors[] = "Row {$csvRow}: actual_question is empty — skipped.";
                continue;
            }

            // ── 2. number_of_options ─────────────────────────────────────────
            $numOptions = filter_var($row['number_of_options'] ?? null, FILTER_VALIDATE_INT);
            if ($numOptions === false || $numOptions === null || $numOptions < 2) {
                $chunkFailed++;
                $chunkErrors[] = "Row {$csvRow}: number_of_options is missing or less than 2 — skipped.";
                continue;
            }

            // ── 3. correct_option_in_number ──────────────────────────────────
            $correctPosition = filter_var($row['correct_option_in_number'] ?? null, FILTER_VALIDATE_INT);
            if ($correctPosition === false || $correctPosition === null || $correctPosition < 1 || $correctPosition > $numOptions) {
                $chunkFailed++;
                $chunkErrors[] = "Row {$csvRow}: correct_option_in_number must be between 1 and {$numOptions} — skipped.";
                continue;
            }

            // ── 4. optional fields ───────────────────────────────────────────
            $explanation = trim((string) ($row['correct_answer_explanation'] ?? ''));
            $isActive    = filter_var($row['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;

            // ── 5. Read exactly number_of_options option columns ─────────────
            $options     = [];
            $optionError = null;

            for ($i = 1; $i <= $numOptions; $i++) {

                // if($i == 2){
                //     // For the first option, we can allow both "option1" or "option_1" as column names for flexibility
                //     continue;
                // }

                $key = "option{$i}";

                if (!array_key_exists($key, $row)) {
                    $optionError = "Row {$csvRow}: number_of_options={$numOptions} but column \"{$key}\" not found in file — skipped.";
                    break;
                }

                $val = trim((string) ($row[$key] ?? ''));
                if ($val === '') {
                    $optionError = "Row {$csvRow}: {$key} is empty (number_of_options={$numOptions}) — skipped.";
                    break;
                }

                $options[] = $val;
            }

            if ($optionError !== null) {
                $chunkFailed++;
                $chunkErrors[] = $optionError;
                continue;
            }

            // ── 6. Persist all options for this question row ─────────────────
            //dd($options);
            try {
                DB::transaction(function () use ($actualQuestion, $options, $correctPosition, $explanation, $isActive) {
                    foreach ($options as $sortOrder => $optionText) {
                        QuestionOptions::create([
                            'question_id'                => $this->questionId,
                            'question_type'              => 'mcq_single',
                            'actual_question'            => $actualQuestion,
                            'option_text'                => $optionText,
                            'is_correct'                 => ($sortOrder + 1) === $correctPosition,
                            'correct_answer_explanation' => $explanation ?: null,
                            'sort_order'                 => $sortOrder + 1,
                            'is_active'                  => $isActive,
                        ]);
                    }
                });
                $chunkProcessed++;
            } catch (\Throwable $e) {
                $chunkFailed++;
                $chunkErrors[] = "Row {$csvRow}: DB error — " . $e->getMessage();
            }
        }

        // ── Atomically update counters ────────────────────────────────────────
        CsvImport::where('id', $this->importId)->update([
            'processed_rows' => DB::raw("processed_rows + {$chunkProcessed}"),
            'failed_rows'    => DB::raw("failed_rows + {$chunkFailed}"),
        ]);

        if (!empty($chunkErrors)) {
            $import   = CsvImport::find($this->importId);
            $existing = $import->errors ?? [];
            $import->update(['errors' => array_merge($existing, $chunkErrors)]);
        }
    }
}
