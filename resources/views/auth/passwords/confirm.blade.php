@extends('auth.layout')

@section('header', __('Confirm Password'))

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-lock me-2"></i>{{ __('Confirm Your Password') }}
        </h3>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <p class="mb-4">
                        {{ __('Please confirm your password before continuing.') }}
                    </p>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock text-muted me-2"></i>{{ __('Password') }}
                            </label>
                            <input id="password" type="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"
                                required autocomplete="current-password" placeholder="********">

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="fas fa-check-circle me-2"></i>{{ __('Confirm Password') }}
                            </button>
                        </div>

                        {{-- Forgot Password --}}
                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}"
                                    class="text-decoration-none fw-semibold text-primary">
                                    <i class="fas fa-question-circle me-1"></i>{{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
