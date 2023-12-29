@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<style>
    .image-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* aspek rasio 4:3 */
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 60%; /* Mengurangi ukuran gambar */
        margin-top: -200px;
        margin-bottom: -200px; /* Contoh: menggeser ke atas sebanyak 50px */
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
        .image-container {
            background-size: 100%; /* Memperbesar gambar lapangan menjadi 100% */
            margin-top: 0px;
            margin-bottom: 0px; /* Contoh: menggeser ke atas sebanyak 50px */
        }
        .left-section,
        .right-section {
            padding: 30px !important;
            margin: 0 !important;
            border: none;
            overflow-y: hidden !important; /* Disable vertical scrolling */
            height: auto !important; /* Adjust height based on content */
        }

        .left-section {
            order: 3; /* Change the order to 3, so it appears after right-section */
        }

        .right-section {
            order: 2; /* Change the order to 2, so it appears before center-section */
            border-left: none; /* Remove left border for better appearance */
        }
    }
    .bi-star-fill {
        color: gold;
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

    .carousel-control-prev {
        left: 10px; /* Posisi dari sisi kiri */
    }

    .carousel-control-next {
        right: 10px; /* Posisi dari sisi kanan */
    }

    .carousel-control-prev-icon, .carousel-control-next-icon {
        /* Anda bisa menambahkan style untuk ikon panah di sini, misalnya dengan mengganti gambar latar belakang */
    }
    .left-section {
        height: 100vh; /* Adjust the height as needed */
        overflow-y: auto;
        padding: 20px;
    }

    .left-section::-webkit-scrollbar,
    .right-section::-webkit-scrollbar {
        display: none;
    }
</style>
@if (!$lapangan->isEmpty())
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    @include("layouts.message")
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
    <!-- Carousel Gambar Lapangan -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="carouselLapangan" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $loop->index }}" {{ $loop->first ? 'class=active aria-current=true' : '' }} aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    @endif
                </div>
                <div class="carousel-inner">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="image-container" style="background-image: url('{{ asset('upload/' . $item->nama_file_lapangan) }}');"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselLapangan" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselLapangan" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Selanjutnya</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Judul Lapangan -->
    <div class="row">
        <div class="col-12">
            <h1><b>{{$lapangan->first()->nama_lapangan}}</b></h1>
        </div>
    </div>
    @php
        $dataTempat  = DB::table('pihak_tempat')->where("id_tempat","=",$lapangan->first()->pemilik_lapangan)->get()->first();
    @endphp
    <p class="mb-2"><i class="bi bi-geo-alt"></i> {{$dataTempat->nama_tempat}}, Kota {{$lapangan->first()->kota_lapangan}}</p>

    @php
        $averageRating = DB::table('rating_lapangan')
                    ->where('fk_id_lapangan', $lapangan->first()->id_lapangan)
                    ->avg('rating');

        $totalReviews = DB::table('rating_lapangan')
                            ->where('fk_id_lapangan', $lapangan->first()->id_lapangan)
                            ->count();

        $averageRating = round($averageRating, 1);
    @endphp
    <p class="text-muted"> 
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
        </svg> {{ $averageRating }} rating ({{ $totalReviews }})
    </p>

    <div class="row">
        <div class="col-11">
            <h2>Rp {{number_format($lapangan->first()->harga_sewa_lapangan, 0, ',', '.')}} /jam</h2>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-6 left-section">
            <h4>Lokasi Lapangan</h4>
            <p class="mb-5"><i class="bi bi-geo-alt"></i> {{$lapangan->first()->lokasi_lapangan}}</p>

            <h4>Detail Lapangan</h4>
            <ul>
                @php
                    $kat = DB::table('kategori')->where("id_kategori","=",$lapangan->first()->fk_id_kategori)->get()->first()->nama_kategori;
                @endphp
                <li>Kategori: {{$kat}}</li>
                <li>Tipe : {{$lapangan->first()->tipe_lapangan}}</li>
                @php
                    $array = explode("x", $lapangan->first()->luas_lapangan);
                @endphp
                <li>Luas: {{$array[0]." m x ".$array[1]." m"}} </li>
                <li>Status : {{$lapangan->first()->status_lapangan}}</li>
            </ul>

            <h4 class="mt-5">Deskripsi Lapangan</h4>
            <p>
                {!! nl2br(e($lapangan->first()->deskripsi_lapangan)) !!}
            </p>
            
            <div class="row mt-5">
                <div class="col-md-6 col-sm-12">
                    <h4>Jam Operasional Lapangan</h4>
                    <ul>
                        @if (!$slot->isEmpty())
                            @foreach ($slot as $item)
                                @php
                                    $tanggalAwal3 = $item->jam_buka;
                                    $tanggalObjek3 = DateTime::createFromFormat('H:i:s', $tanggalAwal3);
                                    $tanggalBaru3 = $tanggalObjek3->format('H:i');

                                    $tanggalAwal4 = $item->jam_tutup;
                                    $tanggalObjek4 = DateTime::createFromFormat('H:i:s', $tanggalAwal4);
                                    $tanggalBaru4 = $tanggalObjek4->format('H:i');
                                @endphp
                                <li>{{$item->hari}}: {{$tanggalBaru3}} - {{$tanggalBaru4}}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="row mt-5">
                <div>
                    <h4>Alat Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang disewakan di lapangan ini"></i></h4>
                    @if ($permintaan->isEmpty() && $penawaran->isEmpty() && $sewa->isEmpty())
                        <p>(Tidak ada alat olahraga yang disewakan di lapangan ini)</p>
                    @endif
                    @if (!$permintaan->isEmpty())
                        @foreach ($permintaan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();

                                $isi = "";
                                if ($dataAlat->fk_id_pemilik == Session::get("dataRole")->id_pemilik) {
                                    $isi = "lihatDetail";
                                }
                                else {
                                    $isi = "detailAlatUmum";
                                }
                            @endphp
                            <a href="/pemilik/{{$isi}}/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                    @if (!$penawaran->isEmpty())
                        @foreach ($penawaran as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();

                                $isi = "";
                                if ($dataAlat->fk_id_pemilik == Session::get("dataRole")->id_pemilik) {
                                    $isi = "lihatDetail";
                                }
                                else {
                                    $isi = "detailAlatUmum";
                                }
                            @endphp
                            <a href="/pemilik/{{$isi}}/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                    @if (!$sewa->isEmpty())
                        @foreach ($sewa as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();

                                $isi = "";
                                if ($dataAlat->fk_id_pemilik == Session::get("dataRole")->id_pemilik) {
                                    $isi = "lihatDetail";
                                }
                                else {
                                    $isi = "detailAlatUmum";
                                }
                            @endphp
                            <a href="/pemilik/{{$isi}}/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
            <!-- Reviews section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h4>Ulasan Lapangan</h4>
                    <!-- Example of a review -->
                    @php
                        $rating = DB::table('rating_lapangan')
                                ->select("user.nama_user", "rating_lapangan.review", "rating_lapangan.rating","rating_lapangan.created_at")
                                ->join("user", "rating_lapangan.fk_id_user","=","user.id_user")
                                ->where("fk_id_lapangan","=",$lapangan->first()->id_lapangan)
                                ->orderBy("rating_lapangan.created_at","desc")
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
                    <!-- Repeat the above card for more reviews -->
                </div>
            </div>
        </div>

        <div class="col-lg-6 right-section">
            <div class="row">
                <!-- Bagian form (menggunakan 6 kolom) -->
                <div class="col-md-12">
                    <form action="/pemilik/penawaran/requestPenawaranAlat" method="post" class="mt-3 mb-4" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Atur Penawaran</b></h5>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Alat Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Pilih alat olahraga yang akan ditawarkan"></i></h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <select class="form-control" name="alat">
                                    <option value="" disabled selected>Pilih Alat Olahraga</option>
                                    @if (!$alat->isEmpty())
                                        @foreach ($alat as $item)
                                        <option value="{{$item->id_alat}}-{{$item->kota_alat}}" {{ old('alat') == $item->id_alat ." - ". $item->kota_alat ? 'selected' : '' }}>{{$item->nama_alat}} - {{$item->kota_alat}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                        <input type="hidden" name="id_tempat" value="{{$lapangan->first()->pemilik_lapangan}}">
                        <input type="hidden" name="id_pemilik" value="{{Session::get("dataRole")->id_pemilik}}">
                        <input type="hidden" name="kota_lapangan" value="{{$lapangan->first()->kota_lapangan}}">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-success">Tawarkan Alat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const kotaLapanganInput = document.querySelector('input[name="kota_lapangan"]');
        const alatSelect = document.querySelector('select[name="alat"]');

        form.addEventListener('submit', function(e) {
            let selectedOption = alatSelect.options[alatSelect.selectedIndex].value;
            let kotaAlat = selectedOption.split('-')[1];

            if (kotaLapanganInput.value !== kotaAlat) {
                e.preventDefault();

                swal({
                    title: "Apakah anda yakin?",
                    text: "Alat olahraga anda berasal dari kota yang berbeda dengan kota tempat lapangan",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Lanjutkan",
                    cancelButtonText: "Batalkan",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        // form.submit();
                    }
                });
            }
        });
    });
</script>
@else
<h1>Lapangan Olahraga tidak tersedia</h1>
@endif
@endsection