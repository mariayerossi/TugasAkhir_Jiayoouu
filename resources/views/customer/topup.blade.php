@extends('layouts.navbar_customer')

@section('content')
<style>
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }

    .h-custom {
        height: calc(100% - 73px);
    }

    @media (max-width: 450px) {
        .h-custom {
            height: 100%;
        }
    }
</style>
<section class="vh-100">
    <div class="container-fluid h-custom">
        @include("layouts.message")
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="{{ asset('assets/img/topup.png') }}" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form action="/customer/saldo/topup" method="post">
                    @csrf

                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <p class="lead fw-normal mb-0 me-3">Top Up Saldo</p>
                    </div>

                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0">Masukkan Nominal Top Up</p>
                    </div>

                    <!-- Amount input -->
                    <div class="form-outline mb-4">
                        <input type="number" name="jumlah" class="form-control form-control-lg" placeholder="Masukkan Nominal..." id="">
                        <label class="form-label" for="jumlah">Nominal Top Up</label>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-success" id="pay-button">Top Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
