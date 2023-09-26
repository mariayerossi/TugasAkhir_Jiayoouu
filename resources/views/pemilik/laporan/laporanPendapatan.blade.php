@extends('layouts.sidebarNavbar_pemilik')

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
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Rp {{ number_format($disewakan->sum('total_komisi_pemilik') - $disewakan->sum("pendapatan_website_alat"), 0, ',', '.') }}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/pemilik/laporan/pendapatan/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>
    
    {{-- grafik --}}
    <div class="mt-5 mb-5">
        <h4>Grafik Pendapatan per Bulan</h4>
        <div class="chart-container">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <div class="mb-5 flex-column flex-md-row">
        @include("layouts.message")
        <div class="mb-2 d-flex justify-content-between align-items-center flex-wrap">
            <span class="mr-2">Tampilkan berdasarkan:</span>
            
            <form action="/pemilik/laporan/pendapatan/fiturPendapatan" method="get" class="d-flex flex-column flex-md-row align-items-center">
                @csrf
                
                {{-- <div class="mb-2 mb-md-0 mr-md-2">
                    <select id="monthFilter" class="form-control" name="monthFilter">
                        <option value="">Pilih Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="mb-2 mb-md-0 mr-md-2">
                    <select id="yearFilter" class="form-control" name="yearFilter">
                        <option value="">Pilih Tahun</option>
                        @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div> --}}

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

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                {{-- <th>Foto</th> --}}
                <th>Nama</th>
                <th>Komisi</th>
                <th>Durasi</th>
                <th>Tanggal Transaksi</th>
                <th>Pendapatan Kotor</th>
                <th>Pendapatan Bersih</th>
            </tr>
        </thead>
        <tbody>
            @if (!$disewakan->isEmpty())
                @foreach ($disewakan as $item)
                    {{-- @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        // $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                    @endphp --}}
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->nama_alat}}</td>
                        <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                        <td>{{$item->durasi_sewa}} jam</td>
                        @php
                            $tanggalAwal2 = $item->tanggal_trans;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>Rp {{ number_format($item->total_komisi_pemilik, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_komisi_pemilik-$item->pendapatan_website_alat, 0, ',', '.') }}</td>
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