<!DOCTYPE html>
<html>
<head>
	<title>Laporan Pendapatan</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h2>Laporan Pendapatan</h2>
		@if ($tanggal_mulai != null && $tanggal_selesai != null)
			@php
				$tanggalAwal = $tanggal_mulai;
				$tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
				$tanggalBaru = $tanggalObjek->format('d-m-Y');

				$tanggalAwal3 = $tanggal_selesai;
				$tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
				$tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
			@endphp
			<h6 class="text-center mb-5">{{$tanggalBaru}} - {{$tanggalBaru3}}</h6>
		@endif
	</center>

	<h4><b>Total Pendapatan: Rp {{ number_format(($disewakan->sum('total_komisi_pemilik') - $disewakan->sum("pendapatan_website_alat")) + ($disewakan->sum('komisi_extend') - $disewakan->sum('pendapatan_extend')), 0, ',', '.') }}</b></h4>
 
	@if (!$data->isEmpty())
        {{-- Pertama-tama, kelompokkan data berdasarkan nama alat --}}
        @php
            $grouped = $data->groupBy('nama_alat');
        @endphp
        {{-- Iterasi untuk setiap grup alat olahraga --}}
        @foreach ($grouped as $nama_alat => $items)
            
            {{-- Tampilkan nama alat olahraga --}}
            <h4 class="mt-5"><b>{{ $nama_alat }}</b></h4>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <th>Komisi Alat (/jam)</th>
                        <th>Durasi</th>
                        <th>Pendapatan Kotor <br>(sebelum biaya aplikasi)</th>
                        <th>Pendapatan Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Iterasi untuk setiap item dalam grup alat olahraga --}}
                    @foreach ($items as $item)
                        <tr>
                            @php
                                $tanggalAwal2 = $item->tanggal_trans;
                                $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                                $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                            @endphp
                            <td>{{ $tanggalBaru2 }}</td>
                            <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                            <td>{{ $item->durasi_sewa + $item->durasi_extend }} jam</td>
                            <td>Rp {{ number_format($item->total_komisi_pemilik + $item->komisi_extend, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format(($item->total_komisi_pemilik - $item->pendapatan_website_alat) + ($item->komisi_extend - $item->pendapatan_extend), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endforeach

    @else
        <p class="text-center">Tidak Ada Data</p>
    @endif
 
</body>
</html>