{{-- kalo "tempat.alat.detailAlat" itu buat liat detail dari alat olahraga miliknya --}}
{{-- kalo "detailAlatUmum" buat liat detail alat olahraga orang lain --}}
@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .container {
        background-color: white;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    }
    .carousel-item {
        position: relative;
        width: 100%;
        padding-bottom: 100%; /* Membuat rasio 1:1 */
        overflow: hidden;
    }

    .carousel-item img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bi-star-fill {
        color: gold;
    }
</style>
@if (!$alat->isEmpty())
<div class="container mt-5 p-5 mb-5" >
    <div class="row">
        <!-- Image section with carousel -->
        <div class="col-lg-6">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $loop->index }}" {{ $loop->first ? 'class=active aria-current=true' : '' }} aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    @endif
                </div>

                <!-- Slides -->
                <div class="carousel-inner">
                    @if (!$files->isEmpty())
                        @foreach ($files as $item)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('upload/' . $item->nama_file_alat)}}" class="d-block w-100" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <!-- Product details section -->
        <div class="col-lg-6">
            <h2><b>{{ ucwords($alat->first()->nama_alat)}}</b></h2>
            <p><i class="bi bi-geo-alt"></i> Kota {{$alat->first()->kota_alat}}</p>
            @php
                $averageRating = DB::table('rating_alat')
                            ->where('fk_id_alat', $alat->first()->id_alat)
                            ->avg('rating');

                $totalReviews = DB::table('rating_alat')
                                    ->where('fk_id_alat', $alat->first()->id_alat)
                                    ->count();

                $averageRating = round($averageRating, 1);
            @endphp
            <p class="text-muted"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg> {{ $averageRating }} rating ({{ $totalReviews }})
            </p>
            <h3>Rp {{ number_format($alat->first()->komisi_alat, 0, ',', '.') }} /jam</h3>

            @include("layouts.message")
            <form action="/tempat/permintaan/requestPermintaanAlat" method="post" class="mt-3" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                @csrf
                <div class="d-flex justify-content-center">
                    <h5><b>Atur Harga dan Tanggal</b></h5>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Harga Sewa <i class="bi bi-info-circle" data-toggle="tooltip" title="Biaya sewa yang harus dibayar pelanggan saat menyewa alat (*sudah termasuk komisi pemilik alat dan tempat olahraga)"></i></h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">Rp</div>
                            </div>
                            <input type="number" class="form-control" min="1" name="harga" placeholder="Contoh: {{ number_format($alat->first()->komisi_alat + 20000, 0, ',', '.') }}" oninput="formatNumber(this)" value="{{old('harga')}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Tanggal Pinjam</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tgl_mulai" id="" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Tanggal Kembali</h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <input type="date" name="tgl_selesai" id="" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 mt-2">
                        <h6>Lapangan <i class="bi bi-info-circle" data-toggle="tooltip" title="Pilih lapangan mana alat olahraga ini akan digunakan."></i></h6>
                    </div>
                    <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                        <select class="form-control" name="lapangan">
                            <option value="" disabled selected>Pilih Lapangan</option>
                            @if (!$lapangan->isEmpty())
                                @foreach ($lapangan as $item)
                                <option value="{{$item->id_lapangan}}-{{$item->kota_lapangan}}" {{ old('lapangan') == $item->nama_lapangan ? 'selected' : '' }}>{{$item->nama_lapangan}} - {{$item->kota_lapangan}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_alat" value="{{$alat->first()->id_alat}}">
                <input type="hidden" name="id_pemilik" value="{{$alat->first()->fk_id_pemilik}}">
                <input type="hidden" name="id_tempat" value="{{Session::get("dataRole")->id_tempat}}">
                <input type="hidden" name="kota_alat" value="{{$alat->first()->kota_alat}}">
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success">Request Alat</button>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mt-5">
        <h4>Detail Alat Olahraga</h4>
        Kategori : {{$alat->first()->kategori_alat}} <br>
        Berat : {{$alat->first()->berat_alat}} gram <br>
        @php
            $array = explode("x", $alat->first()->ukuran_alat);
        @endphp
        Ukuran : {{$array[0]." cm x ".$array[1]." cm x ".$array[2]." cm"}} <br>
        Biaya Ganti Rugi : Rp {{number_format($alat->first()->ganti_rugi_alat, 0, ',', '.')}} <br>
        Status : {{$alat->first()->status_alat}}
    </p>

    <!-- Additional details section -->
    <div class="row mt-4">
        <div class="col-12">
            <h4>Deskripsi Alat Olahraga</h4>
            <p>{!! nl2br(e($alat->first()->deskripsi_alat)) !!}</p>
        </div>
    </div>

    <!-- Reviews section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4>Ulasan Alat Olahraga</h4>
            @php
                $rating = DB::table('rating_alat')
                        ->select("user.nama_user", "rating_alat.review", "rating_alat.rating")
                        ->join("user", "rating_alat.fk_id_user","=","user.id_user")
                        ->where("fk_id_alat","=",$alat->first()->id_alat)
                        ->get();
            @endphp
            @if (!$rating->isEmpty())
                @foreach ($rating as $item)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{$item->nama_user}}</h5>
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
        </div>
    </div>
</div>
<script>
    function formatNumber(input) {
        // Mengambil value dari input
        let value = input.value;

        // Menghapus semua titik dan karakter non-numerik lainnya
        value = value.replace(/\D/g, '');

        // Memformat ulang sebagai angka dengan pemisah ribuan titik
        value = parseFloat(value).toLocaleString('id-ID');

        // Mengembalikan format yang sudah diubah ke input
        input.value = value;
    }
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const kotaAlatInput = document.querySelector('input[name="kota_alat"]');
        const lapanganSelect = document.querySelector('select[name="lapangan"]');

        form.addEventListener('submit', function(e) {
            let selectedOption = lapanganSelect.options[lapanganSelect.selectedIndex].value;
            let kotaLapangan = selectedOption.split('-')[1];

            if (kotaAlatInput.value !== kotaLapangan) {
                e.preventDefault();

                swal({
                    title: "Apakah anda yakin?",
                    text: "Alat olahraga berasal dari kota yang berbeda dengan kota tempat lapangan anda",
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
@else
<h1>Alat Olahraga tidak tersedia</h1>
@endif
@endsection