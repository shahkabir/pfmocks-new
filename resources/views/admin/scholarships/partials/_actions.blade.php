@php /** @var \App\Models\Scholarship\Scholarship $s */ @endphp
<div class="d-flex gap-1">
    <a href="{{ route('admin.scholarships.edit', $s->id) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil"></i>
    </a>
    <form method="POST"
          action="{{ route('admin.scholarships.destroy', $s->id) }}"
          class="d-inline"
          onsubmit="return confirm('Delete this scholarship?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
