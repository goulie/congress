<form method="POST" action="#">
    @csrf

    <div class="box-body">
        <div class="row">
            <!-- Titre (Select) -->
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-vcard"></i>
                    {{ __('registration.step1.fields.title') }}
                </label>
                <select class="form-control" name="title" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
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
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-gender-ambiguous"></i>
                    {{ __('registration.step1.fields.gender') }}
                </label>
                <select class="form-control" name="gender" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
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
            <!-- Nom -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.last_name') }}
                </label>
                <input type="text" class="form-control" name="last_name"
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                    @isset($participant) value="{{ $participant->lname }}" @endisset required>
            </div>

            <!-- Prénom -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}
                </label>
                <input type="text" class="form-control" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                    @isset($participant) value="{{ $participant->fname }}" @endisset required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Téléphone avec indicatif -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>
                <input type="tel" class="form-control" id="telephone-input" name="telephone"
                    placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    @isset($participant) value="{{ $participant->phone }}" @endisset required>
                <input type="hidden" id="telephone-complet" name="telephone_complet">
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control"
                    placeholder="{{ __('registration.step2.placeholders.email') }}"
                    value="{{ auth()->user()->email }}" required disabled>
            </div>
            <!-- Genre (Select) -->


            <!-- Numéro de passeport -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-passport"></i>
                    {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control" name="num_passeport"
                    placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                    @isset($participant) value="{{ $participant->passeport_number }}" @endisset
                    required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Photo passeport -->
            <div class="col-md-12">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-image"></i>
                    {{ __('registration.step3.fields.photo_passeport') }}
                </label>
                <div class="input-group">
                    <input type="file" class="form-control" name="photo_passeport" accept="image/*"
                        @if (!isset($participant) || !$participant->passeport_pdf) required @endif>

                    @if (isset($participant) && $participant->passeport_pdf)
                        <a class="btn btn-outline-primary" href="{{ Voyager::image($participant->passeport_pdf) }}"
                            target="_blank">
                            <i class="bi bi-eye"></i> Voir le document
                        </a>
                    @endif
                </div>
                <small class="form-text text-muted">
                    Formats acceptés: JPG, PNG, PDF - Taille max: 2MB
                </small>
            </div>
        </div>
    </div>

    <div class="box-footer" style="margin-top:20px;">
        <div class="navigation-buttons">
            <button type="submit" class="btn btn-primary btn-block">
                {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>
