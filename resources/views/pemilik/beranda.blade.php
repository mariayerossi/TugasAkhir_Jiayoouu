@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-5">Dashboard Pemilik Alat Olahraga</h1>
    <div class="row">
        <div class="col-md-4">
            <a href="/pemilik/daftarAlat">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Alat</h5>
                        <p class="display-5">{{$jumlahAlat}}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="/pemilik/daftarPermintaan">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Permintaan Alat</h5>
                        <p class="display-5">{{$jumlahPermintaan}}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Penawaran Alat</h5>
                        <p class="display-5">5000</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection