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
    <h3 class="text-center mb-4">Detail Komplain Transaksi</h3>
    @php
        $tanggalAwal1 = $komplain->first()->waktu_komplain;
        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
        $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
        $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');

        $tanggalAwal2 = $dataHtrans->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
    @endphp
    <div class="d-flex justify-content-end me-3">
        @if ($komplain->first()->status_komplain == "Menunggu")
            <h6><b>Status Komplain: </b><b style="color:rgb(239, 203, 0)">{{$komplain->first()->status_komplain}}</b></h6>
        @elseif($komplain->first()->status_komplain == "Diterima")
            <h6><b>Status Komplain: </b><b style="color:rgb(0, 145, 0)">{{$komplain->first()->status_komplain}}</b></h6>
        @elseif($komplain->first()->status_komplain == "Ditolak")
            <h6><b>Status Komplain: </b><b style="color:red">{{$komplain->first()->status_komplain}}</b></h6>
        @endif
    </div>

    <div class="d-flex justify-content-start mt-3">
        <h6><b>Kode Transaksi: {{$dataHtrans->kode_trans}}</b></h6>
    </div>
    <div class="d-flex justify-content-start mt-1">
        @if ($dataHtrans->status_trans == "Menunggu")
            <h6><b>Status Transaksi: </b><b style="color:rgb(239, 203, 0)">{{$dataHtrans->status_trans}}</b></h6>
        @elseif($dataHtrans->status_trans == "Diterima")
            <h6><b>Status Transaksi: </b><b style="color:rgb(0, 145, 0)">{{$dataHtrans->status_trans}}</b></h6>
        @elseif($dataHtrans->status_trans == "Ditolak")
            <h6><b>Status Transaksi: </b><b style="color:red">{{$dataHtrans->status_trans}}</b></h6>
        @elseif($dataHtrans->status_trans == "Berlangsung")
            <h6><b>Status Transaksi: </b><b style="color:rgb(0, 145, 0)">{{$dataHtrans->status_trans}}</b></h6>
        @elseif($dataHtrans->status_trans == "Selesai")
            <h6><b>Status Transaksi: </b><b style="color:blue">{{$dataHtrans->status_trans}}</b></h6>
        @elseif($dataHtrans->status_trans == "Dikomplain")
            <h6><b>Status Transaksi: </b><b style="color:red">{{$dataHtrans->status_trans}}</b></h6>
        @endif
    </div>

    <div class="row mb-3 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$namaUser}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Komplain: {{$tanggalBaru1}}</h6>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalBaru2}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{ \Carbon\Carbon::parse($dataHtrans->jam_sewa)->format('H:i') }} WIB - {{ \Carbon\Carbon::parse($dataHtrans->jam_sewa)->addHours($dataHtrans->durasi_sewa)->format('H:i') }} WIB</h6>
        </div>
    </div>

    <h5>Detail Transaksi</h5>
    @if ($dataHtrans->deleted_at == null)<a href="/admin/lapangan/detailLapanganUmum/{{$dataHtrans->id_lapangan}}">@endif
        <div class="card">
            <div class="card-body">
                <div class="row d-md-flex align-items-md-center">
                    <!-- Gambar -->
                    <div class="col-4">
                        <div class="square-image-container">
                            <img src="{{ asset('upload/' . $dataHtrans->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    
                    <!-- Nama -->
                    <div class="col-8">
                        <h5 class="card-title truncate-text">{{$dataHtrans->nama_lapangan}}</h5>
                        <!-- Contoh detail lain: -->
                        <p class="card-text">Rp {{number_format($dataHtrans->harga_sewa_lapangan, 0, ',', '.')}} x {{$dataHtrans->durasi_sewa}} Jam = Rp {{number_format($dataHtrans->subtotal_lapangan, 0, ',', '.')}}</p>
                        {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal Lapangan: Rp {{number_format($dataHtrans->subtotal_lapangan, 0, ',', '.')}}</b></h5>
    </div>

    <div class="row mt-4">
        @if (!$dataDtrans->isEmpty())
            @foreach ($dataDtrans as $item)
                @php
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                    $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                @endphp
                @if ($dataAlat->deleted_at == null)<a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">@endif
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
                                    <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$dataHtrans->durasi_sewa}} Jam = Rp {{number_format($item->subtotal_alat, 0, ',', '.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <p>(Tidak ada alat olahraga yang disewa)</p>
        @endif
    </div>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal Alat: Rp {{number_format($dataHtrans->subtotal_alat, 0, ',', '.')}}</b></h5>
    </div>
    <hr>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h4><b>Total Transaksi: Rp {{number_format($dataHtrans->total_trans, 0, ',', '.')}}</b></h4>
    </div>

    <div class="row mb-3 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6><b>Jenis Komplain: {{$komplain->first()->jenis_komplain}}</b></h6>
        </div>
        
        <div class="col-md-6 col-sm-12 mb-3">
        </div>
    </div>

    <div class="row mb-3">
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
    {{-- detail request --}}
    {{-- <h5>Detail Transaksi</h5>
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
    </a> --}}
    
    <h5 class="mt-5 mb-3">Kontak Pihak Tempat Olahraga</h5>
    <h6>Email: {{$dataHtrans->email_tempat}}</h6>
    <h6>No. Telepon: {{$dataHtrans->telepon_tempat}}</h6>

    {{-- blm mari --}}
    <h5 class="mb-5 mt-5">Penanganan Komplain</h5>
    @if ($komplain->first()->status_komplain == "Menunggu")
        <form id="terimaForm" action="/admin/komplain/trans/terimaKomplain" method="POST">
            @csrf
            {{-- <div class="row mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox3" id="pengembalianCheckbox3" onchange="toggleInput3()">
                </div>
                <div class="col-md-8 col-sm-10 mt-2" id="pengembalianLabel3">
                    <h6>Pengembalian Dana Customer {{$namaUser}} sebesar</h6>
                </div>
                <div class="col-md-3 col-sm-12" id="pengembalianInput3">
                    <input type="number" name="jumlah" id="" class="form-control" oninput="formatNumber(this)" placeholder="Masukkan Nominal...">
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                </div>
                @if (strpos($komplain->first()->jenis_komplain, 'Alat') !== false)
                    <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel4">
                        <h6>dan dipotong dari saldo wallet milik</h6>
                    </div>
                    <div class="col-md-7 col-sm-12" id="pengembalianInput4">
                        <select class="form-control" name="akun_dikembalikan">
                            <option value="" disabled selected>Masukkan pemilik alat olahraga</option>
                            @if (!$pemilik->isEmpty())
                                @foreach ($pemilik as $item)
                                    <option value="{{$item->id_pemilik}}-pemilik">{{$item->nama_pemilik}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @else
                    <div class="col-md-10 col-sm-10 mt-2" id="pengembalianLabel4">
                        <h6>dan dipotong dari saldo wallet milik {{$tempat->nama_tempat}}</h6>
                    </div>
                    <input type="hidden" name="akun_dikembalikan" value="{{$tempat->id_tempat}}-tempat">
                @endif
            </div> --}}
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox" id="pengembalianCheckbox" onchange="toggleInput()">
                </div>
                @if (strpos($komplain->first()->jenis_komplain, 'Alat') !== false)
                    <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel">
                        <h6>Penghapusan Alat Olahraga</h6>
                    </div>
                    <div class="col-md-7 col-sm-12" id="pengembalianInput">
                        <select class="form-control" name="produk">
                            <option value="" disabled selected>Masukkan alat olahraga yang akan dihapus</option>
                            @if (!$alat->isEmpty())
                                @foreach ($alat as $item)
                                    <option value="{{$item->id_alat}}-alat">{{$item->nama_alat}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @else
                    <div class="col-md-10 col-sm-10 mt-2" id="pengembalianLabel">
                        <h6>Penghapusan Lapangan Olahraga {{$lapangan->nama_lapangan}}</h6>
                    </div>
                    <input type="hidden" name="produk" value="{{$lapangan->id_lapangan}}-lapangan">
                @endif
            </div>
            <div class="row mb-5 mt-5">
                <div class="col-md-1 col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="pengembalianCheckbox2" id="pengembalianCheckbox2" onchange="toggleInput2()">
                </div>
                @if (strpos($komplain->first()->jenis_komplain, 'Alat') !== false)
                    <div class="col-md-4 col-sm-10 mt-2" id="pengembalianLabel2">
                        <h6>Penonaktifkan Akun Pemilik Alat</h6>
                    </div>
                    <div class="col-md-7 col-sm-12" id="pengembalianInput2">
                        <select class="form-control" name="akun">
                            <option value="" disabled selected>Masukkan akun pemilik alat olahraga</option>
                            @if (!$pemilik->isEmpty())
                                @foreach ($pemilik as $item)
                                    <option value="{{$item->id_pemilik}}-pemilik">{{$item->nama_pemilik}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @else
                    <div class="col-md-10 col-sm-10 mt-2" id="pengembalianLabel2">
                        <h6>Penonaktifkan Akun Pihak Tempat {{$tempat->nama_tempat}}</h6>
                    </div>
                    <input type="hidden" name="akun" value="{{$tempat->id_tempat}}-tempat">
                @endif
            </div>
            <input type="hidden" name="user" value="{{$komplain->first()->fk_id_user}}">
            <input type="hidden" name="id_htrans" value="{{$komplain->first()->fk_id_htrans}}">
            <input type="hidden" name="id_komplain" value="{{$komplain->first()->id_komplain_trans}}">
            <div class="d-flex justify-content-end">
                <button class="btn btn-danger" onclick="event.preventDefault(); confirmTolak();">Tolak</button>
                <button id="terima" type="submit" class="btn btn-success ms-3">Terima</button>
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
                    <h6>Penanganan: Pembatalan Transaksi oleh admin</h6>
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
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();

        $("#terima").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = $("#terimaForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/admin/komplain/trans/terimaKomplain",
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
                    // window.location.href = "/admin/komplain/trans/tolakKomplain/{{$komplain->first()->id_komplain_trans}}/{{$komplain->first()->fk_id_htrans}}?alasan=" + encodeURIComponent(inputValue);
                    $.ajax({
                        type: "GET",
                        url: "/admin/komplain/trans/tolakKomplain/{{$komplain->first()->id_komplain_trans}}/{{$komplain->first()->fk_id_htrans}}",
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
    toggleInput3();
    function toggleInput() {
        var checkbox = document.getElementById("pengembalianCheckbox");
        var label = document.getElementById("pengembalianLabel");
        var inputGroup = document.getElementById("pengembalianInput");

        if (inputGroup) {
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
        else {
            if (checkbox.checked) {
                label.style.opacity = "1";
            } else {
                label.style.opacity = "0.5";
            }
        }
    }
    function toggleInput2() {
        var checkbox = document.getElementById("pengembalianCheckbox2");
        var label = document.getElementById("pengembalianLabel2");
        var inputGroup = document.getElementById("pengembalianInput2");

        if (inputGroup) {
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
        else {
            if (checkbox.checked) {
                label.style.opacity = "1";
            } else {
                label.style.opacity = "0.5";
            }
        }
    }
    function toggleInput3() {
        var checkbox = document.getElementById("pengembalianCheckbox3");
        var label = document.getElementById("pengembalianLabel3");
        var inputGroup = document.getElementById("pengembalianInput3");
        var label2 = document.getElementById("pengembalianLabel4");
        var inputGroup2 = document.getElementById("pengembalianInput4");

        if (inputGroup2) {
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
        else {
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