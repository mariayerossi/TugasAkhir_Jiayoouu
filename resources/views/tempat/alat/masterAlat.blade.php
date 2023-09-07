@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    #toggleSwitch {
        cursor: pointer;
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Tambah Alat Olahraga</h3>
    @include("layouts.message")
    <div style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-3 mb-5">
        <i class="bi bi-exclamation-circle"></i> Syarat alat olahraga yang boleh disewakan adalah sebagai berikut: <br>
        <ul>
            <li> Alat olahraga yang diajukan untuk disewakan harus merupakan alat yang sudah tidak lagi digunakan oleh pemiliknya.</li>
            <li> Kondisi alat olahraga yang disewakan harus dalam keadaan BEKAS, namun tetap layak dan aman untuk digunakan. </li>
            <li> Alat olahraga harus bebas dari kerusakan yang dapat mengancam keselamatan pengguna.</li>
        </ul>
    </div>
    <form action="/tempat/alat/tambahAlat" method="post" enctype="multipart/form-data" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-5 mb-5">
        @csrf
        <div class="row">
            <div class="col-md-3 col-12 mt-2">
                <h6>Nama Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" name="alat" placeholder="Masukkan Nama Alat Olahraga" value="{{old('alat')}}">
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
                        <option value="{{$item->nama_kategori}}" {{ old('kategori') == $item->nama_kategori ? 'selected' : '' }}>{{$item->nama_kategori}}</option>
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
                <h6>Deskripsi Alat Olahraga</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">maksimal 500 kata</span>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <textarea id="myTextarea" class="form-control" name="deskripsi" rows="4" cols="50" onkeyup="updateCount()" placeholder="Masukkan Deskripsi Alat Olahraga">{{ old('deskripsi') }}</textarea>
                <p id="charCount">0/500</p>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Berat Alat Olahraga</h6>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" name="berat" step="0.01" min="0" placeholder="Masukkan Berat Alat Olahraga" value="{{old('berat')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">gram</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Ukuran Alat Olahraga</h6>
            </div>
            <div class="col-md-3 col-12 mt-2 col-auto">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="panjang" name="panjang" placeholder="Panjang" value="{{old('panjang')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="lebar" name="lebar" placeholder="Lebar" value="{{old('lebar')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="tinggi" name="tinggi" placeholder="Tinggi" value="{{old('tinggi')}}">
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
                <input type="number" class="form-control" name="stok" min="0" placeholder="Masukkan Jumlah Stok Alat" value="{{old('stok')}}">
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
                    <input type="number" class="form-control" min="0" name="komisi" placeholder="Masukkan Komisi Alat Olahraga" oninput="formatNumber(this)" value="{{old('komisi')}}">
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
                    <input type="number" class="form-control" min="0" name="ganti" placeholder="Masukkan Jumlah Ganti Rugi" oninput="formatNumber(this)" value="{{old('ganti')}}">
                </div>
                <span class="ml-2 ms-2" style="font-size: 13px">uang ganti rugi yang peminjam bayar jika peminjam merusak alat olahraga</span>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Status Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-3 mt-md-0 d-flex align-items-center">
                <svg id="toggleSwitch" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16" style="color: #007466">
                    <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                </svg>
                <span id="toggleLabel" class="ml-2 ms-2">Aktif</span>
                <input type="hidden" id="statusInput" name="status" value="Aktif">
            </div>
        </div>
        <input type="hidden" name="pemilik" value="{{Session::get("dataRole")->id_tempat}}">
        <input type="hidden" name="role" value="Tempat">
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
            statusInput.value = "Non Aktif";
        } else {
            label.textContent = "Aktif";
            svgElement.setAttribute("class", "bi bi-toggle-on");
            svgElement.innerHTML = "<path d='M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z'/>";
            statusInput.value = "Aktif";
        }
    });
    function updateCount() {
        let textarea = document.getElementById('myTextarea');
        let textareaValue = textarea.value;
        let charCount = textareaValue.length;
        let countElement = document.getElementById('charCount');

        if (charCount > 500) {
            // Potong teks untuk membatasi hanya 500 karakter
            textarea.value = textareaValue.substring(0, 500);
            charCount = 500;
            countElement.style.color = 'red';
        } else {
            countElement.style.color = 'black';
        }

        countElement.innerText = charCount + "/500";
    }
</script>
@endsection