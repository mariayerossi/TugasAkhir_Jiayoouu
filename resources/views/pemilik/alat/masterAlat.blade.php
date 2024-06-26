@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<style>
    #toggleSwitch {
        cursor: pointer;
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Tambah Alat Olahraga</h3>
    @include("layouts.message")
    <div class="d-flex justify-content-center">
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-circle"></i> Sebagai website persewaan alat olahraga bekas, kami ingin mengingatkan bahwa penggunaan berulang dari alat olahraga dapat menyebabkan penurunan kualitas. Dengan menyewakan di website ini, pemilik setuju untuk menanggung konsekuensi tersebut.
        </div>
    </div>
    <div style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-3 mb-5">
        <i class="bi bi-exclamation-circle"></i> Syarat alat olahraga yang boleh disewakan adalah sebagai berikut: <br>
        <ul>
            <li> Alat olahraga yang diajukan untuk disewakan harus merupakan alat yang sudah tidak lagi digunakan oleh pemiliknya.</li>
            <li> Kondisi alat olahraga yang disewakan harus dalam keadaan BEKAS, namun tetap layak dan aman untuk digunakan. </li>
            <li> Alat olahraga harus bebas dari kerusakan yang dapat mengancam keselamatan pengguna.</li>
            <li> Uang ganti rugi hanya akan dibayar oleh pengguna yang dengan SENGAJA merusak alat olahraga.</li>
            <li> Jika alat olahraga mengalami kerusakan secara TIDAK SENGAJA, maka tidak ada pihak yang akan dikenakan biaya ganti rugi atas kerusakan.</li>
            <li><b> Apabila anda mengunggah alat olahraga, maka dianggap telah menyetujui ketentuan-ketentuan yang telah disebutkan di atas.</b></li>
        </ul>
    </div>
    <form action="/pemilik/tambahAlat" method="post" enctype="multipart/form-data" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-5 mb-5">
        @csrf
        <div class="row">
            <div class="col-md-3 col-12 mt-2">
                <h6>Nama Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control @error('alat') is-invalid @enderror"  name="alat" placeholder="Masukkan Nama Alat Olahraga" value="{{old('alat')}}">
                @error('alat')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Kategori Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <select class="form-select @error('kategori') is-invalid @enderror" name="kategori">
                    <option value="" disabled selected>Masukkan Kategori Alat Olahraga</option>
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->id_kategori}}" {{ old('kategori') == $item->id_kategori ? 'selected' : '' }}>{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
                @error('kategori')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        {{-- <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Letak Kota <i class="bi bi-info-circle" data-toggle="tooltip" title="Masukkan kota Anda untuk menemukan tempat olahraga terdekat. Pastikan informasi akurat untuk hasil yang tepat."></i></h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control @error('kota') is-invalid @enderror" id="search-kota" name="kota" placeholder="Ketik nama kota..." value="{{old('kota')}}">
                <ul class="list-group" id="suggestion-list"></ul>
                <input type="hidden" id="selected-kota">
                @error('kota')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div> --}}
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Foto Alat Olahraga</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto[]" multiple accept=".jpg,.png,.jpeg">
                @error('foto')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Deskripsi Alat Olahraga</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">maksimal 500 kata</span>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <textarea id="myTextarea" class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" rows="4" cols="50" onkeyup="updateCount()" placeholder="Masukkan Deskripsi Alat Olahraga">{{ old('deskripsi') }}</textarea>
                <p id="charCount">0/500</p>
                @error('deskripsi')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Berat Alat Olahraga</h6>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <input type="number" class="form-control @error('berat') is-invalid @enderror" name="berat" step="0.01" min="0" placeholder="Masukkan Berat Alat Olahraga" value="{{old('berat')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">gram</div>
                    </div>
                    @error('berat')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Ukuran Alat Olahraga</h6>
            </div>
            <div class="col-md-3 col-12 mt-2 col-auto">
                <div class="input-group mb-2">
                    <input type="number" class="form-control @error('panjang') is-invalid @enderror" min="0" id="panjang" name="panjang" placeholder="Panjang" value="{{old('panjang')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                    @error('panjang')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control @error('lebar') is-invalid @enderror" min="0" id="lebar" name="lebar" placeholder="Lebar" value="{{old('lebar')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                    @error('lebar')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control @error('tinggi') is-invalid @enderror" min="0" id="tinggi" name="tinggi" placeholder="Tinggi" value="{{old('tinggi')}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">cm</div>
                    </div>
                    @error('tinggi')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
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
                    <!-- Input yang terlihat oleh pengguna -->
                    <input type="text" class="form-control @error('komisi') is-invalid @enderror" id="komisiDisplay" placeholder="Masukkan Komisi Alat Olahraga" oninput="formatNumber2(this)" value="{{old('komisi')}}">

                    <!-- Input tersembunyi untuk kirim ke server -->
                    <input type="hidden" name="komisi" id="komisiActual" value="{{old('komisi')}}">
                </div>
                @error('komisi')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Uang Ganti Rugi Alat <i class="bi bi-info-circle" data-toggle="tooltip" title="Uang ganti rugi yang peminjam bayar jika peminjam merusak alat olahraga."></i></h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    <!-- Input yang terlihat oleh pengguna -->
                    <input type="text" class="form-control @error('ganti') is-invalid @enderror" id="gantiDisplay" placeholder="Masukkan Jumlah Ganti Rugi" oninput="formatNumber(this)" value="{{old('ganti')}}">

                    <!-- Input tersembunyi untuk kirim ke server -->
                    <input type="hidden" name="ganti" id="gantiActual" value="{{old('ganti')}}">
                </div>
                @error('ganti')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
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
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
</div>
<script>
    
    // function formatNumber(input) {
    //     // Mengambil value dari input
    //     let value = input.value;

    //     // Menghapus semua titik dan karakter non-numerik lainnya
    //     value = value.replace(/\D/g, '');

    //     // Memformat ulang sebagai angka dengan pemisah ribuan titik
    //     value = parseFloat(value).toLocaleString('id-ID');

    //     // Mengembalikan format yang sudah diubah ke input
    //     input.value = value;
    // }

    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('gantiActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('gantiActual').value = '';
        }
    }
    function formatNumber2(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('komisiActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('komisiActual').value = '';
        }
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

    const kota = ['Jakarta', 'Surabaya', 'Semarang', 'Bandung', 'Medan', 'Makassar', 'Tangerang', 'Solo', 'Sidoarjo', 'Depok', 'Malang', 'Bogor', 'Yogyakarta', 'Gresik', 'Bekasi'];
    const inputEl = document.getElementById('search-kota');
    const suggestionList = document.getElementById('suggestion-list');
    const selectedKotaInput = document.getElementById('selected-kota');

    inputEl.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        suggestionList.innerHTML = ''; // Bersihkan list sebelumnya

        if (query) {
            kota.filter(item => item.toLowerCase().includes(query)).forEach(item => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.textContent = item;
                listItem.addEventListener('click', () => {
                    inputEl.value = item;
                    selectedKotaInput.value = item;
                    suggestionList.innerHTML = ''; // Sembunyikan opsi setelah diklik
                });
                suggestionList.appendChild(listItem);
            });
        }
    });

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>
@endsection