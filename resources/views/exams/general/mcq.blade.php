@section('title', $module->name ?? 'MCQ Exam')

@include('layouts.header')

<style>
    /* ===== Shared Theme ===== */
    body {
        background: #f4f6f9;
    }

    /* ===== TOP BAR ===== */
    .exam-topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .exam-topbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .exam-logo {
        font-weight: bold;
        font-size: 20px;
        color: #1a6fc4;
    }

    .candidate-info {
        font-size: 13px;
        color: #333;
    }

    .timer {
        font-weight: bold;
        font-size: 14px;
    }

    .top-icons i {
        font-size: 16px;
        margin-left: 15px;
        cursor: pointer;
        color: #555;
    }

    /* ===== BODY ===== */
    .exam-body {
        min-height: calc(100vh - 200px);
        background: #fff;
        border-bottom: 1px solid #ddd;
        padding: 24px 32px;
    }

    .question {
        margin-bottom: 25px;
    }

    .question h6 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .question label {
        display: block;
        font-weight: normal;
        cursor: pointer;
    }

    /* ===== OPTION STYLING ===== */
    .exam-option {
        padding: 8px 12px;
        border-radius: 6px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        border: 1px solid transparent;
    }

    .exam-option:hover {
        background-color: #f8f9fa;
    }

    .exam-option.selected {
        background-color: #e9f2ff;
        border-color: #c7ddff;
    }

    /* ===== PART TABS ===== */
    .part-tabs {
        background: #f9fafb;
        border-bottom: 1px solid #ddd;
        padding: 8px 16px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
    }

    .footer-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .current-time {
        font-weight: bold;
        font-size: 14px;
    }
</style>

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">{{ $module->exam->name ?? 'MCQ' }}</div>
        <div class="candidate-info">
            <strong>{{ $user->name }}</strong><br>
            <span class="timer" id="exam-timer">{{ $module->duration_minutes ?? 60 }} minutes remaining</span>
        </div>
    </div>

    <div class="top-icons">
        <i class="fas fa-wifi"></i>
        <i class="far fa-bell"></i>
        <i class="fas fa-bars"></i>
        <i class="far fa-edit"></i>
    </div>
</div>

@php
    $parts = [];

    foreach ($questions as $question) {
        $part = $question['part_number'];

        if (!isset($parts[$part])) {
            $parts[$part] = [
                'header' => $question['question_header'],
                'blocks' => []
            ];
        }

        foreach ($question['group']['blocks'] as $block) {
            $blockKey = $block['id'];

            if (!isset($parts[$part]['blocks'][$blockKey])) {
                $parts[$part]['blocks'][$blockKey] = [
                    'instruction' => $block['instruction_text'],
                    'questions'   => []
                ];
            }

            $optionsInBlock = collect($question['options'])
                ->whereIn('id', $block['question_option_ids'])
                ->groupBy('actual_question');

            foreach ($optionsInBlock as $actualQuestion => $options) {
                $parts[$part]['blocks'][$blockKey]['questions'][$actualQuestion] = $options;
            }
        }
    }
@endphp

