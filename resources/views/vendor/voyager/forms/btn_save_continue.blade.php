<div class="box-footer">
    <div class="navigation-buttons mt-3">
        @if(isset($step) && $step > 1)
        <a href="{{ route('form.previous') }}" type="button" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> {{ __('registration.step1.buttons.previous') }}
        </a>
        @endif
        <button type="submit" class="btn btn-outline">
            {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>