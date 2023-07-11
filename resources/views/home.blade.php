@section('title')
Sportiva
@endsection

@include('layouts.main')

<body>
  <header id="header" class="header d-flex align-items-center">

    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      {{-- logo --}}
        <a href="/" class="logo d-flex align-items-center">
            <img src="{{ asset('logo2.ico')}} " alt="Logo">
            <h1 style="font-family: 'Bruno Ace SC', cursive;">sportiva</h1>
        </a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="#hero">Beranda</a></li>
          <li><a href="#about">Tentang Kami</a></li>
          <li><a href="#produk">Produk</a></li>
          <li><a href="#contact">Hubungi Kami</a></li>
          <li><a href="">Masuk</a></li>
        </ul>
      </nav><!-- .navbar -->

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>

    </div>
  </header><!-- End Header -->
  <!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero">
    <img src="assets/img/home2-bg.jpg" alt="" data-aos="fade-in">
    <div class="container position-relative">
      <div class="row gy-5" data-aos="fade-in">
        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center text-center text-lg-start">
          <h2>Selamat Datang di <span>Sportiva</span></h2>
          <p>Kami hadir untuk memenuhi kebutuhan Anda dalam menciptakan pengalaman olahraga yang menyenangkan dan memuaskan.</p>
          <div class="d-flex justify-content-center justify-content-lg-start">
            <a href="#produk" class="btn-get-started">Lihat Produk</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Hero Section -->

  <main id="main">

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Tentang Kami</h2>
          <p>Sportiva adalah platform inovatif yang memudahkan Anda dalam peminjaman alat olahraga dan lapangan olahraga secara praktis dan efisien. Kami berkomitmen untuk menyediakan layanan terbaik bagi individu, keluarga, komunitas, maupun klub olahraga yang ingin menjalani gaya hidup sehat dan aktif. Sportiva merupakan tempat yang tepat bagi Anda yang ingin menjalani gaya hidup sehat dan aktif, tanpa harus memiliki dan menyimpan semua peralatan olahraga sendiri. Kami menyediakan beragam jenis alat olahraga berkualitas tinggi, mulai dari raket tenis, bola basket, hingga sepeda dan banyak lagi. Dengan Sportiva, Anda dapat menikmati olahraga favorit Anda tanpa perlu mengeluarkan biaya besar untuk membeli peralatan baru. Tidak hanya itu, Sportiva juga menyediakan fasilitas peminjaman lapangan olahraga. Dari lapangan sepak bola, lapangan tenis, hingga lapangan voli, kami memiliki berbagai jenis lapangan yang dapat Anda sewa untuk bermain bersama teman dan keluarga.</p>
        </div>

        <div class="row gy-4">
          <div class="col-lg-6">
            <h3>Keuntungan menggunakan Sportiva</h3>
            <img src="assets/img/about.jpg" class="img-fluid rounded-4 mb-4" alt="">
            <p>Kami berkomitmen untuk memberikan pengalaman terbaik kepada pelanggan kami. Dengan menggunakan platform Sportiva, Anda dapat dengan mudah menemukan, memesan, dan membayar alat olahraga dan lapangan sesuai dengan preferensi Anda.</p>
            <p>Nikmati kemudahan peminjaman alat olahraga dan lapangan olahraga dengan Sportiva. Mari bergabung bersama kami dalam menjalani gaya hidup sehat dan aktif. Bersiaplah untuk mengalami kepuasan dan kegembiraan dalam setiap momen olahraga Anda.</p>
          </div>
          <div class="col-lg-6">
            <div class="content ps-0 ps-lg-5">
              <p class="fst-italic">
                Berikut adalah beberapa keuntungan yang didapat oleh pengguna jika menggunakan Sportiva:
              </p>
              <ul>
                <li><i class="bi bi-check-circle-fill"></i> Dengan menggunakan Sportiva, pengguna dapat dengan mudah mengakses informasi dan detail tentang lapangan olahraga yang tersedia dan peralatan olahraga yang dapat disewa.</li>
                <li><i class="bi bi-check-circle-fill"></i> Sportiva menyediakan berbagai pilihan lapangan olahraga yang dapat disewa. Pengguna dapat menemukan lapangan sepak bola, basket, tenis, bulu tangkis, futsal, dan banyak lagi.</li>
                <li><i class="bi bi-check-circle-fill"></i> Melalui Sportiva, pengguna dapat melakukan pemesanan lapangan olahraga dan peralatan olahraga secara online. Proses pemesanan yang mudah dan sederhana memungkinkan pengguna untuk memesan dengan cepat dan efisien tanpa harus datang langsung ke tempat persewaan.</li>
                <li><i class="bi bi-check-circle-fill"></i> Sportiva memungkinkan pengguna untuk memilih waktu dan tanggal sewa yang sesuai dengan jadwal mereka. Pengguna dapat melihat ketersediaan lapangan dalam waktu nyata dan memilih waktu yang paling cocok untuk mereka.</li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </section><!-- End About Us Section -->

    <!-- ======= Stats Counter Section ======= -->
    <section id="stats-counter" class="stats-counter">
      <div class="container" data-aos="fade-up">

        <div class="row gy-4 align-items-center">

          <div class="col-lg-6">
            <img src="assets/img/home1-img.svg" alt="" class="img-fluid">
          </div>

          <div class="col-lg-6">

            <div class="stats-item d-flex align-items-center">
              <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Pelanggan Sportiva</strong> yang puas</p>
            </div><!-- End Stats Item -->

            <div class="stats-item d-flex align-items-center">
              <span data-purecounter-start="0" data-purecounter-end="453" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Lapangan Olahraga</strong> yang disewakan</p>
            </div><!-- End Stats Item -->

            <div class="stats-item d-flex align-items-center">
              <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
              <p><strong>Peralatan Olahraga</strong> yang disewakan</p>
            </div><!-- End Stats Item -->

          </div>

        </div>

      </div>
    </section><!-- End Stats Counter Section -->

    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="testimonials">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Testimoni</h2>
        </div>

        <div class="slides-3 swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <div>
                      <h3>Dian Pramudita</h3>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    Sportiva benar-benar memudahkan saya dalam mencari dan memesan lapangan olahraga. Website ini sangat mudah digunakan dan memberikan informasi yang lengkap tentang lapangan dan peralatan yang tersedia. Saya sangat puas dengan layanan mereka!
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <div>
                      <h3>Farhan Malik</h3>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    Sebagai penggemar sepak bola, saya sering menggunakan Sportiva untuk menyewa lapangan futsal. Proses pemesanan yang cepat dan efisien, serta kemampuan melihat ketersediaan lapangan dalam waktu nyata, membuat saya dapat mengatur jadwal latihan dengan mudah. Terima kasih Sportiva!
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <div>
                      <h3>Tania Fitriani</h3>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    Saya baru-baru ini mencoba Sportiva untuk menyewa lapangan dan peralatan bulu tangkis. Proses pemesanan yang sederhana dan kemampuan memberikan ulasan setelah penggunaan membuat pengalaman saya dengan Sportiva sangat memuaskan.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <div>
                      <h3>Nanda Putra</h3>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    Saya ingin mengucapkan terima kasih kepada Sportiva atas pengalaman menyewa lapangan yang luar biasa. Semua proses dari pemesanan hingga pembayaran berjalan lancar, dan lapangan yang saya sewa sesuai dengan harapan saya. Saya pasti akan merekomendasikan Sportiva kepada teman-teman saya!
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <div>
                      <h3>Hadi Nugroho</h3>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    Sportiva adalah jawaban bagi saya yang sibuk namun ingin tetap aktif secara fisik. Dengan menggunakan website ini, saya dapat merencanakan dan memesan lapangan olahraga sesuai jadwal kosong saya. Saya sangat senang dengan kepraktisan dan kenyamanan yang mereka tawarkan.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>
    </section><!-- End Testimonials Section -->

    <!-- ======= Produk Section ======= -->
    <section id="produk" class="portfolio sections-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Produk</h2>
          <p>Berikut adalah produk-produk yang disewakan di Sportiva</p>
        </div>

        <div class="portfolio-isotope" data-portfolio-filter="*" data-portfolio-layout="masonry" data-portfolio-sort="original-order" data-aos="fade-up" data-aos-delay="100">

          <div>
            <ul class="portfolio-flters">
              <li data-filter="*" class="filter-active">All</li>
              <li data-filter=".filter-lapangan">Lapangan Olahraga</li>
              <li data-filter=".filter-alat">Peralatan Olahraga</li>
            </ul><!-- End Portfolio Filters -->
          </div>

          <div class="row gy-4 portfolio-container">

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/futsal1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/futsal1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Sepak Bola</a></h4>
                  <p>Lokasi Persewaan : Jakarta Selatan<br>Luas Lapangan : 30m x 15m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-alat">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/badminton1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/badminton1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Raket Badminton</a></h4>
                  <p>Lokasi Persewaan : Semarang<br>Berat Alat : 85g</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/basketball1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/basketball1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Basket</a></h4>
                  <p>Lokasi Persewaan : Suarabaya<br>Luas Lapangan : 29m x 15m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/tenis1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/tenis1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Tenis</a></h4>
                  <p>Lokasi Persewaan : Jakarta Utara<br>Luas Lapangan : 23m x 10m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-alat">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/basketball2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/basketball2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Bola Basket Molten</a></h4>
                  <p>Lokasi Persewaan : Bandung<br>Berat Alat : 600g</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/badminton2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/badminton2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Badminton</a></h4>
                  <p>Lokasi Persewaan : Bali<br>Luas Lapangan : 13m x 6m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/voli1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/voli1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Voli</a></h4>
                  <p>Lokasi Persewaan : Surabaya<br>Luas Lapangan : 18m x 9m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-alat">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/voli2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/voli2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Bola Voli</a></h4>
                  <p>Lokasi Persewaan : Solo<br>Berat Alat : 260g</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/basketball3.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/basketball3.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Basket</a></h4>
                  <p>Lokasi Persewaan : Jakarta Barat<br>Luas Lapangan : 28m x 15m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/lari1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/lari1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Olahraga Lari</a></h4>
                  <p>Lokasi Persewaan : Jakarta Pusat<br>Luas Lapangan : 200m x 10m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/futsal2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/futsal2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Sepak Bola</a></h4>
                  <p>Lokasi Persewaan : Sidoarjo<br>Luas Lapangan : 30m x 15m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-lapangan">
              <div class="portfolio-wrap">
                <a href="assets/img/produk/golf1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/produk/golf1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <h4><a href="portfolio-details.html" title="More Details">Lapangan Golf</a></h4>
                  <p>Lokasi Persewaan : Semarang<br>Luas Lapangan : 2.500m x 100m</p>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

          </div><!-- End Produk Container -->

        </div>

      </div>
    </section><!-- End Produk Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Kontak</h2>
          <p>Silahkan hubungi kami jika ada pertanyaan yang ingin ditanyakan.</p>
        </div>

        <div class="row gx-lg-0 gy-4">

          <div class="col-lg-4">

            <div class="info-container d-flex flex-column align-items-center justify-content-center">
              <div class="info-item d-flex">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h4>Location:</h4>
                  <p>A108 Adam Street, New York, NY 535022</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h4>Email:</h4>
                  <p>info@example.com</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-phone flex-shrink-0"></i>
                <div>
                  <h4>Call:</h4>
                  <p>+1 5589 55488 55</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-clock flex-shrink-0"></i>
                <div>
                  <h4>Open Hours:</h4>
                  <p>Mon-Sat: 11AM - 23PM</p>
                </div>
              </div><!-- End Info Item -->
            </div>

          </div>

          <div class="col-lg-8">
            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="7" placeholder="Message" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->

  @include('layouts.footer')

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

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

</html>