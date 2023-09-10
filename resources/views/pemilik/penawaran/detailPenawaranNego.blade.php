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
}
</style>
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Penawaran Alat</h3>
    @php
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->first()->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->first()->fk_id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$penawaran->first()->req_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $penawaran->first()->tanggal_tawar;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
    @endphp
    
    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan kepada: {{$dataTempat->nama_tempat}}</h6>
        </div>
        
        <!-- Tanggal penawaran -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru}}</h6>
        </div>
    </div>

    <div class="row mb-5">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5 class="mt-3">Alat Olahraga yang Ditawarkan <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang ditawarkan oleh pemilik alat"></i></h5>
            <a href="/pemilik/lihatDetail/{{$dataAlat->id_alat}}">
                <div class="card h-75">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Alat -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                            
                            <!-- Nama Alat -->
                            <div class="col-8 d-flex flex-column justify-content-center">
                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                <p class="card-text">komisi: Rp {{number_format($dataAlat->komisi_alat, 0, ',', '.')}}/jam</p>
                                @if ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_tempat == null)
                                    <form action="/pemilik/editHargaKomisi" method="post" id="noRedirectInput">
                                        @csrf
                                        <div class="input-group">
                                            <input type="hidden" name="id_alat" value="{{$dataAlat->id_alat}}">
                                            <input type="number" min="0" name="harga_komisi" value="{{$dataAlat->komisi_alat}}" class="form-control">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"id="submitBtn">Edit Harga</button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <p class="card-text">(Tidak dapat mengedit komisi, penawaran telah {{$penawaran->first()->status_penawaran}})</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Detail Lapangan -->
        <div class="col-md-6 col-sm-12">
            <h5 class="mt-3">Lapangan Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Lokasi penggunaan alat olahraga"></i></h5>
            <a href="/pemilik/detailLapanganUmum/{{$dataLapangan->id_lapangan}}">
                <div class="card h-75">
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
            @if ($penawaran->first()->req_harga_sewa != null)
                <p>Rp {{number_format($penawaran->first()->req_harga_sewa, 0, ',', '.')}}</p>
            @else
                <p>(Harga sewa belum diisi oleh pihak pengelola tempat)</p>
            @endif
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Permintaan Durasi Pinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Durasi peminjaman alat olahraga oleh pihak pengelola tempat olahraga"></i></h6>
            @if ($penawaran->first()->req_durasi != null)
                @if ($penawaran->first()->req_durasi == "12")
                    <p>1 Tahun</p>
                @elseif($penawaran->first()->req_durasi == "24")
                    <p>2 Tahun</p>
                @else
                    <p>{{$penawaran->first()->req_durasi}} Bulan</p>
                @endif
            @else
                <p>(Durasi pinjam belum diisi oleh pihak pengelola tempat)</p>
            @endif
        </div>
        
        <div class="row mb-3 mt-3">
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Tanggal Mulai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga akan mulai disewakan saat kedua pihak menyetujui penawaran"></i></h6>
                @if ($penawaran->first()->req_tanggal_mulai == null)
                    @if ($penawaran->first()->status_penawaran == "Menunggu")
                        <p>(Menunggu Persetujuan)</p>
                    @else
                        <p>(Penawaran telah {{$penawaran->first()->status_penawaran}})</p>
                    @endif
                @else
                    @php
                        $tanggalAwal = $penawaran->first()->req_tanggal_mulai;
                        $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
                        $tanggalBaru = $tanggalObjek->format('d-m-Y');
                    @endphp
                    <p>{{$tanggalBaru}}</p>
                @endif
            </div>
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Tanggal Selesai Dipinjam: <i class="bi bi-info-circle" data-toggle="tooltip" title="Waktu berakhirnya peminjaman ditentukan berdasarkan waktu dimulainya peminjaman"></i></h6>
                @if ($penawaran->first()->req_tanggal_selesai == null)
                    @if ($penawaran->first()->status_penawaran == "Menunggu")
                        <p>(Menunggu Persetujuan)</p>
                    @else
                        <p>(Penawaran telah {{$penawaran->first()->status_penawaran}})</p>
                    @endif
                @else
                    @php
                        $tanggalAwal2 = $penawaran->first()->req_tanggal_selesai;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                    @endphp
                    <p>{{$tanggalBaru2}}</p>
                @endif
            </div>
        </div>
    </div>
    @if ($penawaran->first()->status_penawaran == "Menunggu")
        <div class="container">
            <div class="row justify-content-end mb-3">
                <div class="col-12 col-md-6">
                    <form action="/pemilik/penawaran/konfirmasiPenawaran" method="post">
                        @csrf
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <hr>
                        <span style="font-size: 14px">Konfirmasi detail penawaran setelah pihak pengelola tempat menyetujui penawaran</span>
                        @if ($penawaran->first()->status_pemilik == null && $penawaran->first()->status_tempat == "Setuju")
                            <button type="submit" class="btn btn-success w-100">Konfirmasi</button>
                        @else
                            <button type="submit" disabled class="btn btn-success w-100">Konfirmasi</button>
                        @endif
                    </form>
                    <hr>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-12 col-md-3 mb-3">
                    <a href="" class="btn btn-secondary w-100">Negosiasi</a>
                </div>
                <div class="col-12 col-md-3">
                    <form id="cancelForm" action="/pemilik/penawaran/batalPenawaran" method="post" onsubmit="return konfirmasi();">
                        @csrf
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <button type="submit" class="btn btn-danger w-100">Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
        
        <hr>
        <div class="nego" id="negoDiv">
            <!-- Detail Negosiasi -->
            <h3>Negosiasi</h3>
            <div class="row justify-content-center">
                <div class="col-12 p-4">
                    <!-- Form Balasan -->
                    <form action="/pemilik/penawaran/negosiasi/tambahNego" method="post">
                        @csrf
                        <input type="hidden" name="penawaran" value="{{$penawaran->first()->id_penawaran}}">
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
    function konfirmasi() {
        event.preventDefault(); // Menghentikan submission form secara default

        swal({
            title: "Apakah Anda yakin?",
            text: "Anda akan membatalkan penawaran ini.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, batalkan!",
            cancelButtonText: "Tidak, batal!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                // Jika user mengklik "Ya", submit form
                document.getElementById('cancelForm').submit();
            } else {
                swal.close(); // Tutup SweetAlert jika user memilih "Tidak"
            }
        });

        return false; // Mengembalikan false untuk mencegah submission form
    }
    $("form[action='/pemilik/penawaran/negosiasi/tambahNego']").submit(function(e) {
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
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('noRedirectInput');
        var submitBtn = document.getElementById('submitBtn');

        // Mencegah perilaku default dari <a> saat input diklik
        input.addEventListener('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
        });

        // Mencegah perilaku default dari <a> namun mengizinkan form untuk disubmit
        submitBtn.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection