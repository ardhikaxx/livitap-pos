@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Detail Pelanggan</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold mb-4">Informasi Dasar</h3>
            <table class="w-full">
                <tr><td class="py-2 font-medium">Nama:</td><td>{{ $customer->name }}</td></tr>
                <tr><td class="py-2 font-medium">Telepon:</td><td>{{ $customer->phone }}</td></tr>
                <tr><td class="py-2 font-medium">Email:</td><td>{{ $customer->email ?? '-' }}</td></tr>
                <tr><td class="py-2 font-medium">Alamat:</td><td>{{ $customer->address ?? '-' }}</td></tr>
                <tr><td class="py-2 font-medium">Tier:</td><td>{{ ucfirst($customer->tier) }}</td></tr>
                <tr><td class="py-2 font-medium">Poin:</td><td>{{ $customer->points }}</td></tr>
                <tr><td class="py-2 font-medium">Limit Kredit:</td><td>Rp {{ number_format($customer->credit_limit ?? 0, 0, ',', '.') }}</td></tr>
            </table>
        </div>

        <div>
            <h3 class="font-semibold mb-4">Riwayat Transaksi Terbaru</h3>
            <div class="space-y-2">
                @forelse($customer->sales as $sale)
                    <div class="border p-3 rounded">
                        <div class="flex justify-between">
                            <span>{{ $sale->invoice_number }}</span>
                            <span class="font-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-sm text-gray-500">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-8 flex space-x-4">
        <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Edit Pelanggan
        </a>
        <a href="{{ route('customers.points', $customer) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Riwayat Poin
        </a>
        <a href="{{ route('customers.transactions', $customer) }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
            Semua Transaksi
        </a>
    </div>
</div>
@endsection
