@extends('auth.layout')

@section('header', __('Verify Your Email Address'))

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-envelope-open-text me-2"></i>{{ __('Verify Your Email Address') }}
        </h3>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2 text-success"></i>
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p class="mb-3 fs-5">
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                    </p>

                    <p class="mb-4 text-muted">
                        {{ __('If you did not receive the email, you can request another one below:') }}
                    </p>

                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Resend Verification Email') }}
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Login') }}
                        </a>
                    </div>
                    <a class="btn btn-link mt-3" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
