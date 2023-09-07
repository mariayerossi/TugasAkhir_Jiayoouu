@extends('layouts.sidebarNavbar_tempat')

@section('content')
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-5">Daftar Permintaan Alat</h3>
        </div>
    </div>
    <table class="table table-hover table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Foto Alat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if (!$permintaan->isEmpty())
                @foreach ($permintaan as $item)
                    @php
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->req_id_alat)->get()->first();
                    @endphp
                    <tr>
                        <td>
                            {{-- <div class="square-image-container">
                                <img src="{{ asset('upload/' . $dataAlat->foto_alat) }}" alt="">
                            </div> --}}
                        </td>
                        <td>Permintaan {{$dataAlat->nama_alat}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection