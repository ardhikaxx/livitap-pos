@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Pengaturan Struk</h1>

    <form method="POST" action="{{ route('settings.receipt.update', $outlet) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Header Struk</label>
                <textarea name="receipt_settings[header]" rows="3" class="w-full border rounded px-3 py-2">{{ old('receipt_settings.header', $outlet->receipt_settings['header'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block font-medium mb-2">Footer Struk</label>
                <textarea name="receipt_settings[footer]" rows="3" class="w-full border rounded px-3 py-2">{{ old('receipt_settings.footer', $outlet->receipt_settings['footer'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block font-medium mb-2">Ukuran Kertas</label>
                <select name="receipt_settings[paper_size]" class="w-full border rounded px-3 py-2">
                    <option value="58mm" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == '58mm') ? 'selected' : '' }}>58mm</option>
                    <option value="80mm" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == '80mm') ? 'selected' : '' }}>80mm</option>
                    <option value="A4" {{ (old('receipt_settings.paper_size', $outlet->receipt_settings['paper_size'] ?? '') == 'A4') ? 'selected' : '' }}>A4</option>
                </select>
            </div>

            <div class="space-y-2 pt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="receipt_settings[show_logo]" 
                        {{ old('receipt_settings.show_logo', $outlet->receipt_settings['show_logo'] ?? true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Tampilkan Logo</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="receipt_settings[show_tax]" 
                        {{ old('receipt_settings.show_tax', $outlet->receipt_settings['show_tax'] ?? true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Tampilkan Pajak</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="receipt_settings[show_payment_method]" 
                        {{ old('receipt_settings.show_payment_method', $outlet->receipt_settings['show_payment_method'] ?? true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Tampilkan Metode Pembayaran</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
