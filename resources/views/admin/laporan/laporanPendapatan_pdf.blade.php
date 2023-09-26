<!DOCTYPE html>
<html>
<head>
	<title>Laporan Pendapatan Website</title>
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
		<h2>Laporan Pendapatan Website</h2>
	</center>

	<h4><b>Rp {{ number_format($data->sum("pendapatan_lapangan") + $data->sum("pendapatan_alat"), 0, ',', '.') }}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Nama Lapangan</th>
                <th>Jumlah Alat Disewakan</th>
                <th>Tanggal Transaksi</th>
                <th>Pendapatan Website</th>
            </tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->kode_trans}}</td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>{{$item->jumlah_alat}}</td>
                        @php
                            $tanggalAwal2 = $item->tanggal_trans;
                            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                        @endphp
                        <td>{{$tanggalBaru2}}</td>
                        <td>Rp {{ number_format($item->pendapatan_lapangan + $item->pendapatan_alat, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
		</tbody>
	</table>
 
</body>
</html>