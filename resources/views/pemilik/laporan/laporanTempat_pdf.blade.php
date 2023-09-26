<!DOCTYPE html>
<html>
<head>
	<title>Laporan Tempat Olahraga</title>
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
		<h2>Laporan Tempat Olahraga</h2>
	</center>

	<h4><b>Total Tempat: {{$data->count()}}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                <th>Nama Tempat</th>
                <th>Total Alat Disewakan</th>
                <th>Total Komisi</th>
            </tr>
		</thead>
		<tbody>
			@if (!$data->isEmpty())
				@foreach ($data as $item)
					<tr>
						<td>{{$loop->iteration}}</td>
						<td>{{$item->nama_tempat}}</td>
						<td>{{$item->jumlah}}</td>
						<td>Rp {{ number_format($item->total_komisi, 0, ',', '.') }}</td>
					</tr>
				@endforeach
			@else
                <tr>
                    <td colspan="4" class="text-center">Tidak Ada Data</td>
                </tr>
			@endif
		</tbody>
	</table>
 
</body>
</html>