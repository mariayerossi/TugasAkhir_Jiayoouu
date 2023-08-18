@extends('layouts.main')

@section('title')
Sportiva
@endsection

<body>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
            color: white;
        }
    
        #sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 25px;
            color: white;
            display: block;
            transition: 0.3s;
        }
    
        #sidebar a:hover {
            color: #f1f1f1;
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
    </style>
    
    <div id="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Contact</a>
    </div>

    <div id="main">
        <button onclick="openNav()">Open Sidebar</button>
        <!-- Konten utama Anda -->
        @yield('content')
    </div>
    <script>
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
