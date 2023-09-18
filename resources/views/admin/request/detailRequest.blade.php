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
    <h3 class="text-center mb-5">Detail {{$jenis}} Alat</h3>
    @php
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$request->first()->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->first()->fk_id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$request->first()->req_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal1 = $request->first()->tanggal_minta;
        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
        $tanggalBaru1 = $tanggalObjek1->format('d-m-Y H:i:s');
    @endphp

    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$dataTempat->nama_tempat}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru1}}</h6>
        </div>
    </div>

    <div class="row mb-5">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Diminta <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang dimohon oleh pihak pengelola tempat olahraga"></i></h5>
            <a href="/pemilik/lihatDetail/{{$dataAlat->id_alat}}">
                <div class="card h-70">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Alat -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama Alat -->
                            <div class="col-8 d-flex align-items-center">
                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <!-- Detail Lapangan -->
        <div class="col-md-6 col-sm-12">
            <h5>Lapangan Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Lokasi penggunaan alat olahraga"></i></h5>
            <a href="/pemilik/detailLapanganUmum/{{$dataLapangan->id_lapangan}}">
                <div class="card h-70">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Lapangan -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama Lapangan -->
                            <div class="col-8 d-flex align-items-center">
                                <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row mb-3 mt-3">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Permintaan Harga Sewa: <i class="bi bi-info-circle" data-toggle="tooltip" title="Biaya sewa yang harus dibayar pelanggan saat menyewa alat (*sudah termasuk komisi pemilik dan pihak pengelola tempat). Negosiasikan harga dengan pihak pengelola tempat olahraga apabila merasa tidak puas dengan harga sewa"></i></h6>
            <p>Rp {{number_format($request->first()->req_harga_sewa, 0, ',', '.')}}/jam</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Permintaan Durasi Pinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Durasi peminjaman alat olahraga oleh pihak pengelola tempat olahraga"></i></h6>
            @if ($request->first()->req_durasi == "12")
                <p>1 Tahun</p>
            @elseif($request->first()->req_durasi == "24")
                <p>2 Tahun</p>
            @else
                <p>{{$request->first()->req_durasi}} Bulan</p>
            @endif
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Mulai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga akan mulai disewakan saat anda menyetujui permintaan"></i></h6>
            @if ($request->first()->req_tanggal_mulai == null)
                @if ($request->first()->status_permintaan == "Menunggu")
                    <p>(Menunggu Persetujuan Anda)</p>
                @else
                    <p>(Permintaan telah {{$request->first()->status_permintaan}})</p>
                @endif
            @else
                @php
                    $tanggalAwal2 = $request->first()->req_tanggal_mulai;
                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                    $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                @endphp
                <p>{{$tanggalBaru2}}</p>
            @endif
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Selesai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Waktu berakhirnya peminjaman ditentukan berdasarkan waktu dimulainya peminjaman"></i></h6>
            @if ($request->first()->req_tanggal_selesai == null)
                @if ($request->first()->status_permintaan == "Menunggu")
                    <p>(Menunggu Persetujuan Anda)</p>
                @else
                    <p>(Permintaan telah {{$request->first()->status_permintaan}})</p>
                @endif
            @else
                @php
                    $tanggalAwal3 = $request->first()->req_tanggal_selesai;
                    $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                    $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
                @endphp
                <p>{{$tanggalBaru3}}</p>
            @endif
        </div>
    </div>
</div>
@endsection