{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Reading Test')

@include('layouts.header')
{{-- @section('content') --}}
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
        color: #d32f2f;
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
        display: flex;
        height: calc(100vh - 200px);
        background: #fff;
        border-bottom: 1px solid #ddd;
    }

    .reading-panel {
        width: 55%;
        padding: 20px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
    }

    .question-panel {
        width: 45%;
        padding: 20px;
        overflow-y: auto;
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

    /* ===== QUESTION PALETTE ===== */
    .question-palette {
        background: #f9fafb;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .palette-btn {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background: #fff;
        font-size: 13px;
    }

    .palette-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
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
        <div class="exam-logo">IELTS</div>
        <div class="candidate-info">
            <strong>48887345</strong><br>
            <span class="timer">59 minutes remaining</span>
        </div>
    </div>

    <div class="top-icons">
        <i class="fas fa-wifi"></i>
        <i class="far fa-bell"></i>
        <i class="fas fa-bars"></i>
        <i class="far fa-edit"></i>
    </div>
</div>

{{-- ================= BODY ================= --}}

{{-- {{ dd(get_defined_vars()) }} --}}

@php
    $parts = [];

    foreach ($questions as $question) {

        $part = $question['part_number']; //['group']
        // var_dump($part);

        if (!isset($parts[$part])) {

            $parts[$part] = [
                'header' => $question['question_header'],
                'passage' => $question['passage'],
                'blocks' => []
            ];
        }

        // Each instruction block
        foreach ($question['group']['blocks'] as $block) {

            $blockKey = $block['id'];

            if (!isset($parts[$part]['blocks'][$blockKey])) {

                $parts[$part]['blocks'][$blockKey] = [
                    'instruction' => $block['instruction_text'],
                    'questions' => []
                ];
            }

            // echo 'question_option_ids: '. print_r($block['question_option_ids']);

            // print_r(collect($question['options'])->pluck('id'));

            // Filter only options belonging to this block
            $optionsInBlock = collect($question['options'])
                ->whereIn('id', $block['question_option_ids'])
                ->groupBy('actual_question');

            //dd($optionsInBlock);

            foreach ($optionsInBlock as $actualQuestion => $options) {
                $parts[$part]['blocks'][$blockKey]['questions'][$actualQuestion] = $options;
            }
        }
    }
    // dd($parts);
@endphp

<form class="reading-form">
@csrf
    <div class="exam-body">
        @php $qNo = 1; @endphp
        @foreach($parts as $partNumber => $partData)

                <div class="exam-body reading-part"
                    id="part-{{ $partNumber }}"
                    style="{{ $partNumber === array_key_first($parts) ? '' : 'display:none' }}">

                    {{-- LEFT: PASSAGE --}}
                    <div class="reading-panel">
                        <h5>Part {{ $partNumber }}</h5>
                        <p><strong>{{ $partData['header'] }}</strong></p>
                        <div>
                            {!! nl2br(e($partData['passage'])) !!}
                        </div>
                    </div>

                    {{-- RIGHT: QUESTIONS --}}
                    <div class="question-panel">
                    
                        @foreach($partData['blocks'] as $block)

                            {{-- INSTRUCTION (shown ONCE per block) --}}
                                <div class="alert alert-light mb-3 border-secondary">
                                    <b>{!! nl2br(e($block['instruction'])) !!}</b>
                                </div>
                            {{-- QUESTIONS UNDER THIS INSTRUCTION --}}
                            @foreach($block['questions'] as $actualQuestion => $options)
                                <div class="mb-4">

                                    <h6 class="mb-2">
                                        {{ $qNo }}. {{ $actualQuestion }}
                                    </h6>

                                    {{-- @php dd($actualQuestion, $options); @endphp --}}
                                    {{-- {{ $actualQuestion}} --}}
                                        @foreach($options as $option)

                                        <h6 class="mb-2">
                                            {{ 'question_option_id:'. $option['id']}}
                                            {{ 'question_id: '. $option['question_id'] }}
                                        </h6>

                                            @if($option['question_type'] == 'fill_in_blanks')
                                                
                                            <div class="mb-3">
                                                {!! str_replace(
                                                    '[[blank]]',
                                                    '<input type="text" 
                                                            class="form-control d-inline-block mx-1" 
                                                            style="width:160px" 
                                                            name="answers[' . $option['question_id'] . '][question_option_id][' . $option['id'] . ']"  
                                                            data-question-id="' . $option['question_id'] . '">', ////$qNo
                                                    e($option['actual_question'])
                                                ) !!}
                                                {{-- <input type="hidden" name="answers[{{ $option['question_id'] }}][question_type]" value="{{ $option['question_type'] }}"> --}}
                                            </div>

                                            @elseif($option['question_type'] == 'mcq_single')

                                                <div class="form-check mb-1">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}]"
                                                        {{-- name="answers[{{ $option['id'] }}]" --}}
                                                        {{-- name="answers[{{ md5($actualQuestion) }}]" --}}
                                                        value="{{ $option['id'] }}"
                                                        id="option-{{ $option['id'] }}">

                                                        {{-- <input type="hidden" name="answers[{{ $option['question_id'] }}][question_type]" value="{{ $option['question_type'] }}"> --}}

                                                    <label class="form-check-label" for="option-{{ $option['id'] }}">
                                                        {{ $option['option_text'] }}
                                                    </label>
                                                </div>

                                            @elseif($option['question_type'] === 'mcq_multiple')

                                                <div class="form-check mb-1">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}][]"
                                                        {{-- name="answers[{{ md5($actualQuestion) }}][]" --}}
                                                        value="{{ $option['id'] }}"
                                                        id="option-{{ $option['id'] }}">

                                                        {{-- <input type="hidden" name="answers[{{ $option['question_id'] }}][question_type]" value="{{ $option['question_type'] }}"> --}}

                                                    <label class="form-check-label"
                                                        for="option-{{ $option['id'] }}">
                                                        {{ $option['option_text'] }}
                                                    </label>
                                                </div>
                                            @endif

                                        @endforeach
                                </div>
                             @php $qNo++ @endphp
                            {{-- {{ QUESTION BLOCK ENDS HERE }} --}}
                            @endforeach
                        {{-- BLOCKS loop ends here --}}
                        @endforeach
                    </div>
                </div>
       
        {{-- PARTS loop ends here--}}
        @endforeach 
    </div>


    {{-- ================= Parts ================= --}}
    <div class="mb-3">
        @foreach($parts as $partNumber => $partData)
            <button type="button" class="btn btn-sm btn-primary" onclick="showPart({{ $partNumber }})">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>

    {{-- ================= QUESTION PALETTE ================= --}}
    {{-- <div class="question-palette">
        @for($i = 1; $i <= 13; $i++)
            <button class="palette-btn {{ $i === 1 ? 'active' : '' }}">
                {{ $i }}
            </button>
        @endfor
    </div> --}}



    {{-- ================= FOOTER ================= --}}
    <div class="exam-footer">
        <div class="current-time">
            ⏰ 15:49
        </div>

        <div class="footer-right">
            <button type="submit" class="btn btn-success btn-sm">Submit Answers</button>
            <button type="button" class="btn btn-outline-secondary btn-sm">Exit</button>
        </div>
    </div>
</form>

{{-- @endsection --}}
<script>

    $('.reading-form').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        console.log(formData);

        $.ajax({
            url: "{{ route('exam.ielts.reading.submit') }}",
            method: "POST",
            data: formData,
            success: function(response) {
                alert('Your answers have been submitted successfully!');
                // Optionally, redirect or perform other actions
                console.log(response);
            },
            error: function(xhr, status, error) {
                alert('An error occurred while submitting your answers. Please try again.');
            }
        });
    });


    function showPart(partNumber) {
        document.querySelectorAll('.reading-part').forEach(part => {
            part.style.display = 'none';
        });
        document.getElementById('part-' + partNumber).style.display = 'flex';

        // Update active button
        document.querySelectorAll('.palette-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }
</script>