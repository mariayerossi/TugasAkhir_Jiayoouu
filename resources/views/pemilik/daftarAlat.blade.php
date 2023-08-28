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
                        <td><img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="" style="width:150px"></td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->komisi_alat}}</td>
                        <td>{{$item->stok_alat}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Atur
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/lihatDetail/{{$item->id_alat}}">Lihat Detail</a>
                                    <a class="dropdown-item" href="/lihatDetail/{{$item->id_alat}}">Edit</a>
                                </div>
                            </div>
                        </td>
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