@php /** @var \App\Models\Payment\Payment $p */ @endphp
<div class="d-flex gap-1 flex-wrap">
    <a href="{{ route('admin.payments.show', $p->id) }}"
       class="btn btn-sm btn-outline-primary">
        <i class="bi bi-eye"></i>
    </a>
    @if($p->status === \App\Models\Payment\Payment::STATUS_PENDING)
        <form method="POST" action="{{ route('admin.payments.approve', $p->id) }}"
              class="d-inline"
              onsubmit="return confirm('Approve this payment and unlock the exam?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-check-lg"></i> Approve
            </button>
        </form>
        <button type="button" class="btn btn-sm btn-danger"
                data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $p->id }}">
            <i class="bi bi-x-lg"></i> Reject
        </button>

        <div class="modal fade" id="rejectModal-{{ $p->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.payments.reject', $p->id) }}"
                      class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject payment #{{ $p->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Reason (required)</label>
                        <textarea name="admin_note" class="form-control" rows="3" required
                                  placeholder="e.g. Transaction ID not found in bKash statement"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
