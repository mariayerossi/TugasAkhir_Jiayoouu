@extends('layouts.sidebar_admin')

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
                <img src="{{ asset('assets/img/tarikDana.png') }}" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form action="/customer/saldo/topup" method="get">
                    @csrf

                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start mb-4">
                        <p class="lead fw-normal mb-0 me-3">Tarik Dana</p>
                    </div>

                    <h6 class="fw-bold">Nama Bank: BCA</h6>
                    <h6 class="fw-bold">Nomer Rekening: 1234567890</h6>
                    <h6 class="fw-bold">Nama Rekening: Sportiva</h6>

                    <!-- Amount input -->
                    <div class="form-outline mb-4 mt-3">
                        <!-- Input yang terlihat oleh pengguna -->
                        <input type="text" class="form-control" id="jumlahDisplay" placeholder="Masukkan Nominal yang Ingin Ditarik..." oninput="formatNumber(this)">

                        <!-- Input tersembunyi untuk kirim ke server -->
                        <input type="hidden" name="jumlah" id="jumlahActual">
                        <label class="form-label" for="jumlah">Nominal Penarikan</label>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-success" id="pay-button">Tarik</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('jumlahActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('jumlahActual').value = '';
        }
    }
</script>
@endsection
