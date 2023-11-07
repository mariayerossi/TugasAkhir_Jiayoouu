@extends('layouts.sidebarNavbar_tempat')

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
    <h3 class="text-center mb-4">Laporan Alat Olahraga {{$alat->nama_alat}}</h3>
    @php
        $tanggalAwal = $mulai;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y');

        $tanggalAwal3 = $selesai;
        $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
        $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
    @endphp
    <h6 class="text-center mb-5">{{$tanggalBaru}} - {{$tanggalBaru3}}</h6>
    @if ($request != null)
        @php
            $tanggalAwal1 = $request->req_tanggal_mulai;
            $tanggalObjek1 = DateTime::createFromFormat('Y-m-d', $tanggalAwal1);
            $tanggalBaru1 = $tanggalObjek1->format('d-m-Y');
            
            $tanggalAwal2 = $request->req_tanggal_selesai;
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');
        @endphp
        <h6>Tanggal Mulai Sewa: {{$tanggalBaru1}}</h6>
        <h6>Tanggal Selesai Sewa: {{$tanggalBaru2}}</h6>
    @endif
    <form action="/tempat/laporan/disewakan/fiturPerAlat/{{$alat->id_alat}}" method="get">
        @csrf
        <div class="d-flex justify-content-end mb-5">
            <select class="form-control" name="filter" style="width: 200px;">
                <option value="1" selected>1 Bulan</option>
                <option value="2">2 Bulan</option>
                <option value="3">3 Bulan</option>
                <option value="5">5 Bulan</option>
                <option value="8">8 Bulan</option>
                <option value="10">10 Bulan</option>
                <option value="12">1 Tahun</option>
                <option value="24">2 Tahun</option>
                <option value="36">3 Tahun</option>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>

    {{-- grafik --}}
    <div class="mt-5 mb-5">
        <div class="chart-container">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    
    <div id="keterangan" class="text-center mt-3">
        @if ($increasePercentage > 0)
            Persewaan meningkat sebesar {{ $increasePercentage }}% dalam satu bulan ini.
        @elseif ($increasePercentage < 0)
            Persewaan menurun sebesar {{ abs($increasePercentage) }}% dalam satu bulan ini.
        @else
            Persewaan tetap stabil dalam satu bulan ini.
        @endif
    </div>
    
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Waktu Sewa</th>
                <th>Total Sewa</th>
                <th>Total Pendapatan Bersih</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($monthlyIncome) && count($monthlyIncome) > 0)
                @php
                    $previousIncome = null;
                @endphp
                @foreach($monthlyLabels as $index => $label)
                    @if(isset($monthlyIncome[$index]) && $monthlyIncome[$index] > 0) <!-- Check if the index exists and income is greater than 0 -->
                        <tr>
                            <td>{{ $label }}</td>
                            <td>{{ $monthlyIncome[$index] }}</td>
                            <td>Rp {{ number_format($total[$index], 0, ',', '.') }}</td>
                            <td>
                                @if($index >= 0)
                                    @php
                                        $currentIncome = $monthlyIncome[$index];
                                        $increase = $currentIncome - $previousIncome;
    
                                        if ($previousIncome === 0) {
                                            $percentage = 100;
                                        } elseif ($previousIncome === null) {
                                            $percentage = 0;
                                        } else {
                                            $percentage = ($increase / abs($previousIncome)) * 100;
                                        }
    
                                        $formattedPercentage = number_format($percentage, 2);
                                    @endphp
    
                                    @if ($increase > 0)
                                        <span class="text-success"><i class="bi bi-arrow-up"></i>+{{ $formattedPercentage }}%</span>
                                    @elseif ($increase < 0)
                                        <span class="text-danger"><i class="bi bi-arrow-down"></i>{{ $formattedPercentage }}%</span>
                                    @else
                                        <span>{{ $formattedPercentage }}%</span>
                                    @endif
    
                                    @php
                                        $previousIncome = $currentIncome;
                                    @endphp
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                    <!-- Handle the case where the index does not exist or income is 0 -->
                @endforeach
            @else
                <tr>
                    <td colspan="3">Tidak ada data yang tersedia.</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    
    <script>
        var ctx = document.getElementById('incomeChart').getContext('2d');
        var incomeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
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
@endsection