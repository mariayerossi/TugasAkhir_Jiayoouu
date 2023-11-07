@section('title')
Sportiva
@endsection

@include('layouts.main')
<header style="background-color: #008374;">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between p-2">
        {{-- logo --}}
          <a href="/" class="logo d-flex align-items-center">
              <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
              <h1 style="font-family: 'Bruno Ace SC', cursive; color:white">sportiva</h1>
          </a>
    
        <nav id="navbar" class="navbar">
          <ul>
            <li><a href="/register">Daftar sebagai user</a></li>
            <li><a href="/registerPemilik">Daftar sebagai pemilik alat</a></li>
            <li><a href="/registerTempat">Daftar sebagai tempat olahraga</a></li>
            <li><a href="/login">Login</a></li>
          </ul>
        </nav><!-- .navbar -->
    
        <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
        <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
    
        </div>
</header>
<body>
    <style>
        .aspect-ratio-square {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* Aspek rasio 1:1 */
            background-color: #f5f5f5; /* Warna latar belakang opsional */
            overflow: hidden;
        }
    
        .aspect-ratio-square img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Untuk menjaga gambar agar tidak terdistorsi */
        }
    
        /* Pada ukuran layar kecil (mobile), tampilkan 2 produk per baris */
        @media (max-width: 768px) {
            .product-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .cascading-right {
        margin-right: -50px;
      }
  
      @media (max-width: 991.98px) {
        .cascading-right {
          margin-right: 0;
        }
      }
      .responsive-form {
        width: 50%;
    }

    /* CSS untuk layar dengan lebar maksimum 768px (misalnya tablet) */
    @media (max-width: 768px) {
        .responsive-form {
            width: 70%;
        }
    }

    /* CSS untuk layar dengan lebar maksimum 576px (misalnya smartphone) */
    @media (max-width: 576px) {
        .responsive-form {
            width: 100%;
        }
    }
    </style>
<!-- Section: Design Block -->
<section class="text-center text-lg-start">
    <!-- Jumbotron -->
    <div class="container">
      @include("layouts.message")
      <div class="d-flex justify-content-center align-items-center mb-3"> 
        <form action="/searchLapangan" method="GET" class="input-group responsive-form">
            @csrf
            <div class="input-group-prepend">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Kategori</option>
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->id_kategori}}">{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <input type="text" name="cari" class="form-control" placeholder="Cari Lapangan dan Tempat Olahraga...">
            {{-- <input type="text" name="cariPemilik" class="form-control" placeholder="Cari Tempat Olahraga..."> --}}
            <div class="input-group-append">
                <button class="btn btn-success" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <hr>
      <div class="row">
        @if (!$lapangan->isEmpty())
            @foreach ($lapangan as $item)
                <div class="col-md-3 product-col mb-4">
                    <a href="/detailLapangan/{{$item->id_lapangan}}">
                        <div class="card h-100">
                            <div class="aspect-ratio-square">
                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" class="card-img-top">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{$item->nama_lapangan}}</h5>
                                <h5 class="card-text"><b>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</b></h5>
                                <p class="card-text"><i class="bi bi-geo-alt"></i> Kota {{$item->kota_lapangan}}</p>
                                @php
                                    $averageRating = DB::table('rating_lapangan')
                                                ->where('fk_id_lapangan', $item->id_lapangan)
                                                ->avg('rating');

                                    $totalReviews = DB::table('rating_lapangan')
                                                        ->where('fk_id_lapangan', $item->id_lapangan)
                                                        ->count();

                                    $averageRating = round($averageRating, 1);
                                @endphp
                                <p class="card-text"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg> {{ $averageRating }} rating ({{ $totalReviews }})
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
    </div>
    <!-- Jumbotron -->
  </section>
  <!-- Section: Design Block -->
  
  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
</body>