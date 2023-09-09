@extends('layouts.sidebarNavbar_pemilik')

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
</style>
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
    <p class="mb-2">Kota {{$lapangan->first()->kota_lapangan}}</p>

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
                    <h5><b>Atur Penawaran</b></h5>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Alat Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Pilih alat olahraga yang akan ditawarkan"></i></h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <select class="form-control" name="alat">
                            <option value="" disabled selected>Pilih Alat Olahraga</option>
                            @if (!$alat->isEmpty())
                                @foreach ($alat as $item)
                                <option value="{{$item->id_alat}}-{{$item->kota_alat}}" {{ old('alat') == $item->nama_alat ? 'selected' : '' }}>{{$item->nama_alat}} - {{$item->kota_alat}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_lapangan" value="{{$lapangan->first()->id_lapangan}}">
                <input type="hidden" name="id_tempat" value="{{$lapangan->first()->pemilik_lapangan}}">
                <input type="hidden" name="id_pemilik" value="{{Session::get("dataRole")->id_pemilik}}">
                <input type="hidden" name="kota_lapangan" value="{{$lapangan->first()->kota_lapangan}}">
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success">Tawarkan Alat</button>
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
            </ul>
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
</script>
@endsection