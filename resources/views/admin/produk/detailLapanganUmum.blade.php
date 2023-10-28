@extends('layouts.sidebar_admin')

@section('content')
<style>
    .image-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* aspek rasio 4:3 */
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 70%; /* Mengurangi ukuran gambar */
    }
    @media (min-width: 769px) {
        .image-container {
            margin-top: -80px;
            margin-bottom: -50px; /* Contoh: menggeser ke atas sebanyak 50px */
        }
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
</style>
@if (!$lapangan->isEmpty())
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
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
    @php
        $dataUser = DB::table('pihak_tempat')->where("id_tempat","=",$lapangan->first()->pemilik_lapangan)->get()->first()->nama_tempat;
    @endphp
    <!-- Judul Lapangan -->
    <div class="row">
        <div class="col-12">
            <h1><b>{{$lapangan->first()->nama_lapangan}}</b></h1>
        </div>
    </div>
    <p class="mb-2">{{$dataUser}}, Kota {{$lapangan->first()->kota_lapangan}}</p>
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

    <div class="row mb-4">
        <div class="col-11">
            <h2>Rp {{number_format($lapangan->first()->harga_sewa_lapangan, 0, ',', '.')}} /jam</h2>
        </div>
    </div>
    <h4>Lokasi Lapangan</h4>
    <p class="mb-5"><i class="bi bi-geo-alt"></i> {{$lapangan->first()->lokasi_lapangan}}</p>

    <!-- Deskripsi & Informasi Lainnya -->
    <div class="row">
        <div class="col-md-8">
            <h4>Deskripsi Lapangan</h4>
            <p>
                {!! nl2br(e($lapangan->first()->deskripsi_lapangan)) !!}
            </p>
        </div>
        <div class="col-md-4">
            <h2>Detail</h2>
            <ul>
                <li>Kategori: {{$lapangan->first()->kategori_lapangan}}</li>
                <li>Tipe : {{$lapangan->first()->tipe_lapangan}}</li>
                @php
                    $array = explode("x", $lapangan->first()->luas_lapangan);
                @endphp
                <li>Luas: {{$array[0]." m x ".$array[1]." m"}} </li>
            </ul>
        </div>
    </div>
    <div class="row mt-4 mb-4">
        <div class="col-md-6 col-sm-12">
            <h4>Jam Operasional Lapangan</h4>
            <ul>
                @if (!$slot->isEmpty())
                    @foreach ($slot as $item)
                        <li>{{$item->hari}} : {{$item->jam_buka}} - {{$item->jam_tutup}}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 col-sm-12">
            <h4>Alat Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang disewakan di lapangan ini"></i></h4>
            @if ($permintaan->isEmpty() && $penawaran->isEmpty() && $sewa->isEmpty())
                <p>(Tidak ada alat olahraga yang disewakan di lapangan ini)</p>
            @endif
            @if (!$permintaan->isEmpty())
                @foreach ($permintaan as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                    @endphp
                    <a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">
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
                    @endphp
                    <a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">
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
                    @endphp
                    <a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">
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
                        ->select("user.nama_user", "rating_lapangan.review", "rating_lapangan.rating")
                        ->join("user", "rating_lapangan.fk_id_user","=","user.id_user")
                        ->where("fk_id_lapangan","=",$lapangan->first()->id_lapangan)
                        ->get();
            @endphp
            @if (!$rating->isEmpty())
                @foreach ($rating as $item)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{$item->nama_user}}</h5>
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
@else
<h1>Lapangan Olahraga tidak tersedia</h1>
@endif
@endsection