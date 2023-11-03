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
    <h3 class="text-center mb-5">Laporan Alat Olahraga yang Disewakan</h3>
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Alat: {{$disewakan->count()}}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/tempat/laporan/disewakan/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah Disewakan</th>
                <th>Total Komisi (/jam)</th>
                <th>Total Durasi Sewa</th>
                <th>Total Pendapatan Kotor(sebelum biaya aplikasi)</th>
                <th>Total Pendapatan Bersih</th>
                {{-- <th>Status</th> --}}
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            @if (!$disewakan->isEmpty())
                @foreach ($disewakan as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->total_sewa}} kali</td>
                        @if ($item->fk_id_pemilik != null)
                            <td>Rp {{ number_format($item->harga_sewa_alat - $item->komisi_alat, 0, ',', '.') }}</td>
                        @else
                            <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                        @endif
                        <td>{{$item->total_durasi  + $item->durasi_ext}} jam</td>
                        <td>Rp {{ number_format($item->total_pendapatan + $item->komisi_ext, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format(($item->total_pendapatan + $item->komisi_ext) * 0.91, 0, ',', '.') }}</td>
                        {{-- @if ($item->fk_id_pemilik != null)
                            <td>Alat Sewaan</td>
                            <td><a href="/tempat/detailAlatUmum/{{$item->id_alat}}" class="btn btn-outline-success">Lihat Detail</a></td>
                        @else
                            <td>Alat Pribadi</td>
                            <td><a href="/tempat/laporan/disewakan/laporanPerAlat/{{$item->id_alat}}" class="btn btn-outline-success">Lihat Detail</a></td>
                        @endif --}}
                        <td><a href="/tempat/laporan/disewakan/laporanPerAlat/{{$item->id_alat}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection