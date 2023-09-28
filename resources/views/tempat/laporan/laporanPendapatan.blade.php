@extends('layouts.sidebarNavbar_tempat')

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
    <h3 class="text-center mb-5">Laporan Pendapatan</h3>
    @if ($tanggal_mulai != null && $tanggal_selesai != null)
        @php
            $tanggalAwal = $tanggal_mulai;
            $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
            $tanggalBaru = $tanggalObjek->format('d-m-Y');

            $tanggalAwal3 = $tanggal_selesai;
            $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
            $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
        @endphp
        <h6 class="text-center mb-5">{{$tanggalBaru}} - {{$tanggal_selesai}}</h6>
    @endif
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Rp {{ number_format($trans->sum('subtotal_lapangan') + $trans->sum('total_komisi') - $trans->sum("pendapatan_website_lapangan"), 0, ',', '.') }}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/tempat/laporan/pendapatan/CetakPDF/{{$tanggal_mulai}}/{{$tanggal_selesai}}" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    <div class="mb-5 flex-column flex-md-row">
        @include("layouts.message")
        <div class="mb-2 d-flex justify-content-between align-items-center flex-wrap">
            <span class="mr-2">Tampilkan berdasarkan:</span>
            
            <form action="/tempat/laporan/pendapatan/fiturPendapatan" method="get" class="d-flex flex-column flex-md-row align-items-center">
                @csrf
                <!-- Input date untuk tanggal mulai -->
                <div class="form-group mr-2 mb-2 mb-md-0">
                    <label for="tanggal_mulai" class="mb-0">Mulai:</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control form-control-sm">
                </div>

                <!-- Input date untuk tanggal selesai -->
                <div class="form-group mr-2 mb-5 mb-md-0">
                    <label for="tanggal_selesai" class="mb-0">Selesai:</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control form-control-sm">
                </div>
                
                <div class="mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- grafik --}}
    <div class="mt-5 mb-5">
        <h4>Grafik Pendapatan per Bulan</h4>
        <div class="chart-container">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                {{-- <th>Foto</th> --}}
                <th>Kode Transaksi</th>
                <th>Tanggal Transaksi</th>
                <th>Nama Lapangan</th>
                <th>Subtotal Lapangan</th>
                <th>Jumlah Alat Disewakan</th>
                <th>Total Komisi Alat</th>
                <th>Total Pendapatan Kotor(sebelum biaya aplikasi)</th>
                <th>Total Pendapatan Bersih</th>
            </tr>
        </thead>
        <tbody>
            @if (!$trans->isEmpty())
                @foreach ($trans as $item)
                    <tr>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->kode_trans}}</td>
                        @php
                            $tanggalAwal2 = $item->tanggal_trans;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>Rp {{ number_format($item->subtotal_lapangan, 0, ',', '.') }}</td>
                        <td>{{$item->alat}}</td>
                        <td>Rp {{ number_format($item->total_komisi, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_komisi+$item->subtotal_lapangan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_komisi+$item->subtotal_lapangan-$item->pendapatan_website_lapangan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak Ada Data</td>
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
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection