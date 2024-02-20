@extends('layouts.sidebar_admin')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@section('content')
<style>
    .square-image-container {
        width: 100px;
        height: 100px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .square-image-container img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    .chart-container {
        width: 70%;   /* Atur lebar sesuai keinginan */
        height: 400px; /* Atur tinggi sesuai keinginan */
        margin: 0 auto; /* opsional: untuk mengatur grafik agar berada di tengah */
    }
    @media (max-width: 768px) {  /* angka 768px adalah titik putus umum untuk tablet, Anda bisa menyesuaikannya */
        .chart-container {
            width: 100%;   /* Di mobile, sebaiknya gunakan 100% agar mengisi seluruh lebar */
            height: 250px; /* Tinggi yang lebih kecil untuk mobile */
        }
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Laporan Tempat Olahraga</h3>
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Tempat: {{$tempat->count()}}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/admin/laporan/tempat/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    {{-- grafik --}}
    {{-- <div class="mt-5 mb-5">
        <h4>Grafik Persewaan per Bulan</h4>
        <div class="chart-container">
            <canvas id="incomeChart"></canvas>
        </div>
    </div> --}}

    {{-- laporan pendapatan per alat olahraga --}}
    <div class="card mb-5 p-3">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jumlah Lapangan</th>
                        <th>Total Transaksi</th>
                        <th>Pendapatan Website</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$tempat->isEmpty())
                        @foreach ($tempat as $item)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->nama_tempat}}</td>
                                <td>{{$item->jumlah_lapangan}}</td>
                                <td>{{$item->jumlah_trans}}</td>
                                <td>Rp {{ number_format($item->total_lapangan + $item->lapangan_ext, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });
</script>
{{-- <script>
    $(document).ready(function() {
        var table = $('.table').DataTable();
    });
    var ctx = document.getElementById('incomeChart').getContext('2d');
    var incomeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Jumlah Sewa',
                data: @json($monthlyIncome),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script> --}}
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection