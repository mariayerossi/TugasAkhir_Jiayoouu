@extends('layouts.sidebarNavbar_tempat')

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
    .square-image-container {
        width: 60px;
        height: 60px;
    }
}
</style>
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Transaksi</h3>
    @php
        $dataUser = DB::table('user')->where("id_user","=",$htrans->first()->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $htrans->first()->tanggal_trans;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
    @endphp
    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Disewa oleh: {{$dataUser->nama_user}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Transaksi: {{$tanggalBaru}}</h6>
        </div>
    </div>
    
    <h5>Lapangan yang Disewa</h5>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Gambar -->
                <div class="col-4">
                    <div class="square-image-container">
                        <img src="{{ asset('upload/' . $dataFileLapangan->nama_file_lapangan) }}" alt="" class="img-fluid">
                    </div>
                </div>
                
                <!-- Nama -->
                <div class="col-8 d-flex align-items-center">
                    <h5 class="card-title truncate-text">{{$dataLapangan->nama_lapangan}}</h5>
                    {{-- tambahin detail lain kek harga sewa x durasi sewa --}}
                </div>
            </div>
        </div>
    </div>
    @php
        function getBulan($bulan) {
            $namaBulan = array(
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            );

            return $namaBulan[$bulan];
        }

        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
        
        // Pecah tanggal dan ganti bagian bulannya
        $pecahTanggal = explode('-', $tanggalBaru2);
        $pecahTanggal[1] = getBulan($pecahTanggal[1]);
        $tanggalDenganNamaBulan = implode(' ', $pecahTanggal); 
    @endphp

    <div class="row mb-3 mt-5">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalDenganNamaBulan}}</h6>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{$htrans->first()->jam_sewa}} WIB</h6>
        </div>
    </div>

</div>
@endsection