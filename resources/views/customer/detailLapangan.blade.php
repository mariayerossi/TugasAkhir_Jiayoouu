@extends('layouts.navbar_customer')

@section('content')
<style>
    .image-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* aspek rasio 4:3 */
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
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
</style>
@if (!$lapangan->isEmpty())
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
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
    @php
        $dataTempat  = DB::table('pihak_tempat')->where("id_tempat","=",$lapangan->first()->pemilik_lapangan)->get()->first();
    @endphp
    <p class="mb-2">{{$dataTempat->nama_tempat}}, Kota {{$lapangan->first()->kota_lapangan}}</p>

    <p class="text-muted"> 
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
        </svg> 4.5 rating
        <i class="bi bi-chat-dots ms-5"></i> 5 review
    </p>

    <div class="row">
        <div class="col-11">
            <h2>Rp {{number_format($lapangan->first()->harga_sewa_lapangan, 0, ',', '.')}} /jam</h2>
        </div>
    </div>
    <div class="row">
        <!-- Bagian form (menggunakan 6 kolom) -->
        <div class="col-md-8">
            @include("layouts.message")
            <form action="/pemilik/penawaran/requestPenawaranAlat" method="post" class="mt-3 mb-4" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                @csrf
                <div class="d-flex justify-content-center">
                    <h5><b>Atur Tanggal dan Jam Booking</b></h5>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Tanggal Sewa</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tanggal" id="" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Jam Sewa</h6>
                    </div>
                    <div class="col-md-4 col-12 mt-2 mt-md-0 mb-3">
                        <input type="time" name="mulai" class="form-control" onchange="forceHourOnly(this)">
                    </div>
                    <div class="col-md-4 col-12 mt-2 mt-md-0 mb-3">
                        <input type="time" name="selesai" id="" class="form-control" onchange="forceHourOnly(this)">
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
                            @if ($item["lapangan"] == $lapangan->first()->id_lapangan)
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
                                                <a href="/customer/transaksi/deleteAlat/{{$lapangan->first()->id_lapangan}}/{{$loop->iteration -1}}" class="btn btn-danger btn-sm delete-link"><i class="bi bi-x-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if ($alatDitemukan == 0)
                            <h6>(Tidak ada alat olahraga yang disewa)</h6>
                        @endif
                    </div>
                @else
                    <h6>(Tidak ada alat olahraga yang disewa)</h6>
                @endif
                <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                <input type="hidden" name="id_tempat" value="{{$lapangan->first()->pemilik_lapangan}}">
                <input type="hidden" name="id_user" value="{{Session::get("dataRole")->id_user}}">
                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-success me-2">Booking</button>
                    <a href="" class="btn btn-outline-primary">+ Keranjang</a>
                </div>
            </form>
        </div>
        <!-- Ruang kosong (menggunakan 6 kolom lainnya) -->
        <div class="col-md-4">
            <!-- Kosong atau Anda dapat menambahkan konten lain di sini jika diperlukan -->
        </div>
    </div>
    <h4>Lokasi Lapangan</h4>
    <p class="mb-5"><i class="bi bi-geo-alt"></i> {{$lapangan->first()->lokasi_lapangan}}</p>
    
    <!-- Deskripsi & Informasi Lainnya -->
    <div class="row">
        <div class="col-md-8">
            <h4>Deskripsi Lapangan</h4>
            <p>
                {!! nl2br(e($lapangan->first()->deskripsi_lapangan)) !!}
            </p>
        </div>
        <div class="col-md-4">
            <h2>Detail</h2>
            <ul>
                <li>Kategori: {{$lapangan->first()->kategori_lapangan}}</li>
                <li>Tipe : {{$lapangan->first()->tipe_lapangan}}</li>
                @php
                    $array = explode("x", $lapangan->first()->luas_lapangan);
                @endphp
                <li>Luas: {{$array[0]." m x ".$array[1]." m"}} </li>
                <li>Status : {{$lapangan->first()->status_lapangan}}</li>
            </ul>
        </div>
    </div>
    @php
        $dataJadwal = DB::table('htrans')
                        ->where("fk_id_lapangan","=",$lapangan->first()->id_lapangan)
                        ->where("status_trans","=","Diterima")
                        ->orWhere("status_trans","=","Berlangsung")
                        ->get();
    @endphp
    <div class="row mt-4 mb-4">
        <div class="col-md-6 col-sm-12">
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
                                <td>{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i:s') }}</td>
                                <td>Telah Dibooking</td>
                            @else
                                <td style="background-color:gold">{{$tanggalBaru}}</td>
                                <td style="background-color:gold">{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i:s') }}</td>
                                <td style="background-color:gold">Sedang Dipakai</td>
                            @endif
                        </tr>
                    @endforeach
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
                        <li>{{$item->hari}} : {{$item->jam_buka}} - {{$item->jam_tutup}}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 col-sm-12">
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
                                                    @if ($item["alat"] == $dataAlat->id_alat)
                                                        @php $disableButton = true @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif

                                            <button type="submit" class="btn btn-success btn-sm" @if($disableButton) disabled @endif><i class="bi bi-plus-lg"></i></button>
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

                                            <button type="submit" class="btn btn-success btn-sm" @if($disableButton) disabled @endif><i class="bi bi-plus-lg"></i></button>
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

                                            <button type="submit" class="btn btn-success btn-sm" @if($disableButton) disabled @endif><i class="bi bi-plus-lg"></i></button>
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
            <!-- Example of a review -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Nama Pengguna</h5>
                    <p>Ulasan pengguna tentang produk ini...</p>
                </div>
            </div>
            <!-- Repeat the above card for more reviews -->
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
                    location.reload(); // contoh: reload halaman
                },
                error: function(xhr) {
                    // handle error
                    console.error(xhr.responseText);
                }
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
        const form = document.querySelector('form');
        const kotaLapanganInput = document.querySelector('input[name="kota_lapangan"]');
        const alatSelect = document.querySelector('select[name="alat"]');

        form.addEventListener('submit', function(e) {
            let selectedOption = alatSelect.options[alatSelect.selectedIndex].value;
            let kotaAlat = selectedOption.split('-')[1];

            if (kotaLapanganInput.value !== kotaAlat) {
                e.preventDefault();

                swal({
                    title: "Apakah anda yakin?",
                    text: "Alat olahraga anda berasal dari kota yang berbeda dengan kota tempat lapangan",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Lanjutkan",
                    cancelButtonText: "Batalkan",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        form.submit();
                    }
                });
            }
        });
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
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@else
<h1>Lapangan Olahraga tidak tersedia</h1>
@endif
@endsection