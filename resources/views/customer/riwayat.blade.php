@extends('layouts.navbar_customer')

@section('content')
<style>
    .card-image-container {
    display: flex;
    justify-content: center; /* Pusatkan gambar secara horizontal */
    align-items: center; /* Pusatkan gambar secara vertikal */
    height: 100%; /* Tentukan tinggi agar flexbox dapat bekerja dengan benar */
}

.square-wrapper {
    width: 70%;
    padding-top: 70%; /* Rasio tetap 1:1 */
    position: relative;
    margin: 0 auto; /* Pusatkan wrapper gambar */
}

.square-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}


.card-image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* Sesuaikan dengan preferensi Anda (cover atau contain) */
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
<div class="container mt-5 mb-5">
    <h2 class="text-center mb-5">Daftar Riwayat Transaksi</h2>
    @include("layouts.message")
    @if (!$trans->isEmpty())
        @foreach ($trans as $item)
            <div class="card mb-5" style="max-width: 100%;" data-id="{{$item->id_htrans}}">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="card-image-container">
                                <div class="square-wrapper">
                                    <a href="/customer/detailLapangan/{{$item->id_lapangan}}">
                                        <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="" class="img-fluid">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                @php
                                    $tanggalAwal1 = $item->tanggal_trans;
                                    $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
                                    $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
                                    $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY H:mm');
                                @endphp
                                <div class="d-flex justify-content-end">
                                    <h6><b>Tanggal Transaksi: {{$tanggalBaru1}}</b></h6>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <h6>Status:</h6>
                                    @if ($item->status_trans == "Diterima")
                                        <h6 style="color: rgb(0, 145, 0)">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Ditolak")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Dibatalkan")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Menunggu")
                                        <h6 style="color: rgb(239, 203, 0)">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Selesai")
                                        <h6 style="color: blue">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Dikomplain")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Berlangsung")
                                        <h6 style="color: rgb(255, 145, 0)">{{$item->status_trans}}</h6>
                                    @endif
                                </div>
                                <p class="card-text"><strong>Kode Transaksi: {{$item->kode_trans}}</strong></p>
                                <h5 class="card-title"><b>{{$item->nama_lapangan}}</b></h5>
                                @php
                                    $tanggalAwal2 = $item->tanggal_sewa;
                                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                    $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                                    $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');

                                    $dtrans = DB::table('dtrans')
                                            ->select("alat_olahraga.id_alat", "alat_olahraga.nama_alat","files_alat.nama_file_alat")
                                            ->join("alat_olahraga", "dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                                            ->joinSub(function($query) {
                                                $query->select("fk_id_alat", "nama_file_alat")
                                                    ->from('files_alat')
                                                    ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                                            }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                                            ->where("dtrans.fk_id_htrans","=",$item->id_htrans)
                                            ->where("dtrans.deleted_at","=",null)
                                            ->get();
                                    // dd($dtrans);
                                @endphp
                                <p class="card-text"><strong>Tanggal Sewa: </strong>{{$tanggalBaru2}}</p>
                                <p class="card-text"><strong>Jam Sewa: </strong>{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i') }}</p>
                                <div class="d-flex flex-row flex-wrap">
                                @if (!$dtrans->isEmpty())
                                    @foreach ($dtrans as $item2)
                                    <a href="/customer/detailAlat/{{$item2->id_alat}}">
                                        <div class="card tiny-card h-70 mb-1 mr-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Gambar Alat -->
                                                    <div class="col-4">
                                                        <div class="square-image-container2">
                                                            <img src="{{ asset('upload/' . $item2->nama_file_alat) }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Nama Alat -->
                                                    <div class="col-8 d-flex align-items-center justify-content-between">
                                                        <h5 class="card-title truncate-text">{{$item2->nama_alat}}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <h4><b>Total: Rp {{number_format($item->total_trans, 0, ',', '.')}}</b></h4>
                            </div>
                            @php
                                $komplain = DB::table('komplain_trans')
                                            ->where("fk_id_htrans","=",$item->id_htrans)
                                            ->get()
                                            ->first();
                            @endphp
                            {{-- <h3>{{$item->id_htrans}}</h3> --}}
                            <div class="d-flex justify-content-end">
                                @if ($item->status_trans == "Diterima" && $komplain == null)
                                    <form id="ajukanKomplain" action="/customer/komplain/ajukanKomplain" method="post">
                                        @csrf
                                        <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                                        <button type="submit" class="btn btn-warning" data-id="{{$item->id_htrans}}">Ajukan Komplain <i class="bi bi-pencil-square"></i></button>
                                    </form>
                                @elseif  ($item->status_trans == "Berlangsung")
                                    @php
                                        $cek = DB::table('extend_htrans')
                                                ->where("fk_id_htrans", "=",$item->id_htrans)
                                                ->get();
                                    @endphp
                                    @if ($cek->isEmpty())
                                        <form id="tambahWaktu" action="/customer/extend/detailTambahWaktu" method="get" data-id="{{$item->id_htrans}}">
                                            @csrf
                                            <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                                            <input type="hidden" name="durasi" id="durasi_jam" data-id="{{$item->id_htrans}}">
                                            <button type="submit" class="btn btn-success me-2" data-id="{{$item->id_htrans}}" onclick="showSweetAlert(this)">Extend Waktu Sewa <i class="bi bi-alarm"></i></button>
                                        </form>
                                    @endif
                                @elseif ($item->status_trans == "Dikomplain")
                                    <div class="mt-3">
                                        @if ($komplain->status_komplain == "Menunggu")
                                            <h4 class="card-title"><b>Komplain Anda telah dikirim!</b></h4>
                                            <p class="card-text">Komplain kamu menunggu konfirmasi admin</p>
                                        @elseif ($komplain->status_komplain == "Ditolak")
                                            <h4 class="card-title"><b>Komplain Anda Ditolak!</b></h4>
                                            <p class="card-text">Alasan Ditolak: {{$komplain->alasan_komplain}}</p>
                                        @endif
                                    </div>
                                @elseif ($item->status_trans == "Dibatalkan" && $komplain != null)
                                    <div class="mt-3">
                                        @if ($komplain->status_komplain == "Diterima")
                                            <h4 class="card-title"><b>Yeay! Komplain Anda telah {{$komplain->status_komplain}}!</b></h4>
                                        @endif
                                    </div>
                                @elseif ($item->status_trans == "Selesai")
                                    <a href="/customer/rating/detailRating/{{$item->id_htrans}}" class="btn btn-success">Berikan Ulasan <i class="bi bi-star"></i></a>
                                @endif

                                @if ($item->status_trans == "Menunggu")
                                    <form class="batalTransaksiForm" action="/customer/transaksi/batalBooking" method="post" data-id="{{$item->id_htrans}}">
                                        @csrf
                                        <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                                        <button type="submit" data-id="{{$item->id_htrans}}" class="btn btn-danger ms-3">Batal Booking <i class="bi bi-x-lg"></i></button>
                                    </form>
                                @endif
                            </div>
                            @php
                                $extend = DB::table('extend_htrans')
                                        ->where("fk_id_htrans", "=",$item->id_htrans)
                                        ->get();
                            @endphp
                            @if (!$extend->isEmpty())
                                <hr>
                                <p class="card-text"><strong>Extend Durasi: </strong>{{$extend->first()->durasi_extend}} jam</p>
                                <p class="card-text"><strong>Extend Jam Sewa: </strong>{{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->addHours($extend->first()->durasi_extend)->format('H:i') }}</p>
                                <div class="d-flex justify-content-end">
                                    <h4><b>Total Extend: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
                                </div>
                                <div class="d-flex justify-content-end">
                                    @php
                                        $cek = DB::table('extend_htrans')
                                                ->where("fk_id_htrans", "=",$item->id_htrans)
                                                ->get();
                                    @endphp
                                    <h6 class="mt-4">Status Extend Waktu {{$cek->first()->status_extend}} Pihak Tempat Olahraga</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form_komplain mb-5" data-id="{{$item->id_htrans}}">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end me-3">
                        <button class="close-chat-btn" data-id="{{$item->id_htrans}}">&times;</button>
                    </div>
                    <form id="form{{$item->id_htrans}}" action="/customer/komplain/ajukanKomplain" method="post" enctype="multipart/form-data" >
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Form Ajukan Komplain</b></h5>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jenis Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="radio" class="btn-check" name="jenis" id="info-outlined{{$item->id_htrans}}" autocomplete="off" value="Lapangan tidak sesuai">
                                <label class="btn btn-outline-info" for="info-outlined{{$item->id_htrans}}"><i class="bi bi-house-slash me-2"></i>Lapangan tidak sesuai</label>

                                <input type="radio" class="btn-check" name="jenis" id="danger-outlined{{$item->id_htrans}}" autocomplete="off" value="Alat tidak sesuai">
                                <label class="btn btn-outline-danger" for="danger-outlined{{$item->id_htrans}}"><i class="bi bi-box2 me-2"></i>Alat tidak sesuai</label>

                                <input type="radio" class="btn-check" name="jenis" id="primary-outlined{{$item->id_htrans}}" autocomplete="off" value="Lainnya">
                                <label class="btn btn-outline-primary" for="primary-outlined{{$item->id_htrans}}"><i class="bi bi-justify-left me-2"></i></i>Lainnya</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jelaskan Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <textarea id="myTextarea" class="form-control" name="keterangan" rows="4" cols="50" data-id="{{$item->id_htrans}}" onkeyup="updateCount(this)" placeholder="Masukkan Keterangan Komplain">{{ old('keterangan') }}</textarea>
                                <p id="charCount" data-id="{{$item->id_htrans}}">0/500</p>
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
                        <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                        <div class="d-flex justify-content-end">
                            <button data-id="{{$item->id_htrans}}" type="submit" class="komplain btn btn-success">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- <div class="row konfirmasiBatal mb-5" data-id="{{$item->id_htrans}}">
                <div class="col-md-12">
                    <form action="/customer/komplain/ajukanKomplain" method="post" enctype="multipart/form-data" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);background-color:white">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Transaksi Anda Ingin Dibatalkan Pihak Pengelola Tempat Olahraga</b></h5>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger me-3">Tolak</button>
                            <button type="submit" class="btn btn-success">Setuju</button>
                        </div>
                    </form>
                </div>
            </div> --}}
        @endforeach
    @endif
</div>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $(".close-chat-btn").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            let transaksiId = $(this).data('id'); // Mengambil data-id dari tombol yang ditekan
            $(`.form_komplain[data-id=${transaksiId}]`).hide();
        });
        
        $(".btn-danger").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form
            let transaksiId = $(this).data('id');
            console.log(transaksiId);
            swal({
                title: "Apakah anda yakin membatalkan transaksi ini?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batalkan",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var formData = $(`.batalTransaksiForm[data-id=${transaksiId}]`).serialize(); // Mengambil data dari form
    
                    $.ajax({
                        url: "/customer/transaksi/batalBooking",
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
                }
            });
        });

        // $(".form_komplain").hide();

        $(".btn-warning").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            let transaksiId = $(this).data('id'); // Mengambil data-id dari tombol yang ditekan
            $(`.form_komplain[data-id=${transaksiId}]`).show();
        });

        $(".komplain").click(function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            
            var formData = new FormData($("#form" + id)[0]);
            
            $.ajax({
                url: "/customer/komplain/ajukanKomplain",
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
    });
    function updateCount(textarea) {
        let textareaValue = textarea.value;
        let charCount = textareaValue.length;
        
        // Menggunakan DOM traversal untuk mendapatkan elemen <p> yang tepat
        let countElement = textarea.nextElementSibling;

        if (charCount > 500) {
            // Potong teks untuk membatasi hanya 500 karakter
            textarea.value = textareaValue.substring(0, 500);
            charCount = 500;
            countElement.style.color = 'red';
        } else {
            countElement.style.color = 'black';
        }

        countElement.innerText = charCount + "/500";
    }
    function showSweetAlert(button) {
        event.preventDefault();
        let transaksiId = button.getAttribute('data-id');
        console.log(transaksiId);
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
            document.querySelector(`#durasi_jam[data-id="${transaksiId}"]`).value = inputValue;
            document.querySelector(`#tambahWaktu[data-id="${transaksiId}"]`).submit();
        });

        setTimeout(function() {
            // Mengubah tipe input menjadi number setelah SweetAlert muncul
            var input = document.querySelector(".sweet-alert input");
            if (input) {
                input.type = "number";
            }
        }, 1);
}

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection