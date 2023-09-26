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
                <th>Harga Sewa</th>
                <th>Durasi</th>
                <th>Tanggal Sewa</th>
                <th>Subtotal</th>
			</tr>
		</thead>
		<tbody>
            @if (!$data->isEmpty())
                @foreach($data as $item)
                    @php
                        // $dataFiles = DB::table('files_alat')->where("fk_id_alat","=",$item->id_alat)->get()->first();
                        // $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                        // $dataHtrans = DB::table('htrans')->where("id_htrans","=",$item->fk_id_htrans)->get()->first();

                        $tanggalAwal2 = $item->tanggal_sewa;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
                    @endphp
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        {{-- <td>
                            <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataFiles->nama_file_alat) }}" alt="">
                            </div>
                        </td> --}}
                        <td>{{$item->nama_alat}}</td>
                        <td>Rp {{ number_format($item->harga_sewa_alat, 0, ',', '.') }}</td>
                        <td>{{$item->durasi_sewa}} jam</td>
                        <td>{{$tanggalBaru2}}</td>
                        <td>Rp {{ number_format($item->subtotal_alat, 0, ',', '.') }}</td>
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