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

</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga yang Disewakan</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Waktu Sewa</th>
                <th>Harga Sewa</th>
                <th>Durasi</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if (!$disewakan->isEmpty())
                @foreach ($disewakan as $item)
                    {{-- @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                    @endphp --}}
                    <tr>
                        <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt="">
                            </div>
                        </td>
                        <td>{{$item->nama_alat}}</td>
                        @php

                            $tanggalAwal2 = $item->tanggal_sewa;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                        <td>{{$item->durasi_sewa}} jam</td>
                        <td>Rp {{ number_format($item->subtotal_alat, 0, ',', '.') }}</td>
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
        $('.table').DataTable();
    });

</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection