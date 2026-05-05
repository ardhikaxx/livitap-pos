@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Meja</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Manajemen Meja (F&B)</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('settings.outlet') }}" class="btn btn-primary btn-sm">+ Tambah Meja</a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">Cetak Layout</button>
            </div>
        </div>

        <div class="row g-3">
            @forelse($tables as $table)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card text-center border-2
                    {{ $table->status == 'occupied' ? 'border-danger' : '' }}
                    {{ $table->status == 'empty' ? 'border-success' : '' }}
                    {{ $table->status == 'reserved' ? 'border-warning' : '' }}
                    {{ $table->status == 'requesting_bill' ? 'border-warning' : '' }}">
                    <div class="card-body py-3">
                        <h6 class="fw-bold mb-1">{{ $table->name }}</h6>
                        <p class="small text-muted mb-1">Kapasitas: {{ $table->capacity }}</p>
                        <p class="small text-muted mb-2">{{ $table->area ?? 'Umum' }}</p>
                        <span class="badge
                            {{ $table->status == 'occupied' ? 'bg-danger' : '' }}
                            {{ $table->status == 'empty' ? 'bg-success' : '' }}
                            {{ $table->status == 'reserved' ? 'bg-warning text-dark' : '' }}
                            {{ $table->status == 'requesting_bill' ? 'bg-warning text-dark' : '' }}">
                            {{ $table->status }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">
                Belum ada meja. Buat meja di Pengaturan Outlet.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
