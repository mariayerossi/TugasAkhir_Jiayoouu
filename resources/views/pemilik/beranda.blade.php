@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-5">Dashboard Admin</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Customer</h5>
                    <p class="display-5">100</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Pemilik Alat</h5>
                    <p class="display-5">250</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Lapangan</h5>
                    <p class="display-5">5000</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-5">
        <h3>Grafik Pendapatan</h3>
        <canvas id="pendapatanChart"></canvas>
     </div>
</div>
@endsection