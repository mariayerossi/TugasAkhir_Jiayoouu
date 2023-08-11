@section('title')
Sportiva
@endsection

@include('layouts.main')

<body style="background-color: #008374;">
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
      <div class="row g-0 align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <div class="card cascading-right" style="
              background: hsla(0, 0%, 100%, 0.826);
              backdrop-filter: blur(20px);
              ">
            <div class="card-body p-5 shadow-5 text-center">
              <h2 class="fw-bold mb-5">Register Sportiva</h2>
              <form>
                @csrf
                <!-- Name input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Nama Lengkap</label>
                  <input type="text" name="nama" id="form3Example3" class="form-control" required minlength="5"/>
                </div>
  
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Alamat Email</label>
                  <input type="email" name="email" id="form3Example3" class="form-control" required/>
                </div>

                <!-- Nomor input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example3">Nomer Telepon</label>
                  <input type="number" name="telepon" id="form3Example3" class="form-control" required/>
                </div>
  
                <!-- Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form3Example4">Password</label>
                  <input type="password" name="password" id="form3Example4" class="form-control" required minlength="8"/>
                </div>

                <!-- Confirmation Password input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example2">Konfirmasi Password</label>
                  <input type="password" name="konfirmasi" id="form2Example2" class="form-control" required minlength="8"/>
                </div>
  
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">
                  Daftar
                </button>
              </form>
              <p>Sudah memiliki akun? <a href="/login" class="link-primary">Login disini</a></p>
            </div>
          </div>
        </div>
  
        <div class="col-lg-6 mb-5 mb-lg-0">
          <img src="https://i.pinimg.com/564x/89/67/a1/8967a1a8a6ff258ef077971bd70e9e0c.jpg" class="w-100 rounded-4 shadow-4"
            alt="" />
        </div>
      </div>
    </div>
    <!-- Jumbotron -->
  </section>
  <!-- Section: Design Block -->
  <script>
    var nameField = document.querySelector("input[name=nama]");
    var emailField = document.querySelector("input[name=email]");
    var teleponField = document.querySelector("input[name=telepon]");
    var passField = document.querySelector("input[name=password]");
    var konfirField = document.querySelector("input[name=konfirmasi]");

    nameField.addEventListener("invalid", function(){
    this.setCustomValidity('');
    if (!this.validity.valid) {
        this.setCustomValidity('Nama lengkap tidak valid!');  
        }
    });

    emailField.addEventListener("invalid", function(){
    this.setCustomValidity('');
    if (!this.validity.valid) {
        this.setCustomValidity('Alamat email tidak valid!');  
        }
    });

    teleponField.addEventListener("invalid", function(){
    this.setCustomValidity('');
    if (!this.validity.valid) {
        this.setCustomValidity('Nomor Telepon tidak valid!');  
        }
    });

    passField.addEventListener("invalid", function(){
    this.setCustomValidity('');
    if (!this.validity.valid) {
        this.setCustomValidity('Password minimal 8 karakter!');  
        }
    });

    konfirField.addEventListener("invalid", function(){
    this.setCustomValidity('');
    if (!this.validity.valid) {
        this.setCustomValidity('Password minimal 8 karakter!');  
        }
    });
  </script>
</body>