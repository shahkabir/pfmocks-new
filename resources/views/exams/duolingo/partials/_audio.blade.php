{{-- Limited-replay audio player. Native controls are hidden so the limit can be enforced.
     Vars: $src, $maxReplays (null = unlimited), $autoplay (bool) --}}
<div class="det-audio" data-role="audio"
     data-max-replays="{{ $maxReplays }}"
     @if($autoplay ?? false) data-autoplay="1" @endif>
    <audio preload="none" src="{{ asset(ltrim($src, '/')) }}"></audio>
    <button type="button" class="btn btn-outline-success" data-role="audio-play">
        <i class="bi bi-volume-up-fill me-1"></i><span data-role="audio-label">Play audio</span>
    </button>
    <span class="small text-muted" data-role="audio-remaining"></span>
</div>
