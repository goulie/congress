@extends('auth.layout')

@section('header', __('Login'))

@section('content')

    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login to Your Account') }}
        </h3>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="fas fa-envelope text-muted me-2"></i>{{ __('Email Address') }}
                </label>
                <input id="email" type="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email"
                    placeholder="exemple@email.com">

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="fas fa-lock text-muted me-2"></i>{{ __('Password') }}
                </label>
                <input id="password" type="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password"
                    placeholder="********">

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>

            {{-- Submit --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login') }}
                </button>
            </div>

            {{-- Forgot password & Register links --}}
            <div class="text-center mt-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold text-primary me-2">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>

            <div class="text-center mt-2">
                <small class="text-muted">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold text-primary">
                        {{ __('Register here') }}
                    </a>
                </small>
            </div>
        </form>
    </div>

@endsection
