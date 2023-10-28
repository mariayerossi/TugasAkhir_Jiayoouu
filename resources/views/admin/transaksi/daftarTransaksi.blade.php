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
            <h3 class="text-center mb-5">Daftar Transaksi</h3>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Total</th>
                <th>Penyewa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$trans->isEmpty())
                @foreach ($trans as $item)
                    {{-- @php
                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                    @endphp --}}
                    <tr>
                        <td>{{$item->kode_trans}}</td>
                        <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                            </div>
                        </td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>Rp {{ number_format($item->total_trans + $item->total, 0, ',', '.') }}</td>
                        <td>{{$item->nama_user}}</td>
                        <td><a class="btn btn-outline-success" href="/admin/transaksi/detailTransaksi/{{$item->id_htrans}}">Lihat Detail</a></td>
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