@extends('layouts.sidebarNavbar_tempat')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@section('content')
<style>
    .square-image-container {
        width: 100px;
        height: 100px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .square-image-container img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    .chart-container {
        width: 70%;   /* Atur lebar sesuai keinginan */
        height: 400px; /* Atur tinggi sesuai keinginan */
        margin: 0 auto; /* opsional: untuk mengatur grafik agar berada di tengah */
    }
    @media (max-width: 768px) {  /* angka 768px adalah titik putus umum untuk tablet, Anda bisa menyesuaikannya */
        .chart-container {
            width: 100%;   /* Di mobile, sebaiknya gunakan 100% agar mengisi seluruh lebar */
            height: 250px; /* Tinggi yang lebih kecil untuk mobile */
        }
    }
</style>
<div class="container mt-5">
    <h3 class="text-center mb-5">Laporan Lapangan Olahraga</h3>
    <div class="d-flex justify-content-end mb-2">
        <h2><b>Total Lapangan: {{$lapangan->count()}}</b></h2>
    </div>
    <div class="d-flex justify-content-end mb-5">
        <a href="/tempat/laporan/lapangan/CetakPDF" class="btn btn-primary" target="_blank">Cetak PDF</a>
    </div>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jumlah Disewakan</th>
                        <th>Harga Sewa (/jam)</th>
                        <th>Total Durasi Sewa</th>
                        <th>Total Pendapatan Kotor (sebelum biaya aplikasi)</th>
                        <th>Total Pendapatan Bersih</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$lapangan->isEmpty())
                        @foreach ($lapangan as $item)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->nama_lapangan}}</td>
                                <td>{{$item->total_sewa}} kali</td>
                                <td>Rp {{ number_format($item->harga_sewa_lapangan, 0, ',', '.') }}</td>
                                <td>{{$item->total_durasi + $item->durasi_ext}} jam</td>
                                <td>Rp {{ number_format($item->total_pendapatan + $item->subtotal_ext, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format(($item->total_pendapatan + $item->subtotal_ext) * 0.91, 0, ',', '.') }}</td>
                                @if ($item->status_lapangan == "Aktif")
                                    <td style="color: green">{{$item->status_lapangan}}</td>
                                @else
                                    <td style="color:red">{{$item->status_lapangan}}</td>
                                @endif
                                <td><a href="/tempat/lapangan/lihatDetailLapangan/{{$item->id_lapangan}}" class="btn btn-outline-success">Lihat Detail</a></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    // $(document).ready(function() {
    //     var table = $('.table').DataTable();
    // });
    var ctx = document.getElementById('incomeChart').getContext('2d');
    var incomeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Jumlah Sewa',
                data: @json($monthlyIncome),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection