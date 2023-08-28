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
            background-color: #f3feff;
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

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
    
    <div id="main">
        <!-- Tambahkan navbar sederhana di sini -->
        <nav>
            {{-- logo --}}
            <a href="" class="logo d-flex align-items-center">
                <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
                <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
            </a>
            <div class="coba">
                <div class="profile-dropdown">
                    <img src="../assets/img/user_icon.png" alt="Profile" class="profile-image">
                    <div class="dropdown-content">
                        <a href="/editprofile">Profile</a>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Konten utama Anda -->
        

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
