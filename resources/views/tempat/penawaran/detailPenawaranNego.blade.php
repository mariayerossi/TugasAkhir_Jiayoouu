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
}
#chat-popup {
    display: none;
    position: fixed;
    bottom: 10px;
    right: 20px;
    width: 300px;
    background-color: #f5f5f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    z-index: 999;
}

.chat-header {
    background-color: #007466;
    color: #fff;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-body {
    max-height: 50vh;
    overflow-y: auto;
    padding: 10px;
}

.chat-footer {
    padding: 10px;
}
.form_komplain {
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
    .form_komplain {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%; /* Menyebabkan lebar elemen menjadi 100% dari lebar layar */
        height: 100vh; /* Menyebabkan tinggi elemen menjadi 100% dari tinggi layar */
    }
}
</style>
@if (!$penawaran->isEmpty())
@if ($penawaran->first()->fk_id_tempat == Session::get("dataRole")->id_tempat)
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <h3 class="text-center">Pengajuan Penawaran Alat</h3>
    @php
        $tanggalAwal1 = $penawaran->first()->tanggal_tawar;
        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
        $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
        $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');
    @endphp

    <div class="d-flex justify-content-end mt-4 me-3">
        @if ($penawaran->first()->status_penawaran == "Menunggu")
            <h6><b>Status: </b><b style="color:rgb(239, 203, 0)">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Diterima")
            <h6><b>Status: </b><b style="color:rgb(0, 145, 0)">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Ditolak")
            <h6><b>Status: </b><b style="color:red">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Dibatalkan")
            <h6><b>Status: </b><b style="color:red">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Dikomplain")
            <h6><b>Status: </b><b style="color:red">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Disewakan")
            <h6><b>Status: </b><b style="color:rgb(0, 145, 0)">{{$penawaran->first()->status_penawaran}}</b></h6>
        @elseif($penawaran->first()->status_penawaran == "Selesai")
            <h6><b>Status: </b><b style="color:blue">{{$penawaran->first()->status_penawaran}}</b></h6>
        @endif
    </div>

    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$dataPemilik->nama_pemilik}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru1}}</h6>
        </div>
    </div>
    <div class="row mb-5">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Ditawarkan <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang ditawarkan oleh pemilik alat"></i></h5>
            @if ($dataAlat->deleted_at == null)<a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">@endif
                <div class="card h-70">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Alat -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama Alat -->
                            <div class="col-8 d-flex flex-column justify-content-center">
                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                <p class="card-text">Komisi: Rp {{number_format($dataAlat->komisi_alat, 0, ',', '.')}}/jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Detail Lapangan -->
        <div class="col-md-6 col-sm-12">
            <h5>Lokasi Penggunaan Alat</h5>
            @if ($dataLapangan->deleted_at == null)<a href="/tempat/lapangan/lihatDetailLapangan/{{$dataLapangan->id_lapangan}}">@endif
                <div class="card h-70">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Lapangan -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama Lapangan -->
                            <div class="col-8 d-flex align-items-center">
                                <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @if ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == null)
    <form id="terimaForm" action="/tempat/penawaran/terimaPenawaran" method="post">
        @csrf
    @endif
        <div class="row mb-3 mt-3">
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Permintaan Harga Sewa: <i class="bi bi-info-circle" data-toggle="tooltip" title="Biaya sewa per jam yang harus dibayar pelanggan saat menyewa alat (*sudah termasuk komisi pemilik dan pihak pengelola tempat)"></i></h6>
                @if ($penawaran->first()->req_harga_sewa != null && $penawaran->first()->status_penawaran != "Menunggu")
                    <p>Rp {{number_format($penawaran->first()->req_harga_sewa, 0, ',', '.')}}/jam</p>
                @elseif ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == null)
                    <div class="input-group">
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="hidden" name="status_penawaran" value="{{$penawaran->first()->status_penawaran}}">
                        {{-- <input type="text" class="form-control" min="0" name="harga_sewa" oninput="formatNumber(this)" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}"> --}}
                        <!-- Input yang terlihat oleh pengguna -->
                        <input type="text" class="form-control" placeholder="Masukkan Harga Sewa" oninput="formatNumber(this)" value="{{number_format(old('harga_sewa') ?? $penawaran->first()->req_harga_sewa, 0, ',', '.')}}">

                        <!-- Input tersembunyi untuk kirim ke server -->
                        <input type="hidden" name="harga_sewa" id="actual" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}">
                        {{-- <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Edit Harga</button>
                        </div> --}}
                    </div>
                @elseif ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == "Setuju")
                    <form id="editHargaForm" action="/tempat/penawaran/editHargaSewa" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="hidden" name="komisi" value="{{$dataAlat->komisi_alat}}">
                            <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                            <input type="hidden" name="status_penawaran" value="{{$penawaran->first()->status_penawaran}}">
                            {{-- <input type="text" class="form-control" min="0" name="harga_sewa" oninput="formatNumber(this)" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}"> --}}
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="text" class="form-control" placeholder="Masukkan Harga Sewa" oninput="formatNumber(this)" value="{{number_format(old('harga_sewa') ?? $penawaran->first()->req_harga_sewa, 0, ',', '.')}}">

                            <!-- Input tersembunyi untuk kirim ke server -->
                            <input type="hidden" name="harga_sewa" id="actual" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary" id="editHarga">Edit Harga</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            <div class="col-md-6 col-sm-12 mb-3">
            </div>

            @if ($penawaran->first()->req_tanggal_mulai != null && $penawaran->first()->req_tanggal_selesai != null && $penawaran->first()->status_penawaran != "Menunggu")
                <div class="row mb-3 mt-3">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <h6>Tanggal Mulai Dipinjam:</h6>
                        @php
                            $tanggalAwal2 = $penawaran->first()->req_tanggal_mulai;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                            $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                            $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
                        @endphp
                        <p>{{$tanggalBaru2}}</p>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-3">
                        <h6>Tanggal Selesai Dipinjam:</h6>
                        @php
                            $tanggalAwal3 = $penawaran->first()->req_tanggal_selesai;
                            $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                            $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
                            $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY');
                        @endphp
                        <p>{{$tanggalBaru3}}</p>
                    </div>
                </div>
            @elseif ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == null)
                <div class="row mb-3 mt-3">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <h6>Durasi Pinjam:</h6>
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="hidden" name="status_penawaran" value="{{$penawaran->first()->status_penawaran}}">
                        <input type="text" class="form-control" name="durasi_pinjam" value="{{old('durasi_pinjam')}}" />
                    </div>
                </div>
            @elseif ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == "Setuju")
                <div class="row mb-3 mt-3">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <h6>Tanggal Mulai Dipinjam:</h6>
                        <form id="editMulaiForm" action="/tempat/penawaran/editTanggalMulai" method="post">
                            @csrf
                            <div class="input-group">
                                <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                                <input type="hidden" name="status_penawaran" value="{{$penawaran->first()->status_penawaran}}">
                                {{-- <input type="text" class="form-control" min="0" name="harga_sewa" oninput="formatNumber(this)" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}"> --}}
                                <!-- Input yang terlihat oleh pengguna -->
                                <input type="date" class="form-control" name="tanggal_mulai" id="" value="{{$penawaran->first()->req_tanggal_mulai}}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" id="editMulai">Edit Tanggal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-3">
                        <h6>Tanggal Selesai Dipinjam:</h6>
                        <form id="editSelesaiForm" action="/tempat/penawaran/editSelesaiMulai" method="post">
                            @csrf
                            <div class="input-group">
                                <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                                <input type="hidden" name="status_penawaran" value="{{$penawaran->first()->status_penawaran}}">
                                {{-- <input type="text" class="form-control" min="0" name="harga_sewa" oninput="formatNumber(this)" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}"> --}}
                                <!-- Input yang terlihat oleh pengguna -->
                                <input type="date" class="form-control" name="tanggal_selesai" id="" value="{{$penawaran->first()->req_tanggal_selesai}}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" id="editSelesai">Edit Tanggal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @if ($penawaran->first()->status_penawaran == "Menunggu")
        <div class="container">
            <div class="row justify-content-end mb-3">
                <div class="col-12 col-md-6">
                        <input type="hidden" name="komisi" value="{{$dataAlat->komisi_alat}}">
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <hr>
                        @if ($penawaran->first()->status_tempat != "Setuju")
                            <span style="font-size: 14px">Silahkan konfirmasi dan terima penawaran yang diajukan pemilik</span>
                            <button type="submit" id="terima" class="btn btn-success w-100">Terima</button>
                        @else
                            <span style="font-size: 14px">Anda telah menerima penawaran, tunggu konfirmasi detail dari pemilik alat olahraga</span>
                            <button type="submit" disabled class="btn btn-success w-100">Terima</button>
                        @endif
                    <hr>
                </div>
            </div>
    </form>
            <div class="row justify-content-end">
                <div class="col-12 col-md-3 mb-3">
                    <a href="" class="btn btn-secondary w-100">Negosiasi</a>
                </div>
                <div class="col-12 col-md-3">
                    <form id="tolakForm" action="/tempat/penawaran/tolakPenawaran" method="post">
                        @csrf
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <button type="submit" id="tolak" class="btn btn-danger w-100">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($penawaran->first()->status_penawaran == "Selesai" && $penawaran->first()->status_alat == null)
        {{-- konfirmasi alat telah diantar oleh pemilik ke pihak tempat --}}
        <p>Konfirmasi pengembalian alat olahraga</p>
        @if ($penawaran->first()->kode_selesai != null)
            <button class="btn btn-primary" onclick="generateCodeSelesai()" disabled>Konfirmasi</button>
            <div class="kode mt-3 mb-4">
                <h5><b>{{$penawaran->first()->kode_selesai}}</b></h5>
                <p>Berikan kode ini kepada pemilik alat olahraga untuk mengkonfirmasi bahwa alat olahraga telah dikembalikan</p>
            </div>
        @else
            <button class="btn btn-primary" onclick="generateCodeSelesai()">Konfirmasi</button>
            <div class="kode mt-3 mb-4">

            </div>
        @endif
    @elseif ($penawaran->first()->status_penawaran == "Selesai" && $penawaran->first()->status_alat == "Dikembalikan")
        <h5 class="text-center">Alat Olahraga Telah Dikembalikan kepada Pemilik Alat Olahraga</h5>
    @endif

    @if ($penawaran->first()->status_penawaran == "Diterima")
        {{-- konfirmasi alat telah diantar oleh pemilik ke pihak tempat --}}
        <p>Sudah menerima alat olahraga? Silahkan Konfirmasi penerimaan alat</p>
        @if ($penawaran->first()->kode_mulai != null)
            <button class="btn btn-primary" onclick="generateCode()" disabled>Konfirmasi</button>
            <div class="kode mt-3 mb-4">
                <h5><b>{{$penawaran->first()->kode_mulai}}</b></h5>
                <p>Berikan kode ini kepada pemilik alat olahraga untuk mengkonfirmasi bahwa alat olahraga mulai disewakan</p>
            </div>
        @else
            <button class="btn btn-primary" onclick="generateCode()">Konfirmasi</button>
            <div class="kode mt-3 mb-4">

            </div>
        @endif 

        <hr>
        
        @if ($komplain->isEmpty() && $penawaran->first()->status_penawaran == "Diterima")
            <button class="btn btn-warning">Ajukan Komplain</button>

            <div class="row form_komplain mb-5">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end me-3">
                        <button class="close-chat-btn2">&times;</button>
                    </div>
                    <form id="komplainForm" action="/tempat/komplain/tambahKomplain" method="post" class="mt-3" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Form Ajukan Komplain</b></h5>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jenis Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="radio" class="btn-check" name="jenis" id="info-outlined" autocomplete="off" value="Alat tidak sesuai">
                                <label class="btn btn-outline-info" for="info-outlined"><i class="bi bi-box-seam me-2"></i>Alat tidak sesuai</label>

                                <input type="radio" class="btn-check" name="jenis" id="danger-outlined" autocomplete="off" value="Alat rusak">
                                <label class="btn btn-outline-danger" for="danger-outlined"><i class="bi bi-heartbreak me-2"></i>Alat Rusak</label>

                                <input type="radio" class="btn-check" name="jenis" id="primary-outlined" autocomplete="off" value="Lainnya">
                                <label class="btn btn-outline-primary" for="primary-outlined"><i class="bi bi-justify-left me-2"></i></i>Lainnya</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jelaskan Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <textarea id="myTextarea" class="form-control" name="keterangan" rows="4" cols="50" onkeyup="updateCount()" placeholder="Masukkan Keterangan Komplain">{{ old('keterangan') }}</textarea>
                                <p id="charCount">0/500</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Lampirkan Bukti <i class="bi bi-info-circle" data-toggle="tooltip" title="Lampirkan bukti komplain yang dapat memperkuat pernyataan"></i></h6>
                                <span style="font-size: 14px">lampirkan 2 file sekaligus</span>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="file" class="form-control" name="foto[]" multiple accept=".jpg,.png,.jpeg">
                            </div>
                        </div>
                        <input type="hidden" name="fk_id_request" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="hidden" name="jenis_request" value="Penawaran">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success" id="komplain">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif

    @if (!$komplain->isEmpty())
        @if ($komplain->first()->fk_id_tempat != null)
            <div class="komplain">
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
                                    <h5 class="card-title"><b>Komplain Anda telah dikirim dan menunggu konfirmasi admin!</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                    <p class="card-text">Keterangan Komplain: {{$komplain->first()->keterangan_komplain}}</p>
                                @elseif ($komplain->first()->status_komplain == "Ditolak")
                                    <h5 class="card-title"><b>Maaf, Komplain Anda telah ditolak admin!</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                    <p class="card-text">Keterangan Komplain: {{$komplain->first()->keterangan_komplain}}</p>
                                    <p class="card-text">Alasan Penolakan: {{$komplain->first()->alasan_komplain}}</p>
                                @elseif ($komplain->first()->status_komplain == "Diterima")
                                    <h5 class="card-title"><b>Yeay, Komplain Anda telah diterima admin!</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                    <p class="card-text">Keterangan Komplain: {{$komplain->first()->keterangan_komplain}}</p>
                                    <p class="card-text">Penanganan Komplain: {{$komplain->first()->penanganan_komplain}}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($komplain->first()->fk_id_pemilik != null)
            <div class="komplain">
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
                                    <h5 class="card-title"><b>Pemilik alat olahraga telah mengajukan komplain yang menunggu konfirmasi admin</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                @elseif ($komplain->first()->status_komplain == "Ditolak")
                                    <h5 class="card-title"><b>Komplain pemilik alat telah ditolak admin</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                    <p class="card-text">Alasan Ditolak: {{$komplain->first()->alasan_komplain}}</p>
                                @elseif ($komplain->first()->status_komplain == "Diterima")
                                    <h5 class="card-title"><b>Komplain pemilik alat diterima admin</b></h5>
                                    <p class="card-text">Jenis Komplain: {{$komplain->first()->jenis_komplain}}</p>
                                    <p class="card-text">Penanganan Komplain: {{$komplain->first()->penanganan_komplain}}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
<div id="chat-popup" class="chat-popup">
    <div class="chat-header">
        <h3>Negosiasi</h3>
        <button id="close-chat-btn" class="close-chat-btn">&times;</button>
    </div>
    <div class="chat-body">
        <!-- Chat messages go here -->
        <div class="history">
            @if (!$nego->isEmpty())
                @foreach ($nego as $item)
                    <div class="card mb-4">
                        <div class="card-body">
                            @if ($item->fk_id_pemilik != null)
                                @php
                                    $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                                @endphp
                                <h5><strong>{{$dataPemilik->nama_pemilik}}</strong></h5>
                            @elseif ($item->fk_id_tempat != null)
                                @php
                                    $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                                @endphp
                                <h5><strong>{{$dataTempat->nama_tempat}}</strong></h5>
                            @endif
                            @php
                                $tanggalAwal3 = $item->waktu_negosiasi;
                                $tanggalObjek3 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal3);
                                $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
                                $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY HH:mm:ss');
                            @endphp
                            <p style="font-size: 14px">{{$tanggalBaru3}}</p>
                            <p class="mt-2">{{$item->isi_negosiasi}}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="chat-footer">
        <form action="/tempat/penawaran/negosiasi/tambahNego" method="post">
            @csrf
            <input type="hidden" name="penawaran" value="{{$penawaran->first()->id_penawaran}}">
            <textarea class="form-control mb-3" rows="2" name="isi" placeholder="Tulis pesan Anda di sini..."></textarea>
            <button id="send-nego-btn" class="btn w-100" style="background-color: #007466; color:white;">Kirim</button>
        </form>
    </div>
</div>
<script>
    $(function() {
        $('input[name="durasi_pinjam"]').daterangepicker({
            startDate: moment().add(2, 'day'),
            endDate: moment().add(6, 'day'),
            locale: {
            format: 'DD/MM/YYYY'
            }
        });
    });
    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('actual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('actual').value = '';
        }
    }
    function generateCode() {
        const currentDate = new Date();
        const month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        const day = ("0" + currentDate.getDate()).slice(-2);
        const formattedDate = `${currentDate.getFullYear()}${month}${day}`;
        // Nomor urut (misalnya Anda bisa gunakan timestamp atau counter untuk ini)
        const sequenceNumber = currentDate.getTime(); // Contoh ini menggunakan timestamp, Anda bisa menggantinya dengan sistem nomor urut yang Anda inginkan.

        const code = `REQTM${formattedDate}<?=$penawaran->first()->id_penawaran;?>`;
        const kodeElement = document.querySelector('.kode');
        kodeElement.innerHTML = `<h5><b>${code}</b></h5> <br><p>Berikan kode ini kepada pemilik alat olahraga untuk mengkonfirmasi bahwa alat olahraga mulai disewakan</p>`;
        // kodeElement.style.fontWeight = 'bold';

        // Kirim kode ke server:
        fetch('/tempat/penawaran/simpanKodeMulai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode: code, id: <?= $penawaran->first()->id_penawaran ?> })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });

        const buttonElement = document.querySelector('.btn.btn-primary');
        buttonElement.disabled = true;
    }

    function generateCodeSelesai() {
        const currentDate = new Date();
        const month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        const day = ("0" + currentDate.getDate()).slice(-2);
        const formattedDate = `${currentDate.getFullYear()}${month}${day}`;
        // Nomor urut (misalnya Anda bisa gunakan timestamp atau counter untuk ini)
        const sequenceNumber = currentDate.getTime(); // Contoh ini menggunakan timestamp, Anda bisa menggantinya dengan sistem nomor urut yang Anda inginkan.

        const code = `REQMS${formattedDate}<?=$penawaran->first()->id_penawaran;?>`;
        const kodeElement = document.querySelector('.kode');
        kodeElement.innerHTML = `<h5><b>${code}</b></h5> <br><p>Berikan kode ini kepada pemilik alat olahraga untuk mengkonfirmasi bahwa alat olahraga telah dikembalikan</p>`;
        // kodeElement.style.fontWeight = 'bold';

        // Kirim kode ke server:
        fetch('/tempat/penawaran/simpanKodeSelesai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode: code, id: <?= $penawaran->first()->id_penawaran ?> })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });

        const buttonElement = document.querySelector('.btn.btn-primary');
        buttonElement.disabled = true;
    }
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $(".close-chat-btn2").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            $(`.form_komplain`).hide();
        });
        
        @if($penawaran->first()->status_penawaran == "Menunggu")
            @if($nego->isEmpty())
                $("#chat-popup").hide();
            @elseif (!$nego->isEmpty())
                if ($(window).width() <= 768) {
                    $("#chat-popup").hide();
                } else {
                    $("#chat-popup").show();
                    $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);
                }
            @endif
        @endif

        @if($komplain->isEmpty())
        // Menyembunyikan div nego saat halaman pertama kali dimuat
            $(".form_komplain").hide();
        @endif

        // Mengatur event ketika tombol Negosiasi diklik
        $(".btn-secondary").click(function(e) {
            e.preventDefault();
            $("#chat-popup").show();
            $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);
        });

        // Close the chat pop-up
        $("#close-chat-btn").click(function() {
            $("#chat-popup").hide();
        });

        $(".btn-warning").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            $(".form_komplain").show();   // Menampilkan div nego
        });
        
        $("#tolak").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            swal({
                title: "Apakah Anda yakin?",
                text: "Anda akan menolak penawaran ini.",
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
                    var formData = $("#tolakForm").serialize(); // Mengambil data dari form
    
                    $.ajax({
                        url: "/tempat/penawaran/tolakPenawaran",
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
                            // window.location.reload();
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

        $("#terima").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = $("#terimaForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/tempat/penawaran/terimaPenawaran",
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
                    // window.location.reload();
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $("#komplain").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = new FormData($("#komplainForm")[0]);

            $.ajax({
                url: "/tempat/komplain/tambahKomplain",
                type: "POST",
                data: formData,
                processData: false,  // Important: Don't process the data
                contentType: false,
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
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $("#editHarga").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = new FormData($("#editHargaForm")[0]);

            $.ajax({
                url: "/tempat/penawaran/editHargaSewa",
                type: "POST",
                data: formData,
                processData: false,  // Important: Don't process the data
                contentType: false,
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
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $("#editMulai").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = new FormData($("#editMulaiForm")[0]);

            $.ajax({
                url: "/tempat/penawaran/editTanggalMulai",
                type: "POST",
                data: formData,
                processData: false,  // Important: Don't process the data
                contentType: false,
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
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });

        $("#editSelesai").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = new FormData($("#editSelesaiForm")[0]);

            $.ajax({
                url: "/tempat/penawaran/editTanggalSelesai",
                type: "POST",
                data: formData,
                processData: false,  // Important: Don't process the data
                contentType: false,
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
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });

            return false; // Mengembalikan false untuk mencegah submission form
        });
    });
    function updateCount() {
        let textarea = document.getElementById('myTextarea');
        let textareaValue = textarea.value;
        let charCount = textareaValue.length;
        let countElement = document.getElementById('charCount');

        if (charCount > 500) {
            // Potong teks untuk membatasi hanya 300 karakter
            textarea.value = textareaValue.substring(0, 500);
            charCount = 500;
            countElement.style.color = 'red';
        } else {
            countElement.style.color = 'black';
        }

        countElement.innerText = charCount + "/500";
    }
    $("form[action='/tempat/penawaran/negosiasi/tambahNego']").submit(function(e) {
        e.preventDefault(); // Menghentikan perilaku default (pengiriman form)

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {

                    // Menambahkan pesan ke dalam div history
                    let formattedTime = '<?php
                        date_default_timezone_set("Asia/Jakarta");
                        $tanggalAwal3 = date("Y-m-d H:i:s");
                        $tanggalObjek3 = DateTime::createFromFormat("Y-m-d H:i:s", $tanggalAwal3);
                        $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale("id");
                        echo $carbonDate3->isoFormat("D MMMM YYYY HH:mm:ss");
                    ?>';

                    let newMessage = `
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5><strong>${response.user}</strong></h5>
                                <p style="font-size: 14px">${formattedTime}</p>
                                <p class="mt-2">${response.data.isi}</p>
                            </div>
                        </div>
                    `;

                    $(".history").append(newMessage);
                    $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);
                } else {
                    alert("Terjadi kesalahan saat mengirim pesan.");
                }
                $("textarea[name='isi']").val('');  // Mengosongkan isian form setelah pesan berhasil dikirim
            }
        });
    });
    
</script>
@else
    <h1>Request Penawaran Tidak Tersedia!</h1>
@endif
@else
    <h1>Request Penawaran Tidak Tersedia!</h1>
@endif
@endsection