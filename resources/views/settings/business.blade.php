@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Pengaturan Bisnis</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Pengaturan Bisnis</h5>

        @if(!$currentBusiness)
            <div class="alert alert-warning">
                Anda belum memiliki bisnis. Silakan buat bisnis terlebih dahulu.
            </div>
        @else
        <form method="POST" action="{{ route('settings.business.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Bisnis</label>
                    <input type="text" name="name" value="{{ old('name', $currentBusiness->name ?? '') }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Jenis Usaha</label>
                    <select name="type" class="form-select">
                        <option value="retail" {{ old('type', $currentBusiness->type ?? '') == 'retail' ? 'selected' : '' }}>Retail</option>
                        <option value="fnb" {{ old('type', $currentBusiness->type ?? '') == 'fnb' ? 'selected' : '' }}>F&B</option>
                        <option value="service" {{ old('type', $currentBusiness->type ?? '') == 'service' ? 'selected' : '' }}>Jasa</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $currentBusiness->address ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $currentBusiness->phone ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $currentBusiness->email ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $currentBusiness->npwp ?? '') }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Logo Bisnis</label>
                    @if($currentBusiness->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $currentBusiness->logo) }}" class="img-thumbnail" style="width:128px;height:128px;object-fit:cover;">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
