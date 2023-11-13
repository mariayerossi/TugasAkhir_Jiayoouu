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
    .left-section,
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
            padding: 30px !important;
            margin: 0 !important;
            border: none;
            overflow-y: hidden; /* Disable vertical scrolling */
            height: auto; /* Adjust height based on content */
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
<div class="container mt-5 mb-5">
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
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
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
            <h5>Komisi Pemilik Alat:</h5>
            <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>
        </div>

        <!-- Center Section: Product Details, Description, and Reviews -->
        <div class="col-lg-4 center-section">
            <p class="text-muted mt-2">
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
                                ->select("user.nama_user", "rating_alat.review", "rating_alat.rating")
                                ->join("user", "rating_alat.fk_id_user","=","user.id_user")
                                ->where("fk_id_alat","=",$alat->first()->id_alat)
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
                </div>
            </div>
        </div>

        <!-- Right Section: Form for Price and Date -->
        <div class="col-lg-4 right-section">
            @include("layouts.message")
            <form action="/tempat/permintaan/requestPermintaanAlat" method="post" class="mt-3" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
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
                        <h6>Tanggal Pinjam</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tgl_mulai" id="" class="form-control" value="{{old('tgl_mulai')}}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Tanggal Kembali</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tgl_selesai" id="" class="form-control" value="{{old('tgl_selesai')}}">
                    </div>
                </div>
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
</script>
@else
    <h1>Alat Olahraga tidak tersedia</h1>
@endif
@endsection
