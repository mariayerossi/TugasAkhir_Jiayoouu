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
    <h3 class="text-center mb-5">Laporan Alat Olahraga</h3>
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Alat: {{$alat->count()}}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/admin/laporan/alat/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    {{-- grafik --}}
    <div class="mt-5 mb-5">
        <h4>Grafik Persewaan per Bulan</h4>
        <div class="chart-container">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    {{-- laporan pendapatan per alat olahraga --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Harga Sewa</th>
                <th>Status</th>
                <th>Disewakan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if (!$alat->isEmpty())
                @foreach ($alat as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt="">
                            </div>
                        </td>
                        <td>{{$item->nama_alat}}</td>
                        <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                        @if ($item->status_alat == "Aktif")
                            <td style="color: green">{{$item->status_alat}}</td>
                        @else
                            <td style="color:red">{{$item->status_alat}}</td>
                        @endif
                        <td>{{$item->total_sewa}} Kali</td>
                        <td>Rp {{ number_format($item->total_pendapatan + $item->pendapatan_ext, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<script>
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
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection