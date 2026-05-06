@extends('layouts.guest')

@section('content')
<div class="card shadow-sm border-0 rounded-4" style="width: 100%; max-width: 400px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="fas fa-cash-register text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1">LIVITAP POS</h2>
            <p class="text-secondary">Silakan masuk ke akun Anda</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">EMAIL ADDRESS</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-primary-subtle text-primary"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 border-primary-subtle ps-0 py-2 @error('email') is-invalid @enderror" placeholder="name@company.com" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-primary-subtle text-primary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 border-primary-subtle ps-0 py-2 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2 shadow-sm fw-bold">
                    LOGIN
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="#" class="text-decoration-none text-muted small">Lupa kata sandi?</a>
        </div>
    </div>
</div>
@endsection
