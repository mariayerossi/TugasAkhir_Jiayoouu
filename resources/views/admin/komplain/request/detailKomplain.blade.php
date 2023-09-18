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
    <h5 class="mb-5">Penanganan Komplain</h5>
    <form action="" method="POST">
        @csrf
        <div class="row mb-5 mt-5">
            <div class="col-md-1 col-sm-2 d-flex align-items-center">
                <input type="checkbox" name="pengembalianCheckbox" id="pengembalianCheckbox" onchange="toggleInput()">
            </div>
            <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel">
                <h6>Pengembalian dana user sebesar</h6>
            </div>
            <div class="col-md-3 col-sm-12" id="pengembalianInput">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    <input type="number" class="form-control" min="0" name="dana" placeholder="50.000" oninput="formatNumber(this)" value="{{ old('dana') }}" disabled>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mt-2" id="pengembalianLabel2">
                <h6>dikembalikan kepada {{$namaUser}}</h6>
            </div>
        </div>
        <div class="row mb-5 mt-5">
            <div class="col-md-1 col-sm-2 d-flex align-items-center">
                <input type="checkbox" name="pengembalianCheckbox2" id="pengembalianCheckbox2" onchange="toggleInput2()">
            </div>
            <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel3">
                <h6>Penonaktifkan akun</h6>
            </div>
            <div class="col-md-7 col-sm-12" id="pengembalianInput3">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Masukkan akun yang akan dinonaktifkan</option>
                    {{-- @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->nama_kategori}}" {{ old('kategori') == $item->nama_kategori ? 'selected' : '' }}>{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif --}}
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success me-3">Terima</button>
            <button class="btn btn-danger" onclick="event.preventDefault(); confirmTolak();">Tolak</button>
        </div>
    </form>
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
        var label2 = document.getElementById("pengembalianLabel2");
        var inputGroup = document.getElementById("pengembalianInput");

        if (checkbox.checked) {
            label.style.opacity = "1";
            label2.style.opacity = "1";
            inputGroup.style.opacity = "1";
            inputGroup.querySelector('input').disabled = false;
        } else {
            label.style.opacity = "0.5";
            label2.style.opacity = "0.5";
            inputGroup.style.opacity = "0.5";
            inputGroup.querySelector('input').disabled = true;
        }
    }
    function toggleInput2() {
        var checkbox = document.getElementById("pengembalianCheckbox2");
        var label = document.getElementById("pengembalianLabel3");
        var inputGroup = document.getElementById("pengembalianInput3");

        if (checkbox.checked) {
            label.style.opacity = "1";
            inputGroup.style.opacity = "1";
            inputGroup.querySelector('select').disabled = false;
        } else {
            label.style.opacity = "0.5";
            inputGroup.style.opacity = "0.5";
            inputGroup.querySelector('select').disabled = true;
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