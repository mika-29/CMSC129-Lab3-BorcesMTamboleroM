@extends('layouts.app')

@section('title', 'Deleted Supplies')

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">
                    <i class="fas fa-trash"></i> Deleted Supplies
                </h4>
            </div>

            <div class="card-body">

                @if($items->count() > 0)

                    <!-- INFO ALERT -->
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        These supplies have been removed. You may restore or permanently delete them.
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($items as $item)
                                <tr>

                                    <!-- NAME -->
                                    <td>{{ $item->name }}</td>

                                    <!-- CATEGORY -->
                                    <td>{{ $item->category ?? 'N/A' }}</td>

                                    <!-- QUANTITY -->
                                    <td>{{ $item->quantity }}</td>

                                    <!-- DELETED DATE -->
                                    <td>
                                        {{ $item->deleted_at->format('M d, Y h:i A') }}
                                    </td>

                                    <!-- ACTIONS -->
                                    <td>
                                        <!-- RESTORE -->
                                        <form action="{{ route('inventory.restore', $item->id) }}"
                                              method="POST"
                                              style="display:inline;">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </form>

                                        <!-- DELETE FOREVER -->
                                        <form action="{{ route('inventory.forceDelete', $item->id) }}"
                                            method="POST"
                                            style="display:inline;"
                                            class="force-delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-sm btn-danger open-delete-modal">
                                                <i class="fas fa-times"></i> Delete Forever
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="d-flex justify-content-center">
                        {{ $items->links() }}
                    </div>

                @else

                <p class="text-muted fst-italic">Trash is empty. No deleted supplies found.</p>

                @endif

                <!-- BACK BUTTON -->
                <div class="mt-3">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-triangle-exclamation text-danger"></i>
                    Confirm Permanent Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Permanently delete this supply? This cannot be undone.
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    Delete Forever
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedForm = null;

    const deleteModalEl = document.getElementById('confirmDeleteModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    document.querySelectorAll('.open-delete-modal').forEach(button => {
        button.addEventListener('click', function () {
            selectedForm = this.closest('.force-delete-form');
            deleteModal.show();
        });
    });

    confirmDeleteBtn.addEventListener('click', function () {
        if (selectedForm) {
            selectedForm.submit();
        }
    });
});
</script>
@endsection
