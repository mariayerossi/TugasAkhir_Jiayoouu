{{-- kalo "tempat.alat.detailAlat" itu buat liat detail dari alat olahraga miliknya --}}
{{-- kalo "detailAlatUmum" buat liat detail alat olahraga orang lain --}}
@extends('layouts.sidebar_admin')

@section('content')
<style>
    .container {
        background-color: white;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        height: 70%;
    }
    #productCarousel {
        max-width: 350px; /* Adjust the maximum width as needed */
    }

    #productCarousel .carousel-inner {
        height: 0;
        padding-top: 100%; /* 1:1 aspect ratio */
        position: relative;
    }

    #productCarousel .carousel-inner .carousel-item {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    #productCarousel img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    .bi-star-fill {
        color: gold;
    }
    .right-section {
        height: 100vh; /* Adjust the height as needed */
        overflow-y: auto;
        padding: 20px;
    }

    .left-section::-webkit-scrollbar,
    .right-section::-webkit-scrollbar {
        display: none;
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

    /* Responsive styles for mobile view */
    @media (max-width: 767px) {
        .left-section,
        .right-section {
            /* padding: 30px !important; */
            margin: 0 !important;
            border: none;
            overflow-y: hidden !important; /* Disable vertical scrolling */
            height: auto !important; /* Adjust height based on content */
        }
    }
</style>

@if (!$alat->isEmpty())
<div class="container mt-5 p-4 mb-5">
    <div class="d-flex justify-content-start mb-2 d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <div class="row">
        <!-- Left Section: Image and Product Details -->
        <div class="col-lg-6 left-section">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $loop->index }}" {{ $loop->first ? 'class=active aria-current=true' : '' }} aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    @endif
                </div>

                <!-- Slides -->
                <div class="carousel-inner">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('upload/' . $item->nama_file_alat)}}" class="d-block w-100" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
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

        <!-- Right Section: Product Details and Form -->
        <div class="col-lg-6 right-section">
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
                $pemilik = "";
                if ($alat->first()->fk_id_pemilik != null) {
                    $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->first()->fk_id_pemilik)->first()->nama_pemilik;
                }
                else {
                    $pemilik = DB::table('pihak_tempat')->where("id_tempat","=",$alat->first()->fk_id_tempat)->first()->nama_tempat;
                }
            @endphp
            <p><i class="bi bi-geo-alt"></i>{{$pemilik}}, Kota {{$alat->first()->kota_alat}}</p>
            <h5 class="mb-4">Komisi Pemilik Alat: Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h5>
            @php
                $harga_sewa = 0;
                $cekPermintaan = DB::table('request_permintaan')->where("req_id_alat","=",$alat->first()->id_alat)->where("status_permintaan","=","Disewakan")->get()->first();
                if ($cekPermintaan != null) {
                    $harga_sewa = $cekPermintaan->req_harga_sewa;
                }
                else {
                    $cekPenawaran = DB::table('request_penawaran')->where("req_id_alat","=",$alat->first()->id_alat)->where("status_penawaran","=","Disewakan")->get()->first();
                    if ($cekPenawaran != null) {
                        $harga_sewa = $cekPenawaran->req_harga_sewa;
                    }
                    else {
                        $cekSewa = DB::table('sewa_sendiri')->where("req_id_alat","=",$alat->first()->id_alat)->get()->first();
                        if ($cekSewa != null) {
                            $harga_sewa = $alat->first()->komisi_alat;
                        }
                    }
                }

                $keterangan = "";
                if ($harga_sewa == 0) {
                    $keterangan = "(Belum Disewakan)";
                }
            @endphp
            <h5>Harga Sewa Alat:</h5>
            <h3>Rp {{ number_format($harga_sewa, 0, ',', '.') }} /jam {{$keterangan}}</h3>
            <p class="text-muted mt-5 d-lg-none">
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
                                ->select("user.nama_user", "rating_alat.hide", "rating_alat.review", "rating_alat.rating", "rating_alat.created_at")
                                ->join("user", "rating_alat.fk_id_user","=","user.id_user")
                                ->where("fk_id_alat","=",$alat->first()->id_alat)
                                ->orderBy("rating_alat.created_at","desc")
                                ->get();
                    @endphp
                    @if (!$rating->isEmpty())
                        @foreach ($rating as $item)
                            <div class="card mb-3">
                                <div class="card-body">
                                    @if ($item->hide == "Tidak")
                                        <h5>{{$item->nama_user}}</h5>
                                @else
                                        <h5>Anonymous</h5>
                                    @endif
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
    </div>
</div>
@else
    <h1>Alat Olahraga tidak tersedia</h1>
@endif
@endsection
