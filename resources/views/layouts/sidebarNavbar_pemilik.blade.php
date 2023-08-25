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
        }
    
        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: #007466;
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
            color: rgba(255, 255, 255, 0.669);
            display: block;
            transition: 0.3s;
        }
    
        #sidebar a:hover {
            color: #ffffff;
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
        }
    
        @media screen and (max-height: 450px) {
            #sidebar {padding-top: 15px;}
            #sidebar a {font-size: 18px;}
        }

        /* Tambahkan CSS untuk navbar sederhana */
        nav {
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        nav a:hover {
            color: black;
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

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .dropdown {
            display: inline-block;
            position: relative;
        }

        .dropdown-content-sidebar {
            display: none;
            position: absolute;
            left: 0;
            width: 250px;
            background-color: #007466;
            z-index: 1;
        }

        .dropdown-content-sidebar a {
            color: rgba(255, 255, 255, 0.669);
            text-decoration: none;
            display: block;
            padding: 10px 15px;
        }

        .dropdown:hover .dropdown-content-sidebar {
            display: block;
        }
    </style>
    
    <div id="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="/beranda"><i class="bi bi-house me-3"></i>Beranda</a>
        <!-- Ini adalah struktur dropdown untuk Produk -->
        <div class="dropdown">
            <a href=""><i class="bi bi-dribbble me-3"></i>Alat Olahraga <i class="bi bi-caret-down-fill"></i></a>
            <div class="dropdown-content-sidebar">
                <a href="/masterAlat">Tambah Alat Olahraga</a>
                <a href="">Lihat Alat Olahraga</a>
                <!-- Anda bisa menambahkan lebih banyak link kategori di sini -->
            </div>
        </div>
    </div>

    <div id="main">
        <!-- Tambahkan navbar sederhana di sini -->
        <nav>
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="openNav()" style="cursor: pointer">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>

            <a href="/contact">Contact</a>

            <div class="profile-dropdown">
                <img src="../assets/img/user_icon.png" alt="Profile" class="profile-image">
                <div class="dropdown-content">
                    <a href="/editprofile">Profile</a>
                    <a href="/logout">Logout</a>
                </div>
            </div>
        </nav>

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
    </script>
    
</body>