<form class="mcq-form">
    @csrf

    {{-- Hidden fields for submission --}}
    <input type="hidden" name="module_id"   value="{{ $module->id }}">
    <input type="hidden" name="module_type" value="{{ $module->module_type }}">
    <input type="hidden" name="exam_name"   value="{{ $module->exam->name ?? '' }}">

    {{-- ================= PART TABS ================= --}}
    <div class="part-tabs">
        @foreach($parts as $partNumber => $partData)
            <button type="button"
                    class="btn btn-sm {{ $loop->first ? 'btn-primary' : 'btn-outline-primary' }} part-tab-btn"
                    data-part="{{ $partNumber }}"
                    onclick="showPart({{ $partNumber }}, this)">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>

    {{-- ================= QUESTION PANEL (single column) ================= --}}
    @php $qNo = 1; @endphp

    @foreach($parts as $partNumber => $partData)
        <div class="exam-body mcq-part"
             id="part-{{ $partNumber }}"
             style="{{ $loop->first ? '' : 'display:none' }}">

            <h5 class="mb-1">Part {{ $partNumber }}</h5>
            @if(!empty($partData['header']))
                <p class="text-muted mb-3"><strong>{{ $partData['header'] }}</strong></p>
            @endif

            @foreach($partData['blocks'] as $block)

                {{-- Instruction --}}
                <div class="alert alert-light mb-3 border-secondary">
                    <b>{!! $block['instruction'] !!}</b>
                </div>

                {{-- Questions in this block --}}
                @foreach($block['questions'] as $actualQuestion => $options)
                    <div class="mb-4">

                        <h6 class="mb-2"><b>{{ $qNo }}.</b>&nbsp;{{ $actualQuestion }}</h6>

                        @foreach($options as $option)

                            @if($option['question_type'] === 'mcq_single')
                                <div class="exam-option">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $option['question_id'] }}][question_option_id][{{ $qNo }}]"
                                           value="{{ $option['id'] }}"
                                           id="option-{{ $option['id'] }}">
                                    <label class="form-check-label" for="option-{{ $option['id'] }}">
                                        {{ $option['option_text'] }}
                                    </label>
                                </div>

                            @elseif($option['question_type'] === 'mcq_multiple')
                                <div class="exam-option">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="answers[{{ $option['question_id'] }}][question_option_id][{{ $qNo }}][]"
                                           value="{{ $option['id'] }}"
                                           id="option-{{ $option['id'] }}">
                                    <label class="form-check-label" for="option-{{ $option['id'] }}">
                                        {{ $option['option_text'] }}
                                    </label>
                                </div>
                            @endif

                        @endforeach
                    </div>
                    @php $qNo++ @endphp
                @endforeach

            @endforeach
        </div>
    @endforeach

    {{-- ================= FOOTER ================= --}}
    <div class="exam-footer">
        <div class="current-time" id="clock">⏰ --:--</div>

        <div class="footer-right">
            <button type="submit" class="btn btn-success btn-sm">Submit Answers</button>
            <button type="button" class="btn btn-outline-secondary btn-sm"
                    onclick="if(confirm('Are you sure you want to exit?')) window.location='{{ route('dashboard.student') }}'">
                Exit
            </button>
        </div>
    </div>
</form>

<script>

    // ===== Part tab switching =====
    function showPart(partNumber, btn) {
        document.querySelectorAll('.mcq-part').forEach(function (el) {
            el.style.display = 'none';
        });
        document.getElementById('part-' + partNumber).style.display = 'block';

        document.querySelectorAll('.part-tab-btn').forEach(function (b) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
    }

    // ===== Option highlight on select =====
    $(document).on('change', '.exam-option input', function () {
        const $input     = $(this);
        const $container = $input.closest('.exam-option');

        if ($input.attr('type') === 'radio') {
            const name = $input.attr('name');
            $('input[name="' + name + '"]').each(function () {
                $(this).closest('.exam-option').removeClass('selected');
            });
            $container.addClass('selected');
        }

        if ($input.attr('type') === 'checkbox') {
            $container.toggleClass('selected', $input.is(':checked'));
        }
    });

    // ===== Form submit via AJAX =====
    $('.mcq-form').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: "{{ route('exam.general.mcq.submit') }}",
            method: 'POST',
            data: formData,
            success: function (response) {
                alert('Your answers have been submitted!\nScore: ' + response.achieved_score + ' / ' + response.total_score + ' (' + Math.round(response.score_percentage) + '%)');
                window.location = "{{ route('dashboard.student') }}";
            },
            error: function () {
                alert('An error occurred while submitting. Please try again.');
            }
        });
    });

    // ===== Live clock =====
    function updateClock() {
        const now  = new Date();
        const h    = String(now.getHours()).padStart(2, '0');
        const m    = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('clock').textContent = '⏰ ' + h + ':' + m;
    }
    updateClock();
    setInterval(updateClock, 60000);

</script>
