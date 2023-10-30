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
    
        #main {
            transition: margin-left 0.5s;
            margin-top: 100px;
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
            color: black;
            margin: 0 10px;
            text-decoration: none;
        }
        nav .profile-dropdown a:hover {
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
        /* CSS untuk sidebar */
        .sidebar {
            position: fixed;
            left: -300px; /* inisial di luar viewport */
            top: 0;
            width: 300px;
            height: 100vh;
            background-color: white;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            overflow-x: hidden;
            transition: left 0.3s;
            z-index: 2000;
            padding-top: 20px;
        }
        
        .sidebar a {
            font-family: "Poppins", sans-serif;
            padding: 10px 15px;
            font-weight: 600;
            text-decoration: none;
            font-size: 16px;
            color: #007466;
            display: block;
            transition: 0.3s;
        }

        .sidebar.active {
            left: 0; /* tampilkan saat active */
        }

        /* CSS untuk hamburger */
        .hamburger {
            display: none; /* sembunyikan inisial */
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            nav .logo, .coba {
                display: none !important; /* sembunyikan logo dan profil di navbar */
            }
        }
        .sidebar .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        @media screen and (min-width: 992px) { 
            .search-form-container {
                width: 700px;
            }
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
        @media (min-width: 768px) {
            .float-back-button {
                display: block; /* Menampilkan tombol saat layar lebih besar dari 767px */
            }
        }
    </style>
    
    <div id="main">
        <!-- Tambahkan navbar sederhana di sini -->
        <nav>
            <div class="hamburger">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="toggleNav()" style="cursor: pointer">
                    <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
            {{-- logo --}}
            <a href="/customer/beranda" class="logo d-flex align-items-center">
                <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
                <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
            </a>
            <div class="search-form-container d-flex justify-content-center align-items-center mt-3 mb-3"> 
                <form action="/customer/searchLapangan" method="GET" class="input-group">
                    @csrf
                    <div class="input-group-prepend">
                        <select class="form-control" name="kategori">
                            <option value="" disabled selected>Kategori</option> 
                            @if (!$kategori->isEmpty())
                                @foreach ($kategori as $item)
                                <option value="{{$item->id_kategori}}">{{$item->nama_kategori}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <input type="text" name="cari" class="form-control" placeholder="Cari Lapangan atau Tempat Olahraga"> 
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="coba">
                {{-- daftar komplain --}}
                <a href="/customer/daftarKomplain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                    </svg>
                </a>
                {{-- cart --}}
                <a href="/customer/daftarKeranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                    </svg>
                </a>
                {{-- riwyat transaksi --}}
                <a href="/customer/daftarRiwayat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                    </svg>
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

                    $saldo = decodePrice(Session::get("dataRole")->saldo_user, "mysecretkey");
                @endphp
                <div class="profile-dropdown ms-3">
                    <img src="{{ asset('assets/img/user_icon.png')}}" alt="Profile" class="profile-image">
                    <div class="dropdown-content">
                        <h6 class="m-3">{{Session::get("dataRole")->nama_user}}</h6>
                        <h6 class="m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet" viewBox="0 0 16 16">
                                <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
                              </svg>: Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h6>
                        <hr>
                        {{-- <a href="/customer/editProfile">Profile</a> --}}
                        <a href="/customer/saldo/topupSaldo">Top Up Saldo</a>
                        {{-- <a href="">Ulasan</a> --}}
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <button class="float-back-button mt-3" onclick="goBack()"><i class="bi bi-arrow-90deg-left"></i></button>
        <div class="sidebar">
            <!-- Logo dan profil Anda -->
            <div class="logo-container">
                <a href="/customer/beranda" class="logo d-flex align-items-center">
                    <img class="w-20 h-20" src="{{ asset('logo2.ico') }}" alt="Logo" width="40">
                    <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
                </a>
            </div>
            <div class="d-flex align-items-center ms-3">
                <img src="{{ asset('assets/img/user_icon.png')}}" alt="Profile" class="profile-image">
                <h5 class="m-3">{{Session::get("dataRole")->nama_user}}</h5>
            </div>
            <h6 style="margin-left: 70px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet" viewBox="0 0 16 16">
                    <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
                  </svg>: Rp {{ number_format($saldo, 0, ',', '.') }}
            </h6>
            <a href="/customer/saldo/topupSaldo" class="ms-5">Top Up Saldo</a>
            <hr>
            {{-- <a href="/customer/editProfile" class="ms-3"><i class="bi bi-pencil-square me-2"></i>Edit Profile</a> --}}
            <a href="/logout" class="ms-3"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            <hr>
            <a href="/customer/daftarKeranjang" class="ms-3"><i class="bi bi-cart2 me-2"></i>Keranjang</a>
            <a href="/customer/daftarRiwayat" class="ms-3"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</a>
            <a href="/customer/daftarRiwayat" class="ms-3"><i class="bi bi-pencil-square me-2"></i></i>Daftar Komplain</a>
        </div>

        <!-- Konten utama Anda -->
        @yield('content')

    </div>
    <script>
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
        document.querySelector('.hamburger').addEventListener('click', function() {
            let sidebar = document.querySelector('.sidebar');
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            } else {
                sidebar.classList.add('active');
            }
        });
        document.addEventListener('click', function(event) {
            let sidebar = document.querySelector('.sidebar');
            if (!event.target.closest('.sidebar') && !event.target.closest('.hamburger')) {
                sidebar.classList.remove('active');
            }
        });
        function goBack() {
            window.history.back();
        }
    </script>
    
</body>
