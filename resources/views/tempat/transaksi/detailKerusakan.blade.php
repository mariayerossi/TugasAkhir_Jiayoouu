@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .card-form {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    </style>
@include("layouts.message")
<div class="container mt-5 mb-5 p-3 rounded">
    <form action="/tempat/kerusakan/ajukanKerusakan" method="post" enctype="multipart/form-data">
        @csrf
        <div class="d-flex justify-content-center">
            <h3 class="text-center mb-5">Ajukan Kerusakan Alat</h3>
        </div>
        @foreach ($dtrans as $index => $item)
        <div class="form-template card-form p-5">
            @php
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
            @endphp
            <h5 class="text-center mb-5">{{$dataAlat->nama_alat}} - {{$item->id_dtrans}}</h5>
            <div class="row mb-3">
                <div class="col-md-4 col-12 mt-2">
                    <h6>Apakah terdapat unsur kesengajaan dalam kerusakan alat olahraga?</h6>
                </div>
                <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                    <input type="radio" class="btn-check" name="unsur{{$index}}" id="danger-outlined-{{$index}}" autocomplete="off" value="Ya">
                    <label class="btn btn-outline-danger" for="danger-outlined-{{$index}}">Ya</label>

                    <input type="radio" class="btn-check" name="unsur{{$index}}" id="primary-outlined-{{$index}}" autocomplete="off" value="Tidak">
                    <label class="btn btn-outline-primary" for="primary-outlined-{{$index}}">Tidak</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-12 mt-2">
                    <h6>Lampirkan Bukti</h6>
                </div>
                <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                    <input type="file" class="form-control" name="foto{{$index}}" accept=".jpg,.png,.jpeg">
                </div>
            </div>
            <input type="hidden" name="id_dtrans[{{$index}}]" value="{{$item->id_dtrans}}">
        </div>
        @endforeach
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success">Kirim</button>
        </div>
    </form>
</div>
@endsection