@extends('layouts.sidebarNavbar_pemilik')

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
<section>
    <div class="container-fluid h-custom">
        @include("layouts.message")
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form id="tarikForm" action="/pemilik/saldo/tarikDana" method="post">
                    @csrf

                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start mb-4">
                        <p class="lead fw-normal mb-0 me-3">Tarik Dana</p>
                    </div>

                    <h6 class="fw-bold">Nama Bank: {{Session::get("dataRole")->nama_bank_pemilik}}</h6>
                    <h6 class="fw-bold">Nomer Rekening: {{Session::get("dataRole")->norek_pemilik}}</h6>
                    <h6 class="fw-bold">Nama Rekening: {{Session::get("dataRole")->nama_rek_pemilik}}</h6>

                    <!-- Amount input -->
                    <div class="form-outline mb-4 mt-3">
                        <!-- Input yang terlihat oleh pengguna -->
                        <input type="text" class="form-control" id="jumlahDisplay" placeholder="Masukkan Nominal yang Ingin Ditarik..." oninput="formatNumber(this)">

                        <!-- Input tersembunyi untuk kirim ke server -->
                        <input type="hidden" name="jumlah" id="jumlahActual">
                        <label class="form-label" for="jumlah">Nominal Penarikan</label>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-success" id="tarik">Tarik</button>
                    </div>
                </form>
            </div>
            <div class="col-md-9 col-lg-6 col-xl-5">
                {{-- <img src="{{ asset('assets/img/tarikDana.png') }}" class="img-fluid" alt="Sample image"> --}}
                <div class="card mt-3 mb-5 p-3">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal Penarikan</th>
                                    <th>Total Penarikan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!$dana->isEmpty())
                                    @foreach ($dana as $item)
                                        <tr>
                                            @php
                                                $tanggalAwal2 = $item->tanggal_tarik;
                                                $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                                $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                                                $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');
                                            @endphp
                                            <td>{{$tanggalBaru2}}</td>
                                            <td>Rp {{number_format($item->total_tarik, 0, ',', '.')}}</td>
                                            <td>{{$item->status_tarik}}</td>
                                        </tr>
                                    @endforeach
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
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak Ada Data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('jumlahActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('jumlahActual').value = '';
        }
    }
    $(document).ready(function () {
        $("#tarik").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form

            var formData = $("#tarikForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/pemilik/saldo/tarikDana",
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
    })
</script>
@endsection
