@extends('layouts.navbar_customer')

@section('content')
<style>
    .image-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* aspek rasio 4:3 */
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 50%; /* Mengurangi ukuran gambar */
        margin-top: -250px;
        margin-bottom: -250px; /* Contoh: menggeser ke atas sebanyak 50px */
    }
    .square-image-container {
        width: 100px;
        height: 100px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .square-image-container img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: block;
    }
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        .square-image-container {
            width: 60px;
            height: 60px;
        }
        .image-container {
            background-size: 100%; /* Memperbesar gambar lapangan menjadi 100% */
            margin-top: 0px;
            margin-bottom: 0px; /* Contoh: menggeser ke atas sebanyak 50px */
        }
        .left-section,
        .right-section {
            padding: 30px !important;
            margin: 0 !important;
            border: none;
            overflow-y: hidden !important; /* Disable vertical scrolling */
            height: auto !important; /* Adjust height based on content */
        }

        .left-section {
            order: 3; /* Change the order to 3, so it appears after right-section */
        }

        .right-section {
            order: 2; /* Change the order to 2, so it appears before center-section */
            border-left: none; /* Remove left border for better appearance */
        }
    }
    .tiny-card {
        width: 150px; /* Lebar tetap kartu */
        height: 50px; /* Tinggi tetap kartu */
    }

    .tiny-card .card-body {
        padding: 1px; /* Padding minimal */
    }

    .tiny-card .square-image-container img {
        height: 45px; /* Mengatur tinggi gambar agar sesuai dengan kartu */
        width: 45px;
    }

    .tiny-card .card-title {
        font-size: 10px; /* Ukuran font sangat kecil */
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .square-image-container2 {
        height: 45px;
        width: 45px;
        overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
    }
    .star:not(.filled) {
        font-size: 24px;
        cursor: pointer;
    }
    .bi-star-fill {
        color: gold;
    }
    .carousel-control-prev, .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background-color: rgba(0,0,0,0.5); /* Warna latar belakang tombol dengan sedikit transparansi */
        border-radius: 50%; /* Membuat tombol berbentuk bulat */
        border: none; /* Menghilangkan border */
        z-index: 10; /* Menjamin tombol muncul di atas gambar */
    }

    .carousel-control-prev {
        left: 10px; /* Posisi dari sisi kiri */
    }

    .carousel-control-next {
        right: 10px; /* Posisi dari sisi kanan */
    }

    .carousel-control-prev-icon, .carousel-control-next-icon {
        /* Anda bisa menambahkan style untuk ikon panah di sini, misalnya dengan mengganti gambar latar belakang */
    }
    .left-section {
        height: 100vh; /* Adjust the height as needed */
        overflow-y: auto;
        padding: 20px;
    }

    .left-section::-webkit-scrollbar,
    .right-section::-webkit-scrollbar {
        display: none;
    }
</style>
@if (!$lapangan->isEmpty())
@include("layouts.message")
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
    <!-- Carousel Gambar Lapangan -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="carouselLapangan" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $loop->index }}" {{ $loop->first ? 'class=active aria-current=true' : '' }} aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    @endif
                </div>
                <div class="carousel-inner">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="image-container" style="background-image: url('{{ asset('upload/' . $item->nama_file_lapangan) }}');"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselLapangan" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sebelumnya</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselLapangan" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Selanjutnya</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Judul Lapangan -->
    <div class="row">
        <div class="col-12">
            <h1><b>{{$lapangan->first()->nama_lapangan}}</b></h1>
        </div>
    </div>
    <p class="mb-2"><i class="bi bi-person"></i> {{$dataTempat->nama_tempat}}</p>

    <p class="text-muted"> 
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
        </svg> {{ $averageRating }} rating({{ $totalReviews }})
    </p>

    <div class="row">
        <div class="col-11">
            <h2>Rp {{number_format($lapangan->first()->harga_sewa_lapangan, 0, ',', '.')}} /jam</h2>
        </div>
    </div>
