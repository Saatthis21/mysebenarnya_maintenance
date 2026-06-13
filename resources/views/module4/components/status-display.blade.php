@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


<div class="mt-3">
    @forelse($progress as $entry)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <small class="text-muted">{{ \Carbon\Carbon::parse($entry->progress_Updated_At)->format('d M Y, h:i A') }}</small>
                <h5 class="card-title text-primary mt-1">{{ ucfirst(str_replace('_', ' ', $entry->progress_Status)) }}</h5>
                <p class="card-text mb-0">{{ $entry->progress_Remarks }}</p>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center p-4" style="border: 2px dashed #dee2e6;" role="alert">
            <h6 class="alert-heading fw-bold mb-1">No Updates Yet</h6>
            <p class="mb-0 text-muted small">The assigned agency has not recorded any progress for this inquiry.</p>
        </div>
    @endforelse
</div>
