@extends('layouts.sidebarNavbar_tempat')

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
.form_rusak {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1100;
    display: none;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    padding: 10px;
    border-radius: 5px;
    background-color:white;
}
@media (max-width: 767px) {
    /* Untuk layar dengan lebar maksimum 767px (tampilan mobile) */
    .form_rusak {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%; /* Menyebabkan lebar elemen menjadi 100% dari lebar layar */
        height: 100vh; /* Menyebabkan tinggi elemen menjadi 100% dari tinggi layar */
    }
}
</style>
@if (!$htrans->isEmpty())
@if ($htrans->first()->fk_id_tempat == Session::get("dataRole")->id_tempat)
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <h3 class="text-center mb-5">Detail Transaksi</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        @if ($htrans->first()->status_trans == "Berlangsung" || $htrans->first()->status_trans == "Selesai" || $htrans->first()->status_trans == "Dikomplain")
            <h6><b>Kode Transaksi: {{$htrans->first()->kode_trans}}</b></h6>
        @endif
    </div>
    @php
        $tanggalAwal = $htrans->first()->tanggal_trans;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
        $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
    @endphp
    <div class="d-flex justify-content-end mt-4 me-3">
        @if ($htrans->first()->status_trans == "Menunggu")
            <h6><b>Status: </b><b style="color:rgb(239, 203, 0)">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Diterima")
            <h6><b>Status: </b><b style="color:rgb(0, 145, 0)">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Ditolak")
            <h6><b>Status: </b><b style="color:red">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Dibatalkan")
            <h6><b>Status: </b><b style="color:red">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Dikomplain")
            <h6><b>Status: </b><b style="color:red">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Berlangsung")
            <h6><b>Status: </b><b style="color:rgb(255, 145, 0)">{{$htrans->first()->status_trans}}</b></h6>
        @elseif($htrans->first()->status_trans == "Selesai")
            <h6><b>Status: </b><b style="color:blue">{{$htrans->first()->status_trans}}</b></h6>
        @endif
    </div>
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
        <div class="">
            {{-- col-md-9 col-sm-12 --}}
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
                                            @php
                                                $cek = DB::table('kerusakan_alat')->where("fk_id_dtrans","=",$item->id_dtrans)->first();
                                            @endphp
                                            @if ($htrans->first()->status_trans == "Berlangsung" && $cek == null)
                                                <button class="ajukan btn btn-warning btn-sm" data-id="{{$item->id_dtrans}}">Ajukan Kerusakan</button>
                                            @elseif ($htrans->first()->status_trans == "Berlangsung" && $cek != null)
                                                <button disabled class="ajukan btn btn-warning btn-sm" data-id="{{$item->id_dtrans}}">Alat Olahraga Rusak</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="row form_rusak mb-5" data-id="{{$item->id_dtrans}}">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end me-3">
                                    <button class="close-chat-btn" data-id="{{$item->id_dtrans}}">&times;</button>
                                </div>
                                <form id="form{{$item->id_dtrans}}" action="/tempat/kerusakan/ajukanKerusakan" method="post" enctype="multipart/form-data" >
                                    @csrf
                                    <div class="d-flex justify-content-center">
                                        <h5><b>Form Ajukan Kerusakan</b></h5>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-warning" role="alert">
                                            <i class="bi bi-exclamation-circle"></i>Perhatikan! Jika Alat Olahraga rusak karena kesengajaan customer, customer diharuskan membayar ganti rugi sebesar yang telah ditentukan oleh pemilik alat olahraga. Ganti rugi ini nantinya diberikan kepada pemilik alat olahraga ketika pemilik mengambil alat miliknya.
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Ganti rugi yang harus dibayar customer: </h6>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2">
                                            <h6>Rp {{number_format($dataAlat->ganti_rugi_alat, 0, ',', '.')}}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Apakah ada unsur kesengajaan dalam kerusakan alat olahraga?</h6>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                            <input type="radio" class="btn-check" name="unsur" id="danger-outlined{{$item->id_dtrans}}" autocomplete="off" value="Ya">
                                            <label class="btn btn-outline-danger" for="danger-outlined{{$item->id_dtrans}}"><i class="bi bi-box2 me-2"></i>Ya, Disengaja</label>
            
                                            <input type="radio" class="btn-check" name="unsur" id="primary-outlined{{$item->id_dtrans}}" autocomplete="off" value="Tidak">
                                            <label class="btn btn-outline-primary" for="primary-outlined{{$item->id_dtrans}}"><i class="bi bi-justify-left me-2"></i></i>Tidak Disengaja</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Lampirkan Bukti <i class="bi bi-info-circle" data-toggle="tooltip" title="Lampirkan foto alat olahraga yang rusak"></i></h6>
                                            <span style="font-size: 14px">(.jpg,.png,.jpeg)</span>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                            <input type="file" class="form-control" name="file" data-id="{{$item->id_dtrans}}" accept=".jpg,.png,.jpeg">
                                        </div>
                                    </div>
                                    <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                                    <div class="d-flex justify-content-end">
                                        <button data-id="{{$item->id_dtrans}}" type="submit" class="rusak btn btn-success">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                                            @php
                                                $cek = DB::table('kerusakan_alat')->where("fk_id_dtrans","=",$item->id_dtrans)->first();
                                            @endphp
                                            @if ($htrans->first()->status_trans == "Berlangsung" && $cek == null)
                                                <button class="ajukan btn btn-warning btn-sm" data-id="{{$item->id_dtrans}}">Ajukan Kerusakan</button>
                                            @elseif ($htrans->first()->status_trans == "Berlangsung" && $cek != null)
                                                <button disabled class="ajukan btn btn-warning btn-sm" data-id="{{$item->id_dtrans}}">Alat Olahraga Rusak</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="row form_rusak mb-5" data-id="{{$item->id_dtrans}}">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end me-3">
                                    <button class="close-chat-btn" data-id="{{$item->id_dtrans}}">&times;</button>
                                </div>
                                <form id="form{{$item->id_dtrans}}" action="/tempat/kerusakan/ajukanKerusakan" method="post" enctype="multipart/form-data" >
                                    @csrf
                                    <div class="d-flex justify-content-center">
                                        <h5><b>Form Ajukan Kerusakan</b></h5>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="alert alert-warning" role="alert">
                                            <i class="bi bi-exclamation-circle"></i>Perhatikan! Jika Alat Olahraga rusak karena kesengajaan customer, customer diharuskan membayar ganti rugi sebesar yang telah ditentukan oleh pemilik alat olahraga. Ganti rugi ini nantinya diberikan kepada pemilik alat olahraga ketika pemilik mengambil alat miliknya.
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Ganti rugi yang harus dibayar customer: </h6>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2">
                                            <h6>Rp {{number_format($dataAlat->ganti_rugi_alat, 0, ',', '.')}}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Apakah ada unsur kesengajaan dalam kerusakan alat olahraga?</h6>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                            <input type="radio" class="btn-check" name="unsur" id="danger-outlined{{$item->id_dtrans}}" autocomplete="off" value="Ya">
                                            <label class="btn btn-outline-danger" for="danger-outlined{{$item->id_dtrans}}"><i class="bi bi-box2 me-2"></i>Ya, Disengaja</label>
            
                                            <input type="radio" class="btn-check" name="unsur" id="primary-outlined{{$item->id_dtrans}}" autocomplete="off" value="Tidak">
                                            <label class="btn btn-outline-primary" for="primary-outlined{{$item->id_dtrans}}"><i class="bi bi-justify-left me-2"></i></i>Tidak Disengaja</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-12 mt-2">
                                            <h6>Lampirkan Bukti <i class="bi bi-info-circle" data-toggle="tooltip" title="Lampirkan foto alat olahraga yang rusak"></i></h6>
                                            <span style="font-size: 14px">(.jpg,.png,.jpeg)</span>
                                        </div>
                                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                            <input type="file" class="form-control" name="file" data-id="{{$item->id_dtrans}}" accept=".jpg,.png,.jpeg">
                                        </div>
                                    </div>
                                    <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                                    <div class="d-flex justify-content-end">
                                        <button data-id="{{$item->id_dtrans}}" type="submit" class="rusak btn btn-success">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                    <input type="hidden" name="tanggal_sewa" value="{{$htrans->first()->tanggal_sewa}}">
                    <input type="hidden" name="jam_sewa" value="{{$htrans->first()->jam_sewa}}">
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
        @if (!$extend->isEmpty())
            <div id="extend">
                <hr style="height: 3px;
                border: none;
                background-color: black;">
            <h3 class="text-center mb-5 mt-5">Permintaan Perpanjangan Waktu Sewa</h3>

            <div class="d-flex justify-content-end mt-4 me-3">
                @if ($extend->first()->status_extend == "Menunggu")
                    <h6><b>Status: </b><b style="color:rgb(239, 203, 0)">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Diterima")
                    <h6><b>Status: </b><b style="color:rgb(0, 145, 0)">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Ditolak")
                    <h6><b>Status: </b><b style="color:red">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Dibatalkan")
                    <h6><b>Status: </b><b style="color:red">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Dikomplain")
                    <h6><b>Status: </b><b style="color:red">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Berlangsung")
                    <h6><b>Status: </b><b style="color:rgb(255, 145, 0)">{{$extend->first()->status_extend}}</b></h6>
                @elseif($extend->first()->status_extend == "Selesai")
                    <h6><b>Status: </b><b style="color:blue">{{$extend->first()->status_extend}}</b></h6>
                @endif
            </div>

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
                    <h6>Jam Sewa: {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->format('H:i') }} WIB - {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->addHours($extend->first()->durasi_extend)->format('H:i') }} WIB ({{$extend->first()->durasi_extend}} jam)</h6>
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

            <div class="row mt-5">
                <div class="">
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
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            {{-- @if ($extend->isEmpty())
                <form id="tambahWaktu" action="/tempat/transaksi/detailTambahWaktu" method="get">
                    @csrf
                    <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                    <input type="hidden" name="durasi" id="durasi_jam">
                    <button type="submit" class="btn btn-success me-3" onclick="showSweetAlert(this)">Extend Waktu Sewa</button>
                </form>
            @endif --}}
            @if (!$extend->isEmpty() && $extend->first()->status_extend != "Menunggu" || $extend->isEmpty())
                @if (!$dtrans->isEmpty())
                    {{-- <a href="/tempat/kerusakan/detailKerusakan/{{$htrans->first()->id_htrans}}" class="btn btn-warning me-2">Ajukan Kerusakan Alat</a> --}}
                    {{-- <button class="btn btn-warning me-2">Ajukan Kerusakan Alat</button> --}}
                @endif
                <form action="/tempat/transaksi/cetakNota" method="get">
                    @csrf
                    <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                    <button type="submit" class="btn btn-primary">Konfirmasi Selesai dan Cetak Nota</button>
                </form>
            @endif
        </div>
    @elseif ($htrans->first()->status_trans == "Selesai")
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
    @elseif ($htrans->first()->status_trans == "Dikomplain")
        <div class="komplain mt-4">
            <div class="card h-70">
                <div class="card-body">
                    <div class="row">
                        <!-- Gambar Alat -->
                        <div class="col-3">
                            <div class="square-image-container">
                                @if ($komplain->first()->status_komplain == "Menunggu")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                @elseif ($komplain->first()->status_komplain == "Diterima")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                                        <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5H3z"/>
                                        <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                                    </svg>
                                @elseif ($komplain->first()->status_komplain == "Ditolak")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
                                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Nama Alat -->
                        <div class="col-9 d-flex flex-column justify-content-center">
                            @if ($komplain->first()->status_komplain == "Menunggu")
                                <h5 class="card-title"><b>Customer telah mengajukan komplain yang menunggu konfirmasi admin</b></h5>
                                <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                            @elseif ($komplain->first()->status_komplain == "Ditolak")
                                <h5 class="card-title"><b>Komplain customer telah ditolak admin</b></h5>
                                <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                <p class="card-text">Alasan Ditolak: {{$komplain->first()->alasan_komplain}}</p>
                            @elseif ($komplain->first()->status_komplain == "Diterima")
                                <h5 class="card-title"><b>Komplain customer diterima admin</b></h5>
                                <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                <p class="card-text">Penanganan Komplain: {{$komplain->first()->penanganan_komplain}}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@if (!$extend->isEmpty())
    <button id="scrollDownButton" class="btn btn-primary floating-btn">Lihat Extend <i class="bi bi-arrow-down-circle"></i></button>
