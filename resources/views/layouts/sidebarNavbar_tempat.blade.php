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
            margin-top: 20px;
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
    
        #sidebar a:hover {
            background-color: #007466;
            color: white;
        }
    
        #sidebar .closebtn {
            position: absolute;
            top: 45px;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
    
        #main {
            transition: margin-left 0.5s;
            margin-top: 100px;
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


        /* Tambahkan CSS untuk navbar sederhana */
        nav {
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;       /* Menjadikan navbar tetap di posisi saat digulir */
            top: 0;                /* Menempatkan navbar di bagian atas */
            left: 0;               /* Menjadikan navbar meregang ke sisi kiri */
            right: 0;              /* Menjadikan navbar meregang ke sisi kanan */
            z-index: 1000;
        }
        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        nav .coba a:hover {
            color: #ffffff;
            background-color: #007466
        }

        /* Style untuk foto profil dan dropdown */
        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%; /* membuat gambar menjadi bulat */
            cursor: pointer;
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content.active {
            display: block;
        }
        /* CSS untuk tombol */
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
        <a href="/tempat/beranda"><i class="bi bi-house me-3"></i>Beranda</a>
        <a href="/tempat/cariAlat"><i class="bi bi-search me-3"></i>Cari Alat Olahraga</a>
        <div class="sidebar-dropdown">
            <a href="/tempat/lapangan/daftarLapangan"><i class="bi bi-house-add me-3"></i>Daftar Lapangan</a>
            {{-- <div class="sidebar-dropdown-content">
                <a href="/tempat/lapangan/masterLapangan">Tambah Lapangan</a>
                <a href="/tempat/lapangan/daftarLapangan">Daftar Lapangan</a>
                <!-- Add other sports or categories here -->
            </div> --}}
        </div>
        <div class="sidebar-dropdown">
            <a href="/tempat/alat/daftarAlat"><i class="bi bi-dribbble me-3"></i>Daftar Alat Olahraga</i></a>
            {{-- <div class="sidebar-dropdown-content">
                <a href="/tempat/alat/masterAlat">Tambah Alat</a>
                <a href="/tempat/alat/daftarAlat">Daftar Alat</a>
                <!-- Add other sports or categories here -->
            </div> --}}
        </div>
        <div class="sidebar-dropdown">
            <a href="#"><i class="bi bi-collection me-3"></i>Daftar Request <i class="bi bi-caret-down-fill"></i></a>
            <div class="sidebar-dropdown-content">
                <a href="/tempat/permintaan/daftarPermintaan">Permintaan</a>
                <a href="/tempat/penawaran/daftarPenawaran">Penawaran</a>
                <!-- Add other sports or categories here -->
            </div>
        </div>
        <a href="/tempat/transaksi/daftarTransaksi"><i class="bi bi-journal-text me-3"></i></i>Transaksi</a>
        <a href="/tempat/kerusakan/daftarKerusakan"><i class="bi bi-heartbreak me-3"></i>Daftar Alat yang Rusak</a>
        <div class="sidebar-dropdown">
            <a href="#"><i class="bi bi-clipboard-data me-3"></i>Laporan <i class="bi bi-caret-down-fill"></i></a>
            <div class="sidebar-dropdown-content">
                <a href="/tempat/laporan/pendapatan/laporanPendapatan">Pendapatan</a>
                <a href="/tempat/laporan/stok/laporanStok">Stok Alat Olahraga</a>
                <a href="/tempat/laporan/disewakan/laporanDisewakan">Persewaan Alat Olahraga</a>
                <a href="/tempat/laporan/lapangan/laporanLapangan">Persewaan Lapangan Olahraga</a>
                <!-- Add other sports or categories here -->
            </div>
        </div>
    </div>

    <div id="main">
        <!-- Tambahkan navbar sederhana di sini -->
        <nav>
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="toggleNav()" style="cursor: pointer">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
            {{-- logo --}}
            <a href="/tempat/beranda" class="logo d-flex align-items-center">
                <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
                <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
            </a>
            @php
                function decodePrice($encodedPrice, $key) {
                    $encodedPrice = base64_decode($encodedPrice);
                    $decodedPrice = '';
                    $priceLength = strlen($encodedPrice);
                    $keyLength = strlen($key);

                    for ($i = 0; $i < $priceLength; $i++) {
                        $decodedPrice .= $encodedPrice[$i] ^ $key[$i % $keyLength];
                    }

                    return $decodedPrice;
                }

                $saldo = decodePrice(Session::get("dataRole")->saldo_tempat, "mysecretkey");
            @endphp
            <div class="coba">
                <div class="profile-dropdown">
                    <img src="{{ asset('../assets/img/user_icon.png')}}" alt="Profile" class="profile-image">
                    <div class="dropdown-content">
                        <h6 class="m-3">{{Session::get("dataRole")->nama_pemilik_tempat}}</h6>
                        <h6 class="m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet" viewBox="0 0 16 16">
                                <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
                              </svg>: Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h6>
                        <hr>
                        {{-- <a href="/editprofile">Profile</a> --}}
                        <a href="">Tarik Saldo</a>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <button class="float-back-button mt-3" onclick="goBack()"><i class="bi bi-arrow-90deg-left"></i></button>

        <!-- Konten utama Anda -->
        @yield('content')

    </div>
    <script>
        // openNav();
        function openNav() {
            document.getElementById("sidebar").style.left = "0";
            document.getElementById("main").style.marginLeft = "250px";
        }
    
        function closeNav() {
            document.getElementById("sidebar").style.left = "-250px";
            document.getElementById("main").style.marginLeft= "0";
        }

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
        document.querySelector('.profile-dropdown').addEventListener('click', function() {
            let content = this.querySelector('.dropdown-content');
            if (content.classList.contains('active')) {
                content.classList.remove('active');
            } else {
                // Tutup semua dropdown lain yang aktif
                document.querySelectorAll('.dropdown-content.active').forEach((activeContent) => {
                    activeContent.classList.remove('active');
                });
                content.classList.add('active');
            }
        });

        // Jika user mengklik di luar dropdown, tutup dropdown
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.profile-dropdown')) {
                document.querySelectorAll('.dropdown-content.active').forEach((activeContent) => {
                    activeContent.classList.remove('active');
                });
            }
        });
        function goBack() {
            window.history.back();
        }
    </script>
</body>
