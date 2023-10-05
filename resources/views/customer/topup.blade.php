@extends('layouts.navbar_customer')

@section('content')
<form action="/customer/saldo/topup" method="post">
    @csrf
    <input type="number" name="jumlah" class="form-control" id="">
    <button type="submit" class="btn btn-success" id="pay-button">Top Up</button>
</form>
@endsection