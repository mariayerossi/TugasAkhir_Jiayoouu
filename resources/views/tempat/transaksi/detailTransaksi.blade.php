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
    .square-image-container {
        width: 60px;
        height: 60px;
    }
}
</style>
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Detail Transaksi</h3>
    <div class="d-flex justify-content-end mt-3 me-3">
        @if ($htrans->first()->status_trans == "Berlangsung" || $htrans->first()->status_trans == "Selesai" || $htrans->first()->status_trans == "Dikomplain")
            <h6><b>Kode Transaksi: {{$htrans->first()->kode_trans}}</b></h6>
        @endif
    </div>
    @php
        $dataUser = DB::table('user')->where("id_user","=",$htrans->first()->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal = $htrans->first()->tanggal_trans;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');
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

    <div class="row mb-5 mt-4">
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Sewa: {{$tanggalDenganNamaBulan}}</h6>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Jam Sewa: {{$htrans->first()->jam_sewa}} WIB</h6>
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
                                            <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
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
                                            <p class="card-text">Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}} x {{$htrans->first()->durasi_sewa}} Jam</p>
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
        <h5><b>Subtotal: Rp {{number_format($htrans->first()->subtotal_alat, 0, ',', '.')}}</b></h5>
    </div>
    <hr>
    @if ($htrans->first()->status_trans == "Dibatalkan" || $htrans->first()->status_trans == "Ditolak")
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b style="color: red">Total: Rp {{number_format($htrans->first()->total_trans, 0, ',', '.')}}</b></h4>
        </div>
    @else 
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b>Total: Rp {{number_format($htrans->first()->total_trans, 0, ',', '.')}}</b></h4>
        </div>
    @endif

    @if ($htrans->first()->status_trans == "Menunggu")
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <form id="tolakTransaksiForm" action="/tempat/transaksi/tolakTransaksi" method="post">
                @csrf
                <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                <button class="btn btn-danger me-3" type="submit">Tolak</button>
            </form>
            <form id="terimaTransaksiForm" action="/tempat/transaksi/terimaTransaksi" method="post">
                @csrf
                <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                <button class="btn btn-success" type="submit">Terima</button>
            </form>
        </div>
    @elseif ($htrans->first()->status_trans == "Diterima")
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <form id="konfirmasiDipakai" action="/tempat/transaksi/konfirmasiDipakai" method="post">
                @csrf
                <h6>Konfirmasi ketika customer datang dan menggunakan lapangan</h6>
                <div class="input-group">
                    <input type="text" name="kode" id="" class="form-control" placeholder="Masukkan kode transaksi...">
                    <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                    <input type="hidden" name="kode_htrans" value="{{$htrans->first()->kode_trans}}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </div>
            </form>
        </div>
    @elseif ($htrans->first()->status_trans == "Berlangsung")
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <button class="btn btn-primary">Cetak Nota</button>
            {{-- nyetak nota & status htrans selesai --}}
        </div>
    @endif
</div>
<script>
    $(document).ready(function() {
        $(".btn-success").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form
    
            var formData = $("#terimaTransaksiForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/tempat/transaksi/terimaTransaksi",
                type: "POST",
                data: formData,
                success: function(response) {
                    window.location.reload();
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });
        });

        $(".btn-danger").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form
    
            var formData = $("#tolakTransaksiForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/tempat/transaksi/tolakTransaksi",
                type: "POST",
                data: formData,
                success: function(response) {
                    window.location.reload();
                    // alert('Berhasil Diterima!');
                    // Atau Anda dapat mengupdate halaman dengan respons jika perlu
                    // Anda dapat menyesuaikan feedback yang diberikan ke pengguna berdasarkan respons server
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ada masalah saat mengirim data. Silahkan coba lagi.');
                }
            });
        });

        $('#konfirmasiDipakai').on('submit', function(event) {
            event.preventDefault();  // Prevent form from submitting the default way
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Handle success. 
                    // You can update the frontend or show a success message here
                    window.location.reload();
                    swal("Success!", "Berhasil mengkonfirmasi transaksi!", "success");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    // If you send back errors in a known format, you can display them here
                    swal("Error!", "Gagal mengkonfirmasi transaksi!", "error");
                }
            });
        });
    });
</script>    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection