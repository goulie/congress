@extends('voyager::master')
@section('css')
<style>
.dashboard {
    margin-top: 50px;
}

.dash-btn {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.dash-btn:hover {
    background-color: #e8f0ff;
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.dash-btn h4 {
    font-weight: bold;
    margin-bottom: 15px;
    color: #1a237e;
}

.dash-btn p {
    font-size: 0.9em;
    margin-bottom: 0;
}

.dash-btn i {
    font-size: 2em;
    margin-bottom: 10px;
    color: #3f51b5;
    transition: transform 0.3s ease;
}

.dash-btn:active i {
    transform: scale(0.9);
}

a.dash-btn-link {
    text-decoration: none;
}

/* Responsive spacing */
@media (max-width: 767px) {
    .dash-btn {
        margin-bottom: 20px;
    }
}

</style>

@endsection
@section('content')
    <div class="page-content">
        @include('voyager::alerts')
        <div class="container dashboard">
            <div class="row">

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-person-plus-fill"></i>
                            <h4>Individual Registration</h4>
                            <p>Click here</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-people-fill"></i>
                            <h4>Group Registration</h4>
                            <p style="color:red;">Onsite registration at the Speke Convention Centre-Munyonyo</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-person-lines-fill"></i>
                            <h4>Accompanying Person Registration</h4>
                            <p style="color:red;">Onsite registration at the Speke Convention Centre-Munyonyo</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-credit-card-2-front-fill"></i>
                            <h4>Session Payment / Group Payment</h4>
                            <p>Click here</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-cash-stack"></i>
                            <h4>Sponsorship Payment</h4>
                            <p>Click here</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="#" class="dash-btn-link">
                        <div class="dash-btn">
                            <i class="bi bi-person-badge-fill"></i>
                            <h4>My Profile</h4>
                            <p>Click here</p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
@stop

@section('javascript')


@stop
