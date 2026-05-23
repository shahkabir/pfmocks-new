@extends('layouts.app')

@section('title', 'Scholarships')

@section('content')
<style>
    .sch-hero {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        border-radius: 14px;
        padding: 22px 26px;
        box-shadow: 0 8px 24px rgba(13,110,253,.18);
        margin-bottom: 18px;
    }

    .sch-filters {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 18px;
    }

    .sch-card {
        border: 1px solid #e9ecef;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        transition: transform .15s, box-shadow .2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .sch-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,.08);
    }

    .sch-cover {
        position: relative;
        height: 96px;
        background: linear-gradient(135deg, #e9f2ff, #d0e4ff);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sch-cover img.flag {
        width: 80px;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,.18);
    }
    .sch-cover .featured-pin {
        position: absolute; top: 8px; right: 8px;
        background: #ffd43b; color: #5a3e00;
        border-radius: 999px; font-size: 10px; font-weight: 700;
        padding: 3px 8px; letter-spacing: .3px;
    }

    .sch-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; }
    .sch-body h6 {
        font-size: .95rem; font-weight: 700; color: #212529;
        margin: 0 0 6px; line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sch-body .desc {
        color: #6c757d; font-size: .82rem; line-height: 1.45;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; margin-bottom: 8px;
    }
    .sch-meta { font-size: .8rem; color: #6c757d; margin-bottom: 8px; }
    .sch-meta i { margin-right: 4px; }

    .chip {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10.5px; font-weight: 700; padding: 3px 9px;
        border-radius: 999px; text-transform: uppercase; letter-spacing: .3px;
        border: 1px solid transparent; white-space: nowrap;
    }
    .chip-fully    { background: #d1e7dd; color: #0a5934; border-color: #a3cfbb; }
    .chip-partial  { background: #fff3cd; color: #664d03; border-color: #ffe69c; }
    .chip-deadline-ok    { background: #cfe2ff; color: #084298; border-color: #9ec5fe; }
    .chip-deadline-warn  { background: #fff4e5; color: #b45309; border-color: #ffd8a8; }
    .chip-deadline-over  { background: #f8d7da; color: #58151c; border-color: #f1aeb5; }
    .chip-type     { background: #e7e7fd; color: #3730a3; border-color: #c7d2fe; }

    .sch-footer {
        padding: 10px 16px;
        border-top: 1px solid #f1f3f5;
        background: #fafbfc;
        display: flex; justify-content: space-between; align-items: center;
    }
    .btn-view-sch {
        background: #0d6efd; border-color: #0d6efd; color: #fff;
        font-size: 12px; font-weight: 600; padding: 5px 14px;
        border-radius: 6px; transition: background-color .15s, box-shadow .15s;
    }
    .btn-view-sch:hover { background:#0a58ca; border-color:#0a58ca; color:#fff; box-shadow:0 3px 8px rgba(13,110,253,.25); }

    /* Detail modal */
    #schModal .modal-content { border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    #schModal .modal-header  { background: linear-gradient(135deg,#0d6efd,#0a58ca); color:#fff; border:none; padding: 18px 24px; }
    #schModal .modal-body    { padding: 22px 24px; }
    #schModal .section-title {
        font-weight: 700; color: #0a58ca; font-size: .95rem;
        margin-top: 18px; margin-bottom: 8px;
        padding-bottom: 4px; border-bottom: 1px solid #e9ecef;
    }
    #schModal .wysiwyg-body { font-size: .92rem; color: #212529; line-height: 1.55; }
    #schModal .wysiwyg-body ul, #schModal .wysiwyg-body ol { padding-left: 20px; }
</style>

<div class="container-fluid py-3">

    <div class="sch-hero">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-mortarboard-fill me-2"></i>Scholarships &amp; Opportunities
                </h4>
                <p class="mb-0" style="opacity:.92;">
                    Curated scholarships, fellowships, internships and grants — kept current with deadlines.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <i class="bi bi-globe-americas" style="font-size:3rem;opacity:.35;"></i>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="sch-filters">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Search title, country, description…">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All types</option>
                    @foreach($types as $val => $label)
                        <option value="{{ $val }}" {{ ($filters['type'] ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="country" class="form-select form-select-sm">
                    <option value="">All countries</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ ($filters['country'] ?? '') === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="funding_type" class="form-select form-select-sm">
                    <option value="">All funding</option>
                    @foreach($fundingTypes as $val => $label)
                        <option value="{{ $val }}" {{ ($filters['funding_type'] ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <select name="program_level" class="form-select form-select-sm">
                    <option value="">Level</option>
                    @foreach($programLevels as $val => $label)
                        <option value="{{ $val }}" {{ ($filters['program_level'] ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary btn-sm" type="submit">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
            </div>
        </div>
    </form>

    {{-- CARDS --}}
    <div class="row g-3">
        @forelse($scholarships as $s)
            @php
                $days  = $s->daysToDeadline();
                $deadlineCls = $days === null
                    ? 'chip-deadline-ok'
                    : ($days < 0 ? 'chip-deadline-over'
                                 : ($days <= 14 ? 'chip-deadline-warn' : 'chip-deadline-ok'));
                $fundCls = $s->funding_type === 'fully_funded' ? 'chip-fully' : 'chip-partial';
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="sch-card">
                    <div class="sch-cover">
                        @if($s->flagUrl())
                            <img src="{{ $s->flagUrl(80) }}" class="flag" alt="{{ $s->country_name }}">
                        @else
                            <i class="bi bi-mortarboard-fill" style="font-size:2.2rem;color:#0a58ca;opacity:.45;"></i>
                        @endif
                        @if($s->is_featured)
                            <span class="featured-pin"><i class="bi bi-star-fill"></i> Featured</span>
                        @endif
                    </div>
                    <div class="sch-body">
                        <h6 title="{{ $s->title }}">{{ $s->title }}</h6>
                        @if($s->short_description)
                            <div class="desc">{{ $s->short_description }}</div>
                        @endif
                        <div class="sch-meta">
                            <i class="bi bi-geo-alt-fill"></i>{{ $s->country_name ?: '—' }}
                            @if($s->program_level)
                                · <i class="bi bi-bookmark-fill"></i>{{ \App\Constants\ScholarshipConstants::PROGRAM_LEVELS[$s->program_level] ?? '' }}
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-auto">
                            <span class="chip chip-type">{{ \App\Constants\ScholarshipConstants::TYPES[$s->type] ?? $s->type }}</span>
                            <span class="chip {{ $fundCls }}">
                                <i class="bi bi-cash-coin"></i>
                                {{ \App\Constants\ScholarshipConstants::FUNDING_TYPES[$s->funding_type] ?? '' }}
                            </span>
                            <span class="chip {{ $deadlineCls }}">
                                <i class="bi bi-calendar-event"></i>
                                @if($s->deadline)
                                    @if($days < 0) Closed
                                    @elseif($days === 0) Today
                                    @elseif($days <= 14) {{ $days }} day{{ $days === 1 ? '' : 's' }} left
                                    @else {{ $s->deadline->format('M d, Y') }}
                                    @endif
                                @else
                                    Rolling
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="sch-footer">
                        <small class="text-muted">{{ $s->created_at?->format('M d, Y') }}</small>
                        <button type="button"
                                class="btn btn-view-sch sch-view-btn"
                                data-payload="{{ json_encode([
                                    'title'        => $s->title,
                                    'country_name' => $s->country_name,
                                    'flag'         => $s->flagUrl(160),
                                    'type'         => \App\Constants\ScholarshipConstants::TYPES[$s->type] ?? $s->type,
                                    'funding'      => \App\Constants\ScholarshipConstants::FUNDING_TYPES[$s->funding_type] ?? '',
                                    'funding_key'  => $s->funding_type,
                                    'level'        => $s->program_level ? (\App\Constants\ScholarshipConstants::PROGRAM_LEVELS[$s->program_level] ?? '') : null,
                                    'deadline'     => $s->deadline?->format('M d, Y'),
                                    'days_left'    => $days,
                                    'short'        => $s->short_description,
                                    'eligibility'  => $s->eligibility_criteria,
                                    'benefits'     => $s->benefits,
                                    'documents'    => $s->required_documents,
                                    'link1'        => $s->official_link_1,
                                    'link2'        => $s->official_link_2,
                                ]) }}">
                            <i class="bi bi-eye-fill me-1"></i>View details
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-1"></i>No scholarships match your filters.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $scholarships->links() }}</div>
</div>

{{-- DETAIL MODAL --}}
<div class="modal fade" id="schModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex gap-3 align-items-center">
                    <img src="" id="modalFlag" alt="" style="height:48px;display:none;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,.2);">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="modalTitle">—</h5>
                        <div class="small" id="modalSubtitle" style="opacity:.88;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 mb-3" id="modalChips"></div>
                <div id="modalShort" class="text-muted"></div>

                <div class="section-title">Eligibility Criteria</div>
                <div class="wysiwyg-body" id="modalElig">—</div>

                <div class="section-title">Benefits</div>
                <div class="wysiwyg-body" id="modalBenefits">—</div>

                <div class="section-title">Required Documents</div>
                <div class="wysiwyg-body" id="modalDocs">—</div>

                <div class="section-title">Official Links</div>
                <div id="modalLinks">—</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    const schModal = new bootstrap.Modal(document.getElementById('schModal'));

    $(document).on('click', '.sch-view-btn', function () {
        const p = $(this).data('payload');
        $('#modalTitle').text(p.title || '—');

        // subtitle: country · type · level
        const sub = [p.country_name, p.type, p.level].filter(Boolean).join(' · ');
        $('#modalSubtitle').text(sub);

        if (p.flag) {
            $('#modalFlag').attr('src', p.flag).show();
        } else {
            $('#modalFlag').hide();
        }

        // chips
        const fundCls = p.funding_key === 'fully_funded' ? 'chip-fully' : 'chip-partial';
        let chips = '';
        if (p.funding) {
            chips += `<span class="chip ${fundCls}"><i class="bi bi-cash-coin"></i>${p.funding}</span>`;
        }
        if (p.deadline) {
            let dCls = 'chip-deadline-ok';
            let dText = p.deadline;
            if (p.days_left !== null && p.days_left !== undefined) {
                if (p.days_left < 0) { dCls = 'chip-deadline-over'; dText = 'Closed'; }
                else if (p.days_left === 0) { dCls = 'chip-deadline-warn'; dText = 'Today'; }
                else if (p.days_left <= 14) { dCls = 'chip-deadline-warn'; dText = p.days_left + ' day' + (p.days_left === 1 ? '' : 's') + ' left'; }
            }
            chips += `<span class="chip ${dCls}"><i class="bi bi-calendar-event"></i>${dText}</span>`;
        } else {
            chips += `<span class="chip chip-deadline-ok"><i class="bi bi-calendar-event"></i>Rolling deadline</span>`;
        }
        $('#modalChips').html(chips);

        $('#modalShort').text(p.short || '');
        $('#modalElig').html(p.eligibility || '—');
        $('#modalBenefits').html(p.benefits || '—');
        $('#modalDocs').html(p.documents || '—');

        let links = '';
        if (p.link1) links += `<a href="${p.link1}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary me-2 mb-2"><i class="bi bi-box-arrow-up-right me-1"></i>Official Site 1</a>`;
        if (p.link2) links += `<a href="${p.link2}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary me-2 mb-2"><i class="bi bi-box-arrow-up-right me-1"></i>Official Site 2</a>`;
        $('#modalLinks').html(links || '<span class="text-muted">No official links provided.</span>');

        schModal.show();
    });
});
</script>
@endsection
