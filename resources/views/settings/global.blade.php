@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Pengaturan Global</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Global</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.global.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_name" class="form-label">Nama Toko</label>
                                    <input type="text" class="form-control" id="store_name" name="store_name" 
                                           value="{{ $settings['store_name'] ?? '' }}" maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="store_phone" name="store_phone" 
                                           value="{{ $settings['store_phone'] ?? '' }}" maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="store_address" class="form-label">Alamat Toko</label>
                            <textarea class="form-control" id="store_address" name="store_address" 
                                      rows="3" maxlength="500">{{ $settings['store_address'] ?? '' }}</textarea>
                        </div>

                        <hr>

                        <h6 class="mb-3">Pengaturan Struk</h6>

                        <div class="mb-3">
                            <label for="receipt_header" class="form-label">Header Struk</label>
                            <textarea class="form-control" id="receipt_header" name="receipt_header" 
                                      rows="2" placeholder="Teks yang muncul di bagian atas struk">{{ $settings['receipt_header'] ?? '' }}</textarea>
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="receipt_footer" class="form-label">Footer Struk</label>
                            <textarea class="form-control" id="receipt_footer" name="receipt_footer" 
                                      rows="2" placeholder="Teks yang muncul di bagian bawah struk">{{ $settings['receipt_footer'] ?? '' }}</textarea>
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
    });
</script>
@endpush
