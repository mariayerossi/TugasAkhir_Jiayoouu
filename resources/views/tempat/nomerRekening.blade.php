@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }

    .h-custom {
        height: calc(100% - 73px);
    }

    @media (max-width: 450px) {
        .h-custom {
            height: 100%;
        }
    }
</style>
<section class="vh-100">
    <div class="container-fluid h-custom">
        @include("layouts.message")
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="{{ asset('assets/img/detailBank.png') }}" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form action="/tempat/saldo/tambahDetail" method="post" id="tambahForm">
                    @csrf

                    @if (Session::get("dataRole")->norek_tempat == null)
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <p class="lead fw-normal mb-0 me-3">Detail Rekening</p>
                        </div>

                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Masukkan Detail Rekening Bank</p>
                        </div>

                        <div class="form-outline mb-4">
                            <!-- Input yang terlihat oleh pengguna -->
                            <select class="form-select" name="bank">
                                <option value="" disabled selected>Masukkan Nama Bank</option>
                                <option value="BCA">Bank Central Asia (BCA)</option>
                                <option value="Mandiri">Bank Mandiri</option>
                                <option value="BNI">Bank Negara Indonesia (BNI)</option>
                                <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                                <option value="BTN">Bank BTN</option>
                            </select>

                            <!-- Input tersembunyi untuk kirim ke server -->
                            <label class="form-label" for="jumlah">Nama Bank Rekening</label>
                        </div>

                        <!-- Amount input -->
                        <div class="form-outline mb-4">
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="number" class="form-control" min="1" name="noRek" id="jumlahDisplay" placeholder="Masukkan Nomer Rekening Anda">

                            <!-- Input tersembunyi untuk kirim ke server -->
                            <label class="form-label" for="jumlah">Nomer Rekening</label>
                        </div>

                        <div class="form-outline mb-4">
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="text" class="form-control" name="namaRek" placeholder="Masukkan Nama Rekening Anda">

                            <!-- Input tersembunyi untuk kirim ke server -->
                            <label class="form-label" for="jumlah">Nama Rekening</label>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-success" id="tambah">Tambah</button>
                        </div>
                    @else
                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Detail Rekening Bank</p>
                        </div>

                        <h5>Nama Bank: {{Session::get("dataRole")->nama_bank_tempat}}</h5>
                        <h5>Nomer Rekening: {{Session::get("dataRole")->norek_tempat}}</h5>
                        <h5>Nama Rekening: {{Session::get("dataRole")->nama_rek_tempat}}</h5>
                    @endif
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $("#tambah").click(function(event) {
        event.preventDefault(); // Mencegah perilaku default form

        var formData = new FormData($("#tambahForm")[0]);

        $.ajax({
            url: "/tempat/saldo/tambahDetail",
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
                    setTimeout(() => {
                        // window.history.back();
                        window.location.href = "/tempat/saldo/tarikSaldo"
                    }, 2000); // Setelah 5 detik
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
</script>
@endsection
