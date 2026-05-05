@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.business') }}">Pengaturan</a></li>
<li class="breadcrumb-item active">Struk</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Pengaturan Struk</h5>

        <form method="POST" action="{{ route('settings.receipt.update', $outlet) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Header Struk</label>
                    <textarea name="receipt_settings[header]" rows="3" class="form-control">{{ old('receipt_settings.header', $outlet->receipt_settings['header'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Footer Struk</label>
                    <textarea name="receipt_settings[footer]" rows="3" class="form-control">{{ old('receipt_settings.footer', $outlet->receipt_settings['footer'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Ukuran Kertas</label>
                    <select name="receipt_settings[paper_size]" class="form-select">
                        <option value="58mm" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == '58mm') ? 'selected' : '' }}>58mm</option>
                        <option value="80mm" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == '80mm') ? 'selected' : '' }}>80mm</option>
                        <option value="A4" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == 'A4') ? 'selected' : '' }}>A4</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium d-block">Opsi Tampilan</label>
                    <div class="form-check">
                        <input type="checkbox" name="receipt_settings[show_logo]" id="show_logo"
                            {{ old('receipt_settings.show_logo', $outlet->receipt_settings['show_logo'] ?? true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="show_logo">Tampilkan Logo</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="receipt_settings[show_tax]" id="show_tax"
                            {{ old('receipt_settings.show_tax', $outlet->receipt_settings['show_tax'] ?? true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="show_tax">Tampilkan Pajak</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="receipt_settings[show_payment_method]" id="show_payment_method"
                            {{ old('receipt_settings.show_payment_method', $outlet->receipt_settings['show_payment_method'] ?? true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="show_payment_method">Tampilkan Metode Pembayaran</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
