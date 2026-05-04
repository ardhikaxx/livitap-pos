@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Manajemen Pelanggan</h1>

    <div class="mb-4">
        <a href="{{ route('customers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Tambah Pelanggan
        </a>
    </div>

    <form method="GET" class="mb-6">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari nama/telepon/email..." 
            class="border rounded px-4 py-2 w-96">
        <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 ml-2">Cari</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Telepon</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-center">Tier</th>
                    <th class="px-4 py-2 text-right">Poin</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $customer->name }}</td>
                    <td class="px-4 py-3">{{ $customer->phone }}</td>
                    <td class="px-4 py-3">{{ $customer->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs 
                            {{ $customer->tier == 'platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $customer->tier == 'gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $customer->tier == 'silver' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $customer->tier == 'regular' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ ucfirst($customer->tier) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">{{ $customer->points }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-1">
                        <a href="{{ route('customers.show', $customer) }}" class="text-blue-500 hover:underline">Detail</a>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-green-500 hover:underline">Edit</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Hapus pelanggan?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Belum ada pelanggan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $customers->withQueryString()->links() }}
    </div>
</div>
@endsection
