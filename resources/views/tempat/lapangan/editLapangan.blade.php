@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    #toggleSwitch {
        cursor: pointer;
    }
    .image-container {
        width: 100%;
        padding-top: 100%; /* aspect ratio 1:1 */
        position: relative;
    }
    
    .image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* ini memastikan gambar menutupi seluruh area tanpa mengubah rasio aspeknya */
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Ubah Lapangan Olahraga</h3>
    @include("layouts.message")
    <form action="/tempat/lapangan/editLapangan" method="post" enctype="multipart/form-data" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-5 mb-5">
        @csrf
        <div class="row">
            <div class="col-md-3 col-12 mt-2">
                <h6>Nama Lapangan</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" name="lapangan" placeholder="Masukkan Nama Lapangan Olahraga" value="{{old('lapangan') ?? $lapangan->first()->nama_lapangan}}">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Kategori Lapangan</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <select class="form-control" name="kategori">
                    <option value="" disabled selected>Masukkan Kategori Lapangan Olahraga</option>
                    @if (!$kategori->isEmpty())
                        @foreach ($kategori as $item)
                        <option value="{{$item->id_kategori}}" {{ old('kategori') ?? $lapangan->first()->fk_id_kategori == $item->id_kategori ? 'selected' : '' }}>{{$item->nama_kategori}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Tipe Lapangan</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <select class="form-control" name="tipe">
                    <option value="" disabled selected>Masukkan Tipe Lapangan Olahraga</option>
                    <option value="Outdoor" {{ old('tipe')?? $lapangan->first()->tipe_lapangan == 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                    <option value="Indoor" {{ old('tipe')?? $lapangan->first()->tipe_lapangan == 'Indoor' ? 'selected' : '' }}>Indoor</option>
                </select>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Lokasi Lapangan</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" name="lokasi" placeholder="Masukkan Lokasi Lapangan Olahraga" value="{{old('lokasi') ?? $lapangan->first()->lokasi_lapangan}}">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Letak Kota</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="text" class="form-control" id="search-kota" name="kota" placeholder="Ketik nama kota..." value="{{old('kota') ?? $lapangan->first()->kota_lapangan}}">
                <ul class="list-group" id="suggestion-list"></ul>
                <input type="hidden" id="selected-kota">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Foto Lapangan</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <input type="file" class="form-control" name="foto[]" multiple accept=".jpg,.png,.jpeg">
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Foto Lapangan Sebelumnya</h6>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <div class="row">
                    @foreach($files as $photo)
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card">
                                <div class="image-container">
                                    <img src="{{ asset('upload/' . $photo->nama_file_lapangan) }}" alt="{{ $photo->nama_file_lapangan }}">
                                </div>
                                <div class="card-body p-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="delete_photos[]" value="{{ $photo->id_file_lapangan }}" id="deletePhoto{{ $photo->id_file_lapangan }}">
                                        <label class="custom-control-label" for="deletePhoto{{ $photo->id_file_lapangan }}">Hapus</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Deskripsi Lapangan</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">maksimal 300 kata</span>
            </div>
            <div class="col-md-8 col-12 mt-2 mt-md-0">
                <textarea id="myTextarea" class="form-control" name="deskripsi" rows="4" cols="50" onkeyup="updateCount()" placeholder="Masukkan Deskripsi Lapangan Olahraga">{{ old('deskripsi') ?? $lapangan->first()->deskripsi_lapangan }}</textarea>
                <p id="charCount">0/500</p>
            </div>
        </div>
        @php
            $array = explode("x", $lapangan->first()->luas_lapangan);
        @endphp
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Luas Lapangan</h6>
            </div>
            <div class="col-md-3 col-12 mt-2 col-auto">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="panjang" name="panjang" placeholder="Panjang" value="{{old('panjang') ?? $array[0]}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">m</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 mt-2">
                <div class="input-group mb-2">
                    <input type="number" class="form-control" min="0" id="lebar" name="lebar" placeholder="Lebar" value="{{old('lebar') ?? $array[1]}}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">m</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Harga Sewa Lapangan</h6>
                <span class="ml-2 ms-2" style="font-size: 15px">harga sewa per jam</span>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0 d-flex align-items-center">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Rp</div>
                    </div>
                    {{-- <input type="number" class="form-control" min="0" name="harga" placeholder="Masukkan Harga Sewa Olahraga" oninput="formatNumber(this)" value="{{old('harga') ?? $lapangan->first()->harga_sewa_lapangan}}"> --}}
                    <!-- Input yang terlihat oleh pengguna -->
                    <input type="text" class="form-control" id="sewaDisplay" placeholder="Masukkan Harga Sewa Lapangan Olahraga" oninput="formatNumber(this)" value="{{number_format(old('harga') ?? $lapangan->first()->harga_sewa_lapangan, 0, ',', '.')}}">

                    <!-- Input tersembunyi untuk kirim ke server -->
                    <input type="hidden" name="harga" id="sewaActual" value="{{old('harga') ?? $lapangan->first()->harga_sewa_lapangan}}">
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-md-3 col-12 mt-2">
                <h6>Status Lapangan</h6>
            </div>
            @if ($lapangan->first()->status_lapangan == "Aktif")
                <div class="col-md-8 col-12 mt-3 mt-md-0 d-flex align-items-center">
                    <svg id="toggleSwitch" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16" style="color: #007466">
                        <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    </svg>
                    <span id="toggleLabel" class="ml-2 ms-2">Aktif</span>
                    <input type="hidden" id="statusInput" name="status" value="Aktif">
                </div>
            @else
                <div class="col-md-8 col-12 mt-3 mt-md-0 d-flex align-items-center">
                    <svg id="toggleSwitch" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16" style="color: #007466">
                        <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
                    </svg>
                    <span id="toggleLabel" class="ml-2 ms-2">Non Aktif</span>
                    <input type="hidden" id="statusInput" name="status" value="Non Aktif">
                </div>
            @endif
            <input type="hidden" id="" name="id" value="{{$lapangan->first()->id_lapangan}}">
        </div>
        <input type="hidden" name="pemilik" value="{{Session::get("dataRole")->id_tempat}}">
        <hr>
        <h4>Atur Jam Operasional Lapangan</h4>
        <div id="inputContainer" class="mb-5 mt-3">
            @if (!$slot->isEmpty())
                @foreach ($slot as $item)
                    <input type="hidden" name="id_slot{{$loop->iteration}}" value="{{$item->id_slot}}">
                    <div class="row mb-3">
                        <div class="col">
                            <select id="hari{{$loop->iteration}}" name="hari{{$loop->iteration}}" class="form-select">
                                <option value="" disabled selected>Masukkan Hari</option>
                                <option value="Senin" {{ old('hari'.$loop->iteration) ?? $item->hari == "Senin" ? 'selected' : '' }}>Senin</option>
                                <option value="Selasa" {{ old('hari'.$loop->iteration) ?? $item->hari == "Selasa" ? 'selected' : '' }}>Selasa</option>
                                <option value="Rabu" {{ old('hari'.$loop->iteration) ?? $item->hari == "Rabu" ? 'selected' : '' }}>Rabu</option>
                                <option value="Kamis" {{ old('hari'.$loop->iteration) ?? $item->hari == "Kamis" ? 'selected' : '' }}>Kamis</option>
                                <option value="Jumat" {{ old('hari'.$loop->iteration) ?? $item->hari == "Jumat" ? 'selected' : '' }}>Jumat</option>
                                <option value="Sabtu" {{ old('hari'.$loop->iteration) ?? $item->hari == "Sabtu" ? 'selected' : '' }}>Sabtu</option>
                                <option value="Minggu" {{ old('hari'.$loop->iteration) ?? $item->hari == "Minggu" ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>
                        <div class="col">
                            <select id="buka{{$loop->iteration}}" name="buka{{$loop->iteration}}" class="form-select">
                                <option value="" disabled selected>Masukkan Jam Buka</option>
                                <option value="01:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "01:00:00" ? 'selected' : '' }}>01:00</option>
                                <option value="02:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "02:00:00" ? 'selected' : '' }}>02:00</option>
                                <option value="03:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "03:00:00" ? 'selected' : '' }}>03:00</option>
                                <option value="04:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "04:00:00" ? 'selected' : '' }}>04:00</option>
                                <option value="05:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "05:00:00" ? 'selected' : '' }}>05:00</option>
                                <option value="06:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "06:00:00" ? 'selected' : '' }}>06:00</option>
                                <option value="07:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "07:00:00" ? 'selected' : '' }}>07:00</option>
                                <option value="08:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "08:00:00" ? 'selected' : '' }}>08:00</option>
                                <option value="09:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "09:00:00" ? 'selected' : '' }}>09:00</option>
                                <option value="10:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "10:00:00" ? 'selected' : '' }}>10:00</option>
                                <option value="11:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "11:00:00" ? 'selected' : '' }}>11:00</option>
                                <option value="12:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "12:00:00" ? 'selected' : '' }}>12:00</option>
                                <option value="13:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "13:00:00" ? 'selected' : '' }}>13:00</option>
                                <option value="14:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "14:00:00" ? 'selected' : '' }}>14:00</option>
                                <option value="15:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "15:00:00" ? 'selected' : '' }}>15:00</option>
                                <option value="16:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "16:00:00" ? 'selected' : '' }}>16:00</option>
                                <option value="17:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "17:00:00" ? 'selected' : '' }}>17:00</option>
                                <option value="18:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "18:00:00" ? 'selected' : '' }}>18:00</option>
                                <option value="19:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "19:00:00" ? 'selected' : '' }}>19:00</option>
                                <option value="20:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "20:00:00" ? 'selected' : '' }}>20:00</option>
                                <option value="21:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "21:00:00" ? 'selected' : '' }}>21:00</option>
                                <option value="22:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "22:00:00" ? 'selected' : '' }}>22:00</option>
                                <option value="23:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "23:00:00" ? 'selected' : '' }}>23:00</option>
                                <option value="24:00" {{ old('buka'.$loop->iteration) ?? $item->jam_buka == "24:00:00" ? 'selected' : '' }}>24:00</option>
                            </select>
                        </div>
                        <div class="col">
                            <select id="tutup{{$loop->iteration}}" name="tutup{{$loop->iteration}}" class="form-select">
                                <option value="" disabled selected>Masukkan Jam Tutup</option>
                                <option value="01:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "01:00:00" ? 'selected' : '' }}>01:00</option>
                                <option value="02:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "02:00:00" ? 'selected' : '' }}>02:00</option>
                                <option value="03:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "03:00:00" ? 'selected' : '' }}>03:00</option>
                                <option value="04:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "04:00:00" ? 'selected' : '' }}>04:00</option>
                                <option value="05:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "05:00:00" ? 'selected' : '' }}>05:00</option>
                                <option value="06:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "06:00:00" ? 'selected' : '' }}>06:00</option>
                                <option value="07:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "07:00:00" ? 'selected' : '' }}>07:00</option>
                                <option value="08:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "08:00:00" ? 'selected' : '' }}>08:00</option>
                                <option value="09:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "09:00:00" ? 'selected' : '' }}>09:00</option>
                                <option value="10:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "10:00:00" ? 'selected' : '' }}>10:00</option>
                                <option value="11:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "11:00:00" ? 'selected' : '' }}>11:00</option>
                                <option value="12:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "12:00:00" ? 'selected' : '' }}>12:00</option>
                                <option value="13:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "13:00:00" ? 'selected' : '' }}>13:00</option>
                                <option value="14:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "14:00:00" ? 'selected' : '' }}>14:00</option>
                                <option value="15:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "15:00:00" ? 'selected' : '' }}>15:00</option>
                                <option value="16:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "16:00:00" ? 'selected' : '' }}>16:00</option>
                                <option value="17:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "17:00:00" ? 'selected' : '' }}>17:00</option>
                                <option value="18:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "18:00:00" ? 'selected' : '' }}>18:00</option>
                                <option value="19:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "19:00:00" ? 'selected' : '' }}>19:00</option>
                                <option value="20:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "20:00:00" ? 'selected' : '' }}>20:00</option>
                                <option value="21:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "21:00:00" ? 'selected' : '' }}>21:00</option>
                                <option value="22:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "22:00:00" ? 'selected' : '' }}>22:00</option>
                                <option value="23:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "23:00:00" ? 'selected' : '' }}>23:00</option>
                                <option value="24:00" {{ old('tutup'.$loop->iteration) ?? $item->jam_tutup == "24:00:00" ? 'selected' : '' }}>24:00</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-primary mb-3" onclick="addTimeInput()">Add</button>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>
</div>
<script>
    
    function formatNumber(input) {
        let value = input.value;
        value = value.replace(/\D/g, '');
        let numberValue = parseInt(value, 10);
        
        if (!isNaN(numberValue)) {
            // Update input yang terlihat oleh pengguna dengan format yang sudah diformat
            input.value = numberValue.toLocaleString('id-ID');
            // Update input tersembunyi dengan angka murni
            document.getElementById('sewaActual').value = numberValue;
        } else {
            input.value = '';
            document.getElementById('sewaActual').value = '';
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
            // Potong teks untuk membatasi hanya 300 karakter
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
    let counter = <?php echo count($slot); ?> + 1;
    function addTimeInput() {
        const container = document.getElementById('inputContainer');
        
        const row = document.createElement('div');
        row.className = 'row mb-3';

        const colHari = document.createElement('div');
        colHari.className = 'col';
        colHari.innerHTML = `
        <input type="hidden" name="id_slot${counter}" value="${counter}">
            <select id="hari${counter}" name="hari${counter}" class="form-select">
                <option value="" disabled selected>Masukkan Hari</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
            </select>
        `;

        const colBuka = document.createElement('div');
        colBuka.className = 'col';
        colBuka.innerHTML = `
        <select id="buka${counter}" name="buka${counter}" class="form-select">
            <option value="" disabled selected>Masukkan Jam Buka</option>
            <option value="01:00">01:00</option>
            <option value="02:00">02:00</option>
            <option value="03:00">03:00</option>
            <option value="04:00">04:00</option>
            <option value="05:00">05:00</option>
            <option value="06:00">06:00</option>
            <option value="07:00">07:00</option>
            <option value="08:00">08:00</option>
            <option value="09:00">09:00</option>
            <option value="10:00">10:00</option>
            <option value="11:00">11:00</option>
            <option value="12:00">12:00</option>
            <option value="13:00">13:00</option>
            <option value="14:00">14:00</option>
            <option value="15:00">15:00</option>
            <option value="16:00">16:00</option>
            <option value="17:00">17:00</option>
            <option value="18:00">18:00</option>
            <option value="19:00">19:00</option>
            <option value="20:00">20:00</option>
            <option value="21:00">21:00</option>
            <option value="22:00">22:00</option>
            <option value="23:00">23:00</option>
            <option value="24:00">24:00</option>
        </select>
        `;

        const colTutup = document.createElement('div');
        colTutup.className = 'col';
        colTutup.innerHTML = `
        <select id="tutup${counter}" name="tutup${counter}" class="form-select">
            <option value="" disabled selected>Masukkan Jam Tutup</option>
            <option value="01:00">01:00</option>
            <option value="02:00">02:00</option>
            <option value="03:00">03:00</option>
            <option value="04:00">04:00</option>
            <option value="05:00">05:00</option>
            <option value="06:00">06:00</option>
            <option value="07:00">07:00</option>
            <option value="08:00">08:00</option>
            <option value="09:00">09:00</option>
            <option value="10:00">10:00</option>
            <option value="11:00">11:00</option>
            <option value="12:00">12:00</option>
            <option value="13:00">13:00</option>
            <option value="14:00">14:00</option>
            <option value="15:00">15:00</option>
            <option value="16:00">16:00</option>
            <option value="17:00">17:00</option>
            <option value="18:00">18:00</option>
            <option value="19:00">19:00</option>
            <option value="20:00">20:00</option>
            <option value="21:00">21:00</option>
            <option value="22:00">22:00</option>
            <option value="23:00">23:00</option>
            <option value="24:00">24:00</option>
        </select>
        `;

        row.appendChild(colHari);
        row.appendChild(colBuka);
        row.appendChild(colTutup);
        container.appendChild(row);

        counter++;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection