<!DOCTYPE html>
<html>
<head>
	<title>Laporan Lapangan Olahraga</title>
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
		<h2>Laporan Lapangan Olahraga</h2>
	</center>

	<h4><b>Total Lapangan: {{$data->count()}}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah Disewakan</th>
                <th>Harga Sewa (/jam)</th>
                <th>Total Durasi Sewa</th>
                <th>Total Pendapatan</th>
                <th>Status</th>
            </tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_lapangan}}</td>
                        <td>{{$item->total_sewa}} kali</td>
                        <td>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</td>
                        <td>{{$item->total_durasi + $item->durasi_ext}} jam</td>
                        <td>Rp {{ number_format($item->total_pendapatan + $item->subtotal_ext, 0, ',', '.') }}</td>
                        @if ($item->status_lapangan == "Aktif")
                            <td style="color: green">{{$item->status_lapangan}}</td>
                        @else
                            <td style="color:red">{{$item->status_lapangan}}</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
		</tbody>
	</table>
 
</body>
</html>