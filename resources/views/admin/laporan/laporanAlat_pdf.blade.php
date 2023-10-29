<!DOCTYPE html>
<html>
<head>
	<title>Laporan Alat Olahraga</title>
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
		<h2>Laporan Alat Olahraga</h2>
	</center>

	<h4><b>Total Alat: {{$data->count()}}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
                {{-- <th>Foto</th> --}}
                <th>Nama</th>
                <th>Harga Sewa</th>
                <th>Status</th>
                <th>Disewakan</th>
                <th>Pendapatan Website</th>
			</tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $item->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->nama_alat}}</td>
                        <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                        @if ($item->status_alat == "Aktif")
                            <td style="color: green">{{$item->status_alat}}</td>
                        @else
                            <td style="color:red">{{$item->status_alat}}</td>
                        @endif
                        <td>{{$item->total_sewa}} Kali</td>
                        <td>Rp {{ number_format($item->total_pendapatan + $item->pendapatan_ext, 0, ',', '.') }}</td>
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