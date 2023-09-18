@extends('layouts.sidebar_admin')

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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Tempat Olahraga</th>
                    <th>Nama Pemilik</th>
                    <th>Lokasi</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
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
                                <a href="/admin/konfirmasiTempat/{{$item->id_register}}" class="btn btn-primary">Terima</a>
                                <a href="/admin/tolakKonfirmasiTempat/{{$item->id_register}}" class="btn btn-danger">Tolak</a>
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
<script>
    function showImage(imgPath) {
        document.getElementById('modalImage').src = imgPath;
        $('#imageModal').modal('show');
    }
</script>
@endsection