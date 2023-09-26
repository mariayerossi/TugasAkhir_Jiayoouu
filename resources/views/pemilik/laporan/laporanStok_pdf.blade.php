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
                <th>Komisi</th>
                <th>Disewakan</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
			</tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
			    @foreach ($data as $item)
                    @php
                        // $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->id_alat)->get()->first();
                        // $totalRequest = DB::table('dtrans')->where("fk_id_alat","=",$item->id_alat)->count();

                        $tanggalAwal2 = $item->created_at;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y H:i:s');
                    @endphp
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->nama_alat}}</td>
                        <td>{{$item->kategori_alat}}</td>
                        <td>Rp {{ number_format($item->komisi_alat, 0, ',', '.') }}</td>
                        <td>{{$item->totalRequest}} Kali</td>
                        @if ($item->status_alat == "Aktif")
                            <td style="color: green">{{$item->status_alat}}</td>
                        @else
                            <td style="color:red">{{$item->status_alat}}</td>
                        @endif
                        <td>{{$tanggalBaru2}}</td>
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