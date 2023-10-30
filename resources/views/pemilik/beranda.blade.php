@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-5">Selamat Datang, {{Session::get("dataRole")->nama_pemilik}}</h2>
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
            <a href="/pemilik/permintaan/daftarPermintaan">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Permintaan Alat</h5>
                        <p class="display-5">{{$jumlahPermintaan}}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="/pemilik/penawaran/daftarPenawaran">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Penawaran Alat</h5>
                        <p class="display-5">{{$jumlahPenawaran}}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <h4>Grafik Pendapatan</h4>
        <div class="row">
            <!-- Grafik Garis -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="pendapatanChart"></canvas>
                    </div>
                </div>
            </div>
    
            <!-- Grafik Balok -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="pendapatanBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Data contoh untuk grafik
    var dataPendapatan = {
       labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
       datasets: [{
          label: 'Pendapatan Setahun',
          data: @json($monthlyIncome),
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1,
          fill: false
       }]
    };
    
    var options = {
       scales: {
          yAxes: [{
             ticks: {
                beginAtZero: true
             }
          }]
       }
    };
    
    // Membuat grafik garis
    var ctx = document.getElementById('pendapatanChart').getContext('2d');
    var myLineChart = new Chart(ctx, {
       type: 'line',
       data: dataPendapatan,
       options: options
    });

    var currentYear = new Date().getFullYear();
    var years = [
        currentYear - 4,
        currentYear - 3,
        currentYear - 2,
        currentYear - 1,
        currentYear
    ];

    var dataPendapatanBar = {
    labels: years,
    datasets: [{
        label: 'Pendapatan 5 Tahun Terakhir',
        data: @json($yearlyIncome),
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
    }]
    };

    // Membuat grafik balok
    var ctxBar = document.getElementById('pendapatanBarChart').getContext('2d');
    var myBarChart = new Chart(ctxBar, {
    type: 'bar',
    data: dataPendapatanBar,
    options: options
    });

    </script>
@endsection