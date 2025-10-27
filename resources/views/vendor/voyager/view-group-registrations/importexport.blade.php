@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('participants.store.multiple') }}" enctype="multipart/form-data">
        @csrf

        <div id="participants-wrapper">
            <!-- ==== TEMPLATE DU PARTICIPANT ==== -->
            <div class="participant-block panel panel-default p-3 mb-4" data-index="0" style="border:2px solid #0121a0; border-radius:10px;">
                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h4 class="text-primary font-weight-bold">Participant <span class="participant-number">1</span></h4>
                    <button type="button" class="btn btn-danger btn-sm remove-participant d-none">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </div>
                <div class="panel-body">
                    {{-- Étape 1 : Renseignements personnels --}}
                    <h5 class="text-dark mt-3"><i class="bi bi-person-vcard"></i> Informations personnelles</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step1.fields.title') }}</label>
                            <select class="form-control" name="participants[0][title]" required>
                                <option selected disabled>{{ __('registration.choose') }}</option>
                                @foreach (App\Models\Civility::all()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                                    <option value="{{ $civility->id }}">{{ $civility->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step1.fields.first_name') }}</label>
                            <input type="text" class="form-control" name="participants[0][first_name]" placeholder="{{ __('registration.step1.placeholders.first_name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step1.fields.last_name') }}</label>
                            <input type="text" class="form-control" name="participants[0][last_name]" placeholder="{{ __('registration.step1.placeholders.last_name') }}" required>
                        </div>
                    </div>

                    {{-- Étape 2 : Coordonnées --}}
                    <h5 class="text-dark mt-4"><i class="bi bi-envelope"></i> Coordonnées</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step2.fields.telephone') }}</label>
                            <input type="tel" class="form-control" name="participants[0][telephone]" placeholder="{{ __('registration.step2.placeholders.telephone') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step2.fields.organisation') }}</label>
                            <input type="text" class="form-control" name="participants[0][organisation]" placeholder="{{ __('registration.step2.placeholders.organisation') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step2.fields.fonction') }}</label>
                            <input type="text" class="form-control" name="participants[0][fonction]" placeholder="{{ __('registration.step2.placeholders.fonction') }}" required>
                        </div>
                    </div>

                    {{-- Étape 3 : Détails du congrès --}}
                    <h5 class="text-dark mt-4"><i class="bi bi-people"></i> Détails du congrès</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step3.fields.category') }}</label>
                            <select class="form-control" name="participants[0][category]" required>
                                <option selected disabled>{{ __('registration.choose') }}</option>
                                @foreach (App\Models\CategoryParticipant::all()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                                    <option value="{{ $category->id }}">{{ $category->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step3.fields.membership') }}</label>
                            <select class="form-control" name="participants[0][membership]" required>
                                <option selected disabled>{{ __('registration.choose') }}</option>
                                @foreach (App\Models\TypeMember::all()->translate(app()->getLocale(), 'fallbackLocale') as $typeMember)
                                    <option value="{{ $typeMember->id }}">
                                        {{ $typeMember->libelle }}
                                        ({{ $typeMember->amount }} {{ $typeMember->currency }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">{{ __('registration.step3.fields.num_passeport') }}</label>
                            <input type="text" class="form-control" name="participants[0][num_passeport]" placeholder="{{ __('registration.step3.placeholders.num_passeport') }}" required>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ==== FIN TEMPLATE ==== -->
        </div>

        <!-- Boutons -->
        <div class="text-center mt-4">
            <button type="button" id="add-participant" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Ajouter un participant
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Enregistrer tous les participants
            </button>
        </div>
    </form>
</div>
@endsection


@section('javascript')
<script>
$(document).ready(function() {
    let participantIndex = 0;

    $('#add-participant').on('click', function() {
        participantIndex++;
        let newBlock = $('.participant-block:first').clone();
        newBlock.attr('data-index', participantIndex);
        newBlock.find('.participant-number').text(participantIndex + 1);

        // Met à jour tous les name="participants[0][...]" => "participants[n][...]"
        newBlock.find('input, select').each(function() {
            const name = $(this).attr('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, '[' + participantIndex + ']');
                $(this).attr('name', newName).val('');
            }
        });

        // Active le bouton supprimer
        newBlock.find('.remove-participant').removeClass('d-none');

        $('#participants-wrapper').append(newBlock.hide().fadeIn(300));
    });

    // Suppression d’un bloc participant
    $(document).on('click', '.remove-participant', function() {
        $(this).closest('.participant-block').fadeOut(300, function() {
            $(this).remove();
            updateParticipantNumbers();
        });
    });

    function updateParticipantNumbers() {
        $('.participant-block').each(function(index) {
            $(this).find('.participant-number').text(index + 1);
        });
    }
});
</script>
@endsection
