@extends('layouts.app')
@section('title', 'Add Questions to PTE Module')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:960px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Questions to a PTE Module</h5>
            <a href="{{ route('admin.pte.module-questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form id="addQuestionsForm" method="POST" action="{{ route('admin.pte.module-questions.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">PTE Module <span class="text-danger">*</span></label>
                    <select name="pte_module_id" id="pte_module_id" class="form-select" required>
                        <option value="">— Select PTE Module —</option>
                        @foreach($pteModules as $pm)
                            <option value="{{ $pm->id }}"
                                data-section-name="{{ $pm->section?->name }}"
                                data-section-tag="{{ $pm->section?->tag }}"
                                {{ old('pte_module_id') == $pm->id ? 'selected' : '' }}>
                                #{{ $pm->id }} — {{ $pm->module?->name }} → {{ $pm->section?->name }} ({{ $pm->section?->tag }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Question list below auto-filters to the chosen module's section.</div>
                </div>

                {{-- Toolbar + question list --}}
                <div id="questionPanel" class="border rounded p-3 mb-3 bg-light" style="min-height:160px;">
                    <div id="qPanelEmpty" class="text-muted text-center py-5">
                        <i class="bi bi-arrow-up-circle me-1"></i>
                        Pick a PTE module above to see questions available in that section.
                    </div>

                    <div id="qPanelContent" class="d-none">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="fw-semibold me-2">
                                Section: <span id="sectionLabel" class="badge bg-primary"></span>
                            </span>
                            <span class="badge bg-secondary">Total: <span id="totalCount">0</span></span>
                            <span class="badge bg-success">Selected: <span id="selectedCount">0</span></span>
                            <span class="badge bg-warning text-dark">Already attached: <span id="attachedCount">0</span></span>

                            <div class="ms-auto d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width:auto;">
                                    <span class="input-group-text">Pick random</span>
                                    <input type="number" id="randomN" class="form-control" value="5" min="1" style="width:5rem;">
                                </div>
                                <button type="button" id="btnRandom" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-shuffle me-1"></i>Random
                                </button>
                                <button type="button" id="btnDeselect" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-square me-1"></i>Deselect all
                                </button>
                            </div>
                        </div>

                        <div id="qPanelGroups"></div>
                    </div>

                    <div id="qPanelLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2 small text-muted">Loading questions…</div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" id="submitBtn" class="btn btn-success" disabled>
                        <i class="bi bi-plus-lg me-1"></i> Add selected
                    </button>
                    <a href="{{ route('admin.pte.module-questions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const $module      = $('#pte_module_id');
    const $panelEmpty  = $('#qPanelEmpty');
    const $panelLoad   = $('#qPanelLoading');
    const $panelOK     = $('#qPanelContent');
    const $groups      = $('#qPanelGroups');
    const $sectionLbl  = $('#sectionLabel');
    const $total       = $('#totalCount');
    const $selected    = $('#selectedCount');
    const $attached    = $('#attachedCount');
    const $submitBtn   = $('#submitBtn');
    const fetchUrlTmpl = '{{ route("admin.ajax.pte-questions-by-pte-module", ":id") }}';

    function setMode(mode) {
        $panelEmpty.toggleClass('d-none', mode !== 'empty');
        $panelLoad .toggleClass('d-none', mode !== 'loading');
        $panelOK   .toggleClass('d-none', mode !== 'ready');
    }

    function updateCounters() {
        const totalCheckable = $groups.find('input.q-check:not(:disabled)').length;
        const selectedCount  = $groups.find('input.q-check:checked').length;
        $selected.text(selectedCount);
        $submitBtn.prop('disabled', selectedCount === 0);
        return { totalCheckable, selectedCount };
    }

    function renderGroups(payload) {
        $groups.empty();
        const existing = new Set(payload.existing_question_ids || []);
        let total = 0;

        (payload.groups || []).forEach(g => {
            const items = g.questions || [];
            total += items.length;
            const $card = $(`
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info text-dark me-2">${g.sub_type_tag}</span>
                        <strong>${g.sub_type_name}</strong>
                        <span class="ms-2 text-muted small">(${items.length})</span>
                    </div>
                    <div class="list-group"></div>
                </div>
            `);
            const $list = $card.find('.list-group');
            items.forEach(q => {
                const isAttached = existing.has(q.id);
                $list.append(`
                    <label class="list-group-item d-flex align-items-start gap-2 ${isAttached ? 'text-muted' : ''}">
                        <input type="checkbox"
                               class="form-check-input mt-1 q-check"
                               name="pte_question_granular_ids[]"
                               value="${q.id}"
                               ${isAttached ? 'disabled' : ''}>
                        <div>
                            <div><code>${q.question_granular_id}</code>
                                ${isAttached ? '<span class="badge bg-warning text-dark ms-1">already attached</span>' : ''}
                            </div>
                            <div class="small text-muted">${q.preview || '—'}</div>
                        </div>
                    </label>
                `);
            });
            $groups.append($card);
        });

        $total.text(total);
        $attached.text(payload.existing_question_ids.length);
        $sectionLbl.text((payload.pte_module.section_tag || '') + ' · ' + (payload.pte_module.section_name || ''));
        updateCounters();
    }

    function loadFor(pteModuleId) {
        if (!pteModuleId) { setMode('empty'); $submitBtn.prop('disabled', true); return; }
        setMode('loading');
        $.getJSON(fetchUrlTmpl.replace(':id', pteModuleId))
            .done(payload => { renderGroups(payload); setMode('ready'); })
            .fail(()      => { alert('Failed to load questions for this PTE module.'); setMode('empty'); });
    }

    $module.on('change', () => loadFor($module.val()));

    $groups.on('change', 'input.q-check', updateCounters);

    $('#btnDeselect').on('click', () => {
        $groups.find('input.q-check:checked').prop('checked', false);
        updateCounters();
    });

    $('#btnRandom').on('click', () => {
        const n = Math.max(1, parseInt($('#randomN').val(), 10) || 0);
        const available = $groups.find('input.q-check:not(:disabled)').toArray();
        // shuffle (Fisher–Yates)
        for (let i = available.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [available[i], available[j]] = [available[j], available[i]];
        }
        $groups.find('input.q-check').prop('checked', false);
        available.slice(0, n).forEach(el => { el.checked = true; });
        updateCounters();
    });

    // Auto-load on page open if a module is preselected
    if ($module.val()) loadFor($module.val());
})();
</script>
@endsection
