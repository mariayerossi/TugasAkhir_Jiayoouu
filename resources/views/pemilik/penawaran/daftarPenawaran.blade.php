@extends('layouts.sidebarNavbar_pemilik')
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
          <a class="nav-link" id="diterima-tab" data-toggle="tab" href="#diterima" role="tab" aria-controls="diterima" aria-selected="false">Diterima</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="disewakan-tab" data-toggle="tab" href="#disewakan" role="tab" aria-controls="disewakan" aria-selected="false">Disewakan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ditolak-tab" data-toggle="tab" href="#ditolak" role="tab" aria-controls="ditolak" aria-selected="false">Ditolak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">Selesai</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dibatalkan-tab" data-toggle="tab" href="#dibatalkan" role="tab" aria-controls="dibatalkan" aria-selected="false">Dibatalkan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dikomplain-tab" data-toggle="tab" href="#dikomplain" role="tab" aria-controls="dikomplain" aria-selected="false">Dikomplain</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="baru" role="tabpanel" aria-labelledby="baru-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$baru->isEmpty())
                        @foreach ($baru as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:rgb(239, 203, 0)">Menunggu</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$diterima->isEmpty())
                        @foreach ($diterima as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:rgb(0, 145, 0)">Diterima</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="disewakan" role="tabpanel" aria-labelledby="disewakan-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$disewakan->isEmpty())
                        @foreach ($disewakan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:rgb(0, 145, 0)">Diterima</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$ditolak->isEmpty())
                        @foreach ($ditolak as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:red">Ditolak</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$selesai->isEmpty())
                        @foreach ($selesai as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:blue">Selesai</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$dibatalkan->isEmpty())
                        @foreach ($dibatalkan as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:red">Dibatalkan</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="dikomplain" role="tabpanel" aria-labelledby="dikomplain-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Foto Alat</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$dikomplain->isEmpty())
                        @foreach ($dikomplain as $item)
                            @php
                                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                                $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="square-image-container">
                                        <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="">
                                    </div>
                                </td>
                                <td>Penawaran {{$dataAlat->nama_alat}} kepada {{$dataTempat->nama_tempat}}</td>
                                <td><span style="color:red">Dikomplain</span></td>
                                <td><a href="/pemilik/penawaran/detailPenawaranNego/{{$item->id_penawaran}}" class="btn btn-outline-success">Lihat Detail</a></td>
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