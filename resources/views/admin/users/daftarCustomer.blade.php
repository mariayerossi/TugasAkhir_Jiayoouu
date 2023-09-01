@extends('layouts.sidebar_admin')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-5">Daftar Customer</h2>
    <div class="mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                </tr>
            </thead>
            <tbody>
                @if (!$customer->isEmpty())
                    @foreach ($customer as $item)
                        <tr>
                            <td>{{$item->nama_user}}</td>
                            <td>{{$item->email_user}}</td>
                            <td>{{$item->telepon_user}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">Tidak Ada Data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection