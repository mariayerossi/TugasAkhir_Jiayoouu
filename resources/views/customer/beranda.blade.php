@extends('layouts.navbar_customer')

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
</style>

<div class="container mt-5">
    <div class="row mt-5">
        @if (!$lapangan->isEmpty())
            @foreach ($lapangan as $item)
                <div class="col-md-3 product-col mb-4">
                    <a href="/pemilik/detailLapanganUmum/{{$item->id_lapangan}}">
                        <div class="card h-100">
                            <div class="aspect-ratio-square">
                                <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" class="card-img-top">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{$item->nama_lapangan}}</h5>
                                <h5 class="card-text"><b>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</b></h5>
                                <p class="card-text"><i class="bi bi-geo-alt"></i> Kota {{$item->kota_lapangan}}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection