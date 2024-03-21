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
    <h3 class="text-center mb-5">Atur Jam Lapangan Olahraga</h3>
    @include("layouts.message")
    <div style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.142);" class="p-5 mb-5">
        <h4>Atur Jam Operasional Lapangan</h4>
        <form action="/tempat/lapangan/editJam" method="post">
            @csrf
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
            <input type="hidden" id="" name="id" value="{{$lapangan->first()->id_lapangan}}">
            <div class="d-flex justify-content-end">
                <a href="javascript:history.back()" class="btn btn-outline-danger me-3">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
        <hr>
        <h4>Atur Jam Tutup</h4>
        <form action="/tempat/lapangan/editJamKhusus" method="post">
            @csrf
            <div id="inputContainer2" class="mb-5 mt-3">
                @if (!$jam->isEmpty())
                    @foreach ($jam as $item)
                        <input type="hidden" name="id_jam{{$loop->iteration}}" value="{{$item->id_jam}}">
                        <div class="row mb-3" id="{{$item->id_jam}}">
                            <div class="col">
                                <input type="date" class="form-control" id="tanggal{{$loop->iteration}}" name="tanggal{{$loop->iteration}}" value="{{$item->tanggal}}">
                            </div>
                            <div class="col">
                                <select id="mulai{{$loop->iteration}}" name="mulai{{$loop->iteration}}" class="form-select">
                                    <option value="" disabled selected>Masukkan Jam Mulai</option>
                                    <option value="01:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "01:00:00" ? 'selected' : '' }}>01:00</option>
                                    <option value="02:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "02:00:00" ? 'selected' : '' }}>02:00</option>
                                    <option value="03:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "03:00:00" ? 'selected' : '' }}>03:00</option>
                                    <option value="04:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "04:00:00" ? 'selected' : '' }}>04:00</option>
                                    <option value="05:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "05:00:00" ? 'selected' : '' }}>05:00</option>
                                    <option value="06:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "06:00:00" ? 'selected' : '' }}>06:00</option>
                                    <option value="07:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "07:00:00" ? 'selected' : '' }}>07:00</option>
                                    <option value="08:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "08:00:00" ? 'selected' : '' }}>08:00</option>
                                    <option value="09:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "09:00:00" ? 'selected' : '' }}>09:00</option>
                                    <option value="10:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "10:00:00" ? 'selected' : '' }}>10:00</option>
                                    <option value="11:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "11:00:00" ? 'selected' : '' }}>11:00</option>
                                    <option value="12:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "12:00:00" ? 'selected' : '' }}>12:00</option>
                                    <option value="13:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "13:00:00" ? 'selected' : '' }}>13:00</option>
                                    <option value="14:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "14:00:00" ? 'selected' : '' }}>14:00</option>
                                    <option value="15:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "15:00:00" ? 'selected' : '' }}>15:00</option>
                                    <option value="16:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "16:00:00" ? 'selected' : '' }}>16:00</option>
                                    <option value="17:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "17:00:00" ? 'selected' : '' }}>17:00</option>
                                    <option value="18:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "18:00:00" ? 'selected' : '' }}>18:00</option>
                                    <option value="19:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "19:00:00" ? 'selected' : '' }}>19:00</option>
                                    <option value="20:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "20:00:00" ? 'selected' : '' }}>20:00</option>
                                    <option value="21:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "21:00:00" ? 'selected' : '' }}>21:00</option>
                                    <option value="22:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "22:00:00" ? 'selected' : '' }}>22:00</option>
                                    <option value="23:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "23:00:00" ? 'selected' : '' }}>23:00</option>
                                    <option value="24:00" {{ old('mulai'.$loop->iteration) ?? $item->jam_mulai == "24:00:00" ? 'selected' : '' }}>24:00</option>
                                </select>
                            </div>
                            <div class="col">
                                <select id="selesai{{$loop->iteration}}" name="selesai{{$loop->iteration}}" class="form-select">
                                    <option value="" disabled selected>Masukkan Jam Selesai</option>
                                    <option value="01:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "01:00:00" ? 'selected' : '' }}>01:00</option>
                                    <option value="02:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "02:00:00" ? 'selected' : '' }}>02:00</option>
                                    <option value="03:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "03:00:00" ? 'selected' : '' }}>03:00</option>
                                    <option value="04:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "04:00:00" ? 'selected' : '' }}>04:00</option>
                                    <option value="05:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "05:00:00" ? 'selected' : '' }}>05:00</option>
                                    <option value="06:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "06:00:00" ? 'selected' : '' }}>06:00</option>
                                    <option value="07:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "07:00:00" ? 'selected' : '' }}>07:00</option>
                                    <option value="08:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "08:00:00" ? 'selected' : '' }}>08:00</option>
                                    <option value="09:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "09:00:00" ? 'selected' : '' }}>09:00</option>
                                    <option value="10:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "10:00:00" ? 'selected' : '' }}>10:00</option>
                                    <option value="11:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "11:00:00" ? 'selected' : '' }}>11:00</option>
                                    <option value="12:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "12:00:00" ? 'selected' : '' }}>12:00</option>
                                    <option value="13:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "13:00:00" ? 'selected' : '' }}>13:00</option>
                                    <option value="14:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "14:00:00" ? 'selected' : '' }}>14:00</option>
                                    <option value="15:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "15:00:00" ? 'selected' : '' }}>15:00</option>
                                    <option value="16:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "16:00:00" ? 'selected' : '' }}>16:00</option>
                                    <option value="17:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "17:00:00" ? 'selected' : '' }}>17:00</option>
                                    <option value="18:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "18:00:00" ? 'selected' : '' }}>18:00</option>
                                    <option value="19:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "19:00:00" ? 'selected' : '' }}>19:00</option>
                                    <option value="20:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "20:00:00" ? 'selected' : '' }}>20:00</option>
                                    <option value="21:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "21:00:00" ? 'selected' : '' }}>21:00</option>
                                    <option value="22:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "22:00:00" ? 'selected' : '' }}>22:00</option>
                                    <option value="23:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "23:00:00" ? 'selected' : '' }}>23:00</option>
                                    <option value="24:00" {{ old('selesai'.$loop->iteration) ?? $item->jam_selesai == "24:00:00" ? 'selected' : '' }}>24:00</option>
                                </select>
                            </div>
                            <div class="col text-center mt-2" onclick="close2({{$item->id_jam}})"><i class="bi bi-x-lg" style="cursor: pointer"></i></div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-primary mb-3" onclick="addTimeInput2()">Add</button>
            <input type="hidden" id="" name="id" value="{{$lapangan->first()->id_lapangan}}">
            <div class="d-flex justify-content-end">
                <a href="javascript:history.back()" class="btn btn-outline-danger me-3">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
    let counter = <?php echo count($slot); ?> + 1;
    function addTimeInput() {
        const container = document.getElementById('inputContainer');
        
        const row = document.createElement('div');
        row.className = 'row mb-3';

        const colHari = document.createElement('div');
        colHari.className = 'col';
        colHari.innerHTML = `
        <input type="hidden" name="id_slot${counter}" value="null">
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

    let counter2 = <?php echo count($jam); ?> + 1;
    function addTimeInput2() {
        const container = document.getElementById('inputContainer2');
        
        const row = document.createElement('div');
        row.className = 'row mb-3';
        row.id = counter2;

        const colHari = document.createElement('div');
        colHari.className = 'col';
        colHari.innerHTML = `
        <input type="hidden" name="id_jam${counter2}" value="null">
            <input type="date" class="form-control" id="tanggal${counter2}" name="tanggal${counter2}">
        `;

        const colBuka = document.createElement('div');
        colBuka.className = 'col';
        colBuka.innerHTML = `
        <select id="mulai${counter2}" name="mulai${counter2}" class="form-select">
            <option value="" disabled selected>Masukkan Jam Mulai</option>
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
        <select id="selesai${counter2}" name="selesai${counter2}" class="form-select">
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

        const colClose = document.createElement('div');
        colClose.className = 'col text-center mt-2';
        colClose.setAttribute('onclick', `close2(${counter2})`);
        colClose.innerHTML = `
        <i class="bi bi-x-lg"></i>
        `;

        row.appendChild(colHari);
        row.appendChild(colBuka);
        row.appendChild(colTutup);
        row.appendChild(colClose);
        container.appendChild(row);

        counter2++;
    }

    function close2(id) {
        // var row = document.querySelector(`.row[id="${id}"]`);
        // row.parentNode.removeChild(row);
        
        var formData = {
            _token: '{{ csrf_token() }}', // Laravel CSRF token
            id: id,
            tujuan: '/tempat/lapangan/hapusJam/' + id
        };

        // AJAX request to update the status
        $.ajax({
            url: '/tempat/lapangan/hapusJam/' + id, // Replace with your backend endpoint
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function (data) {
                window.location.reload();
            },
            error: function (error) {
                console.error('Error updating notification status:', error);
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection