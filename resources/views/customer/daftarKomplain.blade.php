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
</style>
<div class="container mt-5 mb-5">
    <h2 class="text-center mb-5">Daftar Komplain</h2>
    @include("layouts.message")
    @if (!$komplain->isEmpty())
        @foreach ($komplain as $item)
            <div class="card mt-5" style="max-width: 100%;" data-id="{{$item->id_komplain_trans}}">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="card-image-container">
                                <div class="square-wrapper">
                                    <img src="{{ asset('upload/' . $item->nama_file_komplain) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    @php
                                        $tanggalAwal2 = $item->waktu_komplain;
                                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                                        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                                        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY H:mm');
                                    @endphp
                                    <h6><b>Tanggal Komplain: {{$tanggalBaru2}}</b></h6>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <h6>Status: </h6>
                                    @if ($item->status_komplain == "Diterima")
                                        <h6 style="color: rgb(0, 145, 0)">{{$item->status_komplain}}</h6>
                                    @elseif ($item->status_komplain == "Ditolak")
                                        <h6 style="color: red">{{$item->status_komplain}}</h6>
                                    @elseif ($item->status_komplain == "Menunggu")
                                        <h6 style="color: rgb(239, 203, 0)">{{$item->status_komplain}}</h6>
                                    @endif
                                </div>
                                <p class="card-text"><strong>Kode Transaksi: {{$item->kode_trans}}</strong></p>
                                <h5 class="card-title"><b>{{$item->jenis_komplain}}</b></h5>
                                <p class="card-text"><strong>Keterangan: </strong>{{$item->keterangan_komplain}}</p>
                                {{-- <p class="card-text"><strong>Jam Sewa: </strong></p> --}}
                                <div class="d-flex flex-row flex-wrap">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                @if ($item->status_komplain == "Diterima")
                                    <h6>Penanganan: {{$item->penanganan_komplain}}</h6>
                                @elseif ($item->status_komplain == "Ditolak")
                                    <h6>Alasan Ditolak: {{$item->alasan_komplain}}</h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <h5>Belum ada Komplain</h5>
    @endif
</div>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        $(".btn-danger").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            swal({
                title: "Apakah anda yakin?",
                text: "Pembatalan booking akan dikenakan kompensasi sebesar 5% dari total transaksi.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batalkan",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var formData = $("#batalTransaksiForm").serialize(); // Mengambil data dari form
    
                    $.ajax({
                        url: "/customer/transaksi/batalBooking",
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

        $(".form_komplain").hide();

        $(".btn-warning").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            let transaksiId = $(this).data('id'); // Mengambil data-id dari tombol yang ditekan
            $(`.form_komplain[data-id=${transaksiId}]`).show();
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