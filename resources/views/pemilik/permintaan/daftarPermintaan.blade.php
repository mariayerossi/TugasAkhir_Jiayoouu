@extends('layouts.sidebarNavbar_pemilik')

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
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Usia</th>
            </tr>
        </thead>
        <tbody>
            @if (!$permintaan->isEmpty())
                @foreach ($permintaan as $item)
                    <tr>
                        <td></td>
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