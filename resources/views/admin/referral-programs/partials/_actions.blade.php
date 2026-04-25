@php /** @var \App\Models\Referral\ReferralProgram $p */ @endphp
<div class="d-flex gap-1">
    <a href="{{ route('admin.referral-programs.edit', $p->id) }}"
       class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil"></i>
    </a>
    <form method="POST"
          action="{{ route('admin.referral-programs.destroy', $p->id) }}"
          class="d-inline"
          onsubmit="return confirm('Delete this referral program? Active invitations using it will be orphaned.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
