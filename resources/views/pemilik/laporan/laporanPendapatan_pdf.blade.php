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
	</center>

	<h4><b>Total: Rp {{ number_format($data->sum('total_komisi_pemilik'), 0, ',', '.') }}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
				<th>Nama</th>
				<th>Komisi</th>
				<th>Durasi</th>
				<th>Tanggal Transaksi</th>
                <th>Pendapatan</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $item)
                @php
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                    $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();
                @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$dataAlat->nama_alat}}</td>
                    <td>Rp {{ number_format($dataAlat->komisi_alat, 0, ',', '.') }}</td>
                    <td>{{$dataHtrans->durasi_sewa}} jam</td>
                    @php
                        $tanggalAwal2 = $dataHtrans->tanggal_trans;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                    @endphp
                    <td>{{$tanggalBaru2}}</td>
                    <td>Rp {{ number_format($item->total_komisi_pemilik, 0, ',', '.') }}</td>
                </tr>
			@endforeach
		</tbody>
	</table>
 
</body>
</html>