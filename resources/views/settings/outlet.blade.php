@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Pengaturan Outlet</h1>

    <form method="POST" action="{{ route('settings.outlet.update', $currentOutlet) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Nama Outlet</label>
                <input type="text" name="name" value="{{ old('name', $currentOutlet->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-medium mb-2">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $currentOutlet->phone) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Alamat</label>
                <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address', $currentOutlet->address) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Logo Outlet</label>
                @if($currentOutlet->logo)
                    <img src="{{ asset('storage/' . $currentOutlet->logo) }}" class="w-32 h-32 object-cover mb-2 rounded">
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
