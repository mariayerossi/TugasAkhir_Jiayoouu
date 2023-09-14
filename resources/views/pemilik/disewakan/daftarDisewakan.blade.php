@extends('layouts.sidebarNavbar_pemilik')

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

</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Daftar Alat Olahraga yang Disewakan</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Keterangan</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @if (!$disewakan->isEmpty())
                @foreach ($disewakan as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                    @endphp
                    <tr>
                        <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td>
                        @php
                            function getBulan($bulan) {
                                $namaBulan = array(
                                    '01' => 'Januari',
                                    '02' => 'Februari',
                                    '03' => 'Maret',
                                    '04' => 'April',
                                    '05' => 'Mei',
                                    '06' => 'Juni',
                                    '07' => 'Juli',
                                    '08' => 'Agustus',
                                    '09' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember',
                                );

                                return $namaBulan[$bulan];
                            }

                            $tanggalAwal2 = $dataHtrans->tanggal_sewa;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                            
                            // Pecah tanggal dan ganti bagian bulannya
                            $pecahTanggal = explode('-', $tanggalBaru2);
                            $pecahTanggal[1] = getBulan($pecahTanggal[1]);
                            $tanggalDenganNamaBulan = implode(' ', $pecahTanggal); 
                        @endphp
                        <td>{{$dataAlat->nama_alat}} disewa pada {{$tanggalDenganNamaBulan}} dengan durasi sewa {{$dataHtrans->durasi_sewa}} jam</td>
                        <td>Rp {{ number_format($item->total_komisi_pemilik, 0, ',', '.') }}</td>
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
@endsection