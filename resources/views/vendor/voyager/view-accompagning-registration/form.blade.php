<div class="row">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif
</div>
<form method="POST" action="{{ route('add.accompagning.form') }}">
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
                        <option value="{{ $civility->id }}">
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
                        <option value="{{ $gender->id }}">
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
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}" required>
            </div>

            <!-- Prénom -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}
                </label>
                <input type="text" class="form-control" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}" required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Téléphone avec indicatif -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-persons"></i>
                    {{ __('registration.step1.fields.type') }}
                </label>
                <select class="form-control" name="accompagnant" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\AccompanyingPersonType::get()->translate(app()->getLocale(), 'fallbackLocale') as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>
                <input type="tel" class="form-control" id="telephone-input" name="telephone"
                    placeholder="{{ __('registration.step2.placeholders.telephone') }}" required>
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control"
                    placeholder="{{ __('registration.step2.placeholders.email') }}" name="email" required>
            </div>

        </div>

        <div class="row" style="margin-top:15px;">
            <!-- Numéro de passeport -->
            <div class="col-md-6">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-passport"></i>
                    {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control" name="num_passeport"
                    placeholder="{{ __('registration.step3.placeholders.num_passeport') }}" required>
            </div>
            <div class="col-md-6">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-image"></i>
                    {{ __('registration.step3.fields.photo_passeport') }}
                </label>
                <div class="input-group">
                    <input type="file" class="form-control" name="photo_passeport" accept="image/*" required>

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

{{-- Liste of accompagnants --}}
<div class="alert alert-info text-center">
    <h3>Liste des accompagnants</h3>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom complet</th>
            <th>Type d'accompagnant</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Passeport</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($participants as $p)
            <tr>
                <td>{{ $p->lname }} {{ $p->fname }}</td>
                <td>{{ $p->type_accompagning->libelle ?? '' }}</td>
                <td>{{ $p->email }}</td>
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
