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
<section class="text-center text-lg-start">
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
                <input type="text" name="nama" id="form2Example1" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"/>
                @error('nama')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Email input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Alamat Email</label>
                  <input type="text" name="email" id="form2Example1" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"/>
                  @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Nomor input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form2Example1" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}"/>
                  @error('telepon')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-outline mb-4">
              <label class="form-label" for="form3Example1">Kota Domisili</label>
              <input type="text" class="form-control @error('kota') is-invalid @enderror" id="search-kota" name="kota" placeholder="Ketik nama kota..." value="{{old('kota')}}">
              <ul class="list-group" id="suggestion-list"></ul>
              <input type="hidden" id="selected-kota">
              @error('kota')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-outline mb-4">
                <label class="form-label" for="form3Example1">Foto KTP</label>
                <input type="file" name="ktp" id="form3Example1" class="form-control @error('ktp') is-invalid @enderror" accept=".jpg,.png,.jpeg" />
                @error('ktp')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Password</label>
                  <input type="password" name="password" id="form2Example2" class="form-control @error('password') is-invalid @enderror" />
                  @error('password')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Confirmation Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Konfirmasi Password</label>
                  <input type="password" name="konfirmasi" id="form2Example2" class="form-control @error('konfirmasi') is-invalid @enderror" />
                  @error('konfirmasi')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
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
  <script>
    const kota = ['Jakarta', 'Surabaya', 'Semarang', 'Bandung', 'Medan', 'Makassar', 'Tangerang', 'Solo', 'Sidoarjo', 'Depok', 'Malang', 'Bogor', 'Yogyakarta', 'Gresik', 'Bekasi'];
    const inputEl = document.getElementById('search-kota');
    const suggestionList = document.getElementById('suggestion-list');
    const selectedKotaInput = document.getElementById('selected-kota');

    inputEl.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        suggestionList.innerHTML = ''; // Bersihkan list sebelumnya

        if (query) {
            kota.filter(item => item.toLowerCase().includes(query)).forEach(item => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.textContent = item;
                listItem.addEventListener('click', () => {
                    inputEl.value = item;
                    selectedKotaInput.value = item;
                    suggestionList.innerHTML = ''; // Sembunyikan opsi setelah diklik
                });
                suggestionList.appendChild(listItem);
            });
        }
    });
  </script>
</section>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('assets/vendor/aos/aos.js')}}"></script>
<script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
<script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js')}}"></script>
<script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
<script src="{{ asset('assets/vendor/php-email-form/validate.js')}}"></script>

<!-- Template Main JS File -->
<script src="{{ asset('assets/js/main.js')}}"></script>
</body>