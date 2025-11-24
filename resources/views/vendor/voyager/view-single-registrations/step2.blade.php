<form method="POST" action="{{ route('form.step1') }}">
    @csrf
    <input type="hidden" name="uuid" value="{{ $participant->uuid }}">

    <div class="box-body">

        {{-- Étape 1 : Renseignements personnels --}}
        <div class="row">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person-vcard"></i>
                    {{ __('registration.step1.fields.title') }}
                </label>
                <select class="form-control" name="title" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                        <option value="{{ $civility->id }}"
                            {{ isset($participant) && $participant->civility_id == $civility->id ? 'selected' : '' }}>
                            {{ $civility->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}
                </label>
                <input type="text" class="form-control" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                    @isset($participant) value="{{ $participant->fname }}" @endisset required>
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.last_name') }}
                </label>
                <input type="text" class="form-control" name="last_name"
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                    @isset($participant) value="{{ $participant->lname }}" @endisset required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-mortarboard"></i>
                    {{ __('registration.step1.fields.education') }}
                </label>
                <select class="form-control" name="education" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\StudentLevel::all()->translate(app()->getLocale(), 'fallbackLocale')->sortBy('order') as $studentLevel)
                        <option value="{{ $studentLevel->id }}"
                            {{ isset($participant) && $participant->student_level_id == $studentLevel->id ? 'selected' : '' }}>
                            {{ $studentLevel->libelle }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-gender-ambiguous"></i>
                    {{ __('registration.step1.fields.gender') }}
                </label>
                <select class="form-control" name="gender" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                        <option value="{{ $gender->id }}"
                            {{ isset($participant) && $participant->gender_id == $gender->id ? 'selected' : '' }}>
                            {{ $gender->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-globe"></i>
                    {{ __('registration.step1.fields.country') }}
                </label>
                <select class="form-control" name="country" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                        <option value="{{ $country->id }}"
                            {{ isset($participant) && $participant->nationality_id == $country->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-hourglass-split"></i>
                    {{ __('registration.step1.fields.age_range') }}
                </label>
                <select class="form-control" name="age_range" id="age" required>
                    <option value="" selected disabled>{{ __('registration.choose') }}</option>
                    @foreach (App\Models\AgeRange::all()->translate(app()->getLocale(), 'fallbackLocale') as $ageRange)
                        <option value="{{ $ageRange->id }}"
                            {{ isset($participant) && $participant->age_range_id == $ageRange->id ? 'selected' : '' }}>
                            {{ $ageRange->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

    </div>

    @include('voyager::forms.btn_save_continue')
</form>
