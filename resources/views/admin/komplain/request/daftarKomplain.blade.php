@extends('layouts.sidebar_admin')

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
            <h3 class="text-center mb-5">Daftar Komplain Request</h3>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="baru-tab" data-toggle="tab" href="#baru" role="tab" aria-controls="baru" aria-selected="true">Komplain Baru ({{$baru->count()}})</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="diterima-tab" data-toggle="tab" href="#diterima" role="tab" aria-controls="diterima" aria-selected="false">Diterima ({{$diterima->count()}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ditolak-tab" data-toggle="tab" href="#ditolak" role="tab" aria-controls="ditolak" aria-selected="false">Ditolak ({{$ditolak->count()}})</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="baru" role="tabpanel" aria-labelledby="baru-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Komplain</th>
                                <th>Pengaju</th>
                                <th>Jenis Request</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$baru->isEmpty())
                                @foreach ($baru as $item)
                                    @php
                                        if ($item->fk_id_pemilik != null) {
                                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                                        }
                                        else if ($item->fk_id_tempat != null) {
                                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$item->jenis_komplain}}</td>
                                        @if ($item->fk_id_pemilik != null)
                                            <td>{{$dataPemilik->nama_pemilik}}</td>
                                        @elseif ($item->fk_id_tempat != null)
                                            <td>{{$dataTempat->nama_tempat}}</td>
                                        @endif

                                        @if ($item->fk_id_permintaan != null)
                                            <td>Permintaan</td>
                                        @else
                                            <td>Penawaran</td>
                                        @endif
                                        <td style="color:rgb(239, 203, 0)">{{$item->status_komplain}}</td>
                                        <td><a class="btn btn-outline-success" href="/admin/komplain/request/detailKomplain/{{$item->id_komplain_req}}">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="diterima" role="tabpanel" aria-labelledby="diterima-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Komplain</th>
                                <th>Pengaju</th>
                                <th>Jenis Request</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$diterima->isEmpty())
                                @foreach ($diterima as $item)
                                    @php
                                        if ($item->fk_id_pemilik != null) {
                                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                                        }
                                        else if ($item->fk_id_tempat != null) {
                                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$item->jenis_komplain}}</td>
                                        @if ($item->fk_id_pemilik != null)
                                            <td>{{$dataPemilik->nama_pemilik}}</td>
                                        @elseif ($item->fk_id_tempat != null)
                                            <td>{{$dataTempat->nama_tempat}}</td>
                                        @endif
                                        
                                        @if ($item->fk_id_permintaan != null)
                                            <td>Permintaan</td>
                                        @else
                                            <td>Penawaran</td>
                                        @endif
                                        <td style="color:rgb(0, 145, 0)">{{$item->status_komplain}}</td>
                                        <td><a class="btn btn-outline-success" href="/admin/komplain/request/detailKomplain/{{$item->id_komplain_req}}">Lihat Detail</a></td>
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
        <div class="tab-pane fade" id="ditolak" role="tabpanel" aria-labelledby="ditolak-tab">
            <div class="card mb-5">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Komplain</th>
                                <th>Pengaju</th>
                                <th>Jenis Request</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$ditolak->isEmpty())
                                @foreach ($ditolak as $item)
                                    @php
                                        if ($item->fk_id_pemilik != null) {
                                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_pemilik)->get()->first();
                                        }
                                        else {
                                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_tempat)->get()->first();
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$item->jenis_komplain}}</td>
                                        @if ($item->fk_id_pemilik != null)
                                            <td>{{$dataPemilik->nama_pemilik}}</td>
                                        @else
                                            <td>{{$dataTempat->nama_tempat}}</td>
                                        @endif
                                        
                                        @if ($item->fk_id_permintaan != null)
                                            <td>Permintaan</td>
                                        @else
                                            <td>Penawaran</td>
                                        @endif
                                        <td style="color:red">{{$item->status_komplain}}</td>
                                        <td><a class="btn btn-outline-success" href="/admin/komplain/request/detailKomplain/{{$item->id_komplain_req}}">Lihat Detail</a></td>
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
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection