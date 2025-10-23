@extends('auth.layout')

@section('title', 'Evenement AAEA')

@section('content')

    <div class="donation-summary">
        Montant : <span id="donation-amount">0 €</span>
    </div>

    <h3 class="form-section-title">Vos informations</h3>
    <form>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" id="firstname" class="form-control" placeholder=" " required>
                    <label for="firstname" class="form-label">Prénom</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" id="lastname" class="form-control" placeholder=" " required>
                    <label for="lastname" class="form-label">Nom</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="email" id="email" class="form-control" placeholder=" " required>
            <label for="email" class="form-label">Adresse e-mail</label>
        </div>
        <div class="form-group">
            <input type="tel" id="phone" class="form-control" placeholder=" ">
            <label for="phone" class="form-label">Numéro de téléphone (facultatif)</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg mt-3">Participer et Faire un don</button>
    </form>

@endsection
