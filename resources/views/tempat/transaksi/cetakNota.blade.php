<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }

        .header, .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
        }

        .content {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{$htrans->nama_tempat}}</h2>
        <p>{{$htrans->alamat_tempat}}</p>
    </div>

    <div class="content">
        <h3>Kode Transaksi: {{$htrans->kode_trans}}</h3>
        @php
            $tanggalAwal = $htrans->tanggal_sewa;
            $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
            $tanggalBaru = $tanggalObjek->format('d-m-Y');
        @endphp
        <p>Tanggal Sewa: {{$tanggalBaru}} {{ \Carbon\Carbon::parse($htrans->jam_sewa)->format('H:i:s') }} WIB - {{ \Carbon\Carbon::parse($htrans->jam_sewa)->addHours($htrans->durasi_sewa + $htrans->durasi_extend)->format('H:i:s') }} WIB</p>

        <table class="mb-5">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lapangan Olahraga</th>
                    <th>Harga Sewa (/jam)</th>
                    <th>Durasi</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contoh item -->
                <tr>
                    <td>1</td>
                    <td>{{$htrans->nama_lapangan}}</td>
                    <td>Rp {{number_format($htrans->harga_sewa_lapangan, 0, ',', '.')}}</td>
                    <td>{{$htrans->durasi_sewa + $htrans->durasi_extend}} jam</td>
                    <td>Rp {{number_format($htrans->subtotal_lapangan + $htrans->extend_subtotal, 0, ',', '.')}}</td>
                </tr>
                <!-- Anda dapat menambahkan item lainnya di sini -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><b>Total Keseluruhan:</b></td>
                    <td><b>Rp {{number_format($htrans->subtotal_lapangan + $htrans->extend_subtotal, 0, ',', '.')}}</b></td>
                </tr>
            </tfoot>
        </table>
        <hr>
        <table class="mt-5">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Alat Olahraga</th>
                    <th>Harga Sewa (/jam)</th>
                    <th>Durasi</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if (!$dtrans->isEmpty())
                    @foreach ($dtrans as $item)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$item->nama_alat}}</td>
                            <td>Rp {{number_format($item->harga_sewa_alat, 0, ',', '.')}}</td>
                            <td>{{$htrans->durasi_sewa + $htrans->durasi_extend}} jam</td>
                            <td>Rp {{number_format($item->subtotal_alat + $item->extend_subtotal, 0, ',', '.')}}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><b>Total Keseluruhan:</b></td>
                    <td><b>Rp {{number_format($htrans->subtotal_alat + $htrans->extend_alat, 0, ',', '.')}}</b></td>
                </tr>
            </tfoot>
        </table>
        <h3 class="text-right">Total Transaksi: Rp {{number_format($htrans->total_trans + $htrans->total, 0, ',', '.')}}</h3>
    </div>

    <div class="footer">
        <img src="{{asset('assets/img/logo.png')}}" alt="Logo Toko XYZ" width="15%">
        <p>Terima kasih telah menggunakan Sportiva!</p>
    </div>
</body>
<script type="text/javascript">
    window.print();
</script>
</html>
