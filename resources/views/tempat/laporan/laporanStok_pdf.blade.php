<!DOCTYPE html>
<html>
<head>
	<title>Laporan Stok Alat Olahraga</title>
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
		<h2>Laporan Stok Alat Olahraga</h2>
	</center>

	<h4><b>Total Alat: {{$data->count()}}</b></h4>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                {{-- <th>Foto</th> --}}
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga Sewa</th>
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
                        <td>{{$item->kategori_alat}}</td>
                        @if ($item->harga_permintaan != null)
                            <td>Rp {{ number_format($item->harga_permintaan, 0, ',', '.') }}</td>
                        @elseif ($item->harga_penawaran != null)
                            <td>Rp {{ number_format($item->harga_penawaran, 0, ',', '.') }}</td>
                        @else
                            <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
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