@endif
<script>
    function showSweetAlert(button) {
        event.preventDefault();
        swal({
            title: "Extend Waktu Sewa",
            text: "Masukkan durasi jam:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Durasi jam"
        }, function(inputValue){
            if (inputValue === false) return false;
            if (inputValue === "" || isNaN(inputValue) || parseInt(inputValue) <= 0) {
                swal.showInputError("Anda harus memasukkan durasi jam yang valid!");
                console.log(inputValue);
                return false;
            }
            document.querySelector(`#durasi_jam`).value = inputValue;
            document.querySelector(`#tambahWaktu`).submit();
        });

        setTimeout(function() {
            // Mengubah tipe input menjadi number setelah SweetAlert muncul
            var input = document.querySelector(".sweet-alert input");
            if (input) {
                input.type = "number";
            }
        }, 1);
    }
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

        $(".close-chat-btn").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            let transaksiId = $(this).data('id'); // Mengambil data-id dari tombol yang ditekan
            $(`.form_rusak[data-id=${transaksiId}]`).hide();
        });

        $(".ajukan").click(function(e) {
            e.preventDefault();
            let transaksiId = $(this).data('id'); // Mengambil data-id dari tombol yang ditekan
            $(`.form_rusak[data-id=${transaksiId}]`).show();
        });

        $(".rusak").click(function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            
            var formData = new FormData($("#form" + id)[0]);
            
            $.ajax({
                url: "/tempat/kerusakan/ajukanKerusakan",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Success!",
                            text: response.message,
                            type: "success",
                            timer: 5000,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    } else {
                        swal({
                            title: "Error!",
                            text: response.message,
                            type: "error",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });

            return false;
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
                            if (response.success) {
                                swal({
                                    title: "Success!",
                                    text: response.message,
                                    type: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                window.location.reload();
                            }
                            else {
                                swal({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
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
                    if (response.success) {
                        swal({
                            title: "Success!",
                            text: response.message,
                            type: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    }
                    else {
                        swal({
                            title: "Error!",
                            text: response.message,
                            type: "error",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    // If you send back errors in a known format, you can display them here
                    swal("Error!", status, "error");
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
                            if (response.success) {
                                swal({
                                    title: "Success!",
                                    text: response.message,
                                    type: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                window.location.reload();
                            }
                            else {
                                swal({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
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
                                swal({
                                    title: "Success!",
                                    text: response.message,
                                    type: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                window.location.reload();
                            }
                            else {
                                swal({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
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
@else
    <h1>Transaksi Tidak Tersedia!</h1>
@endif
@else
    <h1>Transaksi Tidak Tersedia!</h1>
@endif
@endsection