@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .aspect-ratio-square {
        position: relative;
        width: 100%;
        padding-bottom: 100%; /* Aspek rasio 1:1 */
        background-color: #f5f5f5; /* Warna latar belakang opsional */
        overflow: hidden;
    }

    .aspect-ratio-square img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* Untuk menjaga gambar agar tidak terdistorsi */
    }

    /* Pada ukuran layar kecil (mobile), tampilkan 2 produk per baris */
    @media (max-width: 768px) {
        .product-col {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    /* CSS untuk Desktop dan layar besar */
    .responsive-form {
        width: 50%;
    }

    /* CSS untuk layar dengan lebar maksimum 768px (misalnya tablet) */
    @media (max-width: 768px) {
        .responsive-form {
            width: 70%;
        }
    }

    /* CSS untuk layar dengan lebar maksimum 576px (misalnya smartphone) */
    @media (max-width: 576px) {
        .responsive-form {
            width: 100%;
        }
    }
</style>

<div class="container mt-5">
    <div class="d-flex justify-content-center align-items-center mt-3 mb-3"> 
        <form action="/searchAlat" method="GET" class="input-group responsive-form">
            @csrf
            <div class="input-group-prepend">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Kategori</option> 
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->nama_kategori}}">{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <input type="text" name="cari" class="form-control" placeholder="Cari produk..."> 
            <div class="input-group-append">
                <button class="btn btn-success" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    
    <hr>
    <div class="row mt-4">
        @if (!$alat->isEmpty())
            @foreach ($alat as $item)
                @php
                    $dataFiles = $files->get_all_data($item->id_alat)->first();
                @endphp
                <a href="">
                    <div class="col-md-3 product-col mb-4">
                        <div class="card h-100">
                            <div class="aspect-ratio-square">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" class="card-img-top">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{$item->nama_alat}}</h5>
                                <h5 class="card-text"><b>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</b></h5>
                                <p class="card-text">Stok : {{$item->stok_alat}}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</div>

@endsection
