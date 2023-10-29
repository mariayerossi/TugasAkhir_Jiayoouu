<!DOCTYPE html>
<html>
<head>
	<title>Laporan Alat Olahraga yang Disewakan</title>
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
		<h2>Laporan Alat Olahraga yang Disewakan</h2>
	</center>

	<h4><b>Total Alat: {{$data->count()}}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah Disewakan</th>
                <th>Total Komisi (/jam)</th>
                <th>Total Durasi Sewa</th>
                <th>Total Pendapatan</th>
            </tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->total_sewa}} kali</td>
                        <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                        <td>{{$item->total_durasi + $item->durasi_extend}} jam</td>
                        <td>Rp {{ number_format($item->total_pendapatan + $item->komisi_extend, 0, ',', '.') }}</td>
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