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
  <div class="card mb-3">
    <div class="row g-0 d-flex align-items-center">
      <div class="col-lg-4 d-none d-lg-flex">
        <img src="https://i.pinimg.com/564x/e5/bd/4b/e5bd4b9a4399b1d8049c8e1b73bfd3af.jpg" alt="Trendy Pants and Shoes"
          class="w-100 rounded-t-5 rounded-tr-lg-0 rounded-bl-lg-5" />
      </div>
      <div class="col-lg-8">
        <div class="card-body py-5 px-md-5">
          <h2 class="fw-bold mb-5">Register Pemilik Alat Olahraga Sportiva</h2>
          <form method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Owner input -->
            <div class="form-outline mb-4">
                <label class="form-label" for="form2Example1">Nama Lengkap Pemilik Alat Olahraga</label>
                <input type="text" name="pemilik" id="form2Example1" class="form-control" required minlength="5"/>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Email input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Alamat Email</label>
                  <input type="email" name="email" id="form2Example1" class="form-control" required/>
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Nomor input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example1">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form2Example1" class="form-control" required/>
                </div>
              </div>
            </div>

            <div class="form-outline mb-4">
                <label class="form-label" for="form3Example1">Foto KTP</label>
                <input type="file" name="ktp" id="form3Example1" class="form-control" accept=".jpg,.png,.jpeg" required/>
            </div>

            <div class="row">
              <div class="col-md-6 mb-4">
                <!-- Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Password</label>
                  <input type="password" name="password" id="form2Example2" class="form-control" required minlength="8"/>
                </div>
              </div>
              <div class="col-md-6 mb-4">
                <!-- Confirmation Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Konfirmasi Password</label>
                  <input type="password" name="konfirmasi" id="form2Example2" class="form-control" required minlength="8"/>
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
</body>