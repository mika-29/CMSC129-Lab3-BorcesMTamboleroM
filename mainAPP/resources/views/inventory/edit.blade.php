@extends('layouts.app')

@section('title', 'Edit Supply')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header text-white" style="background-color: #e8620a;">
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Edit Emergency Supply
                </h4>
            </div>

            <div class="card-body">

                <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- NAME -->
                    <div class="mb-3">
                        <label class="form-label">Supply Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $inventory->name) }}"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- QUANTITY + MIN STOCK -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="quantity"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $inventory->quantity) }}"
                                   min="0"
                                   required>
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Stock <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="minimum_stock"
                                   class="form-control @error('minimum_stock') is-invalid @enderror"
                                   value="{{ old('minimum_stock', $inventory->minimum_stock) }}"
                                   min="0"
                                   required>
                            @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- EXPIRATION DATE -->
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="date"
                               name="expiration_date"
                               class="form-control @error('expiration_date') is-invalid @enderror"
                               value="{{ old('expiration_date', $inventory->expiration_date) }}">
                        @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Dynamic Status Preview -->
                        @if($inventory->expiry_status == 'expired')
                            <small class="text-danger">⚠️ This item is already expired</small>
                        @elseif($inventory->expiry_status == 'warning')
                            <small class="text-warning">⚠️ This item is near expiry</small>
                        @endif
                    </div>

                    <!-- CATEGORY -->
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="Food" {{ old('category', $inventory->category) == 'Food' ? 'selected' : '' }}>Food</option>
                            <option value="Medicine" {{ old('category', $inventory->category) == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                            <option value="Equipment" {{ old('category', $inventory->category) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                        </select>
                    </div>

                    <!-- ALERT IF LOW STOCK -->
                    @if($inventory->is_low_stock)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            This item is currently LOW STOCK!
                        </div>
                    @endif

                    <!-- ACTION BUTTONS -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>

                        <button type="submit" class="btn btn-orange">
                            <i class="fas fa-save"></i> Update Supply
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
