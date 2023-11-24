@section('title')
Sportiva
@endsection

@include('layouts.main')

<body>
    {{-- <div id="preloader"></div> --}}
    <style>
        body {
            font-family: "Open Sans", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            transition: margin-left 0.5s;
            background-color: #f5f5f9;
        }
    
        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: white;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 20px;
            color: rgba(255, 255, 255, 0.669);
        }
    
        #sidebar a {
            font-family: "Poppins", sans-serif;
            padding: 10px 15px;
            font-weight: 600;
            text-decoration: none;
            font-size: 16px;
            color: #007466;
            display: block;
            transition: 0.3s;
        }
    
        #sidebar .coba a:hover {
            background-color: #007466;
            color: white;
        }
    
        #sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
    
        #main {
            transition: margin-left 0.5s;
            padding: 16px;
        }
    
        @media screen and (max-height: 450px) {
            #sidebar {padding-top: 15px;}
            #sidebar a {font-size: 18px;}
        }

        /* Style for sidebar dropdown */
        .sidebar-dropdown-content {
            display: none;
            position: relative;
            background-color: #e3e3e3;
            min-width: 240px; /* match the sidebar width */
            box-shadow: none; /* you may want to remove this or adjust according to the sidebar's look */
            z-index: 1;
        }

        .sidebar-dropdown-content.active {
            display: block;
        }

        /* Adjust position of dropdown items */
        .sidebar-dropdown-content a {
            padding-left: 30px;
        }

        .sidebar-dropdown-content a:hover {
            background-color: #007466;
            color: white;
        }
        .float-back-button {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: #007466; 
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 14px;
            z-index: 1100;
            cursor: pointer;
            transition: background-color 0.3s;
            display: none; /* default akan disembunyikan */
        }

        .float-back-button:hover {
            background-color: #005744;
        }

        /* Tampilkan tombol hanya pada tampilan desktop */
        @media (min-width: 768px) {
            .float-back-button {
                display: block; /* Menampilkan tombol saat layar lebih besar dari 767px */
            }
        }
    </style>
    
    <div id="sidebar">
        {{-- logo --}}
        <a href="/admin/beranda" class="logo d-flex align-items-center">
            <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
            <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
        </a>
        <div class="coba mb-3">
            <a href="/admin/beranda"><i class="bi bi-house me-3"></i>Beranda</a>
            <div class="sidebar-dropdown">
                <a href="#"><i class="bi bi-chat-left-dots me-3"></i>Komplain <i class="bi bi-caret-down-fill"></i></a>
                <div class="sidebar-dropdown-content">
                    <a href="/admin/komplain/request/daftarKomplain">Request</a>
                    <a href="/admin/komplain/trans/daftarKomplain">Transaksi</a>
                    <!-- Add other sports or categories here -->
                </div>
            </div>
            <a href="/admin/registrasi_tempat"><i class="bi bi-check-circle me-3"></i>Konfirmasi Registrasi</a>
            <a href="/admin/masterKategori"><i class="bi bi-tags me-3"></i>Tambah Kategori</a>
            <div class="sidebar-dropdown">
                <a href="#"><i class="bi bi-box-seam me-3"></i>Daftar Produk <i class="bi bi-caret-down-fill"></i></a>
                <div class="sidebar-dropdown-content">
                    <a href="/admin/alat/cariAlat">Alat Olahraga</a>
                    <a href="/admin/lapangan/cariLapangan">Lapangan Olahraga</a>
                    <!-- Add other sports or categories here -->
                </div>
            </div>
            <div class="sidebar-dropdown">
                <a href="#"><i class="bi bi-people me-3"></i>Daftar User <i class="bi bi-caret-down-fill"></i></a>
                <div class="sidebar-dropdown-content">
                    <a href="/admin/daftarCustomer">Customer</a>
                    <a href="/admin/daftarPemilik">Pemilik Alat Olahraga</a>
                    <a href="/admin/daftarTempat">Tempat Olahraga</a>
                </div>
            </div>
            <a href="/admin/transaksi/daftarTransaksi"><i class="bi bi-journal-text me-3"></i></i>Transaksi</a>
            <a href="/admin/transaksi/daftarKerusakan"><i class="bi bi-heartbreak me-3"></i>Daftar Alat yang Rusak</a>
            <div class="sidebar-dropdown">
                <a href="#"><i class="bi bi-clipboard-data me-3"></i>Laporan <i class="bi bi-caret-down-fill"></i></a>
                <div class="sidebar-dropdown-content">
                    <a href="/admin/laporan/pendapatan/laporanPendapatan">Pendapatan</a>
                    <a href="/admin/laporan/alat/laporanAlat">Persewaan Alat Olahraga</a>
                    <a href="/admin/laporan/tempat/laporanTempat">Persewaan Tempat Olahraga</a>
                    <!-- Add other sports or categories here -->
                </div>
            </div>
            <a href="/logout"><i class="bi bi-box-arrow-right me-3"></i>Logout</a>
        </div>
    </div>

    <div id="main">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="toggleNav()" style="cursor: pointer">
            <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
        </svg>
        {{-- <button class="float-back-button mt-3" onclick="goBack()"><i class="bi bi-arrow-90deg-left"></i></button> --}}

        <!-- Konten utama Anda -->
        @yield('content')

    </div>
    
    <script>
        // openNav();
        let isNavOpen;
        function toggleNav() {
            const sidebar = document.getElementById("sidebar");
            const main = document.getElementById("main");

            if (!isNavOpen) {
                sidebar.style.left = "0";
                main.style.marginLeft = "250px";
                isNavOpen = true;
            } else {
                sidebar.style.left = "-250px";
                main.style.marginLeft = "0";
                isNavOpen = false;
            }
        }
        window.onload = function() {
            // Cek lebar layar
            if (window.innerWidth <= 768) {
                document.getElementById("sidebar").style.left = "-250px";
                isNavOpen = false; // Set status navbar ke tertutup
            } else {
                openNav();
                isNavOpen = true; // Set status navbar ke terbuka
            }
        }
        function openNav() {
            document.getElementById("sidebar").style.left = "0";
            document.getElementById("main").style.marginLeft = "250px";
        }
    
        function closeNav() {
            document.getElementById("sidebar").style.left = "-250px";
            document.getElementById("main").style.marginLeft= "0";
        }

        function toggleNav() {
            if (document.getElementById("sidebar").style.left == "-250px") {
                document.getElementById("sidebar").style.left = "0";
                document.getElementById("main").style.marginLeft = "250px";
            }
            else {
                document.getElementById("sidebar").style.left = "-250px";
                document.getElementById("main").style.marginLeft= "0";
            }
        }
        document.querySelectorAll('.sidebar-dropdown').forEach((dropdown) => {
            dropdown.addEventListener('click', function() {
                let content = this.querySelector('.sidebar-dropdown-content');
                if (content.classList.contains('active')) {
                    content.classList.remove('active');
                } else {
                    document.querySelectorAll('.sidebar-dropdown-content.active').forEach((activeContent) => {
                        activeContent.classList.remove('active');
                    });
                    content.classList.add('active');
                }
            });
        });
        function goBack() {
            window.history.back();
        }
    </script>
</body>
