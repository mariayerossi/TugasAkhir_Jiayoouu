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
    <h2 class="text-center mb-5">Daftar Pihak Tempat Olahraga</h2>
    <div class="card mb-5 mt-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Tempat</th>
                        <th>Nama Pemilik</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Foto KTP</th>
                        <th>Foto NPWP</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$tempat->isEmpty())
                        @foreach ($tempat as $item)
                            <tr>
                                <td>{{$item->nama_tempat}}</td>
                                <td>{{$item->nama_pemilik_tempat}}</td>
                                <td>{{$item->email_tempat}}</td>
                                <td>{{$item->telepon_tempat}}</td>
                                <td>{{$item->alamat_tempat}}</td>
                                <td><img onclick="showImage('{{ asset('upload/'.$item->ktp_tempat) }}')" style="cursor: zoom-in;" class="img-ratio-16-9" src="{{ asset('upload/' . $item->ktp_tempat) }}" alt=""></td>
                                <td><img onclick="showImage('{{ asset('upload/'.$item->npwp_tempat) }}')" style="cursor: zoom-in;" class="img-ratio-16-9" src="{{ asset('upload/' . $item->npwp_tempat) }}" alt=""></td>
                            </tr>
                        @endforeach
                        {{-- fitur show image --}}
                        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-body">
                                <img src="" id="modalImage" class="img-fluid">
                                </div>
                            </div>
                            </div>
                        </div>
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function showImage(imgPath) {
        document.getElementById('modalImage').src = imgPath;
        $('#imageModal').modal('show');
    }
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection