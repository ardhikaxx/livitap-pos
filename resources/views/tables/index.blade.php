@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Manajemen Meja (F&B)</h1>

    <div class="mb-6 flex flex-wrap gap-4">
        <a href="{{ route('tables.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Tambah Meja
        </a>
        <button onclick="printLayout()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Cetak Layout
        </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($tables as $table)
        <div class="border-2 p-4 rounded-lg text-center 
            {{ $table->status == 'occupied' ? 'border-red-500 bg-red-50' : '' }}
            {{ $table->status == 'empty' ? 'border-green-500 bg-green-50' : '' }}
            {{ $table->status == 'reserved' ? 'border-yellow-500 bg-yellow-50' : '' }}
            {{ $table->status == 'requesting_bill' ? 'border-orange-500 bg-orange-50' : '' }}">
            <h3 class="font-bold text-lg">{{ $table->name }}</h3>
            <p class="text-sm">Kapasitas: {{ $table->capacity }}</p>
            <p class="text-sm">{{ $table->area ?? 'Umum' }}</p>
            <span class="inline-block mt-2 px-2 py-1 text-xs rounded
                {{ $table->status == 'occupied' ? 'bg-red-200 text-red-800' : '' }}
                {{ $table->status == 'empty' ? 'bg-green-200 text-green-800' : '' }}
                {{ $table->status == 'reserved' ? 'bg-yellow-200 text-yellow-800' : '' }}
                {{ $table->status == 'requesting_bill' ? 'bg-orange-200 text-orange-800' : '' }}">
                {{ $table->status }}
            </span>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-8">
            Belum ada meja. Buat meja di Pengaturan Outlet.
        </div>
        @endforelse
    </div>
</div>

<script>
function printLayout() {
    window.print();
}
</script>
@endsection
