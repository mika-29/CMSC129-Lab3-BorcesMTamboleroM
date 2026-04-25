@extends('layouts.app')

@section('title', 'Add Supply')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Add Emergency Supply
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <!-- NAME -->
                    <div class="mb-3">
                        <label class="form-label">Supply Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
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
                                   value="{{ old('quantity') }}"
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
                                   value="{{ old('minimum_stock', 5) }}"
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
                               value="{{ old('expiration_date') }}">
                        @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty if item does not expire (e.g., tools)</small>
                    </div>

                    <!-- CATEGORY (OPTIONAL BUT NICE) -->
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="Food">Food</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Supply
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
