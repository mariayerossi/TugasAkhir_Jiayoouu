@extends('layouts.sidebar_admin')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-5">Tambah Kategori Olahraga</h2>
    @include("layouts.message")

    <form action="tambahKategori" method="post">
        @csrf
        <div class="row">
            <div class="col-md-2 col-12">
                <h6 class="mt-2">Nama Kategori</h6>
            </div>
            <div class="col-md-7 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" name="kategori" placeholder="Contoh : Basket">
            </div>
            <div class="col-md-3 col-12 mt-2 mt-md-0">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </div>
    </form>
    
    <div class="mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nomer</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if (!$kategori->isEmpty())
                    @foreach ($kategori as $item)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$item->nama_kategori}}</td>
                            <td><a href="/hapusKategori/{{$item->id_kategori}}" class="btn btn-danger">Hapus</a></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection