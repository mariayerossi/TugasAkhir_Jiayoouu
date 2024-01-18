@extends('layouts.sidebarNavbar_tempat')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
@media (max-width: 768px) {
    .nav-tabs {
    overflow-x: auto;
    display: flex;
    flex-wrap: nowrap;
    -webkit-overflow-scrolling: touch;
}
}
</style>
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-5">Daftar Transaksi</h3>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="baru-tab" data-toggle="tab" href="#baru" role="tab" aria-controls="baru" aria-selected="true">Baru ({{$baru->count()}})</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="diterima-tab" data-toggle="tab" href="#diterima" role="tab" aria-controls="diterima" aria-selected="false">Konfirmasi ({{$diterima->count()}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="berlangsung-tab" data-toggle="tab" href="#berlangsung" role="tab" aria-controls="berlangsung" aria-selected="true">Berlangsung ({{$berlangsung->count()}})</a>
          </li>
        <li class="nav-item">
            <a class="nav-link" id="ditolak-tab" data-toggle="tab" href="#ditolak" role="tab" aria-controls="ditolak" aria-selected="false">Ditolak ({{$ditolak->count()}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">Selesai ({{$selesai->count()}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dibatalkan-tab" data-toggle="tab" href="#dibatalkan" role="tab" aria-controls="dibatalkan" aria-selected="false">Dibatalkan ({{$dibatalkan->count()}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dikomplain-tab" data-toggle="tab" href="#dikomplain" role="tab" aria-controls="dikomplain" aria-selected="false">Dikomplain ({{$dikomplain->count()}})</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="baru" role="tabpanel" aria-labelledby="baru-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$baru->isEmpty())
                                @foreach ($baru as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:rgb(239, 203, 0)">Menunggu</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="berlangsung" role="tabpanel" aria-labelledby="berlangsung-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                {{-- <th>Status</th> --}}
                                <th>Permintaan Extend</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$berlangsung->isEmpty())
                                @foreach ($berlangsung as $item)
                                    @php
                                        $extend = DB::table('extend_htrans')->where("fk_id_htrans","=",$item->id_htrans)->get();
                                    @endphp
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        {{-- <td><span style="color:rgb(255, 145, 0)">Berlangsung</span></td> --}}
                                        @if (!$extend->isEmpty())
                                            @if ($extend->first()->status_extend == "Menunggu")
                                                <td><span style="color:rgb(239, 203, 0)">{{$extend->first()->status_extend}}</span></td>
                                            @elseif ($extend->first()->status_extend == "Diterima")
                                                <td><span style="color:rgb(0, 145, 0)">{{$extend->first()->status_extend}}</span></td>
                                            @elseif ($extend->first()->status_extend == "Ditolak")
                                                <td><span style="color:red">{{$extend->first()->status_extend}}</span></td>
                                            @endif
                                        @else
                                            <td>Tidak ada 
                                                
                                            </td>
                                        @endif
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="diterima" role="tabpanel" aria-labelledby="diterima-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$diterima->isEmpty())
                                @foreach ($diterima as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:rgb(0, 145, 0)">Diterima dan Menunggu Konfirmasi Kedatangan</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="ditolak" role="tabpanel" aria-labelledby="ditolak-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$ditolak->isEmpty())
                                @foreach ($ditolak as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:red">Ditolak</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$selesai->isEmpty())
                                @foreach ($selesai as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:blue">Selesai</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="dibatalkan" role="tabpanel" aria-labelledby="dibatalkan-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$dibatalkan->isEmpty())
                                @foreach ($dibatalkan as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:red">Dibatalkan</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
        <div class="tab-pane fade" id="dikomplain" role="tabpanel" aria-labelledby="dikompalin-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$dikomplain->isEmpty())
                                @foreach ($dikomplain as $item)
                                    {{-- @php
                                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$item->fk_id_lapangan)->get()->first();
                                        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
                                        $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                    @endphp --}}
                                    <tr>
                                        <td style="display: flex; align-items: center;">
                                            <div class="square-image-container" style="margin-right: 10px;">
                                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                                            </div>
                                            {{$item->nama_lapangan}}
                                        </td>
                                        @php
                                            $tanggalAwal = $item->tanggal_trans;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
                                            $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
                                        @endphp
                                        <td>Dipesan oleh {{$item->nama_user}} ({{$item->telepon_user}}) pada {{$tanggalBaru}}</td>
                                        <td><span style="color:red">Dikomplain</span></td>
                                        <td><a href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            </div>
        </div>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection