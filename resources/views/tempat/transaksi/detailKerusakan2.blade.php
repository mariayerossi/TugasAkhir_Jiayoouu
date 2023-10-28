@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .card-form {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    .search-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.search-container .form-control, .search-container .btn {
    margin: 5px;
}
.search-container .form-control {
    margin: 5px;
    max-width: 250px; /* Atur lebar maksimal sesuai keinginan Anda */
}
/* Responsif untuk mobile */
/* Responsif untuk mobile */
@media (max-width: 768px) {
    .search-container {
        flex-direction: row; /* Mengubah ini dari column ke row */
    }
    .search-container .form-control, .search-container .btn {
        max-width: 100%; /* Perubahan ini opsional, bergantung pada tampilan yang Anda inginkan */
    }
}

    </style>
@include("layouts.message")
<div class="container mt-5 mb-5 p-3 rounded">
        <div class="d-flex justify-content-center">
            <h3 class="text-center mb-3">Ajukan Kerusakan Alat</h3>
        </div>
        <div style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-3 mb-5">
            <i class="bi bi-exclamation-circle"></i> Penentuan kerusakan alat olahraga karena unsur kesengajaan berada pada tanggung jawab Anda.<br>
            <ul>
                <li> Jika terdapat kesengajaan, pelanggan wajib memberikan ganti rugi sesuai nominal yang ditentukan oleh pemilik alat.</li>
                <li> Namun, tanpa unsur kesengajaan, ganti rugi tidak dikenakan.</li>
                <li> Ganti rugi diserahkan saat pemilik alat mengambil alat.</li>
            </ul>
        </div>
        <form action="/tempat/kerusakan/tampilkan" method="GET">
            @csrf
            <div class="search-container">
                <select class="form-control" name="id_htrans">
                    <option value="" disabled selected>Masukkan Kode Transaksi</option>
                    @if (!$htrans->isEmpty())
                        @foreach ($htrans as $item)
                            <option value="{{$item->id_htrans}}">{{$item->kode_trans}}</option>
                        @endforeach
                    @endif
                    <!-- Anda bisa menambahkan lebih banyak opsi di sini -->
                </select>
                <button type="submit" class="btn btn-primary">Tampilkan Alat</button>
            </div>
        </form>
        @if ($dtrans != null)
            @foreach ($dtrans as $index => $item)
            <form action="/tempat/kerusakan/ajukanKerusakan" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-template card-form p-5">
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                    @endphp
                    <h5 class="text-center mb-1">{{$dataAlat->nama_alat}}</h5>
                    <h6 class="text-center mb-5">Ganti Rugi: Rp {{number_format($dataAlat->ganti_rugi_alat, 0, ',', '.')}}</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 col-12 mt-2">
                            <h6>Apakah terdapat unsur kesengajaan dalam kerusakan alat olahraga?</h6>
                        </div>
                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                            <input type="radio" class="btn-check" name="unsur{{$index}}" id="danger-outlined-{{$index}}" autocomplete="off" value="Ya">
                            <label class="btn btn-outline-danger" for="danger-outlined-{{$index}}">Ya</label>

                            <input type="radio" class="btn-check" name="unsur{{$index}}" id="primary-outlined-{{$index}}" autocomplete="off" value="Tidak">
                            <label class="btn btn-outline-primary" for="primary-outlined-{{$index}}">Tidak</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-12 mt-2">
                            <h6>Lampirkan Bukti</h6>
                        </div>
                        <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                            <input type="file" class="form-control" name="foto{{$index}}" accept=".jpg,.png,.jpeg">
                        </div>
                    </div>
                    <input type="hidden" name="id_dtrans[{{$index}}]" value="{{$item->id_dtrans}}">
                </div>
            @endforeach
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success">Kirim</button>
            </div>
        </form>
        @endif
</div>
@endsection