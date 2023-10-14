@extends('layouts.sidebar_admin')

@section('content')
<style>
    .aspect-ratio-square {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* Aspek rasio 1:1 */
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
        <form action="/admin/lapangan/searchLapangan" method="GET" class="input-group responsive-form">
            @csrf
            <div class="input-group-prepend">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Kategori</option> 
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->id_kategori}}">{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <input type="text" name="cari" class="form-control" placeholder="Cari Lapangan...">
            <input type="text" name="cariPemilik" class="form-control" placeholder="Cari Tempat Olahraga...">
            <div class="input-group-append">
                <button class="btn btn-success" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <hr>
    <div class="row mt-4">
        @if (!$lapangan->isEmpty())
            @foreach ($lapangan as $item)
                <div class="col-md-3 product-col mb-4">
                    {{-- @php
                        $dataFiles = $files->get_all_data($item->id_lapangan)->first();
                    @endphp --}}
                    <a href="/admin/lapangan/detailLapanganUmum/{{$item->id_lapangan}}">
                        <div class="card h-100">
                            <div class="aspect-ratio-square">
                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" class="card-img-top">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{$item->nama_lapangan}}</h5>
                                <h5 class="card-text"><b>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</b></h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection
