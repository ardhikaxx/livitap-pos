@extends('layouts.guest')

@section('content')
<div class="card shadow-sm border-0" style="width: 400px;">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">🏪 LIVITAP POS</h4>
            <p class="text-muted small">Masuk ke sistem kasir</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Masuk ke Sistem</button>
            </div>
        </form>

         <div class="mt-3 text-center text-muted small">
            Demo: admin@livitap.com / password123
         </div>
    </div>
</div>
@endsection
