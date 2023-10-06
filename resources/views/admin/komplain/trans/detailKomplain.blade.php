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
    <h3 class="text-center mb-5">Detail Komplain Transaksi</h3>
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
        $namaUser = DB::table('user')->where("id_user","=",$komplain->first()->fk_id_user)->get()->first()->nama_user;

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
        $dataHtrans = DB::table('htrans')
                    ->select("files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan", "htrans.kode_trans", "pihak_tempat.id_tempat","pihak_tempat.nama_tempat")
                    ->where("id_htrans","=",$komplain->first()->fk_id_htrans)
                    ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                    ->join("pihak_tempat","lapangan_olahraga.pemilik_lapangan","=","pihak_tempat.id_tempat")
                    ->joinSub(function($query) {
                        $query->select("fk_id_lapangan", "nama_file_lapangan")
                            ->from('files_lapangan')
                            ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                    }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                    ->get()
                    ->first();
    @endphp
    {{-- detail request --}}
    <h5>Detail Transaksi</h5>
    <a href="/admin/transaksi/detailTransaksi/{{$komplain->first()->fk_id_htrans}}">
        <div class="card h-70">
            <div class="card-body">
                <div class="row">
                    <!-- Gambar Alat -->
                    <div class="col-4">
                        <div class="square-image-container">
                            <img src="{{ asset('upload/' . $dataHtrans->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    
                    <!-- Nama Alat -->
                    <div class="col-8 d-flex flex-column justify-content-center">
                        <h5 class="card-title truncate-text">{{$dataHtrans->nama_lapangan}}</h5>
                        <h6><b>Kode Transaksi: {{$dataHtrans->kode_trans}}</b></h6>
                    </div>
                </div>
            </div>
        </div>
    </a>

    {{-- blm mari --}}
    <h5 class="mb-5 mt-5">Penanganan Komplain</h5>
    @if ($komplain->first()->status_komplain == "Menunggu")
        @php
            $tempat = DB::table('pihak_tempat')
                    ->join("htrans","pihak_tempat.id_tempat","=","htrans.fk_id_tempat")
                    ->where("htrans.id_htrans","=",$komplain->first()->fk_id_htrans)
                    ->get()
                    ->first();
            $pemilik = DB::table('pemilik_alat')
                    ->join("dtrans","pemilik_alat.id_pemilik","=","dtrans.fk_id_pemilik")
                    ->where("dtrans.fk_id_htrans","=",$komplain->first()->fk_id_htrans)
                    ->get();
            
            $lapangan = DB::table('lapangan_olahraga')
                    ->join("htrans","lapangan_olahraga.id_lapangan","=","htrans.fk_id_lapangan")
                    ->where("htrans.id_htrans","=",$komplain->first()->fk_id_htrans)
                    ->get()
                    ->first();
            $alat = DB::table('alat_olahraga')
                    ->join("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                    ->where("dtrans.fk_id_htrans","=",$komplain->first()->fk_id_htrans)
                    ->get();
        @endphp
        <form action="/admin/komplain/trans/terimaKomplain" method="POST">
            @csrf
            <div class="row mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox3" id="pengembalianCheckbox3" onchange="toggleInput3()">
                </div>
                <div class="col-md-8 col-sm-10 mt-2" id="pengembalianLabel3">
                    <h6>Pengembalian Dana Customer {{$namaUser}} sebesar</h6>
                </div>
                <input type="hidden" name="user" value="{{$komplain->first()->fk_id_user}}">
                <div class="col-md-3 col-sm-12" id="pengembalianInput3">
                    <input type="number" name="jumlah" id="" class="form-control" oninput="formatNumber(this)" placeholder="Masukkan Nominal...">
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                </div>
                <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel4">
                    <h6>dan dipotong dari saldo wallet milik</h6>
                </div>
                <div class="col-md-7 col-sm-12" id="pengembalianInput4">
                    <select class="form-control" name="akun_dikembalikan">
                        <option value="" disabled selected>Masukkan pemilik saldo wallet</option>
                        <option value="{{$tempat->id_tempat}}-tempat">{{$tempat->nama_tempat}}</option>
                        @if (!$pemilik->isEmpty())
                            @foreach ($pemilik as $item)
                                <option value="{{$item->id_pemilik}}-pemilik">{{$item->nama_pemilik}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox" id="pengembalianCheckbox" onchange="toggleInput()">
                </div>
                <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel">
                    <h6>Penghapusan Produk</h6>
                </div>
                <div class="col-md-7 col-sm-12" id="pengembalianInput">
                    <select class="form-control" name="produk">
                        <option value="" disabled selected>Masukkan produk yang akan dihapus</option>
                        <option value="{{$lapangan->id_lapangan}}-lapangan">{{$lapangan->nama_lapangan}}</option>
                        @if (!$alat->isEmpty())
                            @foreach ($alat as $item)
                                <option value="{{$item->id_alat}}-alat">{{$item->nama_alat}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox2" id="pengembalianCheckbox2" onchange="toggleInput2()">
                </div>
                <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel2">
                    <h6>Penonaktifkan akun</h6>
                </div>
                <div class="col-md-7 col-sm-12" id="pengembalianInput2">
                    <select class="form-control" name="akun">
                        <option value="" disabled selected>Masukkan akun yang akan dinonaktifkan</option>
                        <option value="{{$tempat->id_tempat}}-tempat">{{$tempat->nama_tempat}}</option>
                        @if (!$pemilik->isEmpty())
                            @foreach ($pemilik as $item)
                                <option value="{{$item->id_pemilik}}-pemilik">{{$item->nama_pemilik}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <input type="hidden" name="id_komplain" value="{{$komplain->first()->id_komplain_trans}}">
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
    toggleInput3();
    function toggleInput() {
        var checkbox = document.getElementById("pengembalianCheckbox");
        var label = document.getElementById("pengembalianLabel");
        var inputGroup = document.getElementById("pengembalianInput");

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
    function toggleInput2() {
        var checkbox = document.getElementById("pengembalianCheckbox2");
        var label = document.getElementById("pengembalianLabel2");
        var inputGroup = document.getElementById("pengembalianInput2");

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
    function toggleInput3() {
        var checkbox = document.getElementById("pengembalianCheckbox3");
        var label = document.getElementById("pengembalianLabel3");
        var inputGroup = document.getElementById("pengembalianInput3");
        var label2 = document.getElementById("pengembalianLabel4");
        var inputGroup2 = document.getElementById("pengembalianInput4");

        if (checkbox.checked) {
            label.style.opacity = "1";
            inputGroup.style.opacity = "1";
            inputGroup.querySelector('input').disabled = false;
            label2.style.opacity = "1";
            inputGroup2.style.opacity = "1";
            inputGroup2.querySelector('select').disabled = false;
        } else {
            label.style.opacity = "0.5";
            inputGroup.style.opacity = "0.5";
            inputGroup.querySelector('input').disabled = true;
            label2.style.opacity = "0.5";
            inputGroup2.style.opacity = "0.5";
            inputGroup2.querySelector('select').disabled = true;
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