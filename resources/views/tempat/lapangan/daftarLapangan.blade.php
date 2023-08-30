@extends('layouts.sidebarNavbar_tempat')

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
                <th>Harga Sewa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$lapangan->isEmpty())
                @foreach ($lapangan as $item)
                    @php
                        $dataFiles = $files->get_all_data($item->id_lapangan)->first();
                    @endphp
                    <tr>
                        <td>
                            <div class="square-image-container">
                                <a href="/lihatDetailLapangan/{{$item->id_lapangan}}"><img src="{{ asset('upload/' . $dataFiles->nama_file_lapangan) }}" alt=""></a>
                            </div>
                        </td>
                        <td><a href="/lihatDetailLapangan/{{$item->id_lapangan}}">{{$item->nama_lapangan}}</a></td>
                        <td>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</td>
                        <td><a class="btn btn-outline-success" href="/editAlatdiPemilik/{{$item->id_lapangan}}">Edit</a></td>
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
@endsection