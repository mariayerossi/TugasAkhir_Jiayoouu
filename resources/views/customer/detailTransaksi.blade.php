@extends('layouts.navbar_customer')

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
        @include("layouts.message")
        @php
            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$data["id_lapangan"])->get()->first();
            $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();
        @endphp
    
        @php
    
            $tanggalAwal2 = $data["tanggal"];
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
        @endphp
    
        <div class="row mb-5 mt-4">
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Tanggal Sewa: {{$tanggalBaru2}}</h6>
            </div>
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Jam Sewa: {{$data["mulai"]}} WIB - {{$data["selesai"]}} WIB</h6>
            </div>
        </div>
        
        <h5>Lapangan yang Disewa</h5>
        <a href="/customer/detailLapangan/{{$data["id_lapangan"]}}">
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
                            <p class="card-text">Rp {{number_format($dataLapangan->harga_sewa_lapangan, 0, ',', '.')}} x {{$data["durasi"]}} Jam</p>
                            {{-- Anda bisa menambahkan detail lain di sini sesuai kebutuhan Anda --}}
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <div class="d-flex justify-content-end mt-3 me-3">
            <h5><b>Subtotal: Rp {{number_format($dataLapangan->harga_sewa_lapangan * $data["durasi"], 0, ',', '.')}}</b></h5>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-6 col-sm-12">
                <h5>Alat Olahraga yang Disewa</h5>
                @if (Session::has("sewaAlat"))
                    @foreach (Session::get("sewaAlat") as $item)
                        @php
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item["alat"])->get()->first();
                            $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item["alat"])->get()->first();
                            $sewaSendiri = DB::table("sewa_sendiri")->where("req_id_alat","=",$item["alat"])->get()->first();
                            $permintaan  = DB::table("request_permintaan")->where("req_id_alat","=",$item["alat"])->get()->first();
                            $penawaran  = DB::table("request_penawaran")->where("req_id_alat","=",$item["alat"])->get()->first();

                            $harga = 0;
                            if ($permintaan != null) {
                                $harga = $permintaan->req_harga_sewa;
                            }
                            else if ($penawaran != null) {
                                $harga = $penawaran->req_harga_sewa;
                            }
                            else if ($sewaSendiri != null) {
                                $harga = $dataAlat->komisi_alat;
                            }
                        @endphp
                        <a href="/customer/detailAlat/{{$dataAlat->id_alat}}">
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
                                            <p class="card-text">Rp {{number_format($harga, 0, ',', '.')}} x {{$data["durasi"]}} Jam</p>
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
            <h5><b>Subtotal: Rp {{number_format($data["subtotal_alat"], 0, ',', '.')}}</b></h5>
        </div>
        <div class="d-flex justify-content-end mt-5 me-3">
            <h4><b>Total: Rp {{number_format(($dataLapangan->harga_sewa_lapangan * $data["durasi"]) + $data["subtotal_alat"], 0, ',', '.')}}</b></h4>
        </div>
        <hr>
    
        <div class="d-flex justify-content-end mt-5 me-3 mb-5">
            <a href="javascript:history.back()" class="btn btn-danger me-3" type="submit">Cancel</a>
            <form action="/customer/transaksi/tambahTransaksi" method="post">
                @csrf
                <input type="hidden" name="id_lapangan" value="{{$data['id_lapangan']}}">
                <input type="hidden" name="id_tempat" value="{{$data['id_tempat']}}">
                <input type="hidden" name="tanggal" value="{{$data['tanggal']}}">
                <input type="hidden" name="mulai" value="{{$data['mulai']}}">
                <input type="hidden" name="selesai" value="{{$data['selesai']}}">
                <button class="btn btn-success" type="submit" id="bookingBtn">Booking Sekarang</button>
            </form>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="agreementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agreement</h5>
                    </div>
                    <div class="modal-body">
                        Alat olahraga yang dipergunakan melalui sistem peminjaman harus dijaga dengan baik. Apabila terdapat kerusakan yang disebabkan oleh kesengajaan pelanggan, maka denda akan dikenakan dengan cara <b>pembayaran tunai sebesar ganti rugi yang telah ditetapkan</b> kepada pihak tempat olahraga. Keputusan mengenai apakah kerusakan tersebut disebabkan oleh kesengajaan atau bukan akan ditentukan oleh pihak pengelola fasilitas olahraga
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="confirmBooking">Setuju</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.getElementById('bookingBtn').addEventListener('click', function(event) {
            event.preventDefault();
            $('#agreementModal').modal('show');
        });
    
        document.getElementById('confirmBooking').addEventListener('click', function() {
            $('#agreementModal').modal('hide');
            document.querySelector('form[action="/customer/transaksi/tambahTransaksi"]').submit();
        });
    </script>
        
@endsection