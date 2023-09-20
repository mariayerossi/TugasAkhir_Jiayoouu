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
@include("layouts.message")
<div class="container mt-5 mb-5 bg-white p-4 rounded" style="box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h3 class="text-center mb-5">Permohonan Penawaran Alat</h3>
    @php
        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->first()->req_id_alat)->get()->first();
        $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$dataAlat->id_alat)->get()->first();
        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->first()->fk_id_pemilik)->get()->first();
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->first()->fk_id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$penawaran->first()->req_lapangan)->get()->first();
        $dataFileLapangan = DB::table('files_lapangan')->where("fk_id_lapangan","=",$dataLapangan->id_lapangan)->get()->first();

        $tanggalAwal1 = $penawaran->first()->tanggal_tawar;
        $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal1);
        $tanggalBaru1 = $tanggalObjek1->format('d-m-Y H:i:s');
    @endphp
    <div class="row mb-5 mt-5">
        <!-- Nama Pengirim -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Diajukan oleh: {{$dataPemilik->nama_pemilik}}</h6>
        </div>
        
        <!-- Tanggal Permintaan -->
        <div class="col-md-6 col-sm-12 mb-3">
            <h6>Tanggal Pengajuan: {{$tanggalBaru1}}</h6>
        </div>
    </div>
    <div class="row mb-5">
        <!-- Detail Alat -->
        <div class="col-md-6 col-sm-12">
            <h5>Alat Olahraga yang Ditawarkan <i class="bi bi-info-circle" data-toggle="tooltip" title="Alat olahraga yang ditawarkan oleh pemilik alat"></i></h5>
            @if ($dataAlat->deleted_at == null)<a href="/tempat/detailAlatUmum/{{$dataAlat->id_alat}}">@endif
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
            <h5>Lokasi Penggunaan Alat</h5>
            @if ($dataLapangan->deleted_at == null)<a href="/tempat/lapangan/lihatDetailLapangan/{{$dataLapangan->id_lapangan}}">@endif
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

            @if ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null)
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
            @else
                <p class="card-text">(Tidak dapat mengedit harga sewa, penawaran telah {{$penawaran->first()->status_penawaran}})</p>
            @endif
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
        </div>
        
        <div class="row mb-3 mt-3">
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Tanggal Mulai Dipinjam:</h6>
                @if ($penawaran->first()->req_tanggal_mulai == null)
                    <p>(Anda belum mengisi tanggal peminjaman alat)</p>
                @else
                    @php
                        $tanggalAwal2 = $penawaran->first()->req_tanggal_mulai;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                    @endphp
                    <p>{{$tanggalBaru2}}</p>
                @endif

                @if ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null)
                    <form action="/tempat/penawaran/editTanggalMulai" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') ?? $penawaran->first()->req_tanggal_mulai }}" class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Edit Tanggal</button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="card-text">(Tidak dapat mengedit tanggal mulai dipinjam, penawaran telah {{$penawaran->first()->status_penawaran}})</p>
                @endif
            </div>
            <div class="col-md-6 col-sm-12 mb-3">
                <h6>Tanggal Selesai Dipinjam:</h6>
                @if ($penawaran->first()->req_tanggal_selesai == null)
                    <p>(Anda belum mengisi tanggal pengembalian alat)</p>
                @else
                    @php
                        $tanggalAwal3 = $penawaran->first()->req_tanggal_selesai;
                        $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                        $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
                    @endphp
                    <p>{{$tanggalBaru3}}</p>
                @endif

                @if ($penawaran->first()->status_penawaran == "Menunggu" && $penawaran->first()->status_pemilik == null)
                    <form action="/tempat/penawaran/editTanggalSelesai" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="hidden" name="id_penawaran" value="{{$penawaran->first()->id_penawaran}}">
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') ?? $penawaran->first()->req_tanggal_selesai }}" class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Edit Tanggal</button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="card-text">(Tidak dapat mengedit tanggal selesai dipinjam, penawaran telah {{$penawaran->first()->status_penawaran}})</p>
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
                        @if ($penawaran->first()->status_tempat != "Setuju")
                            <span style="font-size: 14px">Silahkan konfirmasi dan terima penawaran yang diajukan pemilik</span>
                            <button type="submit" class="btn btn-success w-100">Terima</button>
                        @else
                            <span style="font-size: 14px">Penawaran telah disetujui, tunggu konfirmasi dari pemilik alat olahraga</span>
                            <button type="submit" disabled class="btn btn-success w-100">Terima</button>
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
                                            $tanggalAwal4 = $item->waktu_negosiasi;
                                            $tanggalObjek4 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal4);
                                            $tanggalBaru4 = $tanggalObjek4->format('d-m-Y H:i:s');
                                        @endphp
                                        <p>{{$tanggalBaru4}}</p>
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

    @if ($penawaran->first()->status_penawaran == "Selesai" && $penawaran->first()->status_alat == null)
        {{-- konfirmasi alat telah diantar oleh pemilik ke pihak tempat --}}
        <p>Konfirmasi pengembalian alat olahraga</p>
        @if ($penawaran->first()->kode_selesai != null)
            <button class="btn btn-primary" onclick="generateCodeSelesai()" disabled>Konfirmasi</button>
            <div class="kode mt-3 mb-4">
                <h5><b>{{$penawaran->first()->kode_selesai}}</b></h5>
                <p>Berikan kode ini kepada pemiliki alat olahraga untuk mengkonfirmasi</p>
            </div>
        @else
            <button class="btn btn-primary" onclick="generateCodeSelesai()">Konfirmasi</button>
            <div class="kode mt-3 mb-4">

            </div>
        @endif
    @endif

    @if ($penawaran->first()->status_penawaran == "Diterima")
        {{-- konfirmasi alat telah diantar oleh pemilik ke pihak tempat --}}
        <p>Sudah menerima alat olahraga? Silahkan Konfirmasi penerimaan alat</p>
        @if ($penawaran->first()->kode_mulai != null)
            <button class="btn btn-primary" onclick="generateCode()" disabled>Konfirmasi</button>
            <div class="kode mt-3 mb-4">
                <h5><b>{{$penawaran->first()->kode_mulai}}</b></h5>
                <p>Berikan kode ini kepada pemiliki alat olahraga untuk mengkonfirmasi</p>
            </div>
        @else
            <button class="btn btn-primary" onclick="generateCode()">Konfirmasi</button>
            <div class="kode mt-3 mb-4">

            </div>
        @endif 

        <hr>
        
        @if ($komplain->isEmpty())
            <button class="btn btn-warning">Ajukan Komplain</button>

            <div class="row form_komplain mt-4">
                <div class="col-md-8">
                    <form action="/tempat/komplain/tambahKomplain" method="post" class="mt-3" enctype="multipart/form-data" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        @csrf
                        <div class="d-flex justify-content-center">
                            <h5><b>Ajukan Komplain</b></h5>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jenis Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="radio" class="btn-check" name="jenis" id="info-outlined" autocomplete="off" value="Alat tidak sesuai">
                                <label class="btn btn-outline-info" for="info-outlined"><i class="bi bi-box-seam me-2"></i>Alat tidak sesuai</label>

                                <input type="radio" class="btn-check" name="jenis" id="danger-outlined" autocomplete="off" value="Alat rusak">
                                <label class="btn btn-outline-danger" for="danger-outlined"><i class="bi bi-heartbreak me-2"></i>Alat Rusak</label>

                                <input type="radio" class="btn-check" name="jenis" id="primary-outlined" autocomplete="off" value="Lainnya">
                                <label class="btn btn-outline-primary" for="primary-outlined"><i class="bi bi-justify-left me-2"></i></i>Lainnya</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Jelaskan Komplain</h6>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <textarea id="myTextarea" class="form-control" name="keterangan" rows="4" cols="50" onkeyup="updateCount()" placeholder="Masukkan Keterangan Komplain">{{ old('keterangan') }}</textarea>
                                <p id="charCount">0/500</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <h6>Lampirkan Bukti <i class="bi bi-info-circle" data-toggle="tooltip" title="Lampirkan bukti komplain yang dapat memperkuat pernyataan"></i></h6>
                                <span style="font-size: 14px">lampirkan 2 file sekaligus</span>
                            </div>
                            <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                                <input type="file" class="form-control" name="foto[]" multiple accept=".jpg,.png,.jpeg">
                            </div>
                        </div>
                        <input type="hidden" name="fk_id_request" value="{{$penawaran->first()->id_penawaran}}">
                        <input type="hidden" name="jenis_request" value="Penawaran">
                        <input type="hidden" name="id_user" value="{{Session::get("dataRole")->id_tempat}}">
                        <input type="hidden" name="role_user" value="Tempat">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Kirim</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <!-- Kosong atau Anda dapat menambahkan konten lain di sini jika diperlukan -->
                </div>
            </div>
        @else
            <div class="komplain">
                <div class="card h-70">
                    <div class="card-body">
                        <div class="row">
                            <!-- Gambar Alat -->
                            <div class="col-4">
                                <div class="square-image-container">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                      </svg>
                                </div>
                            </div>
                            
                            <!-- Nama Alat -->
                            <div class="col-8 d-flex flex-column justify-content-center">
                                <h4 class="card-title"><b>Komplain Anda telah dikirim!</b></h4>
                                @if ($komplain->first()->status_komplain == "Menunggu")
                                    <p class="card-text">Komplain kamu menunggu konfirmasi admin</p>
                                @else
                                    <p class="card-text">Komplain kamu sudah {{$komplain->first()->status_komplain}} oleh admin</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
