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
		{{-- <h2>Laporan {{$nama_alat}}</h2> --}}
	</center>

 
	<table class='table table-bordered'>
		<thead>
            <tr>
                <th>Waktu Sewa</th>
                <th>Total Sewa</th>
                <th>Total Pendapatan Kotor (sebelum biaya aplikasi)</th>
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
                            <td>Rp {{ number_format($totalKotor[$index], 0, ',', '.') }}</td>
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
 
</body>
</html>