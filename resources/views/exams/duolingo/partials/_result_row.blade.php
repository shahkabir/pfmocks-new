{{-- One row of the published breakdown. Vars: $n, $row (mapping, answer, earned, max) --}}
@php
    $q      = $row['mapping']->question;
    $type   = $q->type;
    $answer = $row['answer'];
    $result = $answer?->score_detail['result'] ?? null;

    // What the student gave, in a short readable form.
    $given = match (true) {
        !$answer => null,
        $type->response_type === 'fill_in_blanks' => collect($answer->response_blanks ?? [])->implode(', '),
        $type->response_type === 'word_classification' => ucfirst((string) $answer->response_text),
        $type->response_type === 'mcq_single' => $q->options->firstWhere('id', ($answer->selected_option_ids ?? [])[0] ?? null)?->option_text,
        $type->response_type === 'highlighting' => $answer->response_highlight['text'] ?? null,
        in_array($type->response_type, ['speaking', 'interactive_speaking'], true) => 'Recorded response',
        $type->response_type === 'interactive_writing' => 'Written response (' . count(array_filter($answer->response_texts ?? [])) . ' parts)',
        default => $answer->response_text,
    };

    // The answer key, for objective questions only.
    $key = match ($type->response_type) {
        'fill_in_blanks'      => $q->blanks->map(fn ($b) => $b->visible_prefix . $b->correct_answer)->implode(', '),
        'word_classification' => $q->correct_ans === 'yes' ? 'Yes, a real word' : 'No, not a real word',
        'mcq_single'          => $q->options->firstWhere('is_correct', true)?->option_text,
        'highlighting'        => $q->highlights->first()?->answer_text,
        'dictation'           => $q->correct_ans,
        default               => null,
    };
@endphp
<tr>
    <td class="text-muted">{{ $n }}</td>
    <td class="small">{{ $type->name }}</td>
    <td class="small">{{ \Illuminate\Support\Str::limit((string) $given, 120) ?: '—' }}</td>
    <td class="small text-muted">{{ \Illuminate\Support\Str::limit((string) $key, 120) ?: '—' }}</td>
    <td class="text-center small text-nowrap">
        @if(!$answer)
            <span class="badge bg-light text-dark border">Not answered</span>
        @elseif($type->isObjective())
            @if($result === 'correct')
                <span class="badge bg-success">Correct</span>
            @elseif($result === 'partial')
                <span class="badge bg-warning text-dark">Partly correct</span>
            @else
                <span class="badge bg-danger">Incorrect</span>
            @endif
            <div class="text-muted">{{ rtrim(rtrim(number_format($row['earned'], 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($row['max'], 2), '0'), '.') }}</div>
        @else
            <span class="badge bg-primary">Evaluated</span>
            <div class="text-muted">{{ rtrim(rtrim(number_format($row['earned'], 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($row['max'], 2), '0'), '.') }}</div>
        @endif
    </td>
</tr>
@if($answer?->feedback)
    <tr><td></td><td colspan="4" class="small"><i class="bi bi-chat-left-text me-1 text-primary"></i>{{ $answer->feedback }}</td></tr>
@endif
