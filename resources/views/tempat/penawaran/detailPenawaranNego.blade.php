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
        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->first()->fk_id_pemilik)->get()->first();
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
            <h6>Diajukan oleh: {{$dataPemilik->nama_pemilik}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru}}</h6>
        </div>
    </div>
    <div class="row mb-5">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Ditawarkan <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang ditawarkan oleh pemilik alat"></i></h5>
            <a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">
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
                            <div class="col-8 d-flex flex-column justify-content-center">
                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                <p class="card-text">komisi: Rp {{number_format($dataAlat->komisi_alat, 0, ',', '.')}}/jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Detail Lapangan -->
        <div class="col-md-6 col-sm-12">
            <h5>Lapangan Olahraga <i class="bi bi-info-circle" data-toggle="tooltip" title="Lokasi penggunaan alat olahraga"></i></h5>
            <a href="/tempat/lapangan/lihatDetailLapangan/{{$dataLapangan->id_lapangan}}">
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
            @if ($penawaran->first()->req_harga_sewa != null)
                <p>Rp {{number_format($penawaran->first()->req_harga_sewa, 0, ',', '.')}}</p>
            @else
                <p>(Anda belum mengisi harga sewa)</p>
            @endif

            @if ($penawaran->first()->status_penawaran == "Menunggu")
                <form action="/tempat/penawaran/editHargaSewa" method="post">
                    @csrf
                    <div class="input-group">
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="number" min="0" name="harga_sewa" value="{{old('harga_sewa') ?? $penawaran->first()->req_harga_sewa}}" class="form-control">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Edit Harga</button>
                        </div>
                    </div>
                </form>
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
                <p>(Anda belum mengisi durasi pinjam)</p>
            @endif
            
            @if ($penawaran->first()->status_penawaran == "Menunggu")
                <form action="/tempat/penawaran/editDurasi" method="post">
                    @csrf
                    <div class="input-group">
                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                            <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                            <select class="form-control" name="durasi">
                                <option value="" disabled selected>Masukkan Durasi Peminjaman</option>
                                <option value="1" {{ old('durasi')?? $penawaran->first()->req_durasi == '1' ? 'selected' : '' }}>1 Bulan</option>
                                <option value="2" {{ old('durasi')?? $penawaran->first()->req_durasi == '2' ? 'selected' : '' }}>2 Bulan</option>
                                <option value="3" {{ old('durasi')?? $penawaran->first()->req_durasi == '3' ? 'selected' : '' }}>3 Bulan</option>
                                <option value="5" {{ old('durasi')?? $penawaran->first()->req_durasi == '5' ? 'selected' : '' }}>5 Bulan</option>
                                <option value="9" {{ old('durasi')?? $penawaran->first()->req_durasi == '9' ? 'selected' : '' }}>9 Bulan</option>
                                <option value="12" {{ old('durasi')?? $penawaran->first()->req_durasi == '12' ? 'selected' : '' }}>1 Tahun</option>
                                <option value="24" {{ old('durasi')?? $penawaran->first()->req_durasi == '24' ? 'selected' : '' }}>2 Tahun</option>
                            </select>
                        </div>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Edit Durasi</button>
                        </div>
                    </div>
                </form>
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
                    <form action="/tempat/penawaran/terimaPenawaran" method="post">
                        @csrf
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <hr>
                        <span style="font-size: 14px">Terima penawaran membutuhkan konfirmasi pemilik alat</span>
                        <button type="submit" class="btn btn-success w-100">Terima</button>
                    </form>
                    <hr>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-12 col-md-3 mb-3">
                    <a href="" class="btn btn-secondary w-100">Negosiasi</a>
                </div>
                <div class="col-12 col-md-3">
                    <form action="/tempat/penawaran/tolakPenawaran" method="post">
                        @csrf
                        <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <button type="submit" class="btn btn-danger w-100">Tolak</button>
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
                    <form action="/tempat/penawaran/negosiasi/tambahNego" method="post">
                        @csrf
                        <input type="hidden" name="penawaran" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="hidden" name="id_user" value="{{Session::get('dataRole')->id_tempat}}">
                        <input type="hidden" name="role" value="Tempat">
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
    $("form[action='/tempat/penawaran/negosiasi/tambahNego']").submit(function(e) {
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