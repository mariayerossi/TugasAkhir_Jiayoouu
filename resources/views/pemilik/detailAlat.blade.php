@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<style>
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

</style>
<div class="container mt-5 p-5 mb-5" >
    <div class="row">
        <!-- Image section with carousel -->
        <div class="col-lg-6">
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
        </div>

        <!-- Product details section -->
        <div class="col-lg-6">
            <h2><b>{{ ucwords($alat->first()->nama_alat)}}</b></h2>
            <p class="text-muted"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg> 4.5 rating
                <i class="bi bi-chat-dots ms-5"></i> 5 review
            </p>
            <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>
            <h5 class="mt-4">Stok : {{$alat->first()->stok_alat}}</h5>
            <p class="text-muted mt-2">
                Kategori : {{$alat->first()->kategori_alat}} <br>
                Berat : {{$alat->first()->berat_alat}} gram <br>
                @php
                    $array = explode("x", $alat->first()->ukuran_alat);
                @endphp
                Ukuran : {{$array[0]." cm x ".$array[1]." cm x ".$array[2]." cm"}} <br>
                Biaya Ganti Rugi : Rp {{number_format($alat->first()->ganti_rugi_alat, 0, ',', '.')}} <br>
                Status : {{$alat->first()->status_alat}}
            </p>

            <a href="/editAlatdiPemilik/{{$alat->first()->id_alat}}" class="btn btn-primary mt-3">Ubah Detail Alat</a>
        </div>
    </div>

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
            <!-- Example of a review -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Nama Pengguna</h5>
                    <p>Ulasan pengguna tentang produk ini...</p>
                </div>
            </div>
            <!-- Repeat the above card for more reviews -->
        </div>
    </div>
</div>
@endsection