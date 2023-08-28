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
        
    </style>
    <div id="main">
        <!-- Tambahkan navbar sederhana di sini -->
        <nav>
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
        

    </div>
</body>