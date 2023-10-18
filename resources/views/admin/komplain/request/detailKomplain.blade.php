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
    <h3 class="text-center mb-5">Detail Komplain Request</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        @if ($komplain->first()->fk_id_permintaan != null)
            <h6><b>Jenis Request: Permintaan</b></h6>
        @else
            <h6><b>Jenis Request: Penawaran</b></h6>
        @endif
    </div>
    <div class="d-flex justify-content-end mt-3 me-3">
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
        $tanggalBaru1 = $tanggalObjek1->format('d-m-Y H:i:s');
    @endphp
    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$namaUser}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Komplain: {{$tanggalBaru1}}</h6>
        </div>
    </div>

    <div class="d-flex justify-content-start mt-3">
        <h6><b>Jenis Komplain: {{$komplain->first()->jenis_komplain}}</b></h6>
    </div>

    <div class="row mb-5 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Keterangan: <br>{{$komplain->first()->keterangan_komplain}}</h6>
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
    @php
        if ($komplain->first()->fk_id_permintaan != null) {
            $dataRequest = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first();
            $id_request = $dataRequest->id_permintaan;

            $id_tempat = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first()->fk_id_tempat;
            $id_pemilik = DB::table('request_permintaan')->where("id_permintaan","=",$komplain->first()->fk_id_permintaan)->get()->first()->fk_id_pemilik;
            $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
        }
        else if ($komplain->first()->fk_id_penawaran != null) {
            $dataRequest = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first();
            $id_request = $dataRequest->id_penawaran;

            $id_tempat = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first()->fk_id_tempat;
            $id_pemilik = DB::table('request_penawaran')->where("id_penawaran","=",$komplain->first()->fk_id_penawaran)->get()->first()->fk_id_pemilik;
            $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$id_pemilik)->get()->first()->nama_pemilik;
        }
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dataRequest->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();

        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataRequest->req_lapangan)->get()->first();
    @endphp
    {{-- detail request --}}
    <h5>Detail Request</h5>
    <a href="/admin/request/detailRequest/{{$komplain->first()->jenis_request}}/{{$id_request}}">
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
    </a>

    <h5 class="mb-5 mt-5">Penanganan Komplain</h5>
    @if ($komplain->first()->status_komplain == "Menunggu")
        <form action="/admin/komplain/request/terimaKomplain" method="POST">
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
                <button type="submit" class="btn btn-success me-3">Terima</button>
                <button class="btn btn-danger" onclick="event.preventDefault(); confirmTolak();">Tolak</button>
            </div>
        </form>
    @elseif ($komplain->first()->status_komplain == "Diterima")
        {{-- tampilkan detail penanganan komplain --}}
        @if ($komplain->first()->penanganan_komplain != null)
            <div class="row mb-5 mt-4">
                <div class="col-md-6 col-sm-12 mb-3">
                    <h6>Penanganan: {{$komplain->first()->penanganan_komplain}} dan Pembatalan Request oleh admin</h6>
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
                <h6>Penanganan: Komplain Ditolak oleh admin</h6>
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
                window.location.href = "/admin/komplain/request/tolakKomplain/{{$komplain->first()->id_komplain_req}}";
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
</script>
@endsection