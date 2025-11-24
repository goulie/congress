@extends('auth.layout')

@section('header', __('auth.reset_password'))

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-key me-2"></i>{{ __('auth.reset_your_password') }}
        </h3>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">

                    {{-- Status message --}}
                    @if (session('status'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-4">
                        {{ __('auth.enter_email') }}
                    </p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-muted me-2"></i>{{ __('auth.email_address') }}
                            </label>
                            <input id="email" type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="{{ __('auth.email_placeholder') }}">

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('auth.send_reset_link') }}
                            </button>
                        </div>

                        {{-- Back to login --}}
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('auth.back_to_login') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection