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

</style>
<div class="container mt-5">
    <h2 class="text-center mb-5">Daftar Keranjang</h2>
    @if($data != null)
        @foreach($data as $item)
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
                                    $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <h5>Tidak ada Keranjang</h5>
    @endif
</div>
@endsection
