@section('title')
Sportiva
@endsection

@include('layouts.main')

<body style="background-color: #008374;">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
    {{-- logo --}}
      <a href="/" class="logo d-flex align-items-center">
          <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
          <h1 style="font-family: 'Bruno Ace SC', cursive; color:white">sportiva</h1>
      </a>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a href="/registerPemilik">Daftar sebagai pemilik alat</a></li>
        <li><a href="/registerTempat">Daftar sebagai tempat olahraga</a></li>
        <li><a href="/login">Login</a></li>
      </ul>
    </nav><!-- .navbar -->

    <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
    <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
  </div>
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
    <div class="container py-4">
      @include("layouts.message")
      <div class="row g-0 align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <div class="card cascading-right" style="
              background: hsla(0, 0%, 100%, 0.826);
              backdrop-filter: blur(20px);
              ">
            <div class="card-body p-5 shadow-5 text-center">
              <h2 class="fw-bold mb-5">Register User</h2>
              <form method="POST" action="/registerUser">
                @csrf
                <!-- Name input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form3Example3">Nama Lengkap</label>
                  <input type="text" name="nama" id="form3Example3" class="form-control" value="{{ old('nama') }}"/>
                </div>
  
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Alamat Email</label>
                  <input type="text" name="email" id="form3Example3" class="form-control" value="{{ old('email') }}"/>
                </div>

                <!-- Nomor input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form3Example3" class="form-control" value="{{ old('telepon') }}"/>
                </div>
  
                <!-- Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example4">Password</label>
                  <input type="password" name="password" id="form3Example4" class="form-control"/>
                </div>

                <!-- Confirmation Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Konfirmasi Password</label>
                  <input type="password" name="konfirmasi" id="form2Example2" class="form-control"/>
                </div>
  
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">
                  Daftar
                </button>
              </form>
            </div>
          </div>
        </div>
  
        <div class="col-lg-6 mb-5 mb-lg-0">
          <img src="{{asset('assets/img/img_reg_user.jpg')}}" class="w-100 rounded-4 shadow-4"
            alt="" />
        </div>
      </div>
    </div>
    <!-- Jumbotron -->
  </section>

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