<hr>
    {{-- disini --}}
    <div class="row">
        <div class="col-lg-6 left-section">
            <h4>Lokasi Lapangan</h4>
            <p><i class="bi bi-geo-alt"></i> {{$lapangan->first()->lokasi_lapangan}}</p>
            <p>Kota {{$lapangan->first()->kota_lapangan}}</p>
            
            <h4 class="mt-5">Detail</h4>
            <ul>
                <li>Kategori: {{$kat}}</li>
                <li>Tipe : {{$lapangan->first()->tipe_lapangan}}</li>
                @php
                    $array = explode("x", $lapangan->first()->luas_lapangan);
                @endphp
                <li>Luas: {{$array[0]." m x ".$array[1]." m"}} </li>
                <li>Status : {{$lapangan->first()->status_lapangan}}</li>
            </ul>
            <h4 class="mt-5">Deskripsi Lapangan</h4>
            <p>
                {!! nl2br(e($lapangan->first()->deskripsi_lapangan)) !!}
            </p>
            <div class="row mt-4 mb-4">
                <div>
                    <h4>Jadwal Ketersediaan Lapangan</h4>
                    <!-- Tabel untuk menampilkan jadwal ketersediaan lapangan -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$dataJadwal->isEmpty())
                            @foreach ($dataJadwal as $item)
                                @php
                                    $tanggalAwal = $item->tanggal_sewa;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y');
                                @endphp
                                <tr>
                                    @if ($item->status_trans == "Diterima")
                                        <td>{{$tanggalBaru}}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i') }}</td>
                                        <td>Telah Dibooking</td>
                                    @elseif ($item->status_trans == "Berlangsung" || $item->status_extend == "Diterima")
                                        <td style="background-color:gold">{{$tanggalBaru}}</td>
                                        <td style="background-color:gold">{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa + $item->durasi_extend)->format('H:i') }}</td>
                                        <td style="background-color:gold">Sedang Dipakai</td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center">Tidak Ada Data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <div class="col-md-6 col-sm-12">
                    <h4>Jam Operasional Lapangan</h4>
                    <ul>
                        @if (!$slot->isEmpty())
                            @foreach ($slot as $item)
                            @php
                                $tanggalAwal3 = $item->jam_buka;
                                $tanggalObjek3 = DateTime::createFromFormat('H:i:s', $tanggalAwal3);
                                $tanggalBaru3 = $tanggalObjek3->format('H:i');

                                $tanggalAwal4 = $item->jam_tutup;
                                $tanggalObjek4 = DateTime::createFromFormat('H:i:s', $tanggalAwal4);
                                $tanggalBaru4 = $tanggalObjek4->format('H:i');
                            @endphp
                            <li>{{$item->hari}}: {{$tanggalBaru3}} - {{$tanggalBaru4}}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            @if (!$jam->isEmpty())
            <div class="row mt-5">
                <div class="">
                    <h4>Jam Tutup Lapangan</h4>
                    <ul>
                        
                        @foreach ($jam as $item)
                            @php
                                $tanggalAwal5 = $item->jam_mulai;
                                $tanggalObjek5 = DateTime::createFromFormat('H:i:s', $tanggalAwal5);
                                $tanggalBaru5 = $tanggalObjek5->format('H:i');

                                $tanggalAwal6 = $item->jam_selesai;
                                $tanggalObjek6 = DateTime::createFromFormat('H:i:s', $tanggalAwal6);
                                $tanggalBaru6 = $tanggalObjek6->format('H:i');

                                $tanggalAwal7 = $item->tanggal;
                                $tanggalObjek7 = DateTime::createFromFormat('Y-m-d', $tanggalAwal7);
                                $carbonDate7 = \Carbon\Carbon::parse($tanggalObjek7)->locale('id');
                                $tanggalBaru7 = $carbonDate7->isoFormat('D MMMM YYYY');
                            @endphp
                        <li>{{$tanggalBaru7}} {{$tanggalBaru5}} - {{$tanggalBaru6}}</li>
                        @endforeach
                        
                    </ul>
                </div>
            </div>
            @endif
            <div class="row mt-4">
                <div>
                    <h4>Alat Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang disewakan di lapangan ini"></i></h4>
                    @if ($permintaan->isEmpty() && $penawaran->isEmpty() && $sewa->isEmpty())
                        <p>(Tidak ada alat olahraga yang disewakan di lapangan ini)</p>
                    @endif
                    @if (!$permintaan->isEmpty())
                        @foreach ($permintaan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                            @endphp
                            <a href="/customer/detailAlat/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                <form action="/customer/transaksi/tambahAlat" method="post" class="ajax-form">
                                                    @csrf
                                                    <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                                                    <input type="hidden" name="id_alat" value="{{$dataAlat->id_alat}}">
                                                    <input type="hidden" name="nama" value="{{$dataAlat->nama_alat}}">
                                                    <input type="hidden" name="file" value="{{$dataFileAlat->nama_file_alat}}">
                                                    @php $disableButton = false @endphp

                                                    @if (Session::has("sewaAlat"))
                                                        @foreach (Session::get("sewaAlat") as $item)
                                                            @if ($item["alat"] == $dataAlat->id_alat && $item["user"] == Session::get("dataRole")->id_user)
                                                                @php $disableButton = true @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if ($disableButton)
                                                    <button type="button" class="btn btn-light btn-sm active">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                                        </svg>
                                                    </button>
                                                    @else
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i></button>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                    @if (!$penawaran->isEmpty())
                        @foreach ($penawaran as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                            @endphp
                            <a href="/customer/detailAlat/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                <form action="/customer/transaksi/tambahAlat" method="post" class="ajax-form">
                                                    @csrf
                                                    <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                                                    <input type="hidden" name="id_alat" value="{{$dataAlat->id_alat}}">
                                                    <input type="hidden" name="nama" value="{{$dataAlat->nama_alat}}">
                                                    <input type="hidden" name="file" value="{{$dataFileAlat->nama_file_alat}}">
                                                    @php $disableButton = false @endphp

                                                    @if (Session::has("sewaAlat"))
                                                        @foreach (Session::get("sewaAlat") as $item)
                                                            @if ($item["alat"] == $dataAlat->id_alat)
                                                                @php $disableButton = true @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if ($disableButton)
                                                    <button type="button" class="btn btn-light btn-sm active">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                                        </svg>
                                                    </button>
                                                    @else
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i></button>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                    @if (!$sewa->isEmpty())
                        @foreach ($sewa as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                            @endphp
                            <a href="/customer/detailAlat/{{$dataAlat->id_alat}}">
                                <div class="card h-70 mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                                <form action="/customer/transaksi/tambahAlat" method="post" class="ajax-form">
                                                    @csrf
                                                    <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                                                    <input type="hidden" name="id_alat" value="{{$dataAlat->id_alat}}">
                                                    <input type="hidden" name="nama" value="{{$dataAlat->nama_alat}}">
                                                    <input type="hidden" name="file" value="{{$dataFileAlat->nama_file_alat}}">
                                                    @php $disableButton = false @endphp

                                                    @if (Session::has("sewaAlat"))
                                                        @foreach (Session::get("sewaAlat") as $item)
                                                            @if ($item["alat"] == $dataAlat->id_alat)
                                                                @php $disableButton = true @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if ($disableButton)
                                                    <button type="button" class="btn btn-light btn-sm active">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                                        </svg>
                                                    </button>
                                                    @else
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i></button>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
            <!-- Reviews section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h4>Ulasan Lapangan</h4>
                    @if (!$rating->isEmpty())
                        @foreach ($rating as $item)
                            <div class="card mb-3">
                                <div class="card-body">
                                    @if ($item->hide == "Tidak")
                                        <h5>{{$item->nama_user}}</h5>
                                    @else
                                        <h5>Anonymous</h5>
                                    @endif
                                    @php
                                        $tanggalAwal1 = $item->created_at;
                                        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
                                        $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
                                        $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');
                                    @endphp
                                    <h6>{{$tanggalBaru1}}</h6>
                                    <!-- Tampilkan bintang -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $item->rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    <p class="mt-3">{{$item->review}}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>Tidak ada ulasan</h5>
                            </div>
                        </div>
                    @endif
                    <!-- Repeat the above card for more reviews -->
                </div>
            </div>
        </div>

        <div class="col-lg-6 right-section">
            <div class="row">
                <!-- Bagian form (menggunakan 6 kolom) -->
                <div class="col-md-12">
                    <form id="bookingForm" action="/customer/transaksi/detailTransaksi" method="get" class="mt-3 mb-4" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Atur Tanggal dan Jam Booking</b></h5>
                        </div>
                        @php
                            $tanggal = "";
                            $mulai = "";
                            $selesai = "";
                        @endphp
                        @if (Session::has("cart"))
                            @foreach (Session::get("cart") as $item)
                                @if ($item["lapangan"] == $lapangan->first()->id_lapangan)
                                    @php
                                        $tanggal = $item["tanggal"];
                                        $mulai = $item["mulai"];
                                        $selesai = $item["selesai"]
                                    @endphp
                                @endif
                            @endforeach
                        @endif
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Tanggal Sewa</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{old('tanggal') ?? $tanggal}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jam Sewa</h6>
                            </div>
                            <div class="col-md-4 col-12 mt-2 mt-md-0 mb-3">
                                <input type="time" name="mulai" id="mulai" class="form-control" onchange="forceHourOnly(this)" value="{{old('mulai') ?? $mulai}}">
                            </div>
                            <div class="col-md-4 col-12 mt-2 mt-md-0 mb-3">
                                <input type="time" name="selesai" id="selesai" class="form-control" onchange="forceHourOnly(this)" value="{{old('selesai') ?? $selesai}}">
                            </div>
                        </div>
                        @if (Session::has("sewaAlat"))
                            @if (Session::get("sewaAlat") == null)
                                <h6>(Tidak ada alat olahraga yang disewa)</h6>
                            @endif
                            <div class="d-flex flex-wrap">
                                @php
                                    $alatDitemukan = 0;
                                @endphp
                                @foreach (Session::get("sewaAlat") as $item)
                                    @if ($item["lapangan"] == $lapangan->first()->id_lapangan && $item["user"] == Session::get("dataRole")->id_user)
                                        @php
                                        $alatDitemukan += 1;
                                        @endphp
                                        <div class="card tiny-card h-70 mb-1 mr-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Gambar Alat -->
                                                    <div class="col-4">
                                                        <div class="square-image-container2">
                                                            <img src="{{ asset('upload/' . $item['file']) }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Nama Alat -->
                                                    <div class="col-8 d-flex align-items-center justify-content-between">
                                                        <h5 class="card-title truncate-text">{{$item["nama"]}}</h5>
                                                        <a href="/customer/transaksi/deleteAlat/{{$loop->iteration -1}}" class="btn btn-danger btn-sm delete-link"><i class="bi bi-x-lg"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if ($alatDitemukan == 0)
                                    {{-- <h6>(Tidak ada alat olahraga yang disewa)</h6> --}}
                                @endif
                            </div>
                        @else
                            <h6>(Tidak ada alat olahraga yang disewa)</h6>
                        @endif
                        <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                        <input type="hidden" name="id_tempat" value="{{$lapangan->first()->pemilik_lapangan}}">
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-success me-2">Booking</button>
                            @php $disableButton = false @endphp
        
                            @if (Session::has("cart"))
                                @foreach (Session::get("cart") as $item)
                                    @if ($item["lapangan"] == $lapangan->first()->id_lapangan)
                                        @php $disableButton = true @endphp
                                        @break
                                    @endif
                                @endforeach
                            @endif
                            <button type="button" id="addToCartBtn" @if($disableButton) disabled @endif class="btn btn-outline-primary">+ Favorit</button>
                            {{-- <a href="/customer/transaksi/tambahCart" class="btn btn-outline-primary">+ Keranjang</a> --}}
                        </div>
                    </form>
                    <form id="cartForm" action="/customer/tambahKeranjang" method="post" style="display: none;">
                        @csrf
                        <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                        <input type="hidden" name="tanggal">
                        <input type="hidden" name="mulai">
                        <input type="hidden" name="selesai">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();

        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // handle response dari server jika sukses
                    swal({
                        title: 'Success!',
                        text: response.message,
                        type: 'success',
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 2000); // Setelah 5 detik

                },
                error: function(xhr) {
                    // handle error
                    console.error(xhr.responseText);
                }
            });
        });

        $('.btn-light').on('click', function(e) {
            e.preventDefault();
            swal({
            title: "error!",
            text: "Alat Olahraga sudah dipilih!",
            type: "error",
            timer: 2000,  // Menampilkan selama 20 detik
            showConfirmButton: false
            });
        });

        $('.delete-link').on('click', function(e) {
            e.preventDefault();

            var link = $(this);

            $.ajax({
                url: link.attr('href'),
                method: 'GET',
                success: function(response) {
                    // handle response dari server jika sukses
                    location.reload(); // contoh: reload halaman
                },
                error: function(xhr) {
                    // handle error
                    console.error(xhr.responseText);
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('#addToCartBtn').addEventListener('click', function() {
            // Ambil data dari form booking
            event.preventDefault();
            const tanggal = document.querySelector('#bookingForm input[name="tanggal"]').value;
            const mulai = document.querySelector('#bookingForm input[name="mulai"]').value;
            const selesai = document.querySelector('#bookingForm input[name="selesai"]').value;

            // Set data ke form keranjang
            const cartForm = document.querySelector('#cartForm');
            cartForm.querySelector('input[name="tanggal"]').value = tanggal;
            cartForm.querySelector('input[name="mulai"]').value = mulai;
            cartForm.querySelector('input[name="selesai"]').value = selesai;

            // Submit form keranjang
            cartForm.submit();
        });

        //-----------------------------------------------------------------------

        const stars = document.querySelectorAll('.star');
        const ratingValueInput = document.getElementById('ratingValue');
        const ratingForm = document.getElementById('ratingForm');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                let ratingValue = this.getAttribute('data-rate');
                fillStars(ratingValue);
                ratingValueInput.value = ratingValue;
            });
        });

        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Menghentikan aksi default form submit

            $.ajax({
                url: this.action,
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    swal("Success!", "Berhasil mengirim rating!", "success");
                    window.location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "Gagal mengirim rating!", "error");
                }
            });
        });

        function fillStars(value) {
            stars.forEach((star, index) => {
                if (index < value) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        }
    });
    function forceHourOnly(input) {
        const time = input.value;
        if (time) {
            const parts = time.split(':');
            if (parts[1] !== '00') {
                input.value = `${parts[0]}:00`;
                // alert('Hanya jam penuh yang diperbolehkan!');
            }
        }
    }
    // // default tanggal besok
    // let today = new Date();
    // today.setDate(today.getDate() + 1);
    // let dd = String(today.getDate()).padStart(2, '0');
    // let mm = String(today.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
    // let yyyy = today.getFullYear();
    // let tomorrow = yyyy + '-' + mm + '-' + dd;
    // document.getElementById('tanggal').value = tomorrow;

    // default tanggal besok
    if (!document.getElementById('tanggal').value) {
        let today = new Date();
        today.setDate(today.getDate() + 1);
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
        let yyyy = today.getFullYear();
        let tomorrow = yyyy + '-' + mm + '-' + dd;
        document.getElementById('tanggal').value = tomorrow;
    }

    // default waktu mulai (sekarang)
    if (!document.getElementById('mulai').value) {
        let now = new Date();
        let hh = String(now.getHours()).padStart(2, '0');
        let currentTime = hh + ':00';
        document.getElementById('mulai').value = currentTime;
    }

    // default waktu selesai
    if (!document.getElementById('selesai').value) {
        let currentDate = new Date();
        let hoursToAdd = 2;
        currentDate.setHours(currentDate.getHours() + hoursToAdd);
        if(currentDate.getHours() >= 24) {
            currentDate.setHours(currentDate.getHours() - 24);
        }
        currentDate.setMinutes(0);
        let twoDigitHours = String(currentDate.getHours()).padStart(2, '0');
        let twoDigitMinutes = String(currentDate.getMinutes()).padStart(2, '0');
        let timeString = `${twoDigitHours}:${twoDigitMinutes}`;
        document.getElementById("selesai").value = timeString;
    }

    // document.querySelector('input[name="tanggal"]').addEventListener('change', function() {
    //     document.querySelector('#hiddenTanggal').value = this.value;
    // });

    // document.querySelector('input[name="mulai"]').addEventListener('change', function() {
    //     document.querySelector('#hiddenMulai').value = this.value;
    // });

    // document.querySelector('input[name="selesai"]').addEventListener('change', function() {
    //     document.querySelector('#hiddenSelesai').value = this.value;
    // });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@else
<h1>Lapangan Olahraga tidak tersedia</h1>
@endif
@endsection