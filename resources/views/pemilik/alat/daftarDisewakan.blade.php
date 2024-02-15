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
    <h3 class="text-center mb-5">Daftar Transaksi Alat Olahraga</h3>

    <div class="card mt-3 mb-5 p-3">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
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
                            <tr>
                                <td>{{$item->kode_trans}}</td>
                                <td>{{$item->nama_alat}}</td>
                                @php
                                    $tanggalAwal2 = $item->tanggal_sewa;
                                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                    $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                                @endphp
                                <td>{{$tanggalBaru2}}</td>
                                <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                                <td>{{$item->durasi_sewa + $item->durasi_extend}} jam</td>
                                <td>Rp {{ number_format($item->subtotal_alat + $item->subtotal_extend, 0, ',', '.') }}</td>
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
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });

</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection