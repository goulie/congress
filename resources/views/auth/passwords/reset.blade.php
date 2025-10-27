@extends('auth.layout')

@section('header', __('Reset Password'))

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-key me-2"></i>{{ __('Set a New Password') }}
        </h3>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        {{-- Token --}}
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-muted me-2"></i>{{ __('Email Address') }}
                            </label>
                            <input id="email" type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                                value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
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
                                class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"
                                required autocomplete="new-password" placeholder="********">

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label fw-semibold">
                                <i class="fas fa-lock text-muted me-2"></i>{{ __('Confirm Password') }}
                            </label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg"
                                name="password_confirmation" required autocomplete="new-password" placeholder="********">
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="fas fa-check-circle me-2"></i>{{ __('Reset Password') }}
                            </button>
                        </div>

                        {{-- Back to login --}}
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Login') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
