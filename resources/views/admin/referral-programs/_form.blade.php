@php
    /** @var \App\Models\Referral\ReferralProgram|null $program */
    $program = $program ?? null;
    $isEdit  = $program !== null;
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-9">
        <label class="form-label fw-semibold">Program Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $program?->name ?? '') }}"
               required maxlength="120" placeholder="e.g. Spring 2026 Friend Referral">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold d-block">Active</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_active" name="is_active" value="1"
                   {{ old('is_active', $program?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Program is live</label>
        </div>
    </div>
</div>

<hr>
<h6 class="text-primary mb-2"><i class="bi bi-percent me-1"></i>Discount for new (referee) user</h6>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Discount Type <span class="text-danger">*</span></label>
        <select name="referee_discount_type" class="form-select" required>
            @foreach(['fixed' => 'Fixed (BDT)', 'percent' => 'Percent (%)'] as $val => $label)
                <option value="{{ $val }}"
                    {{ old('referee_discount_type', $program?->referee_discount_type ?? 'percent') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="referee_discount_value" class="form-control"
               value="{{ old('referee_discount_value', $program?->referee_discount_value ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Max Discount (BDT)</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control"
               value="{{ old('max_discount_amount', $program?->max_discount_amount ?? '') }}"
               placeholder="optional cap">
    </div>
</div>

<hr>
<h6 class="text-primary mb-2"><i class="bi bi-coin me-1"></i>Reward for referrer (optional)</h6>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Reward Type</label>
        <select name="referrer_reward_type" class="form-select">
            <option value="">— No reward —</option>
            @foreach(['fixed' => 'Fixed (BDT)', 'percent' => 'Percent (%)'] as $val => $label)
                <option value="{{ $val }}"
                    {{ old('referrer_reward_type', $program?->referrer_reward_type ?? '') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Reward Value</label>
        <input type="number" step="0.01" min="0" name="referrer_reward_value" class="form-control"
               value="{{ old('referrer_reward_value', $program?->referrer_reward_value ?? '') }}">
    </div>
</div>

<hr>
<h6 class="text-primary mb-2"><i class="bi bi-calendar-event me-1"></i>Eligibility &amp; window</h6>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Min First-Purchase Amount (BDT)</label>
        <input type="number" step="0.01" min="0" name="min_first_purchase_amount" class="form-control"
               value="{{ old('min_first_purchase_amount', $program?->min_first_purchase_amount ?? '') }}"
               placeholder="optional">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Starts At</label>
        <input type="date" name="starts_at" class="form-control"
               value="{{ old('starts_at', $program?->starts_at?->format('Y-m-d') ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Ends At</label>
        <input type="date" name="ends_at" class="form-control"
               value="{{ old('ends_at', $program?->ends_at?->format('Y-m-d') ?? '') }}">
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'Save Changes' : 'Create Program' }}
    </button>
    <a href="{{ route('admin.referral-programs.index') }}" class="btn btn-secondary">Cancel</a>
</div>
