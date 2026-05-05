@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Pelanggan</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Edit Pelanggan</h5>

        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telepon *</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tier</label>
                    <select name="tier" class="form-select">
                        <option value="regular" {{ old('tier', $customer->tier) == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="silver" {{ old('tier', $customer->tier) == 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ old('tier', $customer->tier) == 'gold' ? 'selected' : '' }}>Gold</option>
                        <option value="platinum" {{ old('tier', $customer->tier) == 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Alamat</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Limit Kredit (Rp)</label>
                    <input type="number" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}" class="form-control">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_active">Pelanggan Aktif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Pelanggan</button>
            </div>
        </form>
    </div>
</div>
@endsection
