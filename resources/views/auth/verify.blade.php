@extends('auth.layout')

@section('header', __('auth.verify_email'))

@section('content')
    <div class="row justify-content-center">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="fas fa-envelope-open-text me-2"></i>{{ __('auth.verify_email') }}
        </h3>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2 text-success"></i>
                            {{ __('auth.verification_sent') }}
                        </div>
                    @endif

                    <p class="mb-3 fs-5">
                        {{ __('auth.check_email_instructions') }}
                    </p>

                    <p class="mb-4 text-muted">
                        {{ __('auth.request_new_verification') }}
                    </p>

                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('auth.resend_verification') }}
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('auth.back_to_login') }}
                        </a>
                    </div>

                    <a class="btn btn-link mt-3" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                        {{ __('auth.logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
