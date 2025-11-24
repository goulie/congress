@extends('auth.layout')

@section('header', __('auth.register'))

@section('content')

    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-user-plus me-2"></i>{{ __('auth.create_account') }}
        </h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Nom --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">
                    <i class="fas fa-user text-muted me-2"></i>{{ __('auth.name') }}
                </label>
                <input id="name" type="text" class="form-control form-control @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                    placeholder="{{ __('auth.placeholders.name') }}">

                @error('name')
                    <div class="invalid-feedback">
                        @if ($message === 'The name field is required.')
                            {{ __('auth.validation.name_required') }}
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="fas fa-envelope text-muted me-2"></i>{{ __('auth.email_address') }}
                </label>
                <input id="email" type="email"
                    class="form-control form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email"
                    placeholder="{{ __('auth.placeholders.email') }}">

                @error('email')
                    <div class="invalid-feedback">
                        @if ($message === 'The email field is required.')
                            {{ __('auth.validation.email_required') }}
                        @elseif($message === 'The email must be a valid email address.')
                            {{ __('auth.validation.email_invalid') }}
                        @elseif($message === 'The email has already been taken.')
                            {{ __('auth.validation.email_taken') }}
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="fas fa-lock text-muted me-2"></i>{{ __('auth.password') }}
                </label>
                <input id="password" type="password"
                    class="form-control form-control @error('password') is-invalid @enderror" name="password" required
                    autocomplete="new-password" placeholder="{{ __('auth.placeholders.password') }}">

                @error('password')
                    <div class="invalid-feedback">
                        @if ($message === 'The password field is required.')
                            {{ __('auth.validation.password_required') }}
                        @elseif(str_contains($message, 'The password must be at least'))
                            {{ __('auth.validation.password_min') }}
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @enderror
            </div>

            {{-- Confirmation mot de passe --}}
            <div class="mb-4">
                <label for="password-confirm" class="form-label fw-semibold">
                    <i class="fas fa-lock text-muted me-2"></i>{{ __('auth.confirm_password') }}
                </label>
                <input id="password-confirm" type="password" class="form-control form-control"
                    name="password_confirmation" required autocomplete="new-password"
                    placeholder="{{ __('auth.placeholders.confirm_password') }}">
            </div>

            {{-- ReCaptcha --}}
            <div class="mb-3 form-check">
                <div class="form-group">
                    <strong>{{ __('auth.recaptcha') }}:</strong>
                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>

                    @if ($errors->has('g-recaptcha-response'))
                        <span class="text-danger">{{ __('auth.validation.recaptcha_required') }}</span>
                    @endif
                </div>
            </div>

            {{-- Soumission --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                    <i class="fas fa-check-circle me-2"></i>{{ __('auth.register_button') }}
                </button>
            </div>

            {{-- Lien de connexion --}}
            <div class="text-center mt-3">
                <small class="text-muted">
                    {{ __('auth.already_have_account') }}
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                        {{ __('auth.login_here') }}
                    </a>
                </small>
            </div>
        </form>
    </div>

@endsection
