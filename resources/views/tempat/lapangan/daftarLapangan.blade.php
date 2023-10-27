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
    <h3 class="text-center mb-5">Daftar Lapangan Olahraga</h3>
    <div class="d-flex justify-content-end mb-4">
        <a href="/tempat/lapangan/masterLapangan" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Lapangan</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Harga Sewa</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$lapangan->isEmpty())
                @foreach ($lapangan as $item)
                    {{-- @php
                        $dataFiles = $files->get_all_data($item->id_lapangan)->first();
                    @endphp --}}
                    <tr>
                        <td>
                            <div class="square-image-container">
                                <a href="/tempat/lapangan/lihatDetailLapangan/{{$item->id_lapangan}}"><img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt=""></a>
                            </div>
                        </td>
                        <td><a href="/tempat/lapangan/lihatDetailLapangan/{{$item->id_lapangan}}">{{$item->nama_lapangan}}</a></td>
                        <td>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</td>
                        @if ($item->status_lapangan == "Aktif")
                            <td style="color: green">{{$item->status_lapangan}}</td>
                        @else
                            <td style="color:red">{{$item->status_lapangan}}</td>
                        @endif
                        <td><a class="btn btn-outline-success" href="/tempat/lapangan/editLapangan/{{$item->id_lapangan}}">Edit</a></td>
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