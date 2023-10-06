@extends('layouts.navbar_customer')

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
    .star:not(.filled) {
        font-size: 24px;
        cursor: pointer;
    }
    .bi-star-fill {
        color: gold;
    }
</style>
@if (!$alat->isEmpty())
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
            <p><i class="bi bi-geo-alt"></i> Kota {{$alat->first()->kota_alat}}</p>
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
                </svg> {{ $averageRating }} rating
                <i class="bi bi-chat-dots ms-5"></i> {{ $totalReviews }} review
            </p>
            <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>
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
        </div>
    </div>

    <!-- Additional details section -->
    <div class="row mt-4">
        <div class="col-12">
            <h4>Deskripsi Alat Olahraga</h4>
            <p>{!! nl2br(e($alat->first()->deskripsi_alat)) !!}</p>
        </div>
    </div>
    @php
        $cekStatus = DB::table('dtrans')
                        ->join("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                        ->where("dtrans.fk_id_alat","=",$alat->first()->id_alat)
                        ->where("htrans.fk_id_user","=",Session::get("dataRole")->id_user)
                        ->where("htrans.status_trans","=","Selesai")
                        ->get();
        $cekRating = DB::table('rating_alat')
                        ->where("fk_id_alat","=",$alat->first()->id_alat)
                        ->where("fk_id_user","=",Session::get("dataRole")->id_user)
                        ->get();
    @endphp
    @if (!$cekStatus->isEmpty() && $cekRating->isEmpty())
        <div class="row mt-5">
            <div class="col-12">
                <h4>Beri Ulasan</h4>
                <form action="/customer/rating/alat/tambahRating" method="post" id="ratingForm">
                    @csrf
                    <div class="rating-container">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star star" data-rate="{{ $i }}"></i>
                        @endfor
                        <input type="hidden" name="rating" id="ratingValue">
                    </div>
                    <div class="form-group mt-3">
                        <label for="comment">Review (opsional):</label>
                        <textarea class="form-control" name="review" id="comment" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="id_alat" value="{{$alat->first()->id_alat}}">
                    <button type="submit" class="btn btn-primary mt-2">Kirim</button>
                </form>
            </div>
        </div>
    @endif
    <!-- Reviews section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Ulasan Alat Olahraga</h4>
            @php
                $rating = DB::table('rating_alat')
                        ->select("user.nama_user", "rating_alat.review", "rating_alat.rating")
                        ->join("user", "rating_alat.fk_id_user","=","user.id_user")
                        ->where("fk_id_alat","=",$alat->first()->id_alat)
                        ->where("fk_id_user","=",Session::get("dataRole")->id_user)
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
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingValueInput = document.getElementById('ratingValue');
        const ratingForm = document.getElementById('ratingForm');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                let ratingValue = this.getAttribute('data-rate');
                fillStars(ratingValue);
                ratingValueInput.value = ratingValue;
            });
        });

        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Menghentikan aksi default form submit

            $.ajax({
                url: this.action,
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    swal("Success!", "Berhasil mengirim rating!", "success");
                    window.location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "Gagal mengirim rating!", "error");
                }
            });
        });

        function fillStars(value) {
            stars.forEach((star, index) => {
                if (index < value) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@else
<h1>Alat Olahraga tidak tersedia</h1>
@endif
@endsection