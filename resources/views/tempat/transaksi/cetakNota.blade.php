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
    </style>
</head>

<body>
    <div class="header">
        <h2>Toko XYZ</h2>
        <p>Jalan ABC, Nomor 123</p>
    </div>

    <div class="content">
        <h3>Nota Transaksi</h3>
        <p>Tanggal: 12 Oktober 2023</p>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contoh item -->
                <tr>
                    <td>1</td>
                    <td>Pensil</td>
                    <td>Rp. 1.500</td>
                    <td>3</td>
                    <td>Rp. 4.500</td>
                </tr>
                <!-- Anda dapat menambahkan item lainnya di sini -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">Total Keseluruhan:</td>
                    <td>Rp. 4.500</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di Toko XYZ!
    </div>
</body>

</html>
