@extends('layouts.app')

@section('title', 'Supply Details')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">

    <div class="card">
      <div class="card-header bg-primary-blue text-white">
        <h4 class="mb-0">
          <i class="fas fa-box"></i> Supply Details
        </h4>
      </div>

      <div class="card-body">

        <!-- ALERTS -->
        @if($item->is_low_stock)
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            This item is LOW STOCK!
          </div>
        @endif

        @if($item->expiry_status == 'expired')
          <div class="alert alert-danger">
            <i class="fas fa-skull-crossbones"></i>
            This item is EXPIRED!
          </div>
        @elseif($item->expiry_status == 'warning')
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i>
            This item is NEAR EXPIRY!
          </div>
        @endif

        <!-- DETAILS TABLE -->
        <table class="table table-bordered">
          <tr>
            <th width="35%">Supply Name</th>
            <td>{{ $item->name }}</td>
          </tr>

          <tr>
            <th>Category</th>
            <td>
              {{ $item->category ?? 'N/A' }}
            </td>
          </tr>

          <tr>
            <th>Quantity</th>
            <td class="fw-bold">
              {{ $item->quantity }}
            </td>
          </tr>

          <tr>
            <th>Minimum Stock</th>
            <td>{{ $item->minimum_stock }}</td>
          </tr>

          <tr>
            <th>Stock Status</th>
            <td>
              @if($item->is_low_stock)
                <span class="badge bg-danger">Low Stock</span>
              @else
                <span class="badge bg-success">Sufficient</span>
              @endif
            </td>
          </tr>

          <tr>
            <th>Expiration Date</th>
            <td>
              @if($item->expiration_date)
                {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
              @else
                <span class="text-muted">No Expiration</span>
              @endif
            </td>
          </tr>

          <tr>
            <th>Expiry Status</th>
            <td>
              @if($item->expiry_status == 'expired')
                <span class="badge bg-danger">Expired</span>
              @elseif($item->expiry_status == 'warning')
                <span class="badge bg-warning text-dark">Near Expiry</span>
              @else
                <span class="badge bg-success">Safe</span>
              @endif
            </td>
          </tr>

          <tr>
            <th>Created At</th>
            <td>{{ $item->created_at->format('M d, Y h:i A') }}</td>
          </tr>

          <tr>
            <th>Updated At</th>
            <td>{{ $item->updated_at->format('M d, Y h:i A') }}</td>
          </tr>
        </table>

        <!-- DESCRIPTION -->
        @if($item->description)
        <div class="mt-3">
          <h5>Description:</h5>
          <p>{{ $item->description }}</p>
        </div>
        @endif

        <!-- ACTIONS -->
        <div class="d-flex justify-content-between mt-4">
          <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
          </a>

          <div>
            <a href="{{ route('inventory.edit', $item) }}" class="btn btn-warning">
              <i class="fas fa-edit"></i> Edit
            </a>

            <form action="{{ route('inventory.destroy', $item) }}"
                  method="POST"
                  style="display:inline;"
                  onsubmit="return confirm('Delete this supply?');">
              @csrf
              @method('DELETE')

              <button class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
