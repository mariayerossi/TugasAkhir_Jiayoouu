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
                {{-- <th>Foto</th> --}}
                <th>Nama</th>
                <th>Harga Sewa</th>
                <th>Status</th>
                <th>Disewakan</th>
                <th>Total Pendapatan</th>
            </tr>
		</thead>
		<tbody>
			@foreach($data as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                {{-- <td>
                    <div class="square-image-container">
                        <img src="{{ asset('upload/' . $item->nama_file_lapangan) }}" alt="">
                    </div>
                </td> --}}
                <td>{{$item->nama_lapangan}}</td>
                <td>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</td>
                @if ($item->status_lapangan == "Aktif")
                    <td style="color: green">{{$item->status_lapangan}}</td>
                @else
                    <td style="color:red">{{$item->status_lapangan}}</td>
                @endif
                <td>{{$item->total_sewa}} kali</td>
                <td>Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
			@endforeach
		</tbody>
	</table>
 
</body>
</html>