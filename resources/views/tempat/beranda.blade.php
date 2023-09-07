@extends('layouts.sidebarNavbar_tempat')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-5">Dashboard Tempat Olahraga</h1>
    <div class="row">
        <div class="col-md-4">
            <a href="/tempat/lapangan/daftarLapangan">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Lapangan</h5>
                        <p class="display-5">{{$jumlahLapangan}}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Permintaan Alat</h5>
                        <p class="display-5">250</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Penawaran Alat</h5>
                    <p class="display-5">5000</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection