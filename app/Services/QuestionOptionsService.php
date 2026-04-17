<?php

namespace App\Services;

use App\Models\Question\QuestionOptions;
use App\Repositories\Interfaces\QuestionOptionsRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class QuestionOptionsService
{
    public function __construct(private readonly QuestionOptionsRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('question');
    }

    public function byQuestion(int $questionId): Collection
    {
        return $this->repo->byQuestion($questionId);
    }

    public function findOrFail(int $id): QuestionOptions
    {
        return $this->repo->find($id);
    }

    public function create(array $data): QuestionOptions
    {
        $validated = $this->validate($data);
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): QuestionOptions
    {
        $validated = $this->validate($data);
        return $this->repo->update($id, $validated);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $questionType = $data['question_type'] ?? null;

        $rules = [
            'question_id'                    => 'required|exists:questions,id',
            'question_type'                  => 'required|in:mcq_single,mcq_multiple,fill_in_blanks,writing,essay,audio,speaking,ielts_speaking,no_question,generic_question,highlighting,reordering',
            'actual_question'                => 'nullable|string',
            'option_text'                    => 'nullable|string|max:1000',
            'is_correct'                     => 'boolean',
            'correct_answer_explanation'     => 'nullable|string',
            'sort_order'                     => 'nullable|integer|min:0',
            'is_active'                      => 'boolean',
            'question_image_path'            => 'nullable|string|max:500',
            'question_audio_path'            => 'nullable|string|max:500',
        ];

        // Require option_text for MCQ types
        if (in_array($questionType, ['mcq_single', 'mcq_multiple'])) {
            $rules['option_text'] = 'required|string|max:1000';
        }

        // Require actual_question for question types that have question text
        if (in_array($questionType, ['mcq_single', 'mcq_multiple', 'fill_in_blanks', 'writing', 'essay', 'generic_question', 'highlighting', 'reordering'])) {
            $rules['actual_question'] = 'required|string';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
