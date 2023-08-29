@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<style>
    #toggleSwitch {
        cursor: pointer;
    }

</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Ubah Alat Olahraga</h3>
    @include("layouts.message")
    <form action="/editAlatdiPemilik" method="post" enctype="multipart/form-data" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-5 mb-5">
        @csrf
        <div class="row">
            <div class="col-md-3 col-12 mt-2">
                <h6>Nama Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" name="alat" placeholder="Masukkan Nama Alat Olahraga" value="{{old('alat') ?? $alat->first()->nama_alat}}">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Kategori Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Masukkan Kategori Alat Olahraga</option>
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->nama_kategori}}" {{ old('kategori') ?? $alat->first()->kategori_alat == $item->nama_kategori ? 'selected' : '' }}>{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Foto Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="file" class="form-control" name="foto[]" multiple accept=".jpg,.png,.jpeg">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Foto Alat Olahraga Sebelumnya</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                @foreach($files as $photo) <!-- Diasumsikan memiliki relasi ke model foto -->
                    <img src="{{ asset('upload/' . $photo->nama_file_alat) }}" width="100">
                    <!-- Checkbox untuk menghapus foto -->
                    <input type="checkbox" name="delete_photos[]" value="{{ $photo->id_file_alat }}"> Hapus
                @endforeach
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Deskripsi Alat Olahraga</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">maksimal 300 kata</span>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <textarea class="form-control" name="deskripsi" rows="3" placeholder="Masukkan Deskripsi Alat Olahraga">{{ old('deskripsi') ?? $alat->first()->deskripsi_alat }}</textarea>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Berat Alat Olahraga</h6>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" name="berat" step="0.01" min="0" placeholder="Masukkan Berat Alat Olahraga" value="{{old('berat') ?? $alat->first()->berat_alat}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">gram</div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $array = explode("x", $alat->first()->ukuran_alat);
        @endphp
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Ukuran Alat Olahraga</h6>
            </div>
            <div class="col-md-3 col-12 mt-2 col-auto">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="panjang" name="panjang" placeholder="Panjang" value="{{old('panjang') ?? $array[0]}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="lebar" name="lebar" placeholder="Lebar" value="{{old('lebar') ?? $array[1]}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="tinggi" name="tinggi" placeholder="Tinggi" value="{{old('tinggi') ?? $array[2]}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Stok Alat Olahraga</h6>
            </div>
            <div class="col-md-4 col-12 mt-2 mt-md-0">
                <input type="number" class="form-control" name="stok" min="0" placeholder="Masukkan Jumlah Stok Alat" value="{{old('stok') ?? $alat->first()->stok_alat}}">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Komisi Alat Olahraga</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">harga komisi per jam</span>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    <input type="number" class="form-control" min="0" name="komisi" placeholder="Masukkan Komisi Alat Olahraga" oninput="formatNumber(this)" value="{{old('komisi') ?? $alat->first()->komisi_alat}}">
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Uang Ganti Rugi Alat</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    <input type="number" class="form-control" min="0" name="ganti" placeholder="Masukkan Jumlah Ganti Rugi" oninput="formatNumber(this)" value="{{old('ganti') ?? $alat->first()->ganti_rugi_alat}}">
                </div>
                <span class="ml-2 ms-2" style="font-size: 13px">uang ganti rugi yang peminjam bayar jika peminjam merusak alat olahraga</span>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Status Alat Olahraga</h6>
            </div>
            @if ($alat->first()->status_alat == "Aktif")
                <div class="col-md-8 col-12 mt-3 mt-md-0 d-flex align-items-center">
                    <svg id="toggleSwitch" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16" style="color: #007466">
                        <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    </svg>
                    <span id="toggleLabel" class="ml-2 ms-2">Aktif</span>
                    <input type="hidden" id="statusInput" name="status" value="Aktif">
                </div>
            @else
                <div class="col-md-8 col-12 mt-3 mt-md-0 d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16" style="color: #007466">
                        <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
                    </svg>
                    <span id="toggleLabel" class="ml-2 ms-2">Non Aktif</span>
                    <input type="hidden" id="statusInput" name="status" value="Non Aktif">
                </div>
            @endif
            <input type="hidden" id="statusInput" name="id" value="{{$alat->first()->id_alat}}">
        </div>
        <input type="hidden" name="pemilik" value="{{Session::get("dataRole")->id_pemilik}}">
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
</div>
<script>
    
    function formatNumber(input) {
        // Mengambil value dari input
        let value = input.value;

        // Menghapus semua titik dan karakter non-numerik lainnya
        value = value.replace(/\D/g, '');

        // Memformat ulang sebagai angka dengan pemisah ribuan titik
        value = parseFloat(value).toLocaleString('id-ID');

        // Mengembalikan format yang sudah diubah ke input
        input.value = value;
    }

    document.getElementById('toggleSwitch').addEventListener('click', function() {
        // Mengganti SVG menjadi toggle off

        var label = document.getElementById('toggleLabel');
        var svgElement = document.getElementById("toggleSwitch");
        if (label.textContent === "Aktif") {
            label.textContent = "Non Aktif";
            svgElement.setAttribute("class", "bi bi-toggle-off");
            svgElement.innerHTML = "<path d='M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z'/>";
            inputStatus.value = "Non Aktif";
        } else {
            label.textContent = "Aktif";
            svgElement.setAttribute("class", "bi bi-toggle-on");
            svgElement.innerHTML = "<path d='M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z'/>";
            inputStatus.value = "Aktif";
        }
    });
</script>
@endsection