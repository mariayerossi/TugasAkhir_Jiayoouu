@extends('layouts.sidebar_admin')

@section('content')
<style>
    .img-ratio-16-9 {
        width: 150px;
        height: 84.375px;
        object-fit: cover;
    }

</style>
<div class="container mt-5">
    <h2 class="text-center mb-5">Registrasi Tempat Olahraga</h2>
    @include("layouts.message")
    <div class="mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Tempat Olahraga</th>
                    <th>Nama Pemilik</th>
                    <th>Lokasi</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Foto KTP</th>
                    <th>Foto NPWP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (Session::has("regTempat"))
                    @foreach (Session::get("regTempat") as $item)
                        <tr>
                            <td>{{$item["nama"]}}</td>
                            <td>{{$item["pemilik"]}}</td>
                            <td>{{$item["alamat"]}}</td>
                            <td>{{$item["email"]}}</td>
                            <td>{{$item["telepon"]}}</td>
                            <td><a href="{{ asset('upload/' . $item['ktp']) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item['ktp']) }}" alt=""></a></td>
                            <td><a href="{{ asset('upload/' . $item['npwp']) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item['npwp']) }}" alt=""></a></td>
                            <td><a href="/admin/konfirmasiTempat/{{$item['ktp']}}" class="btn btn-primary">Konfirmasi</a></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection