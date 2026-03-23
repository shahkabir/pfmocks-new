{{-- @extends('layouts.app') --}}

{{-- @section('title', 'IELTS Listening Test') --}}

{{-- @section('content') --}}
<style>
    /* body {
        background: #f4f6f9;
    } */

    /* ===== TOP BAR ===== */
    /* .exam-topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    } */

    /* .exam-topbar-left {
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
    }

    .top-icons i {
        margin-left: 15px;
        color: #555;
        cursor: pointer;
    } */

    /* ===== CONTENT ===== */
    .exam-content {
        background: #fff;
        height: calc(100vh - 260px);
        overflow-y: auto;
        padding: 20px;
    }

    .question-block {
        margin-bottom: 25px;
    }

    .question-block input {
        width: 120px;
        display: inline-block;
        margin: 0 5px;
        text-align: center;
    }

    /* ===== QUESTION PALETTE ===== */
    .question-palette {
        background: #f9fafb;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
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

    .palette-nav {
        margin-left: auto;
        display: flex;
        gap: 5px;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* .exam-footer audio {
        width: 320px;
    } */

    .footer-right {
        margin-left: auto;
    }
</style>

{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Listening Test')

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
        font-size: 15px;
        /* color: #333; */
        color: rgb(120, 121, 122);
    }

    .timer {
        font-weight: bold;
        font-size: 15px;
    }

    .top-icons i {
        font-size: 16px;
        margin-left: 15px;
        cursor: pointer;
        color: #555;
    }

    /* ===== BODY ===== */
    .exam-body {
        /* display: flex; */
        height: calc(100vh - 200px);
        background: #fff;
        /* border-bottom: 1px solid #ddd; */
    }

    .listening-panel {
        width: 100%;
        padding: 20px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
        background: #e5e5e5; /* very soft blue background for reading panel */
    }

    .question-panel {
        width: 100%;
        padding: 10px 20px;
        overflow-y: auto;
        background: #e5e5e5;
    }

    .border-secondary{
        margin-bottom: 5px !important;
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
        /* border-top: 1px solid #ddd; */
        padding: 5px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .parts-area{
        margin: 0px !important;
        padding-left: 20px;
        display: flex;
        gap: 15px;
        background: #f9fafb;
    }

    .btn-parts {
       border-radius: 5px;
    }

    .palette-btn {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background: #676767;
        font-size: 13px;
    }

    .palette-btn.active {
        background: #ffffff;
        color: #000000;
        border-color: #676767;
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

    /* ====== Audio Player ====== */
    .audio-div{
        display: flex;
        padding: 2px 15px 10px 15px;
        justify-content: center;
    }

    .audio-player {
        width: 100%;
        max-width: 1200px;
    }
</style>

<style>
    /* Style for mcq, multi select */
    /* Default option block */
    .exam-option {
        padding: 8px 12px;
        border-radius: 6px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        border: 1px solid transparent;
    }

    /* Hover */
    .exam-option:hover {
        background-color: #f8f9fa;
    }

    /* Selected */
    .exam-option.selected {
        background-color: #e9f2ff;   /* very soft blue */
        border-color: #c7ddff;
    }
</style>

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">IELTS</div>  {{ $module->name }}
    </div>

    <div class="exam-topbar-center">
        <div class="time" id="time">
            <svg xmlns="http://w3.org" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
            </svg>
            <strong><span id="timer">{{ $module->duration_minutes }}:00</span></strong>
        </div>
    </div>

    <div class="exam-topbar-right">
        <div class="candidate-info">
            <strong> {{ $user->name }}</strong><br>
        </div>
    </div>
</div>

{{-- ================= BODY ================= --}}

@php
    $parts = [];
    $totalNumberOfQuestionsInAllParts = 0;
    foreach ($questions as $question) {

        $part = $question['part_number']; //['group']
        // var_dump($part);

        if (!isset($parts[$part])) {

            $parts[$part] = [
                'header' => $question['question_header'],
                'passage' => $question['passage'],
                'part_audio_url' => $question['part_audio_url'],
                'part_image_url' => $question['part_image_url'],
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
                $totalNumberOfQuestionsInAllParts++;
            }
        }
    }
    
@endphp

{{-- {{ dd(get_defined_vars(), $parts, $totalNumberOfQuestionsInAllParts) }} --}}

<form class="listening-form">
@csrf
    <div class="exam-body">
        @php $qNo = 1; @endphp
        @foreach($parts as $partNumber => $partData)

                <div class="exam-body listening-part"
                    id="part-{{ $partNumber }}"
                    style="{{ $partNumber === array_key_first($parts) ? '' : 'display:none' }}">

                    
                    {{-- RIGHT: QUESTIONS --}}
                    <div class="question-panel">

                        @foreach($partData['blocks'] as $block)

                            {{-- INSTRUCTION (shown ONCE per block) --}}
                                <div class="alert alert-light mb-3 border-secondary">
                                    <b>{!! $block['instruction'] !!}</b>
                                    {{-- nl2br(e()) --}}
                                </div>

                                <div class="audio-div">
                                    <audio controls class="audio-player" controlsList="nodownload">
                                        <source src="{{ asset('').$parts[1]['part_audio_url'] }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                    </audio>    
                                </div>

                            {{-- QUESTIONS UNDER THIS INSTRUCTION --}}
                            @foreach($block['questions'] as $actualQuestion => $options)
                            {{-- Actual Question div --}}
                                <div class="mb-4 question-item" id="question-{{ $qNo }}" 
                                            data-part="{{ $partNumber }}"
                                            data-qno="{{ $qNo }}">
                                    {{-- @php var_dump($options); @endphp --}}
                                    <h6 class="mb-2">
                                             @if(!str_contains($actualQuestion, '[[blank]]') 
                                                && $options[0]['question_type'] != 'fill_in_blanks'
                                                && $options[0]['question_type'] != 'no_question')
                                                <b>{{ $qNo }}&nbsp;</b>{{ $actualQuestion }}
                                             @elseif($options[0]['question_type'] == 'no_question')
                                              @php $qNo--; @endphp
                                                {{ $actualQuestion }}
                                             @endif  
                                    </h6>
                                    
                                    {{-- {{ dd($options); }} --}}

                                    {{-- show question image if exists --}}
                                    @if(!empty($options[0]['question_image_path']))
                                        <div class="mb-3">
                                            <img src="{{ asset($options[0]['question_image_path']) }}" 
                                            alt="Question Image" class="img-fluid"
                                            style="max-width: 400px; max-height: 400px;">
                                        </div>
                                    @endif

                                    {{-- @php dd($actualQuestion, $options, $options[0]['ielts_listening_question_line']); @endphp --}}
                                    {{-- {{ $actualQuestion}} --}}
                                        @foreach($options as $option)

                                        {{-- <h6 class="mb-2">
                                            {{ 'question_option_id:'. $option['id']}}
                                            {{ 'question_id: '. $option['question_id'] }}
                                            {{ 'correct: '. ($option['is_correct'] ? 'true' : 'false') }}
                                        </h6> --}}

                                            @if($option['question_type'] == 'fill_in_blanks')
                                                
                                            <div class="mb-3">
                                                <b>{{ $qNo }}&nbsp;</b>
                                                {!! str_replace(
                                                    '[[blank]]',
                                                    '<input type="text" 
                                                            class="form-control d-inline-block mx-1" 
                                                            style="width:160px" 
                                                            name="answers[' . $option['question_id'] . '][question_option_id][' . $option['id'] . ']"  
                                                            data-question-id="' . $option['question_id'] . '"
                                                            placeholder="'. $qNo .'">', ////$qNo
                                                    e($option['actual_question'])
                                                ) !!}
                                                {{-- <input type="hidden" name="answers[{{ $option['question_id'] }}][question_type]" value="{{ $option['question_type'] }}"> --}}
                                            </div>

                                            @elseif($option['question_type'] == 'mcq_single')

                                                <div class="exam-option">
                                                    {{-- mb-1 --}}
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

                                                <div class="mb-1 exam-option">
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

                                            @elseif($option['question_type'] === 'mcq_select')
                                                <div class="mb-3">
                                                    <select class="form-select"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}]"
                                                        {{-- name="answers[{{ md5($actualQuestion) }}]" --}}>

                                                        <option value="">-- Select Answer --</option>

                                                        @foreach($options as $option)
                                                            <option value="{{ $option['id'] }}">
                                                                {{ $option['option_text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>

                                            {{-- @elseif($option['question_type'] === 'no_question')
                                                <div class="mb-3">
                                                    <b>{{ $option['actual_question'] }}</b>
                                                </div> --}}

                                                @php break; @endphp
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
    <div class="mb-3 parts-area">
        @foreach($parts as $partNumber => $partData)
            <button type="button" class="btn btn-sm btn-secondary btn-parts" onclick="showPart({{ $partNumber }})">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>
    {{-- {{dd($qNo)}} --}}
    {{-- ================= QUESTION PALETTE ================= --}}
    <div class="question-palette">
        @for($i = 1; $i <= $qNo; $i++)
            <button type="button" class="palette-btn {{ $i === 1 ? 'active' : '' }}" data-target="question-{{ $i }}">
                {{ $i }}
            </button>
        @endfor
    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="exam-footer">
        <div class="footer-right">
            <button type="submit" class="btn btn-success btn-sm">Submit Answers</button>
            <button class="btn btn-outline-secondary btn-sm">Exit</button>
            <input type="hidden" name="module_id" value="{{ $module->id }}">
            <input type="hidden" name="exam_name" value="{{ $module->name }}">
            <input type="hidden" name="module_type" value="{{ $module->module_type }}">
        </div>
    </div>
</form>

{{-- @endsection --}}
<script>

    $('.listening-form').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        console.log(formData);

        $.ajax({
            url: "{{ route('exam.ielts.listening.submit') }}",
            method: "POST",
            data: formData,
            success: function(response) {
                //alert('Your answers have been submitted successfully!');
                // Optionally, redirect or perform other actions
                console.log(response);
                alert(response.message+" Going back to dashboard.");
                window.location.href = "{{ route('dashboard.student') }}";
            },
            error: function(xhr, status, error) {
                alert('An error occurred while submitting your answers. Please try again.');
            }
        });
    });


    function showPart(partNumber) {
        document.querySelectorAll('.listening-part' ,'.audio-div').forEach(part => {
            part.style.display = 'none';
        });

        document.getElementById('part-' + partNumber).style.display = 'flex';

        // Update active button
        document.querySelectorAll('.palette-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Update audio source
        const audioPlayer = document.querySelector('.audio-player');
        const partAudioUrl = "{{ asset('') }}" + @json($parts)[partNumber]['part_audio_url'];
        audioPlayer.src = partAudioUrl;
        audioPlayer.load();

    }


    $(document).on('change', '.exam-option input', function () {

        const $input = $(this);
        const $container = $input.closest('.exam-option');

        // If radio → remove selection from siblings
        if ($input.attr('type') === 'radio') {
            const name = $input.attr('name');

            $('input[name="' + name + '"]').each(function () {
                $(this).closest('.exam-option').removeClass('selected');
            });

            $container.addClass('selected');
        }

        // If checkbox → toggle individually
        if ($input.attr('type') === 'checkbox') {
            if ($input.is(':checked')) {
                $container.addClass('selected');
            } else {
                $container.removeClass('selected');
            }
        }

    });



    // Countdown timer (dummy)
    let seconds = {{ $module->duration_minutes * 60 }}; // 59 minutes

    setInterval(() => {
        if (seconds <= 0) return;
        seconds--;
        let m = Math.floor(seconds / 60);
        let s = seconds % 60;
        document.getElementById('timer').innerText =
            `${m}:${s.toString().padStart(2, '0')}`;
    }, 1000);

    // Question palette navigation
    $(document).on('click', '.palette-btn', function () {

            let targetId = $(this).data('target');
            let $target = $('#' + targetId);

            if (!$target.length) return;

            let part = $target.data('part');

            // 🔹 Step 1: Show correct part
            $('.listening-part').hide();
            $('#part-' + part).show();

            // 🔹 Step 2: Scroll to question
            $('html, body').animate({
                scrollTop: $target.offset().top - 100
            }, 300);

            // 🔹 Step 3: Highlight active palette
            $('.palette-btn').removeClass('active');
            $(this).addClass('active');
    });

</script>
{{-- @endsection --}}
