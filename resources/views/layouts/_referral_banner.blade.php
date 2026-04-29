@php
    /** @var array $refBanner */
    /** @var string $refBanner['code'] */
    /** @var string $refBanner['display_value'] */
@endphp

<style>
    .ref-banner {
        position: relative;
        background: linear-gradient(95deg, #d1f4e0 0%, #fff8e1 50%, #ffe4d6 100%);
        background-size: 200% 100%;
        border-bottom: 1px solid #f5e9c8;
        animation: ref-banner-pan 14s ease-in-out infinite;
        padding: 7px 16px;
        font-size: .85rem;
        color: #4a3b00;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        line-height: 1.35;
    }
    @keyframes ref-banner-pan {
        0%, 100% { background-position: 0%   50%; }
        50%      { background-position: 100% 50%; }
    }

    .ref-banner .gift-ico {
        font-size: 1rem;
        color: #d6336c;
        animation: ref-gift-bounce 2.4s ease-in-out infinite;
    }
    @keyframes ref-gift-bounce {
        0%, 100% { transform: translateY(0)    rotate(-6deg); }
        50%      { transform: translateY(-3px) rotate(6deg);  }
    }

    /* The amount itself — flashing/shimmer */
    .ref-amount {
        position: relative;
        display: inline-block;
        font-weight: 800;
        font-size: 1.05rem;
        color: #d6336c;
        background: linear-gradient(90deg, #d6336c 0%, #f59f00 35%, #d6336c 70%, #f59f00 100%);
        background-size: 220% auto;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        padding: 0 2px;
        animation: ref-amount-shimmer 1.6s linear infinite,
                   ref-amount-pulse   1.4s ease-in-out infinite;
    }
    @keyframes ref-amount-shimmer {
        from { background-position: 0%   center; }
        to   { background-position: 220% center; }
    }
    @keyframes ref-amount-pulse {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.10); }
    }
    .ref-amount::after {
        content: '';
        position: absolute;
        inset: -3px -6px;
        border-radius: 6px;
        background: rgba(214,51,108,.12);
        z-index: -1;
        animation: ref-amount-glow 1.4s ease-in-out infinite;
    }
    @keyframes ref-amount-glow {
        0%, 100% { opacity: .35; transform: scale(.94); }
        50%      { opacity: .85; transform: scale(1.08); }
    }

    .ref-banner .ref-cta {
        color: #084298;
        font-weight: 600;
        text-decoration: none;
        border-bottom: 1px dashed rgba(8,66,152,.5);
        margin-left: 4px;
    }
    .ref-banner .ref-cta:hover { color: #052c65; border-bottom-color: #052c65; }

    .ref-banner .ref-close {
        background: rgba(0,0,0,.06);
        border: none;
        width: 22px; height: 22px;
        border-radius: 50%;
        color: #4a3b00;
        font-size: .75rem;
        line-height: 1;
        cursor: pointer;
        margin-left: 6px;
        transition: background-color .15s;
    }
    .ref-banner .ref-close:hover { background: rgba(0,0,0,.12); }

    /* Hidden once user clicks dismiss this session */
    body.ref-banner-dismissed .ref-banner { display: none; }

    @media (max-width: 480px) {
        .ref-banner { font-size: .78rem; padding: 6px 10px; }
        .ref-amount { font-size: .98rem; }
    }
</style>

<div class="ref-banner" id="refBanner" role="status" aria-live="polite">
    <i class="bi bi-gift-fill gift-ico"></i>
    <span>You have an unredeemed referral code</span>
    <span class="text-muted" style="opacity:.7;">·</span>
    <span>Save</span>
    <span class="ref-amount">{{ $refBanner['display_value'] }}</span>
    @if(!empty($refBanner['max_cap']))
        <span class="small" style="opacity:.75;">(up to ৳ {{ number_format($refBanner['max_cap'], 0) }})</span>
    @endif
    <span>on your first paid module</span>

    <a href="{{ route('dashboard') }}" class="ref-cta">
        <i class="bi bi-arrow-right-short"></i>Pick a module
    </a>

    <button type="button" class="ref-close" id="refBannerClose" aria-label="Dismiss"
            title="Dismiss for this session">
        <i class="bi bi-x"></i>
    </button>
</div>

<script>
(function () {
    if (sessionStorage.getItem('refBannerDismissed') === '1') {
        document.body.classList.add('ref-banner-dismissed');
    }
    document.getElementById('refBannerClose')?.addEventListener('click', function () {
        document.body.classList.add('ref-banner-dismissed');
        sessionStorage.setItem('refBannerDismissed', '1');
    });
})();
</script>
