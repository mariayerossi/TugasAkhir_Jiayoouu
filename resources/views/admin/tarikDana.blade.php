@extends('layouts.sidebar_admin')

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-5">Permintaan Penarikan Dana</h2>
    @include("layouts.message")
    <div class="mt-5">
        <div class="card mt-3 mb-5 p-3">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal Penarikan</th>
                            <th>Nama Akun</th>
                            <th>Bank</th>
                            <th>Nomer Rekening</th>
                            <th>Total Penarikan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$tarik->isEmpty())
                            @foreach ($tarik as $item)
                                <tr>
                                    @php
                                        $tanggalAwal2 = $item->tanggal_tarik;
                                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                                        $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
                                        $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');
                                    @endphp
                                    <td>{{$tanggalBaru2}}</td>
                                    @if ($item->fk_id_pemilik != null)
                                        <td>{{$item->nama_rek_pemilik}}</td>
                                        <td>{{$item->nama_bank_pemilik}}</td>
                                        <td>{{$item->norek_pemilik}}</td>
                                        <td>Rp {{number_format($item->total_tarik, 0, ',', '.')}}</td>
                                        <td>
                                            <a href="/admin/saldo/terimaTarik/{{$item->id_tarik}}" class="btn btn-success confirm-btn" data-id="{{$item->id_tarik}}"><i class="bi bi-check2"></i></a>
                                            <a href="/admin/saldo/tolakTarik/{{$item->id_tarik}}" class="btn btn-danger reject-btn" data-id="{{$item->id_tarik}}"><i class="bi bi-x-lg"></i></a>
                                        </td>
                                    @elseif ($item->fk_id_tempat != null)
                                        <td>{{$item->nama_rek_tempat}}</td>
                                        <td>{{$item->nama_bank_tempat}}</td>
                                        <td>{{$item->norek_tempat}}</td>
                                        <td>Rp {{number_format($item->total_tarik, 0, ',', '.')}}</td>
                                        <td>
                                            <a href="/admin/saldo/terimaTarik/{{$item->id_tarik}}" class="btn btn-success confirm-btn" data-id="{{$item->id_tarik}}"><i class="bi bi-check2"></i></a>
                                            <a href="/admin/saldo/tolakTarik/{{$item->id_tarik}}" class="btn btn-danger reject-btn" data-id="{{$item->id_tarik}}"><i class="bi bi-x-lg"></i></a>
                                        </td>
                                    @endif
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
                                <td colspan="6" class="text-center">Tidak Ada Data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.table').DataTable();
    });
    $('.confirm-btn').on('click', function(e) {
        e.preventDefault();
        var tarikId = $(this).attr('data-id');
        // console.log(tarikId);
        
        var formData = {
            _token: '{{ csrf_token() }}', // Laravel CSRF token
            tarikId: tarikId
        };
        
        $.ajax({
            url: '/admin/saldo/terimaTarik/' + tarikId,
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
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle error jika diperlukan
                swal("Error!", "Gagal mengirim rating!", "error");
            }
        });
    });

    // Fungsi untuk menangani penolakan
    $('.reject-btn').on('click', function(e) {
        e.preventDefault();
        var tarikId = $(this).data('id');

        var formData = {
            _token: '{{ csrf_token() }}', // Laravel CSRF token
            tarikId: tarikId
        };

        $.ajax({
            url: '/admin/saldo/tolakTarik/' + tarikId,
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
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle error jika diperlukan
                swal("Error!", "Gagal mengirim rating!", "error");
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endsection