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
            <h3 class="text-center mb-5">Daftar Komplain Transaksi</h3>
        </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="baru-tab" data-toggle="tab" href="#baru" role="tab" aria-controls="baru" aria-selected="true">Komplain Baru</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="diterima-tab" data-toggle="tab" href="#diterima" role="tab" aria-controls="diterima" aria-selected="false">Diterima</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ditolak-tab" data-toggle="tab" href="#ditolak" role="tab" aria-controls="ditolak" aria-selected="false">Ditolak</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="baru" role="tabpanel" aria-labelledby="baru-tab">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Jenis Komplain</th>
                        <th>Pengaju</th>
                        <th>Kode Transaksi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$baru->isEmpty())
                        @foreach ($baru as $item)
                            @php
                                $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                            @endphp
                            <tr>
                                <td>{{$item->jenis_komplain}}</td>
                                <td>{{$dataUser->nama_user}}</td>
                                <td>{{$dataHtrans->kode_trans}}</td>
                                <td style="color:rgb(239, 203, 0)">{{$item->status_komplain}}</td>
                                <td><a class="btn btn-outline-success" href="/admin/komplain/trans/detailKomplain/{{$item->id_komplain_trans}}">Lihat Detail</a></td>
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
                <thead>
                    <tr>
                        <th>Jenis Komplain</th>
                        <th>Pengaju</th>
                        <th>Kode Transaksi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$diterima->isEmpty())
                        @foreach ($diterima as $item)
                            @php
                                $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                            @endphp
                            <tr>
                                <td>{{$item->jenis_komplain}}</td>
                                <td>{{$dataUser->nama_user}}</td>
                                <td>{{$dataHtrans->kode_trans}}</td>
                                <td style="color:rgb(0, 145, 0)">{{$item->status_komplain}}</td>
                                <td><a class="btn btn-outline-success" href="/admin/komplain/trans/detailKomplain/{{$item->id_komplain_trans}}">Lihat Detail</a></td>
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
                <thead>
                    <tr>
                        <th>Jenis Komplain</th>
                        <th>Pengaju</th>
                        <th>Kode Transaksi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$ditolak->isEmpty())
                        @foreach ($ditolak as $item)
                            @php
                                $dataUser = DB::table('user')->where("id_user","=",$item->fk_id_user)->get()->first();
                                $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                            @endphp
                            <tr>
                                <td>{{$item->jenis_komplain}}</td>
                                <td>{{$dataUser->nama_user}}</td>
                                <td>{{$dataHtrans->kode_trans}}</td>
                                <td style="color:red">{{$item->status_komplain}}</td>
                                <td><a class="btn btn-outline-success" href="/admin/komplain/trans/detailKomplain/{{$item->id_komplain_trans}}">Lihat Detail</a></td>
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
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });

</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection