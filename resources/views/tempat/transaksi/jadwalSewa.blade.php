@extends('layouts.sidebarNavbar_tempat')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-5">Jadwal Transaksi Hari Ini</h3>

    <div class="mb-5">
        @include("layouts.message")
        <div class="mb-3">
            <form action="/tempat/transaksi/fiturJadwal" method="get" class="d-flex flex-column flex-md-row align-items-center">
                @csrf
                <h5 class="mb-2 mb-md-0 me-md-5">Tampilkan Jadwal dari</h5>
                <!-- Input date untuk tanggal mulai -->
                <div class="form-group d-flex flex-column flex-md-row mr-3 mb-2 mb-md-0">
                    <select class="form-select" name="lapangan">
                        @if (!$lapangan->isEmpty())
                            @foreach ($lapangan as $item)
                            <option value="{{$item->id_lapangan}}" {{$fitur == $item->id_lapangan ? 'selected' : '' }}>{{$item->nama_lapangan}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
    
                <div class="mt-2 mt-md-0 ms-3">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3 mb-5 p-3">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Nama Customer</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$trans->isEmpty())
                        @foreach ($trans as $item)
                            <tr>
                                @if ($item->status_trans == "Berlangsung")
                                    <td style="background-color: yellow">{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} WIB</td>
                                    <td style="background-color: yellow">{{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i') }} WIB</td>
                                    <td style="background-color: yellow">{{$item->nama_user}}</td>
                                    <td style="background-color: yellow">{{$item->status_trans}}</td>
                                    <td style="background-color: yellow"><a class="btn btn-outline-success" href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}">Lihat Detail</a></td>
                                @else
                                    <td>{{ \Carbon\Carbon::parse($item->jam_sewa)->format('H:i') }} WIB</td>
                                    <td>{{ \Carbon\Carbon::parse($item->jam_sewa)->addHours($item->durasi_sewa)->format('H:i') }} WIB</td>
                                    <td>{{$item->nama_user}}</td>
                                    <td>{{$item->status_trans}}</td>
                                    <td><a class="btn btn-outline-success" href="/tempat/transaksi/detailTransaksi/{{$item->id_htrans}}">Lihat Detail</a></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection