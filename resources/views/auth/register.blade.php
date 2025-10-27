@extends('auth.layout')

@section('header', __('Register'))

@section('content')

    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-user-plus me-2"></i>{{ __('Create an Account') }}
        </h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">
                    <i class="fas fa-user text-muted me-2"></i>{{ __('Name') }}
                </label>
                <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                    placeholder="Entrez votre nom complet">

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="fas fa-envelope text-muted me-2"></i>{{ __('Email Address') }}
                </label>
                <input id="email" type="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" placeholder="exemple@email.com">

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
                    class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required
                    autocomplete="new-password" placeholder="********">

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <label for="password-confirm" class="form-label fw-semibold">
                    <i class="fas fa-lock text-muted me-2"></i>{{ __('Confirm Password') }}
                </label>
                <input id="password-confirm" type="password" class="form-control form-control-lg"
                    name="password_confirmation" required autocomplete="new-password"
                    placeholder="Confirmez votre mot de passe">
            </div>

            {{-- Submit --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                    <i class="fas fa-check-circle me-2"></i>{{ __('Register') }}
                </button>
            </div>

            {{-- Login link --}}
            <div class="text-center mt-3">
                <small class="text-muted">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                        {{ __('Login here') }}
                    </a>
                </small>
            </div>
        </form>
    </div>

@endsection
