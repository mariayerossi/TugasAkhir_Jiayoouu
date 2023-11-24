@extends('layouts.navbar_customer')

@section('content')
<style>
    .card-image-container {
        width: 70%;  /* mengurangi lebar dari 100% ke 80% */
        padding-top: 70%; /* rasio tetap 1:1 karena padding-top sama dengan lebar */
        position: relative;
        margin: 0 auto; /* pusatkan container gambar */
    }

    .card-image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
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
<div class="container mt-5">
    <h2 class="text-center mb-5">Daftar Favorit</h2>
    @include("layouts.message")
    <div class="d-flex justify-content-start">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
    @if($data != null)
        @foreach($data as $item)
        <a href="/customer/detailLapangan/{{$item->id_lapangan}}">
        <div class="card mb-3" style="max-width: 100%;">
            <div class="card-body">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="card-image-container">
                            <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card-body">
                            <h5 class="card-title"><b>{{$item->nama_lapangan}}</b></h5>
                            @php
                                $tanggalAwal2 = $item->tanggal;
                                if ($tanggalAwal2 != null) {
                                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                    $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                                    $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
                                }
                                else {
                                    $tanggalBaru2 = "(Anda belum menentukan tanggal sewa)";
                                }
                            @endphp
                            <p class="card-text"><strong>Tanggal Sewa: </strong>{{$tanggalBaru2}}</p>
                            @if ($item->mulai != null && $item->selesai != null)
                                <p class="card-text"><strong>Jam Sewa: </strong>{{$item->mulai}} - {{$item->selesai}}</p>
                            @else
                                <p class="card-text"><strong>Jam Sewa: </strong>(Anda belum menentukan jam sewa)</p>
                            @endif
                            <div class="d-flex flex-row flex-wrap">
                            @foreach ($item->id_alat as $item2)
                                <div class="card tiny-card h-70 mb-1 mr-1">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container2">
                                                    <img src="{{ asset('upload/' . $item2['file_alat']) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$item2["nama_alat"]}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form action="/customer/transaksi/detailTransaksi" method="get">
                                @csrf
                                <input type="hidden" name="id_lapangan" value="{{$item->id_lapangan}}">
                                <input type="hidden" name="id_tempat" value="{{$item->pemilik_lapangan}}">
                                <input type="hidden" name="tanggal" value="{{$item->tanggal}}">
                                <input type="hidden" name="mulai" value="{{$item->mulai}}">
                                <input type="hidden" name="selesai" value="{{$item->selesai}}">
                                <button class="btn btn-success me-3">Booking <i class="bi bi-bag-check"></i></button>
                            </form>
                            <button data-url="/customer/hapusKeranjang/{{$loop->count - $loop->iteration}}" class="btn btn-danger delete-cart-item"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </a>
        @endforeach
    @else
        <h5 class="mt-4">Daftar Favorit Kosong</h5>
    @endif
</div>
<script>
    $(document).ready(function() {
        $('.delete-cart-item').on('click', function(e) {
            e.preventDefault(); // Mencegah aksi default

            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Sukses menghapus item dari keranjang. Anda dapat melakukan aksi seperti memperbarui tampilan dsb.
                    // alert('Item berhasil dihapus dari keranjang');
                    location.reload(); // sebagai contoh, reload halaman. Anda bisa lakukan yang lain.
                },
                error: function(err) {
                    // Terjadi kesalahan saat menghapus
                    alert('Terjadi kesalahan saat menghapus item');
                }
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
