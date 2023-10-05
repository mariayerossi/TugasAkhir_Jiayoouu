@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<form action="/pemilik/saldo/tarikDana" method="post">
    @csrf
    <input type="number" class="form-control" name="jumlah" id="">
    <button type="submit" class="btn btn-success">Tarik</button>
</form>
@endsection