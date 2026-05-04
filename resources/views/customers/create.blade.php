@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Tambah Pelanggan Baru</h1>

    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Nama Lengkat *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Telepon *</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror" required>
                @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">
                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Tier</label>
                <select name="tier" class="w-full border rounded px-3 py-2">
                    <option value="regular" {{ old('tier') == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="silver" {{ old('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ old('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ old('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">Jenis Kelamin</label>
                <select name="gender" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">Tanggal Lahir</label>
                <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Alamat</label>
                <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block font-medium mb-2">Limit Kredit (Rp)</label>
                <input type="number" name="credit_limit" value="{{ old('credit_limit', 0) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Catatan</label>
                <input type="text" name="notes" value="{{ old('notes') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="rounded">
                <span class="ml-2">Aktif</span>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('customers.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Simpan Pelanggan
            </button>
        </div>
    </form>
</div>
@endsection
