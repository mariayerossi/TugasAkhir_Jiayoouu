@extends('layouts.sidebar_admin')

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
<script>
    // Data contoh untuk grafik
    var dataPendapatan = {
       labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
       datasets: [{
          label: 'Pendapatan',
          data: [5000, 7500, 6500, 8200, 7000, 8500, 6000, 5500, 7000, 7500, 8000, 7000],
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
    </script>
    
    
@endsection