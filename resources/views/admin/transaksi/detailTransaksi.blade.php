@extends('layouts.sidebar_admin')

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
.truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: block;
}
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    .square-image-container {
        width: 60px;
        height: 60px;
    }
}
</style>
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-1"></i>Kembali</a>
    </div>
    <h3 class="text-center mb-5">Detail Transaksi</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h6><b>Kode Transaksi: {{$htrans->first()->kode_trans}}</b></h6>
    </div>
    <div class="d-flex justify-content-end mt-3 me-3 mb-5">
        @if ($htrans->first()->status_trans == "Ditolak" || $htrans->first()->status_trans == "Dibatalkan")
            <h6><b>Status Transaksi: </b><b style="color: red">{{$htrans->first()->status_trans}}</b></h6>
        @else
            <h6><b>Status Transaksi: {{$htrans->first()->status_trans}}</b></h6>
        @endif
    </div>
    @php
        $dataUser = DB::table('user')->where("id_user","=",$htrans->first()->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $htrans->first()->tanggal_trans;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek)->locale('id');
        $tanggalBaru = $carbonDate->isoFormat('D MMMM YYYY HH:mm');
    @endphp
    <div class="row mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Disewa oleh: {{$dataUser->nama_user}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Transaksi: {{$tanggalBaru}}</h6>
        </div>
    </div>

    @php
        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY');
    @endphp

    <div class="row mb-4 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalBaru2}}</h6>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{$htrans->first()->jam_sewa}} WIB</h6>
        </div>
    </div>
    <h5>Lapangan yang Disewa</h5>
    @if ($dataLapangan->deleted_at == null)<a href="/admin/lapangan/detailLapanganUmum/{{$htrans->first()->fk_id_lapangan}}">@endif
        <div class="card">
            <div class="card-body">
                <div class="row d-md-flex align-items-md-center">
                    <!-- Gambar -->
                    <div class="col-4">
                        <div class="square-image-container">
                            <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    
                    <!-- Nama -->
                    <div class="col-8">
                        <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                        <!-- Contoh detail lain: -->
                        <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
                        {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($htrans->first()->subtotal_lapangan, 0, ',', '.')}}</b></h5>
    </div>
    <div class="row mt-5">
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Disewa</h5>
            @if (!$dtrans->isEmpty())
                @foreach ($dtrans as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                    @endphp
                    @if ($dataAlat->deleted_at == null)<a href="/admin/alat/detailAlatUmum/{{$dataAlat->id_alat}}">@endif
                        <div class="card h-70 mb-3">
                            <div class="card-body">
                                <div class="row d-md-flex align-items-md-center">
                                    <!-- Gambar Alat -->
                                    <div class="col-4">
                                        <div class="square-image-container">
                                            <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                        </div>
                                    </div>
                                    
                                    <!-- Nama Alat -->
                                    <div class="col-8">
                                        <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                        <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <p>(Tidak ada alat olahraga yang disewa)</p>
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($htrans->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
    </div>
    <hr>
    <div class="d-flex justify-content-end mt-5 me-3 mb-5">
        <h4><b>Total: Rp {{number_format($htrans->first()->total_trans, 0, ',', '.')}}</b></h4>
    </div>

    @php
        $extend = DB::table('extend_htrans')
                ->where("fk_id_htrans","=",$htrans->first()->id_htrans)
                ->get();
    @endphp
    @if (!$extend->isEmpty())
    <div id="extend">
        <hr style="height: 3px;
        border: none;
        background-color: black;">
    <h3 class="text-center mb-5 mt-5">Permintaan Perpanjangan Waktu Sewa</h3>
    @php
        $tanggalAwal4 = $extend->first()->tanggal_extend;
        $tanggalObjek4 = DateTime::createFromFormat('Y-m-d', $tanggalAwal4);
        $tanggalBaru4 = $tanggalObjek4->format('d-m-Y');
    @endphp

    <div class="row mb-5 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalBaru4}}</h6>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->format('H:i:s') }} WIB - {{ \Carbon\Carbon::parse($extend->first()->jam_sewa)->addHours($extend->first()->durasi_extend)->format('H:i:s') }} WIB ({{$extend->first()->durasi_extend}} jam)</h6>
        </div>
    </div>

    <h5>Lapangan yang Disewa</h5>
    <a href="/tempat/lapangan/lihatDetailLapangan/{{$htrans->first()->fk_id_lapangan}}">
        <div class="card">
            <div class="card-body">
                <div class="row d-md-flex align-items-md-center">
                    <!-- Gambar -->
                    <div class="col-4">
                        <div class="square-image-container">
                            <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                        </div>
                    </div>
                    
                    <!-- Nama -->
                    <div class="col-8">
                        <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                        <!-- Contoh detail lain: -->
                        <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                        {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_lapangan, 0, ',', '.')}}</b></h5>
    </div>

    @php
        $extendDtrans = DB::table('extend_dtrans')
                ->where("fk_id_extend_htrans","=",$extend->first()->id_extend_htrans)
                ->get();
    @endphp

    <div class="row mt-5">
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Disewa</h5>
            @if (!$dtrans->isEmpty())
                @foreach ($dtrans as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        $cekSendiri = DB::table("sewa_sendiri")->where("req_id_alat","=",$item->fk_id_alat)->get()->first();
                    @endphp
                    @if ($cekSendiri != null)
                        <a href="/tempat/alat/lihatDetail/{{$dataAlat->id_alat}}">
                            <div class="card h-70 mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Gambar Alat -->
                                        <div class="col-4">
                                            <div class="square-image-container">
                                                <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        
                                        <!-- Nama Alat -->
                                        <div class="col-8">
                                            <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @else
                        <a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">
                            <div class="card h-70 mb-3">
                                <div class="card-body">
                                    <div class="row d-md-flex align-items-md-center">
                                        <!-- Gambar Alat -->
                                        <div class="col-4">
                                            <div class="square-image-container">
                                                <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        
                                        <!-- Nama Alat -->
                                        <div class="col-8">
                                            <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$extend->first()->durasi_extend}} Jam</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            @else
                <p>(Tidak ada alat olahraga yang disewa)</p>
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3 me-3">
        <h5><b>Subtotal: Rp {{number_format($extend->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
    </div>
    <hr>
    @if ($extend->first()->status_extend == "Dibatalkan" || $extend->first()->status_extend == "Ditolak")
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b style="color: red">Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
        </div>
    @else 
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b>Total: Rp {{number_format($extend->first()->total, 0, ',', '.')}}</b></h4>
        </div>
    @endif
    @endif
</div>
@endsection