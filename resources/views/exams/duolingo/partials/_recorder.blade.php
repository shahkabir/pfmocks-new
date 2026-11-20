{{-- Microphone recorder. Recordings stay in the browser until answer saving exists.
     Vars: $savedUrl (nullable) --}}
<div class="det-recorder" data-role="recorder">
    <button type="button" class="btn btn-danger" data-role="record-start">
        <i class="bi bi-mic-fill me-1"></i>Record
    </button>
    <button type="button" class="btn btn-outline-secondary" data-role="record-stop" disabled>
        <i class="bi bi-stop-fill me-1"></i>Stop
    </button>
    <span class="small text-muted" data-role="record-status">Click Record when you are ready.</span>
    <audio controls class="det-playback" data-role="playback"
           @if($savedUrl ?? null) src="{{ asset(ltrim($savedUrl, '/')) }}" @else style="display:none;" @endif></audio>
</div>
