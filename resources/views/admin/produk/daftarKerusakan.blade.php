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

</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga yang Rusak</h3>
    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Ganti Rugi</th>
                        <th>Unsur Kesengajaan</th>
                        <th>Pemilik Alat</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$rusak->isEmpty())
                        @foreach ($rusak as $item)
                            {{-- @php
                                $dataFiles = $files->get_all_data($item->id_alat)->first();
                            @endphp --}}
                            <tr>
                                <td><a href="/admin/transaksi/detailTransaksi/{{$item->id_htrans}}">{{$item->kode_trans}}</a></td>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>{{$item->nama_alat}}</td>
                                <td>Rp {{ number_format($item->ganti_rugi_alat, 0, ',', '.') }}</td>
                                @if ($item->kesengajaan == "Ya")
                                    <td style="color: red">{{$item->kesengajaan}}, Disengaja</td>
                                @else
                                    <td style="color:green">{{$item->status_alat}}</td>
                                @endif
                                <td>{{$item->nama_pemilik}}</td>
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
    </div>
</div>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection