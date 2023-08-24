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
        <li><a href="/register">Daftar sebagai user</a></li>
        <li><a href="/registerTempat">Daftar sebagai tempat olahraga</a></li>
        <li><a href="/login">Login</a></li>
      </ul>
    </nav><!-- .navbar -->

    <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
    <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
  </div>
<!-- Section: Design Block -->
<section class=" text-center text-lg-start">
  <style>
    .rounded-t-5 {
      border-top-left-radius: 0.5rem;
      border-top-right-radius: 0.5rem;
    }

    @media (min-width: 992px) {
      .rounded-tr-lg-0 {
        border-top-right-radius: 0;
      }

      .rounded-bl-lg-5 {
        border-bottom-left-radius: 0.5rem;
      }
    }
  </style>
  @include("layouts.message")
  <div class="card mb-3">
    <div class="row g-0 d-flex align-items-center">
      <div class="col-lg-4 d-none d-lg-flex">
        <img src="https://i.pinimg.com/564x/e5/bd/4b/e5bd4b9a4399b1d8049c8e1b73bfd3af.jpg" alt="Trendy Pants and Shoes"
          class="w-100 rounded-t-5 rounded-tr-lg-0 rounded-bl-lg-5" />
      </div>
      <div class="col-lg-8">
        <div class="card-body py-5 px-md-5">
          <h2 class="fw-bold mb-5">Register Pemilik Alat Olahraga</h2>
          <form method="POST" action="/registerPemilik" enctype="multipart/form-data">
            @csrf
            <!-- Owner input -->
            <div class="form-outline mb-4">
                <label class="form-label" for="form2Example1">Nama Lengkap Pemilik Alat Olahraga</label>
                <input type="text" name="nama" id="form2Example1" class="form-control" value="{{ old('nama') }}"/>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Email input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Alamat Email</label>
                  <input type="text" name="email" id="form2Example1" class="form-control" value="{{ old('email') }}"/>
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Nomor input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form2Example1" class="form-control" value="{{ old('telepon') }}"/>
                </div>
              </div>
            </div>

            <div class="form-outline mb-4">
                <label class="form-label" for="form3Example1">Foto KTP</label>
                <input type="file" name="ktp" id="form3Example1" class="form-control" accept=".jpg,.png,.jpeg" />
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Password</label>
                  <input type="password" name="password" id="form2Example2" class="form-control" />
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Confirmation Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Konfirmasi Password</label>
                  <input type="password" name="konfirmasi" id="form2Example2" class="form-control" />
                </div>
              </div>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-block mb-4">Daftar</button>

          </form>
        </div>
      </div>
    </div>
  </div>
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