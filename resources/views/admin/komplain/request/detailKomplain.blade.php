@extends('layouts.sidebar_admin')

@section('content')
<style>
.card-custom {
    max-width: 80%; /* Ubah sesuai keinginan */
    margin: 0 auto; /* Pusatkan card */
}

.image-square {
    background-size: cover;
    background-position: center;
    width: 100%;
    padding-bottom: 100%;
    position: relative;
}
.image-square:hover {
    transform: scale(1.1); /* Zoom 10% */
    transition: transform .2s; /* Animasi smooth */
    cursor: zoom-in;
}
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
</style>
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <h3 class="text-center mb-4">Detail Komplain Request</h3>
    @php
        if ($komplain->first()->fk_id_permintaan != null) {
            $dataRequest = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first();
            $id_request = $dataRequest->id_permintaan;
            $tanggal_req = $dataRequest->tanggal_minta;
            $status = $dataRequest->status_permintaan;

            $id_tempat = $dataRequest->fk_id_tempat;
            $id_pemilik = $dataRequest->fk_id_pemilik;
            $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
        }
        else if ($komplain->first()->fk_id_penawaran != null) {
            $dataRequest = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first();
            $id_request = $dataRequest->id_penawaran;
            $tanggal_req = $dataRequest->tanggal_tawar;
            $status = $dataRequest->status_penawaran;

            $id_tempat = $dataRequest->fk_id_tempat;
            $id_pemilik = $dataRequest->fk_id_pemilik;
            $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
        }
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dataRequest->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();

        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataRequest->req_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataRequest->req_lapangan)->get()->first();
    @endphp

    <div class="d-flex justify-content-end me-3">
        @if ($komplain->first()->fk_id_permintaan != null)
            <h6><b>Jenis Request: Permintaan</b></h6>
        @else
            <h6><b>Jenis Request: Penawaran</b></h6>
        @endif
    </div>
    <div class="d-flex justify-content-end mt-1 me-3">
        @if ($komplain->first()->status_komplain == "Menunggu")
            <h6><b>Status Komplain: </b><b style="color:rgb(239, 203, 0)">{{$komplain->first()->status_komplain}}</b></h6>
        @elseif($komplain->first()->status_komplain == "Diterima")
            <h6><b>Status Komplain: </b><b style="color:rgb(0, 145, 0)">{{$komplain->first()->status_komplain}}</b></h6>
        @elseif($komplain->first()->status_komplain == "Ditolak")
            <h6><b>Status Komplain: </b><b style="color:red">{{$komplain->first()->status_komplain}}</b></h6>
        @endif
    </div>
    @php
        if ($komplain->first()->fk_id_pemilik != null) {
            $namaUser = DB::table('pemilik_alat')->where("id_pemilik","=",$komplain->first()->fk_id_pemilik)->get()->first()->nama_pemilik;
        }
        else {
            $namaUser = DB::table('pihak_tempat')->where("id_tempat","=",$komplain->first()->fk_id_tempat)->get()->first()->nama_tempat;
        }

        $tanggalAwal1 = $komplain->first()->waktu_komplain;
        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
        $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
        $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');

        $tanggalAwal2 = $tanggal_req;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');
    @endphp

    @if ($status == "Menunggu")
    <h6><b>Status Request: </b><b style="color:rgb(239, 203, 0)">{{$status}}</b></h6>
    @elseif($status == "Diterima")
        <h6><b>Status Request: </b><b style="color:rgb(0, 145, 0)">{{$status}}</b></h6>
    @elseif($status == "Ditolak")
        <h6><b>Status Request: </b><b style="color:red">{{$status}}</b></h6>
    @elseif($status == "Disewakan")
        <h6><b>Status Request: </b><b style="color:rgb(0, 145, 0)">{{$status}}</b></h6>
    @elseif($status == "Selesai")
        <h6><b>Status Request: </b><b style="color:blue">{{$status}}</b></h6>
    @elseif($status == "Dikomplain")
        <h6><b>Status Request: </b><b style="color:red">{{$status}}</b></h6>
    @endif

    <div class="row mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$namaUser}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Komplain: {{$tanggalBaru1}}</h6>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan Request: {{$tanggalBaru2}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
        </div>
    </div>

    <div class="row mt-4">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Diminta <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang direquest"></i></h5>
            @if ($dataAlat->deleted_at == null)<a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">@endif
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
            @if ($dataLapangan->deleted_at == null)<a href="/admin/lapangan/detailLapanganUmum/{{$dataLapangan->id_lapangan}}">@endif
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
                            <div class="col-8 d-flex flex-column justify-content-center">
                                <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                                <p class="card-text">Harga Sewa: Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}}/jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-3 mt-5">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Permintaan Harga Sewa: <i class="bi bi-info-circle" data-toggle="tooltip" title="Biaya sewa yang harus dibayar pelanggan saat menyewa alat (*sudah termasuk komisi pemilik dan pihak pengelola tempat)"></i></h6>
            <p>Rp {{number_format($dataRequest->req_harga_sewa, 0, ',', '.')}}/jam</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Mulai Dipinjam:</h6>
            @php
                $tanggalAwal2 = $dataRequest->req_tanggal_mulai;
                $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
            @endphp
            <p>{{$tanggalBaru2}}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Selesai Dipinjam:</h6>
            @php
                $tanggalAwal3 = $dataRequest->req_tanggal_selesai;
                $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
                $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY');
            @endphp
            <p>{{$tanggalBaru3}}</p>
        </div>
    </div>

    <div class="d-flex justify-content-start mt-3">
        <h6><b>Jenis Komplain: {{$komplain->first()->jenis_komplain}}</b></h6>
    </div>

    <div class="row mb-4 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h5>Keterangan:</h5>
            <h6>{{$komplain->first()->keterangan_komplain}}</h6>
        </div>
        
        <div class="col-md-6 col-sm-12 mb-3">
        </div>
    </div>

    <h5 class="mb-2">Foto Bukti Komplain</h5>
    <div class="row mb-5">
        @if (!$files->isEmpty())
            @foreach ($files as $gambar)
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card card-custom" onclick="showImage('{{ asset('upload/'.$gambar->nama_file_komplain) }}')">
                    <div class="image-square" style="background-image: url('{{ asset('upload/'.$gambar->nama_file_komplain) }}');"></div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    {{-- fitur show image --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-body">
              <img src="" id="modalImage" class="img-fluid">
            </div>
          </div>
        </div>
    </div>
    {{-- <h5>Detail Request</h5>
    @if ($komplain->first()->fk_id_permintaan != null)
        <a href="/admin/request/detailRequest/Permintaan/{{$id_request}}">
    @elseif ($komplain->first()->fk_id_penawaran != null)
        <a href="/admin/request/detailRequest/Penawaran/{{$id_request}}">
    @endif
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
                        <h5 class="card-title truncate-text">{{$komplain->first()->jenis_request}} {{$dataAlat->nama_alat}}</h5>
                        <p class="card-text">Pemilik alat: {{$nama_pemilik}}</p>
                    </div>
                </div>
            </div>
        </div>
    </a> --}}

    <h5 class="mb-5 mt-5">Penanganan Komplain</h5>
    @if ($komplain->first()->status_komplain == "Menunggu")
        <form id="terimaForm" action="/admin/komplain/request/terimaKomplain" method="POST">
            @csrf
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox" id="pengembalianCheckbox" onchange="toggleInput()">
                </div>
                @if (strpos($komplain->first()->jenis_komplain, 'Alat') !== false)
                    <div class="col-md-11 col-sm-10 mt-2" id="pengembalianLabel">
                        <h6>Penghapusan Alat Olahraga {{$dataAlat->nama_alat}}</h6>
                    </div>
                    <input type="hidden" name="produk" value="{{$dataAlat->id_alat}}-alat">
                @else
                    <div class="col-md-11 col-sm-10 mt-2" id="pengembalianLabel">
                        <h6>Penghapusan Lapangan Olahraga {{$dataLapangan->nama_lapangan}}</h6>
                    </div>
                    <input type="hidden" name="produk" value="{{$dataLapangan->id_lapangan}}-lapangan">
                @endif
                {{-- <div class="col-md-7 col-sm-12" id="pengembalianInput">
                    <select class="form-control" name="produk">
                        <option value="" disabled selected>Masukkan produk yang akan dihapus</option>
                        <option value="{{$dataAlat->id_alat}}-alat" {{ old('produk') == $dataAlat->id_alat ? 'selected' : '' }}>{{$dataAlat->nama_alat}}</option>
                        <option value="{{$dataLapangan->id_lapangan}}-lapangan" {{ old('produk') == $dataLapangan->id_lapangan ? 'selected' : '' }}>{{$dataLapangan->nama_lapangan}}</option>
                    </select>
                </div> --}}
            </div>
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox2" id="pengembalianCheckbox2" onchange="toggleInput2()">
                </div>
                @php
                    if ($komplain->first()->fk_id_permintaan == "Permintaan") {
                        $id_tempat = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first()->fk_id_tempat;
                        $id_pemilik = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first()->fk_id_pemilik;

                        $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
                        $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
                    }
                    else if ($komplain->first()->fk_id_penawaran == "Penawaran") {
                        $id_tempat = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first()->fk_id_tempat;
                        $id_pemilik = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first()->fk_id_pemilik;

                        $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
                        $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
                    }
                @endphp
                @if (strpos($komplain->first()->jenis_komplain, 'Alat') !== false)
                    <div class="col-md-11 col-sm-10 mt-2" id="pengembalianLabel2">
                        <h6>Penonaktifkan Akun Pemilik Alat Olahraga {{$nama_pemilik}}</h6>
                    </div>
                    <input type="hidden" name="akun" value="{{$id_pemilik}}-pemilik">
                @else
                    <div class="col-md-11 col-sm-10 mt-2" id="pengembalianLabel2">
                        <h6>Penonaktifkan Akun Tempat Olahraga {{$nama_tempat}}</h6>
                    </div>
                    <input type="hidden" name="akun" value="{{$id_tempat}}-tempat">
                @endif
                {{-- <div class="col-md-7 col-sm-12" id="pengembalianInput2">
                    <select class="form-control" name="akun">
                        <option value="" disabled selected>Masukkan akun yang akan dinonaktifkan</option>
                        <option value="{{$id_tempat}}-tempat" {{ old('akun') == $id_tempat ? 'selected' : '' }}>{{$nama_tempat}}</option>
                        <option value="{{$id_pemilik}}-pemilik" {{ old('akun') == $id_pemilik ? 'selected' : '' }}>{{$nama_pemilik}}</option>
                    </select>
                </div> --}}
            </div>
            @if ($komplain->first()->fk_id_permintaan != null)
                <input type="hidden" name="id_permintaan" value="{{$komplain->first()->fk_id_permintaan}}">
                <input type="hidden" name="id_penawaran">
            @elseif ($komplain->first()->fk_id_penawaran != null)
                <input type="hidden" name="id_permintaan">
                <input type="hidden" name="id_penawaran" value="{{$komplain->first()->fk_id_penawaran}}">
            @endif
            <input type="hidden" name="id_komplain" value="{{$komplain->first()->id_komplain_req}}">
            <div class="d-flex justify-content-end">
                <button class="btn btn-danger" onclick="event.preventDefault(); confirmTolak();">Tolak</button>
                <button type="submit" class="btn btn-success ms-3" id="terima">Terima</button>
            </div>
        </form>
    @elseif ($komplain->first()->status_komplain == "Diterima")
        {{-- tampilkan detail penanganan komplain --}}
        @if ($komplain->first()->penanganan_komplain != null)
            <div class="row mb-5 mt-4">
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Penanganan: {{$komplain->first()->penanganan_komplain}}</h6>
                </div>
                
                <div class="col-md-6 col-sm-12 mb-3">
                </div>
            </div>
        @else
            <div class="row mb-5 mt-4">
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Penanganan: Pembatalan Request oleh admin</h6>
                </div>
                
                <div class="col-md-6 col-sm-12 mb-3">
                </div>
            </div>
        @endif
    @elseif ($komplain->first()->status_komplain == "Ditolak")
        {{-- tampilkan detail penanganan komplain --}}
        <div class="row mb-5 mt-4">
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Penanganan: Komplain Ditolak oleh admin dengan alasan {{$komplain->first()->alasan_komplain}}</h6>
            </div>
            
            <div class="col-md-6 col-sm-12 mb-3">
            </div>
        </div>
    @endif
</div>
<script>
    function showImage(imgPath) {
        document.getElementById('modalImage').src = imgPath;
        $('#imageModal').modal('show');
    }
    function confirmTolak() {
        swal({
            title: "Apakah Anda yakin?",
            text: "Apakah Anda yakin ingin menolak komplain ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Tolak!",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                swal({
                    title: "Alasan Komplain Ditolak",
                    text: "Masukkan alasan ditolak:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Alasan..."
                }, function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Anda harus memasukkan alasan yang valid!");
                        console.log(inputValue);
                        return false;
                    }
                    // window.location.href = "/admin/komplain/request/tolakKomplain/{{$komplain->first()->id_komplain_req}}?alasan=" + encodeURIComponent(inputValue);
                    $.ajax({
                        type: "GET",
                        url: "/admin/komplain/request/tolakKomplain/{{$komplain->first()->id_komplain_req}}",
                        data: {
                            alasan: inputValue
                        },
                        success: function(response) {
                            // Handle success response
                            // You can show a success message or perform additional actions
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
                        error: function(error) {
                            // Handle error response
                            console.error(error);
                        }
                    });
                });

                setTimeout(function() {
                    // Mengubah tipe input menjadi number setelah SweetAlert muncul
                    var input = document.querySelector(".sweet-alert input");
                    if (input) {
                        input.type = "text";
                    }
                }, 1);
            }
        });
    }
    toggleInput();
    toggleInput2();
    function toggleInput() {
        var checkbox = document.getElementById("pengembalianCheckbox");
        var label = document.getElementById("pengembalianLabel");
        // var inputGroup = document.getElementById("pengembalianInput");

        if (checkbox.checked) {
            label.style.opacity = "1";
            // inputGroup.style.opacity = "1";
            // inputGroup.querySelector('select').disabled = false;
        } else {
            label.style.opacity = "0.5";
            // inputGroup.style.opacity = "0.5";
            // inputGroup.querySelector('select').disabled = true;
        }
    }
    function toggleInput2() {
        var checkbox = document.getElementById("pengembalianCheckbox2");
        var label = document.getElementById("pengembalianLabel2");
        // var inputGroup = document.getElementById("pengembalianInput2");

        if (checkbox.checked) {
            label.style.opacity = "1";
            // inputGroup.style.opacity = "1";
            // inputGroup.querySelector('select').disabled = false;
        } else {
            label.style.opacity = "0.5";
            // inputGroup.style.opacity = "0.5";
            // inputGroup.querySelector('select').disabled = true;
        }
    }
    function formatNumber(input) {
        // Mengambil value dari input
        let value = input.value;

        // Menghapus semua titik dan karakter non-numerik lainnya
        value = value.replace(/\D/g, '');

        // Memformat ulang sebagai angka dengan pemisah ribuan titik
        value = parseFloat(value).toLocaleString('id-ID');

        // Mengembalikan format yang sudah diubah ke input
        input.value = value;
    }
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();

        $("#terima").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = $("#terimaForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/admin/komplain/request/terimaKomplain",
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
</script>
@endsection