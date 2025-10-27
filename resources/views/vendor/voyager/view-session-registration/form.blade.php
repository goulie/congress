<form method="POST" action="#">
    @csrf

    <div class="box-body">
        <div class="row">
            <!-- Title (Select) -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark"><i class="bi bi-person-vcard"></i>
                    {{ __('registration.step1.fields.title') }}</label>
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

            <!-- First Name -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark"><i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}</label>
                <input type="text" class="form-control" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                    @isset($participant) value="{{ $participant->fname }}" @endisset required>
            </div>

            <!-- Last Name -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark"><i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.last_name') }}</label>
                <input type="text" class="form-control" name="last_name"
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                    @isset($participant) value="{{ $participant->lname }}" @endisset required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Company -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-building"></i>
                    Company (Entreprise)*
                </label>
                <input type="text" class="form-control" name="company" placeholder="Enter your company name"
                    @isset($participant) value="{{ $participant->company }}" @endisset required>
            </div>

            <!-- Country -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark"><i class="bi bi-globe"></i>
                    Country (Pays)*</label>
                <select class="form-control" name="country" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                        <option value="{{ $country->id }} "
                            {{ isset($participant) && $participant->nationality_id == $country->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }} </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
            </div>

            <!-- Invoice Number -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-receipt"></i>
                    Invoice Number*
                </label>
                <input type="text" class="form-control" name="invoice_number" placeholder="Enter invoice number"
                    @isset($participant) value="{{ $participant->invoice_number }}" @endisset required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Enter Amount -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-currency-dollar"></i>
                    Enter Amount*
                </label>
                <input type="number" class="form-control" name="amount" placeholder="Enter amount"
                    @isset($participant) value="{{ $participant->amount }}" @endisset required
                    step="0.01">
            </div>

            <!-- Gender -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark"><i class="bi bi-gender-ambiguous"></i>
                    {{ __('registration.step1.fields.gender') }}</label>
                <select class="form-control" name="gender" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                        <option value="{{ $gender->id }}"
                            {{ isset($participant) && $participant->gender_id == $gender->id ? 'selected' : '' }}>
                            {{ $gender->libelle }}</option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>

            <!-- Telephone -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>
                <input type="tel" class="form-control" id="telephone-input" name="telephone"
                    placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    @isset($participant) value="{{ $participant->phone }}" @endisset required>
                <input type="hidden" id="telephone" name="telephone_complet">
            </div>
        </div>


    </div>

    <div class="box-footer">
        <div class="navigation-buttons">
            <button type="submit" class="btn btn-outline btn-block">
                {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>


{{-- Liste of accompagnants --}}
<h3 class="text-center text-primary">Liste des Sessions</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom complet</th>
            <th>Téléphone</th>
            <th>Passeport</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($participants as $p)
            <tr>
                <td>{{ $p->lname }} {{ $p->fname }}</td>
                <td>{{ $p->phone }}</td>
                <td>{{ $p->passeport_number }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-info">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="#" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet accompagnant ?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty

            <tr>
                <td colspan="4">Aucun accompagnant</td>
            </tr>
        @endforelse
    </tbody>
</table>

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation du sélecteur de téléphone international
            const phoneInput = document.querySelector("#telephone-input");
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "fr",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                preferredCountries: ['fr', 'be', 'ch', 'de'],
                separateDialCode: true
            });
        });
    </script>
@endsection
