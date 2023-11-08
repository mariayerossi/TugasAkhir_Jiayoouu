@extends('layouts.sidebar_admin')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@section('content')
<style>
    .img-ratio-16-9 {
        width: 150px;
        height: 84.375px;
        object-fit: cover;
    }

</style>
<div class="container mt-5">
    <h2 class="text-center mb-5">Daftar Pemilik Alat</h2>
    <div class="card mb-5 p-3">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Foto KTP</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$pemilik->isEmpty())
                        @foreach ($pemilik as $item)
                            <tr>
                                <td>{{$item->nama_pemilik}}</td>
                                <td>{{$item->email_pemilik}}</td>
                                <td>{{$item->telepon_pemilik}}</td>
                                <td><a href="{{ asset('upload/' . $item->ktp_pemilik) }}"><img class="img-ratio-16-9" src="{{ asset('upload/' . $item->ktp_pemilik) }}" alt=""></a></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection