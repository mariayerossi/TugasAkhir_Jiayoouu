@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<style>
    .square-image-container {
    width: 100px;
    height: 100px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.square-image-container img {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

</style>
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
                        <td>
                            <div class="square-image-container">
                                <a href="/lihatDetaildiPemilik/{{$item->id_alat}}"><img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt=""></a>
                            </div>
                        </td>
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