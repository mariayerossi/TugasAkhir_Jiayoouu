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
        width: 100%;   /* Atur lebar sesuai keinginan */
        height: 300px; /* Atur tinggi sesuai keinginan */
        margin: 0 auto; /* opsional: untuk mengatur grafik agar berada di tengah */
    }
    @media (max-width: 768px) {  /* angka 768px adalah titik putus umum untuk tablet, Anda bisa menyesuaikannya */
        .chart-container {
            width: 100%;   /* Di mobile, sebaiknya gunakan 100% agar mengisi seluruh lebar */
            height: 200px; /* Tinggi yang lebih kecil untuk mobile */
        }
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Laporan Pendapatan Website</h3>
    @if ($tanggal_mulai != null && $tanggal_selesai != null)
        @php
            $tanggalAwal = $tanggal_mulai;
            $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
            $tanggalBaru = $tanggalObjek->format('d-m-Y');

            $tanggalAwal3 = $tanggal_selesai;
            $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
            $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
        @endphp
        <h6 class="text-center mb-5">{{$tanggalBaru}} - {{$tanggalBaru3}}</h6>
    @endif
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Pendapatan: Rp {{ number_format($trans->sum("pendapatan_lapangan") + $trans->sum("pendapatan_alat") + $trans->sum("lapangan_ext") + $trans->sum("alat_ext"), 0, ',', '.') }}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/admin/laporan/pendapatan/CetakPDF/{{$tanggal_mulai}}/{{$tanggal_selesai}}" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    <div class="mb-5">
        @include("layouts.message")
        <div class="mb-3">
            <form action="/admin/laporan/pendapatan/fiturPendapatan" method="get" class="d-flex flex-column flex-md-row align-items-center">
                @csrf
                <h5 class="mb-2 mb-md-0 me-md-5">Tampilkan berdasarkan:</h5>
                <!-- Input date untuk tanggal mulai -->
                <div class="form-group d-flex flex-column flex-md-row mr-3 mb-2 mb-md-0">
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control form-control-sm">
                </div>

                <div class="mt-2 mt-md-0 me-3 ms-3">
                    <i class="bi bi-dash-lg"></i>
                </div>
    
                <!-- Input date untuk tanggal selesai -->
                <div class="form-group d-flex flex-column flex-md-row mr-3 mb-2 mb-md-0">
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control form-control-sm">
                </div>
    
                <div class="mt-2 mt-md-0 ms-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- grafik --}}
    <div class="row mt-5 mb-5">
        <div class="col-md-6">
            <h4>Grafik Pendapatan per Bulan</h4>
            <div class="chart-container">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
    
        <div class="col-md-6">
            <h4>Grafik Pendapatan 5 Tahun Terakhir</h4>
            <div class="chart-container">
                <canvas id="yearlyIncomeChart"></canvas>
            </div>
        </div>    
    </div>

    {{-- laporan pendapatan per alat olahraga --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Tanggal Transaksi</th>
                <th>Nama Lapangan</th>
                <th>Jumlah Alat Disewakan</th>
                <th>Pendapatan Website</th>
            </tr>
        </thead>
        <tbody>
            @if (!$trans->isEmpty())
                @foreach ($trans as $item)
                    <tr>
                        <td>{{$item->kode_trans}}</td>
                        @php
                            $tanggalAwal2 = $item->tanggal_trans;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>{{$item->jumlah_alat}}</td>
                        <td>Rp {{ number_format(($item->pendapatan_lapangan + $item->pendapatan_alat) + ($item->lapangan_ext + $item->alat_ext), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">Tidak Ada Data</td>
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
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Total Pendapatan',
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

    var currentYear = new Date().getFullYear();
    var yearlyCtx = document.getElementById('yearlyIncomeChart').getContext('2d');
    var yearlyIncomeChart = new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: [currentYear-4, currentYear-3, currentYear-2, currentYear-1, currentYear],
            datasets: [{
                label: 'Pendapatan Tahunan',
                data: @json($yearlyIncome), // Pastikan Anda sudah mengirim data pendapatan tahunan ke view
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