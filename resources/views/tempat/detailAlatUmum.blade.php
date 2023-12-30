@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    /* Add any existing styles here */
    .container {
        background-color: white;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    }
    .carousel-item {
        position: relative;
        width: 100%;
        padding-bottom: 100%; /* Membuat rasio 1:1 */
        overflow: hidden;
    }

    .carousel-control-prev, .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background-color: rgba(0,0,0,0.5); /* Warna latar belakang tombol dengan sedikit transparansi */
        border-radius: 50%; /* Membuat tombol berbentuk bulat */
        border: none; /* Menghilangkan border */
        z-index: 10; /* Menjamin tombol muncul di atas gambar */
    }

    .carousel-item img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bi-star-fill {
        color: gold;
    }
    /* Custom styles for the three side-by-side sections */
    .center-section,
    .right-section {
        height: 100vh; /* Adjust the height as needed */
        overflow-y: auto;
        padding: 20px;
    }

    .left-section::-webkit-scrollbar,
    .center-section::-webkit-scrollbar,
    .right-section::-webkit-scrollbar {
        display: none;
    }


    /* Responsive styles for mobile view */
    @media (max-width: 767px) {
        .left-section,
        .center-section,
        .right-section {
            /* padding: 30px !important; */
            margin: 0 !important;
            border: none;
            overflow-y: hidden !important; /* Disable vertical scrolling */
            height: auto !important; /* Adjust height based on content */
        }

        .center-section {
            order: 3; /* Change the order to 3, so it appears after right-section */
        }

        .right-section {
            order: 2; /* Change the order to 2, so it appears before center-section */
            border-left: none; /* Remove left border for better appearance */
        }
    }
</style>


