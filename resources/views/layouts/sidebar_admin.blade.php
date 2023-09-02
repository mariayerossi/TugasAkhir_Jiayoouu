@section('title')
Sportiva
@endsection

@include('layouts.main')

<body>
    <style>
        body {
            font-family: "Open Sans", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            transition: margin-left 0.5s;
            background-color: #f9feff;
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
            padding-top: 60px;
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

        .sidebar-dropdown:hover .sidebar-dropdown-content {
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
    </style>
    
    <div id="sidebar">
        {{-- logo --}}
        <a href="" class="logo d-flex align-items-center">
            <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
            <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
        </a>
        <div class="coba">
            <a href="/admin/beranda"><i class="bi bi-house me-3"></i>Beranda</a>
            <a href="#"><i class="bi bi-chat-left-dots me-3"></i>Komplain</a>
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
            <a href=""><i class="bi bi-clipboard-data me-3"></i>Laporan</a>
            <a href="/logout"><i class="bi bi-box-arrow-right me-3"></i>Logout</a>
        </div>
    </div>

    <div id="main">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="toggleNav()" style="cursor: pointer">
            <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
        </svg>

        <!-- Konten utama Anda -->
        @yield('content')

    </div>
    <script>
        openNav();
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
    </script>
    
</body>
