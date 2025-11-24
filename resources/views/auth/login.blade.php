@extends('auth.layout')

@section('header', app()->getLocale() == 'fr' ? 'Congres AAEA' : 'AfWASA Congress')

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-sign-in-alt me-2"></i>{{ __('auth.login.header') }}
        </h3>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="fas fa-envelope text-muted me-2"></i>{{ __('auth.fields.email') }}
                </label>
                <input id="email" type="email" class="form-control form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email"
                    placeholder="{{ __('auth.placeholders.email') }}">

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="fas fa-lock text-muted me-2"></i>{{ __('auth.fields.password') }}
                </label>
                <input id="password" type="password"
                    class="form-control form-control @error('password') is-invalid @enderror" name="password" required
                    autocomplete="current-password" placeholder="{{ __('auth.placeholders.password') }}">

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="remember">
                    {{ __('auth.fields.remember_me') }}
                </label>
            </div>

            {{-- Recaptcha --}}
            <div class="mb-3 form-check">
                <div class="form-group">
                    <strong>{{ __('auth.recaptcha') }}:</strong>
                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>

                    @if ($errors->has('g-recaptcha-response'))
                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                    @endif
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('auth.login.button') }}
                </button>
            </div>

            {{-- Forgot password & Register links --}}
            <div class="text-center mt-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold text-primary me-2">
                        {{ __('auth.forgot_password') }}
                    </a>
                @endif
            </div>

            <div class="text-center mt-2">
                <small class="text-muted">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold text-primary">
                        {{ __('auth.register_here') }}
                    </a>
                </small>
            </div>

            
                <a href="{{ route('auth.login.google') }}" type="submit" class="btn btn-outline-dark rounded-pill">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/2048px-Google_%22G%22_logo.svg.png"
                        width="20px"> {{ __('auth.login.google') }}
                </a>
            
        </form>
    </div>
@endsection
