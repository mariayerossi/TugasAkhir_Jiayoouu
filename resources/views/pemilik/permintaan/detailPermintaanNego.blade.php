@extends('layouts.sidebarNavbar_pemilik')

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
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Permintaan Alat</h3>
    @php
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->first()->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->first()->fk_id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$permintaan->first()->req_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $permintaan->first()->tanggal_minta;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
    @endphp

    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$dataTempat->nama_tempat}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru}}</h6>
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
            <p>Rp {{number_format($permintaan->first()->req_harga_sewa, 0, ',', '.')}}/jam</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Permintaan Durasi Pinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Durasi peminjaman alat olahraga oleh pihak pengelola tempat olahraga"></i></h6>
            @if ($permintaan->first()->req_durasi == "12")
                <p>1 Tahun</p>
            @elseif($permintaan->first()->req_durasi == "24")
                <p>2 Tahun</p>
            @else
                <p>{{$permintaan->first()->req_durasi}} Bulan</p>
            @endif
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Mulai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga akan mulai disewakan saat anda menyetujui permintaan"></i></h6>
            @if ($permintaan->first()->req_tanggal_mulai == null)
                @if ($permintaan->first()->status_permintaan == "Menunggu")
                    <p>(Menunggu Persetujuan Anda)</p>
                @else
                    <p>(Permintaan telah {{$permintaan->first()->status_permintaan}})</p>
                @endif
            @else
                @php
                    $tanggalAwal = $permintaan->first()->req_tanggal_mulai;
                    $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
                    $tanggalBaru = $tanggalObjek->format('d-m-Y');
                @endphp
                <p>{{$tanggalBaru}}</p>
            @endif
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Selesai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Waktu berakhirnya peminjaman ditentukan berdasarkan waktu dimulainya peminjaman"></i></h6>
            @if ($permintaan->first()->req_tanggal_selesai == null)
                @if ($permintaan->first()->status_permintaan == "Menunggu")
                    <p>(Menunggu Persetujuan Anda)</p>
                @else
                    <p>(Permintaan telah {{$permintaan->first()->status_permintaan}})</p>
                @endif
            @else
                @php
                    $tanggalAwal2 = $permintaan->first()->req_tanggal_selesai;
                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                    $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                @endphp
                <p>{{$tanggalBaru2}}</p>
            @endif
        </div>
    </div>
    @if ($permintaan->first()->status_permintaan == "Menunggu")
        <div class="d-flex justify-content-end">
            <form action="/pemilik/permintaan/terimaPermintaan" method="post">
                @csrf
                <input type="hidden" name="id_permintaan" value="{{$permintaan->first()->id_permintaan}}">
                <button type="submit" class="btn btn-success me-3">Terima</button>
            </form>
            <a href="" class="btn btn-secondary me-3">Negosiasi</a>
            <form action="/pemilik/permintaan/tolakPermintaan" method="post">
                @csrf
                <input type="hidden" name="id_permintaan" value="{{$permintaan->first()->id_permintaan}}">
                <button type="submit" class="btn btn-danger me-3">Tolak</button>
            </form>
        </div>
        <hr>
        <div class="nego" id="negoDiv">
            <!-- Detail Negosiasi -->
            <h3>Negosiasi</h3>
            <div class="row justify-content-center">
                <div class="col-12 p-4">
                    <!-- Form Balasan -->
                    <form action="/pemilik/permintaan/negosiasi/tambahNego" method="post">
                        @csrf
                        <input type="hidden" name="permintaan" value="{{$permintaan->first()->id_permintaan}}">
                        <input type="hidden" name="id_user" value="{{Session::get('dataRole')->id_pemilik}}">
                        <input type="hidden" name="role" value="Pemilik">
                        <textarea class="form-control mb-3" rows="4" name="isi" placeholder="Tulis pesan Anda di sini..."></textarea>
                        <button type="submit" class="btn btn-primary w-100 mb-5">Kirim</button>
                    </form>
                    
                    <div class="history">
                        @if (!$nego->isEmpty())
                            @foreach ($nego as $item)
                                <div class="card mb-4">
                                    <div class="card-body">
                                        @if ($item->role_user == "Pemilik")
                                            @php
                                                $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$item->fk_id_user)->get()->first();
                                            @endphp
                                            <h5><strong>{{$dataPemilik->nama_pemilik}}</strong></h5>
                                        @elseif ($item->role_user == "Tempat")
                                            @php
                                                $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$item->fk_id_user)->get()->first();
                                            @endphp
                                            <h5><strong>{{$dataTempat->nama_pemilik_tempat}}</strong></h5>
                                        @endif
                                        @php
                                            $tanggalAwal = $item->waktu_negosiasi;
                                            $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
                                            $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
                                        @endphp
                                        <p>{{$tanggalBaru}}</p>
                                        <p class="mt-2">{{$item->isi_negosiasi}}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   

        @if($nego->isEmpty())
        // Menyembunyikan div nego saat halaman pertama kali dimuat
            $(".nego").hide();
        @endif

        // Mengatur event ketika tombol Negosiasi diklik
        $(".btn-secondary").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            $(".nego").show();   // Menampilkan div nego
        });
    });

    $("form[action='/pemilik/permintaan/negosiasi/tambahNego']").submit(function(e) {
        e.preventDefault(); // Menghentikan perilaku default (pengiriman form)

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {

                    // Menambahkan pesan ke dalam div history
                    let newMessage = `
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5><strong>${response.user}</strong></h5>
                                <p>${response.waktu}</p>
                                <p class="mt-2">${response.data.isi}</p>
                            </div>
                        </div>
                    `;

                    $(".history").prepend(newMessage);
                } else {
                    alert("Terjadi kesalahan saat mengirim pesan.");
                }
                $("textarea[name='isi']").val('');  // Mengosongkan isian form setelah pesan berhasil dikirim
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection
