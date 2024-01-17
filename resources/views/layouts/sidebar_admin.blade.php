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
            background-color: #f8f9fc;
        }
    
        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: #007466;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 80px;
            color: rgba(255, 255, 255, 0.669);
        }
    
        #sidebar a {
            font-family: "Montserrat", sans-serif;
            padding: 10px 15px;
            font-weight: 600;
            text-decoration: none;
            font-size: 15px;
            color: white;
            display: block;
            transition: 0.3s;
        }
    
        #sidebar .coba a:hover {
            background-color: white;
            color: #007466;
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
            background-color: #006559;
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
        nav {
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 5px;
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
            text-decoration: none;
        }
        nav .coba .profile-dropdown a:hover {
            color: white;
            background-color: #007466
        }

        nav a:hover {
            color: #007466;
        }
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
            background-color: white;
            min-width: 190px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5%;
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
        /* ---------------------- */
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
        }
        
        .notif-dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .notif-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            max-width: 290px;
            max-height: 400px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5%;
        }

        .notif-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .notif-dropdown-content.active {
            display: block;
        }
        .notif-isi {
            max-height: 250px;
            border-bottom-style: solid;
            border-bottom-width: thin;
            border-top-style: solid;
            border-top-width: thin;
        }
        .notif-isi a {
            /* background-color: rgb(239, 239, 239); */
            margin-bottom: 1px;
            border-top-style: solid;
            border-top-width: thin;
        }
    </style>
    
    <div id="sidebar">
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
            <a href="/logout"><i class="bi bi-power me-2"></i>Logout</a>
        </div>
    </div>

    <div id="main">
        <nav>
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16" onclick="toggleNav()" style="cursor: pointer">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
            {{-- logo --}}
            <a href="/admin/beranda" class="logo d-flex align-items-center">
                <img class="w-20 h-20" src="{{ asset('logo2.ico')}} " alt="Logo" width="40">
                <h2 style="font-family: 'Bruno Ace SC', cursive; color:#007466">sportiva</h2>
            </a>
            <div class="coba">
                <div class="notif-dropdown me-3">
                    <div title="Notifikasi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                        </svg>
                    </div>
                    <div class="notif-dropdown-content ">
                        <h6 class="text-center m-3">Notifikasi</h6>
                        <div class="notif-isi truncate-text">
                            @php
                                $data = DB::table('notifikasi')->where("admin","=",1)->orderBy("waktu_notifikasi","DESC")->get();    
                            @endphp
                            @if (!$data->isEmpty())
                                @foreach ($data as $item)
                                    @php
                                        $tanggalAwal3 = $item->waktu_notifikasi;
                                        $tanggalObjek3 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal3);
                                        $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
                                        $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY HH:mm');
                                    @endphp
                                    <a href="{{$item->link_notifikasi}}" id="notificationLink{{$item->id_notifikasi}}" style="background-color: {{$item->status_notifikasi === 'Dibaca' ? 'white' : 'rgb(239, 239, 239)'}};">
                                        <div>
                                            <h6>{{$item->keterangan_notifikasi}}</h6>
                                            <label style="font-size:14px">{{$tanggalBaru3}}</label>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <a>
                                    <div>
                                        <h5>Tidak ada notifikasi!</h5>
                                    </div>
                                </a>
                            @endif
                        </div>
                        <a href="/notifikasi/admin/lihatNotifikasi" class="text-center">Lihat Semua Notifikasi</a>
                    </div>
                </div>
            </div>
        </nav>
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
        document.querySelector('.notif-dropdown').addEventListener('click', function() {
            let content = this.querySelector('.notif-dropdown-content');
            if (content.classList.contains('active')) {
                content.classList.remove('active');
            } else {
                // Tutup semua dropdown lain yang aktif
                document.querySelectorAll('.notif-dropdown-content.active').forEach((activeContent) => {
                    activeContent.classList.remove('active');
                });
                content.classList.add('active');
            }
        });
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.notif-dropdown')) {
                document.querySelectorAll('.notif-dropdown-content.active').forEach((activeContent) => {
                    activeContent.classList.remove('active');
                });
            }
        });
        $(document).ready(function () {
            // Click event for the notification links
            $('a[id^="notificationLink"]').on('click', function (e) {
                e.preventDefault(); // Prevent the default behavior of the link
                
                // Extract notification ID from the link's id attribute
                var notificationId = $(this).attr('id').replace('notificationLink', '');
                var hlmTujuan = $(this).attr('href');
                
                var formData = {
                    _token: '{{ csrf_token() }}', // Laravel CSRF token
                    notificationId: notificationId,
                    tujuan: hlmTujuan
                };

                // AJAX request to update the status
                $.ajax({
                    url: '/notifikasi/editStatusDibaca/' + notificationId, // Replace with your backend endpoint
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log('Notification status updated successfully.');
                        if (data.redirect) { // Change 'response' to 'data'
                            window.location.href = data.redirect; // Change 'response' to 'data'
                        }
                    },
                    error: function (error) {
                        console.error('Error updating notification status:', error);
                    }
                });
            });
        });
        function goBack() {
            window.history.back();
        }
    </script>
</body>
