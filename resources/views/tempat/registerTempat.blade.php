@section('title')
Sportiva
@endsection

@include('layouts.main')

<body style="background-color: #008374;">
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
        <img src="https://i.pinimg.com/564x/5a/0e/19/5a0e19347b7d88cb8194efafccc7f8b5.jpg" alt="Trendy Pants and Shoes"
          class="w-100 rounded-t-5 rounded-tr-lg-0 rounded-bl-lg-5" />
      </div>
      <div class="col-lg-8">
        <div class="card-body py-5 px-md-5">
          <h2 class="fw-bold mb-5">Register Tempat Olahraga Sportiva</h2>
          <form method="POST" action="/registerTempat" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Name input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nama Tempat Olahraga</label>
                  <input type="text" name="nama" id="form2Example1" class="form-control" />
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Owner input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nama Lengkap Pemilik Tempat Olahraga</label>
                  <input type="text" name="pemilik" id="form2Example1" class="form-control" />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Email input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Alamat Email</label>
                  <input type="email" name="email" id="form2Example1" class="form-control" />
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Nomor input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form2Example1" class="form-control" />
                </div>
              </div>
            </div>

            <!-- 2 column grid layout with text inputs for the first and last names -->
            <div class="row">
              <div class="col-md-6 mb-4">
                <div class="form-outline">
                  <label class="form-label" for="form3Example1">Foto KTP</label>
                  <input type="file" name="ktp" id="form3Example1" class="form-control" accept=".jpg,.png,.jpeg" />
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <div class="form-outline">
                  <label class="form-label" for="form3Example2">Foto NPWP</label>
                  <input type="file" name="npwp" id="form3Example2" class="form-control" accept=".jpg,.png,.jpeg"  />
                </div>
              </div>
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
          <p>Sudah memiliki akun? <a href="/login" class="link-primary">Login disini</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
</body>