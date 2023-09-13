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
@media (max-width: 768px) {
    .nav-tabs {
    overflow-x: auto;
    display: flex;
    flex-wrap: nowrap;
    -webkit-overflow-scrolling: touch;
}
}
</style>
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-5">Daftar Penawaran Alat</h3>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="baru-tab" data-toggle="tab" href="#baru" role="tab" aria-controls="baru" aria-selected="true">Penawaran Baru</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="diterima-tab" data-toggle="tab" href="#diterima" role="tab" aria-controls="diterima" aria-selected="false">Penawaran Diterima</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ditolak-tab" data-toggle="tab" href="#ditolak" role="tab" aria-controls="ditolak" aria-selected="false">Penawaran Ditolak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">Penawaran Selesai</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dibatalkan-tab" data-toggle="tab" href="#dibatalkan" role="tab" aria-controls="dibatalkan" aria-selected="false">Penawaran Dibatalkan</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="baru" role="tabpanel" aria-labelledby="baru-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Pengaju</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$baru->isEmpty())
                        @foreach ($baru as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}}</td>
                                @php
                                    $tanggalAwal = $item->tanggal_tawar;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                @endphp
                                <td>Diajukan oleh {{$dataPemilik->nama_pemilik}} pada {{$tanggalBaru}}</td>
                                <td><a href="/tempat/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="diterima" role="tabpanel" aria-labelledby="diterima-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Pengaju</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$diterima->isEmpty())
                        @foreach ($diterima as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} sudah <span style="color:rgb(0, 145, 0)">Diterima</span></td>
                                @php
                                    $tanggalAwal = $item->tanggal_tawar;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                @endphp
                                <td>Diajukan oleh {{$dataPemilik->nama_pemilik}} pada {{$tanggalBaru}}</td>
                                <td><a href="/tempat/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="ditolak" role="tabpanel" aria-labelledby="ditolak-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Pengaju</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$ditolak->isEmpty())
                        @foreach ($ditolak as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} sudah <span style="color:red">Ditolak</span></td>
                                @php
                                    $tanggalAwal = $item->tanggal_tawar;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                @endphp
                                <td>Diajukan oleh {{$dataPemilik->nama_pemilik}} pada {{$tanggalBaru}}</td>
                                <td><a href="/tempat/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Pengaju</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$selesai->isEmpty())
                        @foreach ($selesai as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} sudah <span style="color:blue">Selesai</span></td>
                                @php
                                    $tanggalAwal = $item->tanggal_tawar;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                @endphp
                                <td>Diajukan oleh {{$dataPemilik->nama_pemilik}} pada {{$tanggalBaru}}</td>
                                <td><a href="/tempat/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="dibatalkan" role="tabpanel" aria-labelledby="dibatalkan-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Pengaju</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$dibatalkan->isEmpty())
                        @foreach ($dibatalkan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} sudah <span style="color:red">Dibatalkan</span></td>
                                @php
                                    $tanggalAwal = $item->tanggal_tawar;
                                    $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                    $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                @endphp
                                <td>Diajukan oleh {{$dataPemilik->nama_pemilik}} pada {{$tanggalBaru}}</td>
                                <td><a href="/tempat/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection