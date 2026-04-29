@php /** @var object $row */ @endphp
<div class="d-flex gap-1">
    @if(!$row->evaluation_id || $row->evaluation_status !== \App\Models\Evaluation\Evaluation::STATUS_COMPLETED)
        <button type="button"
                class="btn btn-sm btn-primary assign-btn"
                data-attempt-id="{{ $row->attempt_id }}"
                data-student="{{ $row->student_name }}"
                data-module="{{ $row->module_name }}">
            <i class="bi bi-person-plus-fill"></i>
            {{ $row->evaluation_id ? 'Reassign' : 'Assign' }}
        </button>
    @else
        <span class="text-success small fw-semibold">
            <i class="bi bi-check-circle-fill me-1"></i>Done
        </span>
    @endif
</div>
