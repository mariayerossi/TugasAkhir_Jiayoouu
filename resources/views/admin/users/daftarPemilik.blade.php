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
    <h2 class="text-center mb-5">Daftar Pemilik Alat</h2>
    <div class="mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Foto KTP</th>
                </tr>
            </thead>
            <tbody>
                @if (!$pemilik->isEmpty())
                    @foreach ($pemilik as $item)
                        <tr>
                            <td>{{$item->nama_pemilik}}</td>
                            <td>{{$item->email_pemilik}}</td>
                            <td>{{$item->telepon_pemilik}}</td>
                            <td><a href="{{ asset('upload/' . $item->ktp_pemilik) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item->ktp_pemilik) }}" alt=""></a></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection