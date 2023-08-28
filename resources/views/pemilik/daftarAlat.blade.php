@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Komisi</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$alat->isEmpty())
                @foreach ($alat as $item)
                    @php
                        $dataFiles = $files->get_all_data($item->id_alat)->first();
                    @endphp
                    <tr>
                        <td><a href="/lihatDetaildiPemilik/{{$item->id_alat}}"><img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="" style="width:150px"></a></td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->komisi_alat}}</td>
                        <td>{{$item->stok_alat}}</td>
                        <td><a class="btn btn-outline-success" href="/editAlatdiPemilik/{{$item->id_alat}}">Edit Alat</a></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection