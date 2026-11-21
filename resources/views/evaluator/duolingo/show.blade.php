@extends('layouts.app')

@section('title', 'Duolingo Evaluation #' . $evaluation->id)

@section('content')
@php
    $labels = [
        'relevance'  => 'Relevance and task completion',
        'grammar'    => 'Grammar',
        'vocabulary' => 'Vocabulary',
        'coherence'  => 'Coherence and organization',
        'fluency'    => 'Fluency and pronunciation',
    ];
    $locked   = $evaluation->isCompleted();
    $words    = fn (?string $t) => $t ? count(preg_split('/\s+/u', trim($t), -1, PREG_SPLIT_NO_EMPTY)) : 0;
    $answered = $rubricRows->filter(fn ($r) => $r['answer'])->count();
    $scored   = $rubricRows->filter(fn ($r) => $r['answer']?->isEvaluated())->count();
@endphp

<div class="container-fluid py-3" style="max-width:1100px;">
    @foreach(['success' => 'success', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }}">{{ session($key) }}</div>
        @endif
    @endforeach
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <a href="{{ route('evaluator.duolingo.index') }}" class="small">&larr; All Duolingo evaluations</a>
            <h4 class="fw-bold mb-0 mt-1">{{ $attempt->module->name }}</h4>
            <div class="text-muted small">
                {{ $attempt->user->name }} · {{ $attempt->user->email }} · submitted {{ $attempt->ended_at?->format('Y-m-d H:i') }}
            </div>
        </div>
        <div class="text-end small">
            <div>Objective questions: <strong>{{ $objective['earned'] }} / {{ $objective['max'] }}</strong> marks (auto-marked)</div>
            <div>Writing and speaking: <strong>{{ $scored }} of {{ $answered }}</strong> responses scored</div>
            @if($locked)<span class="badge bg-success mt-1">Published {{ $attempt->published_at?->format('Y-m-d H:i') }}</span>@endif
        </div>
    </div>

    <div class="alert alert-light border small">
        Score each criterion from 0 to {{ rtrim(rtrim(number_format($criterionMax, 1), '0'), '.') }}.
        Weights follow the PerfectMocks rubric: relevance 25%, grammar 25%, vocabulary 20%, coherence 20%, fluency 10%.
        A response counts as scored once every criterion has a value. Unanswered questions score 0 automatically.
    </div>

    <form method="POST" action="{{ route('evaluator.duolingo.update', $evaluation->id) }}">
        @csrf

        @foreach($rubricRows as $i => $row)
            @php
                $q      = $row['mapping']->question;
                $type   = $q->type;
                $answer = $row['answer'];
                $skill  = $grading->rubricSkillFor($type);
            @endphp
            <div class="card mb-3" id="answer-{{ $answer?->id }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-secondary me-1">{{ $i + 1 }}</span>
                        <strong>{{ $type->name }}</strong>
                        <span class="text-muted small ms-1">{{ $row['mapping']->duolingoModule->section->name }} · {{ $q->question_code }}</span>
                    </div>
                    @if($answer?->isEvaluated())
                        <span class="badge bg-success">Scored {{ $answer->raw_score }} / {{ $answer->max_score }}</span>
                    @elseif($answer)
                        <span class="badge bg-warning text-dark">Needs scoring</span>
                    @else
                        <span class="badge bg-light text-dark border">Not answered · 0</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Prompt --}}
                        <div class="col-md-5">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Prompt</div>
                            @if($q->group?->scenario)<div class="small text-muted mb-1">{{ $q->group->scenario }}</div>@endif
                            @if($q->question_text)<div class="mb-2">{{ $q->question_text }}</div>@endif
                            @if($q->image_url)
                                <img src="{{ asset($q->image_url) }}" alt="{{ $q->image_alt_text }}" class="img-fluid rounded border mb-2" style="max-height:220px;">
                            @endif
                            @if($q->audio_url)
                                <audio controls preload="none" src="{{ asset($q->audio_url) }}" class="w-100 mb-1"></audio>
                                @if($q->audio_transcript)<div class="small text-muted fst-italic">{{ $q->audio_transcript }}</div>@endif
                            @endif
                            @if($type->response_type === 'interactive_writing')
                                @foreach($q->turns as $turn)
                                    <div class="small mt-1"><strong>Part {{ $turn->turn_order }}:</strong> {{ $turn->prompt_text }}</div>
                                @endforeach
                            @endif
                            @if($q->group && $type->tag === 'ILSC')
                                <div class="small text-muted mt-2">Conversation transcript:</div>
                                @foreach($q->group->questions as $part)
                                    @if($part->audio_transcript)<div class="small fst-italic" style="white-space:pre-line;">{{ $part->audio_transcript }}</div>@endif
                                @endforeach
                            @endif
                        </div>

                        {{-- Response --}}
                        <div class="col-md-7">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Student response</div>
                            @if(!$answer)
                                <div class="text-muted">The student did not answer this question.</div>
                            @else
                                @switch($type->response_type)
                                    @case('interactive_writing')
                                        @foreach($answer->response_texts ?? [] as $s => $text)
                                            <div class="small fw-semibold mt-1">Part {{ $s + 1 }} · {{ $words($text) }} words</div>
                                            <div class="border rounded p-2 bg-light" style="white-space:pre-wrap;">{{ $text ?: '—' }}</div>
                                        @endforeach
                                        @break
                                    @case('speaking')
                                        @if($answer->response_audio_url)
                                            <audio controls preload="none" src="{{ asset($answer->response_audio_url) }}" class="w-100"></audio>
                                        @else
                                            <div class="text-muted">No recording.</div>
                                        @endif
                                        @break
                                    @case('interactive_speaking')
                                        @foreach($q->turns as $turn)
                                            @php $url = ($answer->response_audio_urls ?? [])[(string) $turn->turn_order] ?? null; @endphp
                                            <div class="small mt-1"><strong>Turn {{ $turn->turn_order }}:</strong> {{ $turn->prompt_text }}</div>
                                            @if($url)
                                                <audio controls preload="none" src="{{ asset($url) }}" class="w-100"></audio>
                                            @else
                                                <div class="small text-muted">No recording for this turn.</div>
                                            @endif
                                        @endforeach
                                        @break
                                    @default
                                        <div class="small text-muted mb-1">{{ $words($answer->response_text) }} words</div>
                                        <div class="border rounded p-2 bg-light" style="white-space:pre-wrap;">{{ $answer->response_text ?: '—' }}</div>
                                @endswitch
                            @endif
                        </div>
                    </div>

                    @if($answer)
                        <hr>
                        <div class="row g-2 align-items-end">
                            @foreach($criteria[$skill] as $criterion => $weight)
                                <div class="col-6 col-md">
                                    <label class="form-label small mb-1">{{ $labels[$criterion] ?? ucfirst($criterion) }} <span class="text-muted">({{ $weight }}%)</span></label>
                                    <input type="number" class="form-control form-control-sm" step="0.5" min="0" max="{{ $criterionMax }}"
                                           name="scores[{{ $answer->id }}][{{ $criterion }}]"
                                           value="{{ old("scores.{$answer->id}.{$criterion}", ($answer->rubric_scores ?? [])[$criterion] ?? '') }}"
                                           @disabled($locked)>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <label class="form-label small mb-1">Feedback for the student (optional)</label>
                            <textarea class="form-control form-control-sm" rows="2" name="feedback[{{ $answer->id }}]" @disabled($locked)>{{ old("feedback.{$answer->id}", $answer->feedback) }}</textarea>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label fw-semibold">Overall feedback (optional)</label>
                <textarea class="form-control" rows="3" name="feedback_text" @disabled($locked)>{{ old('feedback_text', $evaluation->feedback_text) }}</textarea>
            </div>
        </div>

        @unless($locked)
            <div class="d-flex justify-content-end gap-2 mb-4">
                <button type="submit" name="action" value="save" class="btn btn-outline-primary">Save scores</button>
                <button type="submit" name="action" value="publish" class="btn btn-success"
                        onclick="return confirm('Publish the final result to the student? Scores cannot be changed afterwards.');">
                    Save and publish result
                </button>
            </div>
        @endunless
    </form>
</div>
@endsection
