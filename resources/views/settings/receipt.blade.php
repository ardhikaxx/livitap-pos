@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.business') }}">Pengaturan</a></li>
<li class="breadcrumb-item active">Struk</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Pengaturan Struk</h5>

        <form method="POST" action="{{ route('settings.receipt.update') }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Header Struk</label>
                    <textarea name="receipt_header" rows="3" class="form-control">{{ old('receipt_header', session('business_id') ? \App\Models\Business::find(session('business_id'))->receipt_header : '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Footer Struk</label>
                    <textarea name="receipt_footer" rows="3" class="form-control">{{ old('receipt_footer', session('business_id') ? \App\Models\Business::find(session('business_id'))->receipt_footer : '') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
