@extends('layouts.sidebar_admin')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-5">Tambah Kategori Olahraga</h2>
    @include("layouts.message")

    @if ($id == "")
        <form id="tambahForm" action="/admin/tambahKategori" method="post">
            @csrf
            <div class="row">
                <div class="col-md-2 col-12">
                    <h6 class="mt-2">Nama Kategori</h6>
                </div>
                <div class="col-md-7 col-12 mt-2 mt-md-0">
                    <input type="text" class="form-control" name="kategori" placeholder="Contoh : Basket" value="{{$edit}}">
                </div>
                <div class="col-md-3 col-12 mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary" id="tambah">Tambah</button>
                </div>
            </div>
        </form>
    @else
        <form id="editForm" action="/admin/editKategori" method="post">
            @csrf
            <div class="row">
                <div class="col-md-2 col-12">
                    <h6 class="mt-2">Nama Kategori</h6>
                </div>
                <div class="col-md-7 col-12 mt-2 mt-md-0">
                    <input type="text" class="form-control" name="kategori" placeholder="Contoh : Basket" value="{{$edit}}">
                </div>
                <div class="col-md-3 col-12 mt-2 mt-md-0">
                    <input type="hidden" name="id" value="{{$id}}">
                    <button type="submit" class="btn btn-primary" id="edit">Edit</button>
                </div>
            </div>
        </form>
    @endif

    <div class="card mb-5 mt-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
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
                                <td><a href="/admin/editKategori/{{$item->id_kategori}}" class="btn btn-outline-success">Edit</a></td>
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
</div>
<script>
    $("#tambah").click(function(event) {
        event.preventDefault(); // Mencegah perilaku default form

        var formData = $("#tambahForm").serialize(); // Mengambil data dari form

        $.ajax({
            url: "/admin/tambahKategori",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                // alert('Berhasil Diterima!');
                // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
            }
        });

        return false; // Mengembalikan false untuk mencegah submission form
    });

    $("#edit").click(function(event) {
        event.preventDefault(); // Mencegah perilaku default form

        var formData = $("#editForm").serialize(); // Mengambil data dari form

        $.ajax({
            url: "/admin/editKategori",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                // alert('Berhasil Diterima!');
                // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
            }
        });

        return false; // Mengembalikan false untuk mencegah submission form
    });
</script>
@endsection