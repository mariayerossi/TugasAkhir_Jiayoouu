@extends('layouts.navbar_customer')

@section('content')
<style>
    .card-image-container {
    display: flex;
    justify-content: center; /* Pusatkan gambar secara horizontal */
    align-items: center; /* Pusatkan gambar secara vertikal */
    height: 100%; /* Tentukan tinggi agar flexbox dapat bekerja dengan benar */
}

.square-wrapper {
    width: 70%;
    padding-top: 70%; /* Rasio tetap 1:1 */
    position: relative;
    margin: 0 auto; /* Pusatkan wrapper gambar */
}

.square-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}


.card-image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* Sesuaikan dengan preferensi Anda (cover atau contain) */
}
    .tiny-card {
        width: 150px; /* Lebar tetap kartu */
        height: 50px; /* Tinggi tetap kartu */
    }

    .tiny-card .card-body {
        padding: 1px; /* Padding minimal */
    }

    .tiny-card .square-image-container img {
        height: 45px; /* Mengatur tinggi gambar agar sesuai dengan kartu */
        width: 45px;
    }

    .tiny-card .card-title {
        font-size: 10px; /* Ukuran font sangat kecil */
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .square-image-container2 {
        height: 45px;
        width: 45px;
        overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
    }
</style>
<div class="container mt-5">
    <h2 class="text-center mb-5">Daftar Riwayat Transaksi</h2>
    @if (!$trans->isEmpty())
        @foreach ($trans as $item)
            <div class="card mb-3" style="max-width: 100%;">
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="card-image-container">
                                <div class="square-wrapper">
                                    <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    <h6><b>Tanggal Transaksi: {{$item->tanggal_trans}}</b></h6>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <h6>Status: </h6>
                                    @if ($item->status_trans == "Diterima")
                                        <h6 style="color: rgb(0, 145, 0)">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Ditolak")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Dibatalkan")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Menunggu")
                                        <h6 style="color: rgb(239, 203, 0)">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Selesai")
                                        <h6 style="color: blue">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Dikomplain")
                                        <h6 style="color: red">{{$item->status_trans}}</h6>
                                    @elseif ($item->status_trans == "Berlangsung")
                                        <h6 style="color: rgb(255, 145, 0)">{{$item->status_trans}}</h6>
                                    @endif
                                </div>
                                <p class="card-text"><strong>{{$item->kode_trans}}</strong></p>
                                <h5 class="card-title"><b>{{$item->nama_lapangan}}</b></h5>
                                @php
                                    $tanggalAwal2 = $item->tanggal_sewa;
                                    $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                    $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');

                                    $dtrans = DB::table('dtrans')
                                            ->select("alat_olahraga.id_alat", "alat_olahraga.nama_alat","files_alat.nama_file_alat")
                                            ->join("alat_olahraga", "dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                                            ->joinSub(function($query) {
                                                $query->select("fk_id_alat", "nama_file_alat")
                                                    ->from('files_alat')
                                                    ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                                            }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                                            ->where("dtrans.fk_id_htrans","=",$item->id_htrans)
                                            ->get();
                                    // dd($dtrans);
                                @endphp
                                <p class="card-text"><strong>Tanggal Sewa: </strong>{{$tanggalBaru2}}</p>
                                <p class="card-text"><strong>Jam Sewa: </strong>{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i:s') }}</p>
                                <div class="d-flex flex-row flex-wrap">
                                @if (!$dtrans->isEmpty())
                                    @foreach ($dtrans as $item2)
                                        <div class="card tiny-card h-70 mb-1 mr-1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Gambar Alat -->
                                                    <div class="col-4">
                                                        <div class="square-image-container2">
                                                            <img src="{{ asset('upload/' . $item2->nama_file_alat) }}" alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Nama Alat -->
                                                    <div class="col-8 d-flex align-items-center justify-content-between">
                                                        <h5 class="card-title truncate-text">{{$item2->nama_alat}}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <h4><b>Total: Rp {{number_format($item->total_trans, 0, ',', '.')}}</b></h4>
                            </div>
                            <div class="d-flex justify-content-end">
                                @if ($item->status_trans == "Selesai")
                                    <form id="bookingLagi" action="/customer/transaksi/bookingLagi" method="post">
                                        @csrf
                                        <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                                        <button type="submit" class="btn btn-success">Booking Lagi <i class="bi bi-bag-check"></i></button>
                                    </form>
                                @elseif ($item->status_trans == "Menunggu" || $item->status_trans == "Diterima")
                                    <form id="batalTransaksiForm" action="/customer/transaksi/batalBooking" method="post">
                                        @csrf
                                        <input type="hidden" name="id_htrans" value="{{$item->id_htrans}}">
                                        <button type="submit" class="btn btn-danger">Batal Booking <i class="bi bi-x-lg"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
<script>
    $(document).ready(function() {
        $(".btn-danger").click(function(event) {
            event.preventDefault(); // Mencegah perilaku default form
    
            var formData = $("#batalTransaksiForm").serialize(); // Mengambil data dari form
    
            $.ajax({
                url: "/customer/transaksi/batalBooking",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        swal("Success!", response.message, "success");
                    }
                    else {
                        swal("Error!", response.message, "error");
                    }
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
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection