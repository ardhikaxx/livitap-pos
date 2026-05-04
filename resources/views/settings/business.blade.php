@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Pengaturan Bisnis</h1>

    <form method="POST" action="{{ route('settings.business.update', $currentBusiness) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Nama Bisnis</label>
                <input type="text" name="name" value="{{ old('name', $currentBusiness->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-medium mb-2">Jenis Usaha</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="retail" {{ old('type', $currentBusiness->type) == 'retail' ? 'selected' : '' }}>Retail</option>
                    <option value="fnb" {{ old('type', $currentBusiness->type) == 'fnb' ? 'selected' : '' }}>F&B</option>
                    <option value="service" {{ old('type', $currentBusiness->type) == 'service' ? 'selected' : '' }}>Jasa</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">Alamat</label>
                <input type="text" name="address" value="{{ old('address', $currentBusiness->address) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $currentBusiness->phone) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $currentBusiness->email) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">NPWP</label>
                <input type="text" name="npwp" value="{{ old('npwp', $currentBusiness->npwp) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Logo Bisnis</label>
                @if($currentBusiness->logo)
                    <img src="{{ asset('storage/' . $currentBusiness->logo) }}" class="w-32 h-32 object-cover mb-2 rounded">
                @endif
                <input type="file" name="logo" accept="image/*" class="w-full border rounded px-3 py-2">
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
