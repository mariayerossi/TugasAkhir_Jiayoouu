@section('title')
Sportiva
@endsection

@include('layouts.main')

<body style="background-color: #008374;">
  <header id="header" class="header d-flex align-items-center">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
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
        </ul>
      </nav><!-- .navbar -->

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>

      </div>
  </header>
<!-- Section: Design Block -->
<section class="text-center text-lg-start">
    <style>
      .cascading-right {
        margin-right: -50px;
      }
  
      @media (max-width: 991.98px) {
        .cascading-right {
          margin-right: 0;
        }
      }
    </style>
  
    <!-- Jumbotron -->
    <div class="container">
      @include("layouts.message")
      <div class="row g-0 align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <div class="card cascading-right" style="
              background: hsla(0, 0%, 100%, 0.826);
              backdrop-filter: blur(20px);
              ">
            <div class="card-body p-5 shadow-5 text-center">
              <h2 class="fw-bold mb-5">Login</h2>
              <form method="POST" action="/login">
                @csrf
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Alamat Email</label>
                  <input type="text" name="email" id="form3Example3" class="form-control" value="{{old('email')}}"/>
                </div>
  
                <!-- Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example4">Password</label>
                  <input type="password" name="password" id="form3Example4" class="form-control" />
                </div>
  
                {{-- <!-- Checkbox -->
                <div class="form-check d-flex justify-content-center mb-4">
                  <input class="form-check-input me-2" type="checkbox" value="" id="form2Example33" />
                  <label class="form-check-label" for="form2Example33">
                    Remember me
                  </label>
                </div> --}}
  
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">
                  Masuk
                </button>
                
              </form>
            </div>
          </div>
        </div>
  
        <div class="col-lg-6 mb-5 mb-lg-0">
          <img src="{{asset('assets/img/img_login.jpg')}}" class="w-100 rounded-4 shadow-4"
            alt="" />
        </div>
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