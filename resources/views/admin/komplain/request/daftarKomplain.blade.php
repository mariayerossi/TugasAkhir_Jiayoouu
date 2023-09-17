@extends('layouts.sidebar_admin')

<!-- DataTables -->
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
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-5">Daftar Komplain Request</h3>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Jenis Komplain</th>
                <th>Pengaju</th>
                <th>Jenis Request</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$komplain->isEmpty())
                @foreach ($komplain as $item)
                    @php
                        if ($item->jenis_role == "Pemilik") {
                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_user)->get()->first();
                        }
                        else {
                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_user)->get()->first();
                        }
                    @endphp
                    <tr>
                        <td>{{$item->jenis_komplain}}</td>
                        @if ($item->jenis_role == "Pemilik")
                            <td>{{$dataPemilik->nama_pemilik}}</td>
                        @else
                            <td>{{$dataTempat->nama_tempat}}</td>
                        @endif
                        <td>{{$item->jenis_request}}</td>
                        @if ($item->status_komplain == "Menunggu")
                            <td style="color:rgb(239, 203, 0)">{{$item->status_komplain}}</td>
                        @elseif($item->status_komplain == "Diterima")
                            <td style="color:rgb(0, 145, 0)">{{$item->status_komplain}}</td>
                        @elseif($item->status_komplain == "Ditolak")
                            <td style="color:red">{{$item->status_komplain}}</td>
                        @endif
                        <td><a class="btn btn-outline-success" href="/admin/komplain/request/detailKomplain/{{$item->id_komplain_req}}">Lihat Detail</a></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center">Tidak Ada Data</td>
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