@if (!$alat->isEmpty())
<div class="container mt-5 p-4 mb-5">
    <div class="d-flex justify-content-start mb-2 d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <div class="row">
        <!-- Left Section: Image, Product Name, and Rating -->
        <div class="col-lg-4 left-section">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    <!-- Add your carousel indicators code here -->
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $loop->index }}" {{ $loop->first ? 'class=active aria-current=true' : '' }} aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    @endif
                </div>

                <!-- Slides -->
                <div class="carousel-inner">
                    <!-- Add your carousel inner code here -->
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }} mt-3">
                                <img src="{{ asset('upload/' . $item->nama_file_alat)}}" class="d-block w-100" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Controls -->
                <!-- Add your carousel controls code here -->
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <div class="d-lg-none">
                <h2><b>{{ ucwords($alat->first()->nama_alat)}}</b></h2>
                @php
                    $averageRating = DB::table('rating_alat')
                                ->where('fk_id_alat', $alat->first()->id_alat)
                                ->avg('rating');

                    $totalReviews = DB::table('rating_alat')
                                        ->where('fk_id_alat', $alat->first()->id_alat)
                                        ->count();

                    $averageRating = round($averageRating, 1);
                @endphp
                <p class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg> {{ $averageRating }} rating ({{ $totalReviews }})
                </p>
                @php
                    $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->first()->fk_id_pemilik)->first()->nama_pemilik;
                @endphp
                <p><i class="bi bi-geo-alt"></i>{{$pemilik}}, Kota {{$alat->first()->kota_alat}}</p>
                <h5>Komisi Pemilik Alat:</h5>
                <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>
            </div>
            <p class="text-muted mt-4 d-none d-md-block">
                @php
                    $kat = DB::table('kategori')->where("id_kategori","=",$alat->first()->fk_id_kategori)->get()->first()->nama_kategori;
                @endphp
                Kategori : {{$kat}} <br>
                Berat : {{$alat->first()->berat_alat}} gram <br>
                @php
                    $array = explode("x", $alat->first()->ukuran_alat);
                @endphp
                Ukuran : {{$array[0]." cm x ".$array[1]." cm x ".$array[2]." cm"}} <br>
                Biaya Ganti Rugi : Rp {{number_format($alat->first()->ganti_rugi_alat, 0, ',', '.')}} <br>
                Status : {{$alat->first()->status_alat}}
            </p>
        </div>

        <!-- Center Section: Product Details, Description, and Reviews -->
        <div class="col-lg-4 center-section">
            <div class="d-none d-md-block">
                <h2><b>{{ ucwords($alat->first()->nama_alat)}}</b></h2>
                @php
                    $averageRating = DB::table('rating_alat')
                                ->where('fk_id_alat', $alat->first()->id_alat)
                                ->avg('rating');

                    $totalReviews = DB::table('rating_alat')
                                        ->where('fk_id_alat', $alat->first()->id_alat)
                                        ->count();

                    $averageRating = round($averageRating, 1);
                @endphp
                <p class="text-muted"> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg> {{ $averageRating }} rating ({{ $totalReviews }})
                </p>
                @php
                    $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->first()->fk_id_pemilik)->first()->nama_pemilik;
                @endphp
                <p><i class="bi bi-geo-alt"></i>{{$pemilik}}, Kota {{$alat->first()->kota_alat}}</p>
                <h5>Komisi Pemilik Alat:</h5>
                <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>
            </div>
            <p class="text-muted d-lg-none">
                @php
                    $kat = DB::table('kategori')->where("id_kategori","=",$alat->first()->fk_id_kategori)->get()->first()->nama_kategori;
                @endphp
                Kategori : {{$kat}} <br>
                Berat : {{$alat->first()->berat_alat}} gram <br>
                @php
                    $array = explode("x", $alat->first()->ukuran_alat);
                @endphp
                Ukuran : {{$array[0]." cm x ".$array[1]." cm x ".$array[2]." cm"}} <br>
                Biaya Ganti Rugi : Rp {{number_format($alat->first()->ganti_rugi_alat, 0, ',', '.')}} <br>
                Status : {{$alat->first()->status_alat}}
            </p>

            <!-- Additional details section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h4>Deskripsi Alat Olahraga</h4>
                    <p>{!! nl2br(e($alat->first()->deskripsi_alat)) !!}</p>
                </div>
            </div>

            <!-- Reviews section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h4>Ulasan Alat Olahraga</h4>
                    @php
                        $rating = DB::table('rating_alat')
                                ->select("user.nama_user", "rating_alat.review", "rating_alat.rating", "rating_alat.created_at")
                                ->join("user", "rating_alat.fk_id_user","=","user.id_user")
                                ->where("fk_id_alat","=",$alat->first()->id_alat)
                                ->orderBy("rating_alat.created_at","desc")
                                ->get();
                    @endphp
                    @if (!$rating->isEmpty())
                        @foreach ($rating as $item)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5>{{$item->nama_user}}</h5>
                                    @php
                                        $tanggalAwal1 = $item->created_at;
                                        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
                                        $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
                                        $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');
                                    @endphp
                                    <h6>{{$tanggalBaru1}}</h6>
                                    <!-- Tampilkan bintang -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $item->rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    <p class="mt-3">{{$item->review}}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>Tidak ada ulasan</h5>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Section: Form for Price and Date -->
        <div class="col-lg-4 right-section">
            @include("layouts.message")
            <form action="/tempat/permintaan/requestPermintaanAlat" method="post" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                @csrf
                <div class="d-flex justify-content-center">
                    <h5><b>Atur Harga dan Tanggal</b></h5>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Harga Sewa <i class="bi bi-info-circle" data-toggle="tooltip" title="Biaya sewa yang harus dibayar pelanggan saat menyewa alat (*sudah termasuk komisi pemilik alat dan tempat olahraga)"></i></h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">Rp</div>
                            </div>
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="text" class="form-control" id="sewaDisplay" placeholder="Contoh: {{ number_format($alat->first()->komisi_alat + 20000, 0, ',', '.') }}" oninput="formatNumber(this)" value="{{old('harga')}}">

                            <!-- Input tersembunyi untuk kirim ke server -->
                            <input type="hidden" name="harga" id="sewaActual" value="{{old('harga')}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Durasi Pinjam</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        {{-- <input type="date" name="tgl_mulai" id="" class="form-control" value="{{old('tgl_mulai')}}"> --}}
                        <input type="text" class="form-control" name="durasi_pinjam" value="{{old('durasi_pinjam')}}" />
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Tanggal Kembali</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tgl_selesai" id="" class="form-control" value="{{old('tgl_selesai')}}">
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Lapangan <i class="bi bi-info-circle" data-toggle="tooltip" title="Pilih lapangan mana alat olahraga ini akan digunakan."></i></h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <select class="form-control" name="lapangan">
                            <option value="" disabled selected>Pilih Lapangan</option>
                            @if (!$lapangan->isEmpty())
                                @foreach ($lapangan as $item)
                                <option value="{{$item->id_lapangan}}-{{$item->kota_lapangan}}" {{ old('lapangan') == $item->id_lapangan."-".$item->kota_lapangan ? 'selected' : '' }}>{{$item->nama_lapangan}} - {{$item->kota_lapangan}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_alat" value="{{$alat->first()->id_alat}}">
                <input type="hidden" name="id_pemilik" value="{{$alat->first()->fk_id_pemilik}}">
                <input type="hidden" name="id_tempat" value="{{Session::get("dataRole")->id_tempat}}">
                <input type="hidden" name="kota_alat" value="{{$alat->first()->kota_alat}}">
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success">Request Alat</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('sewaActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('sewaActual').value = '';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const kotaAlatInput = document.querySelector('input[name="kota_alat"]');
    const harga = document.querySelector('input[name="harga"]');
    const lapangan = document.querySelector('select[name="lapangan"]');

    form.addEventListener('submit', function(e) {
        let selectedOption = lapangan.options[lapangan.selectedIndex].value;
        e.preventDefault();
        if (selectedOption !== "" && harga.value !== "") {
            let kotaLapangan = selectedOption.split('-')[1];

            if (kotaAlatInput.value !== kotaLapangan) {

                swal({
                    title: "Apakah anda yakin?",
                    text: "Alat olahraga ini berasal dari kota yang berbeda dengan kota tempat lapangan anda",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Lanjutkan",
                    cancelButtonText: "Batalkan",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        let formData = new FormData(form);

                        $.ajax({
                            type: "POST",
                            url: "/tempat/permintaan/requestPermintaanAlat",
                            data: formData,
                            processData: false,
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
                                } else {
                                    // swal("Error!", response.message, "error");
                                    swal({
                                        title: "Error!",
                                        text: response.message,
                                        type: "error",
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                                // window.location.reload();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                            }
                        });
                    }
                });
            }
            else {
                let formData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/tempat/permintaan/requestPermintaanAlat",
                    data: formData,
                    processData: false,
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
                        } else {
                            swal({
                                title: "Error!",
                                text: response.message,
                                type: "error",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                        // window.location.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        swal({
                            title: "Error!",
                            text: 'Ada masalah saat mengirim data. Silahkan coba lagi.',
                            type: "error",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        }
        else {
            swal({
                title: "Error!",
                text: "Silahkan Isi Harga dan Pilih Lapangan Olahraga",
                type: "error",
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});


    // $("form[action='/tempat/permintaan/requestPermintaanAlat']").submit(function(e) {
    //     e.preventDefault(); // Menghentikan perilaku default (pengiriman form)

    //     $.ajax({
    //         type: "POST",
    //         url: $(this).attr('action'),
    //         data: $(this).serialize(),
    //         success: function(response) {
    //             if (response.success) {
    //                 // swal("Success!", response.message, "success");
    //                 swal({
    //                 title: "Success!",
    //                 text: response.message,
    //                 type: "success",
    //                 timer: 1500,  // Menampilkan selama 20 detik
    //                 showConfirmButton: false
    //                 });
    //             }
    //             else {
    //                 swal("Error!", response.message, "error");
    //             }
    //             window.location.reload();
    //             // alert('Berhasil Diterima!');
    //             // Atau Anda dapat mengupdate halaman dengan respons jika perlu
    //             // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
    //         },
    //         error: function(jqXHR, textStatus, errorThrown) {
    //             alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
    //         }
    //     });
    // });

    $(function() {
        $('input[name="durasi_pinjam"]').daterangepicker({
            startDate: moment().add(2, 'day'),
            endDate: moment().add(6, 'day'),
            locale: {
            format: 'DD/MM/YYYY'
            }
        });
    });
</script>
@else
    <h1>Alat Olahraga tidak tersedia</h1>
@endif
@endsection
