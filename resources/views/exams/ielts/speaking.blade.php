
{{-- @extends('layouts.app') --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'IELTS Speaking Test')

{{-- @section('content') --}}
<style>
    body {
        background: #f4f6f9;
    }

    /* ===== TOP BAR ===== */
    .exam-topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    }

    .top-icons i {
        margin-left: 15px;
        color: #555;
        cursor: pointer;
    }

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

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">IELTS</div>
        <div class="candidate-info">
            <strong>48887345</strong><br>
            <span>14 minutes remaining</span>
        </div>
    </div>

    <div class="top-icons">
        <i class="fas fa-wifi"></i>
        <i class="far fa-bell"></i>
        <i class="fas fa-bars"></i>
        <i class="far fa-edit"></i>
    </div>
</div>

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
            }
        }
    }
    
    // dd($parts);

@endphp

{{-- ================= MAIN CONTENT ================= --}}
<form class="speaking-form">
@csrf
<div class="exam-content">

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

                            {{-- QUESTIONS UNDER THIS INSTRUCTION --}}
                            @foreach($block['questions'] as $actualQuestion => $options)
                                <div class="mb-4">
                                    {{-- @php var_dump($options); @endphp --}}
                                    <h6 class="mb-2">
                                             @if(!str_contains($actualQuestion, '[[blank]]') 
                                                && $options[0]['question_type'] != 'fill_in_blanks'
                                                && $options[0]['question_type'] != 'no_question')
                                                <strong>{{ $qNo }}&nbsp;{{ $actualQuestion }} </strong>
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

                                            @if($option['question_type'] == 'ielts_speaking')

                                                <div class="speaking-question mb-3" data-question-id="{{ $option['question_id'] }}">
                                                    
                                                    {{-- <audio controls>
                                                        <source src="{{ asset($option['option_text']) }}" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio> --}}

                                                    <button type="button" class="btn btn-sm btn-primary start-btn">Start</button>
                                                    <button type="button" class="btn btn-sm btn-danger stop-btn">Stop</button>

                                                    <audio class="preview mt-2" controls></audio>

                                                    {{-- store uploaded file path --}}
                                                    {{-- <input type="hidden"
                                                        name="answers[{{ $question['id'] }}][audio_path]"
                                                        class="audio-path"> --}}
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

</div>
</form>

{{-- ================= Parts ================= --}}
    <div class="mb-3">
        @foreach($parts as $partNumber => $partData)
            <button type="button" class="btn btn-sm btn-primary" onclick="showPart({{ $partNumber }})">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>

    {{-- ================= QUESTION PALETTE ================= --}}
    <div class="question-palette">
        @for($i = 1; $i <= 10; $i++)
            <button class="palette-btn {{ $i === 1 ? 'active' : '' }}">
                {{ $i }}
            </button>
        @endfor

        <div class="palette-nav">
            <button class="btn btn-outline-secondary btn-sm">
                ←
            </button>
            <button class="btn btn-primary btn-sm">
                →
            </button>
        </div>
    </div>

{{-- ================= FOOTER ================= --}}
<div class="exam-footer">
    <div class="time-left">
        ⏱ Time left: 14:00
    </div>

    <div class="footer-right">
        <button class="btn btn-success btn-sm">
            Submit Speaking Test
        </button>
    </div>
</div>

<script>
let recorders = {};

document.querySelectorAll('.speaking-question').forEach(container => {

    const startBtn = container.querySelector('.start-btn');
    const stopBtn = container.querySelector('.stop-btn');
    const audioPreview = container.querySelector('.preview');
    const hiddenInput = container.querySelector('.audio-path');

    let mediaRecorder;
    let chunks = [];

    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {

        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = e => chunks.push(e.data);

        mediaRecorder.onstop = () => {
            const blob = new Blob(chunks, { type: 'audio/webm' });
            chunks = [];

            audioPreview.src = URL.createObjectURL(blob);

            uploadAudio(blob, container.dataset.questionId, hiddenInput);
        };

        startBtn.onclick = () => mediaRecorder.start();
        stopBtn.onclick = () => mediaRecorder.stop();
    });
});

function uploadAudio(blob, questionId, hiddenInput) {

    let formData = new FormData();
    formData.append('audio', blob, 'speaking.webm');
    formData.append('question_id', questionId);

    fetch('/speaking-upload-audio', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json());
    // .then(data => {
    //     hiddenInput.value = data.path;
    // });
}


function showPart(partNumber) {
        document.querySelectorAll('.listening-part').forEach(part => {
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

{{-- @endsection --}}
