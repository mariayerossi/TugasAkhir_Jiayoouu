@extends('layouts.sidebar_admin')

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
</style>
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Komplain</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h6><b>Jenis Request: {{$komplain->first()->jenis_request}}</b></h6>
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
        if ($komplain->first()->jenis_role == "Pemilik") {
            $namaUser = DB::table('pemilik_alat')->where("id_pemilik","=",$komplain->first()->fk_id_user)->get()->first()->nama_pemilik;
        }
        else if ($komplain->first()->jenis_role == "Tempat") {
            $namaUser = DB::table('pihak_tempat')->where("id_tempat","=",$komplain->first()->fk_id_user)->get()->first()->nama_tempat;
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

    <div class="row mb-5 mt-5">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Keterangan: <br>{{$komplain->first()->keterangan_komplain}}</h6>
        </div>
        
        <div class="col-md-6 col-sm-12 mb-3">
        </div>
    </div>

    <h5 class="mb-5">Penanganan Komplain</h5>
    <form action="" method="POST">
        @csrf
        <div class="row mb-5 mt-5">
            <div class="col-md-4 col-sm-12 mt-2">
                <h6>Pengembalian dana sebesar </h6>
            </div>
            
            <div class="col-md-4 col-sm-12">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    <input type="number" class="form-control" min="0" name="dana" placeholder="50.000" oninput="formatNumber(this)" value="{{old('dana')}}">
                </div>
            </div>

            <div class="col-md-4 col-sm-12 mt-2">
                <h6>dikembalikan kepada {{$namaUser}}</h6>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success me-3">Terima</button>
            <button class="btn btn-danger" onclick="event.preventDefault(); confirmTolak();">Tolak</button>
        </div>
    </form>
</div>
<script>
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