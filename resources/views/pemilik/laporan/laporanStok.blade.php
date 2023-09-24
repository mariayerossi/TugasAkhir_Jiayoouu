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
    <h3 class="text-center mb-5">Laporan Stok Alat Olahraga</h3>
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Alat: {{$alat->count()}}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/pemilik/laporan/stok/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Komisi</th>
                <th>Disewakan</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @if (!$alat->isEmpty())
                @foreach ($alat as $item)
                    @php
                        // $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->id_alat)->get()->first();
                        // $totalRequest = DB::table('dtrans')->where("fk_id_alat","=",$item->id_alat)->count();

                        $tanggalAwal2 = $item->created_at;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                    @endphp
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt="">
                            </div>
                        </td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->kategori_alat}}</td>
                        <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                        <td>{{$item->totalRequest}} Kali</td>
                        @if ($item->status_alat == "Aktif")
                            <td style="color: green">{{$item->status_alat}}</td>
                        @else
                            <td style="color:red">{{$item->status_alat}}</td>
                        @endif
                        <td>{{$tanggalBaru2}}</td>
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