<script>
    function generateCode() {
        const currentDate = new Date();
        const month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        const day = ("0" + currentDate.getDate()).slice(-2);
        const formattedDate = `${currentDate.getFullYear()}${month}${day}`;
        // Nomor urut (misalnya Anda bisa gunakan timestamp atau counter untuk ini)
        const sequenceNumber = currentDate.getTime(); // Contoh ini menggunakan timestamp, Anda bisa menggantinya dengan sistem nomor urut yang Anda inginkan.

        const code = `REQTM${formattedDate}<?=$penawaran->first()->id_penawaran;?>`;
        const kodeElement = document.querySelector('.kode');
        kodeElement.innerHTML = `<h5><b>${code}</b></h5> <br><p>Berikan Kode Konfirmasi ini kepada pemilik alat olahraga untuk mengkonfirmasi</p>`;
        // kodeElement.style.fontWeight = 'bold';

        // Kirim kode ke server:
        fetch('/tempat/penawaran/simpanKodeMulai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode: code, id: <?= $penawaran->first()->id_penawaran ?> })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });

        const buttonElement = document.querySelector('.btn.btn-primary');
        buttonElement.disabled = true;
    }

    function generateCodeSelesai() {
        const currentDate = new Date();
        const month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        const day = ("0" + currentDate.getDate()).slice(-2);
        const formattedDate = `${currentDate.getFullYear()}${month}${day}`;
        // Nomor urut (misalnya Anda bisa gunakan timestamp atau counter untuk ini)
        const sequenceNumber = currentDate.getTime(); // Contoh ini menggunakan timestamp, Anda bisa menggantinya dengan sistem nomor urut yang Anda inginkan.

        const code = `REQMS${formattedDate}<?=$penawaran->first()->id_penawaran;?>`;
        const kodeElement = document.querySelector('.kode');
        kodeElement.innerHTML = `<h5><b>${code}</b></h5> <br><p>Berikan Kode Konfirmasi ini kepada pemilik alat olahraga untuk mengkonfirmasi</p>`;
        // kodeElement.style.fontWeight = 'bold';

        // Kirim kode ke server:
        fetch('/tempat/penawaran/simpanKodeSelesai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode: code, id: <?= $penawaran->first()->id_penawaran ?> })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
        });

        const buttonElement = document.querySelector('.btn.btn-primary');
        buttonElement.disabled = true;
    }
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        @if($nego->isEmpty())
        // Menyembunyikan div nego saat halaman pertama kali dimuat
            $(".nego").hide();
        @endif

        @if($komplain->isEmpty())
        // Menyembunyikan div nego saat halaman pertama kali dimuat
            $(".form_komplain").hide();
        @endif

        // Mengatur event ketika tombol Negosiasi diklik
        $(".btn-secondary").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            $(".nego").show();   // Menampilkan div nego
        });

        $(".btn-warning").click(function(e) {
            e.preventDefault();  // Menghentikan perilaku default (navigasi)
            $(".form_komplain").show();   // Menampilkan div nego
        });
    });
    function updateCount() {
        let textarea = document.getElementById('myTextarea');
        let textareaValue = textarea.value;
        let charCount = textareaValue.length;
        let countElement = document.getElementById('charCount');

        if (charCount > 500) {
            // Potong teks untuk membatasi hanya 300 karakter
            textarea.value = textareaValue.substring(0, 500);
            charCount = 500;
            countElement.style.color = 'red';
        } else {
            countElement.style.color = 'black';
        }

        countElement.innerText = charCount + "/500";
    }
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