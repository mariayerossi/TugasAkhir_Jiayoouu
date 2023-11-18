@extends('layouts.sidebarNavbar_tempat')
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
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
.truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: block;
}
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    .square-image-container {
        width: 60px;
        height: 60px;
    }
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 90%;
        display: block;
    }
}
.tiny-card {
    width: 150px; /* Lebar tetap kartu */
    height: 50px; /* Tinggi tetap kartu */
}

.tiny-card .card-body {
    padding: 1px; /* Padding minimal */
}

.tiny-card .square-image-container img {
    height: 45px; /* Mengatur tinggi gambar agar sesuai dengan kartu */
    width: 45px;
}

.tiny-card .card-title {
    font-size: 10px; /* Ukuran font sangat kecil */
    margin-bottom: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.square-image-container2 {
    height: 45px;
    width: 45px;
    overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
}
.floating-btn {
    position: fixed;
    bottom: 20px;  /* Anda bisa menyesuaikan jarak dari bawah */
    right: 20px;   /* Anda bisa menyesuaikan jarak dari sisi kanan */
    z-index: 1000; /* Z-index tinggi agar button berada di atas elemen lainnya */
}

</style>
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Transaksi</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        @if ($htrans->first()->status_trans == "Berlangsung" || $htrans->first()->status_trans == "Selesai" || $htrans->first()->status_trans == "Dikomplain")
            <h6><b>Kode Transaksi: {{$htrans->first()->kode_trans}}</b></h6>
        @endif
    </div>
    @php
        $dataUser = DB::table('user')->where("id_user","=",$htrans->first()->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $htrans->first()->tanggal_trans;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
        $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
    @endphp
    <div class="row mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Disewa oleh: {{$dataUser->nama_user}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Transaksi: {{$tanggalBaru}}</h6>
        </div>
    </div>

    @php
        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
    @endphp

    <div class="row mb-5 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalBaru2}}</h6>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->format('H:i') }} WIB - {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->addHours($htrans->first()->durasi_sewa)->format('H:i') }} WIB</h6>
        </div>
    </div>
    
    <h5>Lapangan yang Disewa</h5>
    <a href="/tempat/lapangan/lihatDetailLapangan/{{$htrans->first()->fk_id_lapangan}}">
        <div class="card">
            <div class="card-body">
                <div class="row d-md-flex align-items-md-center">
                    <!-- Gambar -->
                    <div class="col-4">
                        <div class="square-image-container">
                            <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    
                    <!-- Nama -->
                    <div class="col-8">
                        <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                        <!-- Contoh detail lain: -->
                        <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
                        {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($htrans->first()->subtotal_lapangan, 0, ',', '.')}}</b></h5>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-9 col-sm-12">
            <h5>Alat Olahraga yang Disewa</h5>
            @if (!$dtrans->isEmpty())
                @foreach ($dtrans as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        $cekSendiri = DB::table("sewa_sendiri")->where("req_id_alat","=",$item->fk_id_alat)->get()->first();
                    @endphp
                    @if ($cekSendiri != null)
                        <a href="/tempat/alat/lihatDetail/{{$dataAlat->id_alat}}">
                            <div class="card h-70 mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Gambar Alat -->
                                        <div class="col-4">
                                            <div class="square-image-container">
                                                <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        
                                        <!-- Nama Alat -->
                                        <div class="col-8 d-flex flex-wrap align-items-center justify-content-between">
                                            <div>
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
                                            </div>
                                            @if ($htrans->first()->status_trans == "Berlangsung")
                                                <form id="uploadForm" enctype="multipart/form-data" action="/tempat/kerusakan/ajukanKerusakan" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                                                    <input type="hidden" name="unsur" id="unsurInput" value="">
                                                    <input type="hidden" name="file" id="fileInput" value="">
                                                    <button type="submit" class="ajukan btn btn-warning btn-sm">Ajukan Kerusakan</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @else
                        <a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">
                            <div class="card h-70 mb-3">
                                <div class="card-body">
                                    <div class="row d-md-flex align-items-md-center">
                                        <!-- Gambar Alat -->
                                        <div class="col-4">
                                            <div class="square-image-container">
                                                <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        
                                        <!-- Nama Alat -->
                                        <div class="col-8 d-flex flex-wrap align-items-center justify-content-between">
                                            <div>
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
                                            </div>
                                            @if ($htrans->first()->status_trans == "Berlangsung")
                                                <form id="uploadForm" enctype="multipart/form-data" action="/tempat/kerusakan/ajukanKerusakan" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                                                    <input type="hidden" name="unsur" id="unsurInput" value="">
                                                    <input type="hidden" name="file" id="fileInput" value="">
                                                    <button type="submit" class="ajukan btn btn-warning btn-sm">Ajukan Kerusakan</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            @else
                <p>(Tidak ada alat olahraga yang disewa)</p>
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($htrans->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
    </div>
    <hr>
    @if ($htrans->first()->status_trans == "Dibatalkan" || $htrans->first()->status_trans == "Ditolak")
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b style="color: red">Total: Rp {{number_format($htrans->first()->total_trans, 0, ',', '.')}}</b></h4>
        </div>
    @else 
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b>Total: Rp {{number_format($htrans->first()->total_trans, 0, ',', '.')}}</b></h4>
        </div>
    @endif

    @php
        $extend = DB::table('extend_htrans')
                ->where("fk_id_htrans","=",$htrans->first()->id_htrans)
                ->get();
    @endphp

    @if ($htrans->first()->status_trans == "Menunggu")
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <form id="tolakTransaksiForm" action="" method="post">
                @csrf
                <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                <input type="hidden" name="id_user" value="{{$htrans->first()->fk_id_user}}">
                <input type="hidden" name="saldo_user" value="{{$dataUser->saldo_user}}">
                <input type="hidden" name="total" value="{{$htrans->first()->total_trans}}">
                <button class="btn btn-danger me-3" id="tolakTrans" type="submit">Tolak</button>
            </form>
            <form id="terimaTransaksiForm" action="" method="post">
                @csrf
                <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                <button class="btn btn-success" id="terimaTrans" type="submit">Terima</button>
            </form>
        </div>
    @elseif ($htrans->first()->status_trans == "Diterima")
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <form id="konfirmasiDipakai" action="/tempat/transaksi/konfirmasiDipakai" method="post">
                @csrf
                <h6>Konfirmasi ketika customer datang dan menggunakan lapangan</h6>
                <div class="input-group">
                    <input type="text" name="kode" id="" class="form-control" placeholder="Masukkan kode transaksi...">
                    <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                    <input type="hidden" name="kode_htrans" value="{{$htrans->first()->kode_trans}}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </div>
            </form>
        </div>
        {{-- uda pilihan dibawah ini kudu minta konfirmasi customer --}}
        <div class="d-flex justify-content-end mt-5 me-3">
            <h6>Terjadi Kendala Dalam Booking?</h6>
        </div>
        <div class="d-flex justify-content-end me-3 mb-5">
            <form class="me-3" id="editTrans" action="/tempat/transaksi/tampilanEditTransaksi/{{$htrans->first()->id_htrans}}" method="get">
                @csrf
                <div class="input-group-append">
                    <button type="submit" class="btn btn-warning w-100">Edit Transaksi <i class="bi bi-pencil-square"></i></button>
                </div>
            </form>
            <form id="batalTrans" action="/tempat/transaksi/batalTrans" method="post">
                @csrf
                <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-danger w-100">Batalkan Transaksi <i class="bi bi-x-lg"></i></button>
                </div>
            </form>
        </div>
    {{-- ---------------------------------------------------------------------------------------------- --}}
    @elseif ($htrans->first()->status_trans == "Berlangsung")
        @php
            $extend = DB::table('extend_htrans')
                    ->where("fk_id_htrans","=",$htrans->first()->id_htrans)
                    ->get();
        @endphp
        @if (!$extend->isEmpty())
            <div id="extend">
                <hr style="height: 3px;
                border: none;
                background-color: black;">
            <h3 class="text-center mb-5 mt-5">Permintaan Perpanjangan Waktu Sewa</h3>
            @php
                $tanggalAwal4 = $extend->first()->tanggal_extend;
                $tanggalObjek4 = DateTime::createFromFormat('Y-m-d', $tanggalAwal4);
                $tanggalBaru4 = $tanggalObjek4->format('d-m-Y');
            @endphp

            <div class="row mb-5 mt-4">
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Tanggal Sewa: {{$tanggalBaru4}}</h6>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Jam Sewa: {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->format('H:i:s') }} WIB - {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->addHours($extend->first()->durasi_extend)->format('H:i:s') }} WIB ({{$extend->first()->durasi_extend}} jam)</h6>
                </div>
            </div>

            <h5>Lapangan yang Disewa</h5>
            <a href="/tempat/lapangan/lihatDetailLapangan/{{$htrans->first()->fk_id_lapangan}}">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-md-flex align-items-md-center">
                            <!-- Gambar -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama -->
                            <div class="col-8">
                                <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                                <!-- Contoh detail lain: -->
                                <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <div class="d-flex justify-content-end mt-3 me-3">
                <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_lapangan, 0, ',', '.')}}</b></h5>
            </div>

            @php
                $extendDtrans = DB::table('extend_dtrans')
                        ->where("fk_id_extend_htrans","=",$extend->first()->id_extend_htrans)
                        ->get();
            @endphp

            <div class="row mt-5">
                <div class="col-md-6 col-sm-12">
                    <h5>Alat Olahraga yang Disewa</h5>
                    @if (!$dtrans->isEmpty())
                        @foreach ($dtrans as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                                $cekSendiri = DB::table("sewa_sendiri")->where("req_id_alat","=",$item->fk_id_alat)->get()->first();
                            @endphp
                            @if ($cekSendiri != null)
                                <a href="/tempat/alat/lihatDetail/{{$dataAlat->id_alat}}">
                                    <div class="card h-70 mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Gambar Alat -->
                                                <div class="col-4">
                                                    <div class="square-image-container">
                                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                                
                                                <!-- Nama Alat -->
                                                <div class="col-8">
                                                    <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                    <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">
                                    <div class="card h-70 mb-3">
                                        <div class="card-body">
                                            <div class="row d-md-flex align-items-md-center">
                                                <!-- Gambar Alat -->
                                                <div class="col-4">
                                                    <div class="square-image-container">
                                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                                
                                                <!-- Nama Alat -->
                                                <div class="col-8">
                                                    <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                    <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    @else
                        <p>(Tidak ada alat olahraga yang disewa)</p>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3 me-3">
                <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
            </div>
            <hr>
            @if ($extend->first()->status_extend == "Dibatalkan" || $extend->first()->status_extend == "Ditolak")
                <div class="d-flex justify-content-end mt-5 me-3">
                    <h4><b style="color: red">Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
                </div>
            @else 
                <div class="d-flex justify-content-end mt-5 me-3">
                    <h4><b>Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
                </div>
            @endif
            
            @if ($extend->first()->status_extend == "Menunggu")
                <div class="d-flex justify-content-end mt-5 me-3 mb-5">
                    {{-- button tolak --}}
                    <form id="tolakExtendForm" action="/tempat/extend/tolakExtend" method="post">
                        @csrf
                        <input type="hidden" name="saldo_user" value="{{$dataUser->saldo_user}}">
                        <input type="hidden" name="id_user" value="{{$dataUser->id_user}}">
                        <input type="hidden" name="total" value="{{$extend->first()->total}}">
                        <input type="hidden" name="id_extend" value="{{$extend->first()->id_extend_htrans}}">
                        <button type="submit" id="tolakExtend" class="btn btn-danger">Tolak</button>
                    </form>

                    {{-- button terima --}}
                    <form id="terimaExtendForm" action="/tempat/extend/terimaExtend" method="post" class="ms-2">
                        @csrf
                        <input type="hidden" name="id_extend" value="{{$extend->first()->id_extend_htrans}}">
                        <button type="submit" id="terimaExtend" class="btn btn-success">Terima</button>
                    </form>
                </div>
            @elseif ($extend->first()->status_extend == "Diterima")
                <div class="d-flex justify-content-end mt-5 me-3 mb-5">
                    <h5 style="color: rgb(0, 145, 0)">Extend Waktu telah diterima</h5>
                </div>
            @elseif ($extend->first()->status_extend == "Ditolak")
                <div class="d-flex justify-content-end mt-5 me-3 mb-5">
                    <h5 style="color: red">Extend Waktu telah ditolak</h5>
                </div>
            @endif
            </div>
        @endif
        @if (!$extend->isEmpty() && $extend->first()->status_extend != "Menunggu" || $extend->isEmpty())
            <div class="d-flex justify-content-end mt-5 me-3 mb-5">
                @if (!$dtrans->isEmpty())
                    {{-- <a href="/tempat/kerusakan/detailKerusakan/{{$htrans->first()->id_htrans}}" class="btn btn-warning me-2">Ajukan Kerusakan Alat</a> --}}
                    {{-- <button class="btn btn-warning me-2">Ajukan Kerusakan Alat</button> --}}
                @endif
                <form action="/tempat/transaksi/cetakNota" method="get">
                    @csrf
                    <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                    <button type="submit" class="btn btn-primary">Konfirmasi Selesai dan Cetak Nota</button>
                </form>
                {{-- klo cetak nota diprint, maka status htrans berubah selesai --}}
            </div>
        @endif
    @elseif ($htrans->first()->status_trans == "Selesai")
    @php
            $extend = DB::table('extend_htrans')
                    ->where("fk_id_htrans","=",$htrans->first()->id_htrans)
                    ->get();
        @endphp
        @if (!$extend->isEmpty())
            <div id="extend">
                <hr style="height: 3px;
                border: none;
                background-color: black;">
            <h3 class="text-center mb-5 mt-5">Permintaan Perpanjangan Waktu Sewa</h3>
            @php
                $tanggalAwal4 = $extend->first()->tanggal_extend;
                $tanggalObjek4 = DateTime::createFromFormat('Y-m-d', $tanggalAwal4);
                $tanggalBaru4 = $tanggalObjek4->format('d-m-Y');
            @endphp

            <div class="row mb-5 mt-4">
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Tanggal Sewa: {{$tanggalBaru4}}</h6>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Jam Sewa: {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->format('H:i:s') }} WIB - {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->addHours($extend->first()->durasi_extend)->format('H:i:s') }} WIB ({{$extend->first()->durasi_extend}} jam)</h6>
                </div>
            </div>

            <h5>Lapangan yang Disewa</h5>
            <a href="/tempat/lapangan/lihatDetailLapangan/{{$htrans->first()->fk_id_lapangan}}">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-md-flex align-items-md-center">
                            <!-- Gambar -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama -->
                            <div class="col-8">
                                <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                                <!-- Contoh detail lain: -->
                                <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <div class="d-flex justify-content-end mt-3 me-3">
                <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_lapangan, 0, ',', '.')}}</b></h5>
            </div>

            @php
                $extendDtrans = DB::table('extend_dtrans')
                        ->where("fk_id_extend_htrans","=",$extend->first()->id_extend_htrans)
                        ->get();
            @endphp

            <div class="row mt-5">
                <div class="col-md-6 col-sm-12">
                    <h5>Alat Olahraga yang Disewa</h5>
                    @if (!$dtrans->isEmpty())
                        @foreach ($dtrans as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                                $cekSendiri = DB::table("sewa_sendiri")->where("req_id_alat","=",$item->fk_id_alat)->get()->first();
                            @endphp
                            @if ($cekSendiri != null)
                                <a href="/tempat/alat/lihatDetail/{{$dataAlat->id_alat}}">
                                    <div class="card h-70 mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Gambar Alat -->
                                                <div class="col-4">
                                                    <div class="square-image-container">
                                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                                
                                                <!-- Nama Alat -->
                                                <div class="col-8">
                                                    <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                    <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">
                                    <div class="card h-70 mb-3">
                                        <div class="card-body">
                                            <div class="row d-md-flex align-items-md-center">
                                                <!-- Gambar Alat -->
                                                <div class="col-4">
                                                    <div class="square-image-container">
                                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                                
                                                <!-- Nama Alat -->
                                                <div class="col-8">
                                                    <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                    <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    @else
                        <p>(Tidak ada alat olahraga yang disewa)</p>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3 me-3">
                <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
            </div>
            <hr>
            @if ($extend->first()->status_extend == "Dibatalkan" || $extend->first()->status_extend == "Ditolak")
                <div class="d-flex justify-content-end mt-5 me-3">
                    <h4><b style="color: red">Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
                </div>
            @else 
                <div class="d-flex justify-content-end mt-5 me-3">
                    <h4><b>Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
                </div>
            @endif
        @endif
    @endif
</div>
@if (!$extend->isEmpty())
    <button id="scrollDownButton" class="btn btn-primary floating-btn">Lihat Extend <i class="bi bi-arrow-down-circle"></i></button>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var scrollDownButton = document.getElementById('scrollDownButton');

        if (scrollDownButton) {  // Cek apakah elemen ada
            scrollDownButton.addEventListener('click', function() {
                var extendElement = document.getElementById('extend');

                // Menggunakan 'scrollIntoView' untuk menggulung ke elemen
                extendElement.scrollIntoView({
                    behavior: 'smooth'
                });
            });
        }
    });
    $(document).ready(function() {
        $("#terimaTrans").click(function(event) {
            console.log("klik");
            event.preventDefault(); // Mencegah perilaku default form
    
            var formData = $("#terimaTransaksiForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/tempat/transaksi/terimaTransaksi",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: 'Success!',
                            text: response.message,
                            type: 'success',
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000); // Setelah 5 detik
                    } else if (!response.success) {
                        swal({
                            title: 'Error!',
                            text: response.message,
                            type: 'error',
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });
        });

        $(".ajukan").click(async function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            const inputOptions = new Promise((resolve) => {
                resolve({
                    'Ya': 'Ya, Disengaja',
                    'Tidak': 'Tidak Sengaja'
                });
                });

                let unsur;
                let uploadedFile;

                await Swal.fire({
                title: 'Apakah terdapat unsur kesengajaan dalam kerusakan alat olahraga?',
                input: 'radio',
                inputOptions: inputOptions,
                inputValidator: (value) => {
                    if (!value) {
                    return 'Anda harus memasukan unsur kesengajaan!';
                    }
                },
                theme: 'light'
                }).then((result) => {
                unsur = result.value;
                }).then(async () => {
                const { value: file } = await Swal.fire({
                    title: 'Lampirkan Bukti Foto (.jpg/.png/.jpeg)',
                    input: 'file',
                    inputAttributes: {
                    'accept': 'image/*',
                    'aria-label': 'Tambahkan Bukti Foto yang Valid'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                        return 'Anda harus memasukan Bukti Foto!';
                        }
                    },
                    theme: 'light'
                });

                // if (file) {
                //     const reader = new FileReader();
                //     reader.onload = (e) => {
                //         Swal.fire({
                //             title: 'Your uploaded picture',
                //             imageUrl: e.target.result,
                //             imageAlt: 'The uploaded picture'
                //         });
                //     };
                //     reader.readAsDataURL(file);
                // }

                // Set the values of the hidden inputs in the form
                console.log(file);
                document.getElementById('unsurInput').value = unsur;
                document.getElementById('fileInput').value = file ? file.name : '';
                console.log(document.getElementById('unsurInput').value);

                // Submit the form to the server
                document.getElementById('uploadForm').submit();
            });

        });
        
        $("#tolakTrans").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            swal({
                title: "Apakah Anda yakin?",
                text: "Anda akan menolak transaksi ini.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Tolak!",
                cancelButtonText: "Tidak!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    // Jika user mengklik "Ya", submit form
                    var formData = $("#tolakTransaksiForm").serialize(); // Mengambil data dari form
    
                    $.ajax({
                        url: "/tempat/transaksi/tolakTransaksi",
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            window.location.reload();
                            // alert('Berhasil Diterima!');
                            // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                            // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                        }
                    });
                } else {
                    swal.close(); // Tutup SweetAlert jika user memilih "Tidak"
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $('#konfirmasiDipakai').on('submit', function(event) {
            event.preventDefault();  // Prevent form from submitting the default way
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Handle success. 
                    // You can update the frontend or show a success message here
                    window.location.reload();
                    swal("Success!", "Berhasil mengkonfirmasi transaksi!", "success");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    // If you send back errors in a known format, you can display them here
                    swal("Error!", "Gagal mengkonfirmasi transaksi!", "error");
                }
            });
        });

        $("#terimaExtendForm").on('submit', function(event) {
            event.preventDefault(); // Mencegah perilaku default form
    
            var formData = $("#terimaExtendForm").serialize(); // Mengambil data dari form
            // console.log(formData)
            
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: 'Success!',
                            text: response.message,
                            type: 'success',
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000); // Setelah 5 detik
                    } else if (!response.success) {
                        swal({
                            title: 'Error!',
                            text: response.message,
                            type: 'error',
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        });

        $("#tolakExtendForm").on('submit', function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            swal({
                title: "Apakah Anda yakin?",
                text: "Anda akan menolak extend waktu ini.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Tolak!",
                cancelButtonText: "Tidak!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    // Jika user mengklik "Ya", submit form
                    var formData = $("#tolakExtendForm").serialize(); // Mengambil data dari form
                    // console.log(formData)
                    
                    $.ajax({
                        url: "/tempat/extend/tolakExtend",
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            window.location.reload();
                            // alert('Berhasil Diterima!');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                } else {
                    swal.close(); // Tutup SweetAlert jika user memilih "Tidak"
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $("#batalTrans").on('submit', function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            swal({
                title: "Apakah anda yakin?",
                text: "Pembatalan booking akan dikenakan kompensasi sebesar 10% dari total transaksi.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batalkan",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var formData = $("#batalTrans").serialize(); // Mengambil data dari form
    
                    $.ajax({
                        url: "/tempat/transaksi/batalTrans",
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                swal("Success!", response.message, "success");
                            }
                            else {
                                swal("Error!", response.message, "error");
                            }
                            window.location.reload();
                            // alert('Berhasil Diterima!');
                            // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                            // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                        }
                    });
                }
            });
        });

        // $(".form_rusak").hide();
        // $(".btn-warning").click(function(e) {
        //     e.preventDefault();  // Menghentikan perilaku default (navigasi)
        //     $(`.form_rusak`).show();
        // });
    });
</script>    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection