@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.business') }}">Pengaturan</a></li>
<li class="breadcrumb-item active">Outlet</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Pengaturan Outlet</h5>

        <form method="POST" action="{{ route('settings.outlet.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Outlet</label>
                    <input type="text" name="name" value="{{ old('name', $currentOutlet->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $currentOutlet->phone) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Alamat</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $currentOutlet->address) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Logo Outlet</label>
                    @if($currentOutlet->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $currentOutlet->logo) }}" class="img-thumbnail" style="width:128px;height:128px;object-fit:cover;">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
