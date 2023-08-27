@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto Alat Olahraga</th>
                <th>Nama Alat Olahraga</th>
                <th>Komisi Alat Olahraga</th>
                <th>Stok Alat Olahraga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if (!$alat->isEmpty())
                @foreach ($kategori as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_kategori}}</td>
                        <td><a href="/hapusKategori/{{$item->id_kategori}}" class="btn btn-danger">Hapus</a></td>
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