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
    <h2 class="text-center mb-5">Daftar Pihak Tempat Olahraga</h2>
    <div class="mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Tempat</th>
                    <th>Nama Pemilik</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Foto KTP</th>
                    <th>Foto NPWP</th>
                </tr>
            </thead>
            <tbody>
                @if (!$tempat->isEmpty())
                    @foreach ($tempat as $item)
                        <tr>
                            <td>{{$item->nama_tempat}}</td>
                            <td>{{$item->nama_pemilik_tempat}}</td>
                            <td>{{$item->email_tempat}}</td>
                            <td>{{$item->telepon_tempat}}</td>
                            <td>{{$item->alamat_tempat}}</td>
                            <td><a href="{{ asset('upload/' . $item->ktp_tempat) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item->ktp_tempat) }}" alt=""></a></td>
                            <td><a href="{{ asset('upload/' . $item->npwp_tempat) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item->npwp_tempat) }}" alt=""></a></td>
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