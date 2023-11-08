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

</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga</h3>
    <div class="d-flex justify-content-end mb-4">
        <a href="/tempat/alat/masterAlat" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Alat</a>
    </div>
    <div class="card mb-5 p-3">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Komisi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$alat->isEmpty())
                        @foreach ($alat as $item)
                            {{-- @php
                                $dataFiles = $files->get_all_data($item->id_alat)->first();
                            @endphp --}}
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <a href="/tempat/alat/lihatDetail/{{$item->id_alat}}"><img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt=""></a>
                                    </div>
                                </td>
                                <td><a href="/tempat/alat/lihatDetail/{{$item->id_alat}}">{{$item->nama_alat}}</a></td>
                                <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                                @if ($item->status_alat == "Aktif")
                                    <td style="color: green">{{$item->status_alat}}</td>
                                @else
                                    <td style="color:red">{{$item->status_alat}}</td>
                                @endif
                                <td><a class="btn btn-outline-success" href="/tempat/alat/editAlat/{{$item->id_alat}}">Edit</a></td>
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
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection