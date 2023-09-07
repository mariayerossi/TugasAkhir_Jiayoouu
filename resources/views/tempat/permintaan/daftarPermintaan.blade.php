@extends('layouts.sidebarNavbar_tempat')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-5">Daftar Permintaan Alat</h3>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="permintaan-tab" data-toggle="tab" href="#permintaan" role="tab" aria-controls="permintaan" aria-selected="true">Permintaan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">Selesai</a>
        </li>
        <!-- Anda bisa menambahkan tab lainnya di sini -->
    </ul>
      
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="permintaan" role="tabpanel" aria-labelledby="permintaan-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$permintaan->isEmpty())
                        @foreach ($permintaan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Permintaan {{$dataAlat->nama_alat}}</td>
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
        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
          <table>
            <thead>
                <tr>
                    <td>hai</td>
                </tr>
            </thead>
          </table>
        </div>
        <!-- Konten untuk tab lainnya di sini -->
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection