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
        h4 {
            margin-top: 50px
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

	<h4><b>Total Pendapatan: Rp {{ number_format(($data->sum('subtotal_lapangan') + $data->sum('total_komisi') - $data->sum("pendapatan_website_lapangan")) + ($data->sum("subtotal_ext") + $data->sum('komisi_extend') - $data->sum("pendapatan_ext")), 0, ',', '.') }}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                {{-- <th>Foto</th> --}}
                <th>Kode Transaksi</th>
                <th>Tanggal Transaksi</th>
                <th>Nama Lapangan</th>
                <th>Subtotal Lapangan</th>
                <th>Jumlah Alat Disewakan</th>
                <th>Total Komisi Alat</th>
                <th>Total Pendapatan Kotor(sebelum biaya aplikasi)</th>
                <th>Total Pendapatan Bersih</th>
            </tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    <tr>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->kode_trans}}</td>
                        @php
                            $tanggalAwal2 = $item->tanggal_trans;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>Rp {{ number_format($item->subtotal_lapangan + $item->subtotal_ext, 0, ',', '.') }}</td>
                        <td>{{$item->alat}}</td>
                        <td>Rp {{ number_format($item->total_komisi + $item->komisi_extend, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_komisi + $item->subtotal_lapangan + $item->komisi_extend + $item->subtotal_ext, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format(($item->total_komisi + $item->subtotal_lapangan - $item->pendapatan_website_lapangan) + ($item->komisi_extend + $item->subtotal_ext - $item->pendapatan_ext), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
		</tbody>
	</table>
 
</body>
</html>