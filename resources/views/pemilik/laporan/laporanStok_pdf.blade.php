<!DOCTYPE html>
<html>
<head>
	<title>Laporan Stok Alat</title>
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
                <th>Nama</th>
                <th>Kategori</th>
                <th>Disewakan</th>
                <th>Status</th>
			</tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
			    @foreach ($data as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->kategori_alat}}</td>
                        <td>{{$item->totalRequest}} Kali</td>
                        @if ($item->status_alat == "Aktif")
                            <td style="color: green">{{$item->status_alat}}</td>
                        @else
                            <td style="color:red">{{$item->status_alat}}</td>
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