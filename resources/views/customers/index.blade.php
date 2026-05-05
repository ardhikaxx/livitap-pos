@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Pelanggan</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Manajemen Pelanggan</h5>
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">+ Tambah Pelanggan</a>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/telepon/email..." class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th class="text-center">Tier</th>
                        <th class="text-end">Poin</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge
                                {{ $customer->tier == 'platinum' ? 'bg-purple' : '' }}
                                {{ $customer->tier == 'gold' ? 'bg-warning text-dark' : '' }}
                                {{ $customer->tier == 'silver' ? 'bg-secondary' : '' }}
                                {{ $customer->tier == 'regular' ? 'bg-primary' : '' }}">
                                {{ ucfirst($customer->tier) }}
                            </span>
                        </td>
                        <td class="text-end">{{ $customer->points }}</td>
                        <td class="text-center">
                            <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info btn-sm">Detail</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus pelanggan?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada pelanggan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $customers->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
