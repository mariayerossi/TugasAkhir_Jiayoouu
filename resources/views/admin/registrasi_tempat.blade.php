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
    <h2 class="text-center mb-5">Registrasi Tempat Olahraga</h2>
    @include("layouts.message")
    <div class="mt-5">
        <div class="card mt-3 mb-5 p-3">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Tempat Olahraga</th>
                            <th>Nama Pemilik</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Lokasi</th>
                            <th>Foto KTP</th>
                            <th>Foto NPWP</th>
                            <th>Konfirmasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$register->isEmpty())
                            @foreach ($register as $item)
                                <tr>
                                    <td>{{$item->nama_tempat_reg}}</td>
                                    <td>{{$item->nama_pemilik_tempat_reg}}</td>
                                    <td>{{$item->email_tempat_reg}}</td>
                                    <td>{{$item->telepon_tempat_reg}}</td>
                                    <td>{{$item->alamat_tempat_reg}}</td>
                                    <td><img onclick="showImage('{{ asset('upload/'.$item->ktp_tempat_reg) }}')" style="cursor: zoom-in;" class="img-ratio-16-9" src="{{ asset('upload/' . $item->ktp_tempat_reg) }}" alt=""></td>
                                    <td><img onclick="showImage('{{ asset('upload/'.$item->npwp_tempat_reg) }}')" style="cursor: zoom-in;" class="img-ratio-16-9" src="{{ asset('upload/' . $item->npwp_tempat_reg) }}" alt=""></td>
                                    <td>
                                        <a href="/admin/konfirmasiTempat/{{$item->id_register}}" class="btn btn-success confirm-btn" data-id="{{$item->id_register}}"><i class="bi bi-check2"></i></a>
                                        <a href="/admin/tolakKonfirmasiTempat/{{$item->id_register}}" class="btn btn-danger reject-btn" data-id="{{$item->id_register}}"><i class="bi bi-x-lg"></i></a>
                                    </td>
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
                                <td colspan="8" class="text-center">Tidak Ada Data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function showImage(imgPath) {
        document.getElementById('modalImage').src = imgPath;
        $('#imageModal').modal('show');
    }
    $(document).ready(function() {
        $('.table').DataTable();
    });
    $('.confirm-btn').on('click', function(e) {
        e.preventDefault();
        var registerId = $(this).data('id');
        
        var formData = {
            _token: '{{ csrf_token() }}', // Laravel CSRF token
            registerId: registerId
        };
        
        $.ajax({
            url: '/admin/konfirmasiTempat/' + registerId,
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Handle response jika diperlukan
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(error) {
                // Handle error jika diperlukan
                console.error(error);
            }
        });
    });

    // Fungsi untuk menangani penolakan
    $('.reject-btn').on('click', function(e) {
        e.preventDefault();
        var registerId = $(this).data('id');

        var formData = {
            _token: '{{ csrf_token() }}', // Laravel CSRF token
            registerId: registerId
        };

        $.ajax({
            url: '/admin/tolakKonfirmasiTempat/' + registerId,
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Handle response jika diperlukan
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(error) {
                // Handle error jika diperlukan
                console.error(error);
            }
        });
    });
</script>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection