@extends('layouts.sidebarNavbar_pemilik')

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

</style>
<div class="container mt-5 p-5" >
    <div class="row">
        <!-- Image section with carousel -->
        <div class="col-lg-6">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>

                <!-- Slides -->
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://static.sehatq.com/content/review/product/image/767120211206100834.jpeg" class="d-block w-100" alt="Gambar Produk 1">
                    </div>
                    <div class="carousel-item">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Contoh_Produk_Wardah.jpg" class="d-block w-100" alt="Gambar Produk 2">
                    </div>
                    <div class="carousel-item">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSByAlYO1kpX9W4XtMbhqtbbnIHyQUx3eb_pw&usqp=CAU" class="d-block w-100" alt="Gambar Produk 3">
                    </div>
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
            <h2>Nama Produk</h2>
            <p class="text-muted">Kode Produk: KODE1234</p>
            <h4>Rp 200.000,00</h4>
            <p>Deskripsi singkat produk...</p>

            <button class="btn btn-primary mt-3">Beli Sekarang</button>
        </div>
    </div>

    <!-- Additional details section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Detail Produk</h4>
            <p>Deskripsi lengkap produk, termasuk spesifikasi, fitur, dan informasi lainnya...</p>
        </div>
    </div>

    <!-- Reviews section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Ulasan Produk</h4>
            <!-- Example of a review -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Nama Pengguna</h5>
                    <p>Ulasan pengguna tentang produk ini...</p>
                </div>
            </div>
            <!-- Repeat the above card for more reviews -->
        </div>
    </div>
</div>
@endsection