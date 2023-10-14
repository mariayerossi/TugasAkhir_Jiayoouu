@extends('layouts.navbar_customer')

@section('content')
<style>
    body {
    background: #007466
}

.form-control:focus {
    box-shadow: none;
    border-color: #007466
}

.profile-button {
    background: #007466;
    box-shadow: none;
    border: none
}

.profile-button:hover {
    background: #00534b
}

.profile-button:focus {
    background: #007466;
    box-shadow: none
}

.profile-button:active {
    background: #007466;
    box-shadow: none
}

.back:hover {
    color: #007466;
    cursor: pointer
}

.labels {
    font-size: 11px
}

.add-experience:hover {
    background: #007466;
    color: #fff;
    cursor: pointer;
    border: solid 1px #007466
}
</style>
<div class="container rounded bg-white mt-5 mb-5">
    <div class="row">
        <div class="col-md-5 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <img class="rounded-circle mt-5" width="150px" src="{{asset('assets/img/user_icon2.png')}}">
                <span class="font-weight-bold">{{$cust->first()->nama_user}}</span>
                <span class="text-black-50">{{$cust->first()->email_user}}</span><span> </span></div>
        </div>
        <div class="col-md-7 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Edit Profil</h4>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12"><label class="labels">Nama</label><input type="text" class="form-control" placeholder="Masukkan nama" name="nama" value="{{old('nama') ?? $cust->first()->nama_user}}"></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12"><label class="labels">Nomer Telepon</label><input type="number" class="form-control" placeholder="Masukkan nomer telepon" name="telepon" value="{{old('telepon') ?? $cust->first()->telepon_user}}"></div>
                    <div class="col-md-12"><label class="labels">Alamat Email</label><input type="email" class="form-control" placeholder="Masukkan alamat email" name="email" value="{{old('email') ?? $cust->first()->email_user}}"></div>
                    <div class="col-md-12"><label class="labels">Password</label><input type="password" class="form-control" placeholder="Masukkan password" name="pass" value=""></div>
                    <div class="col-md-12"><label class="labels">Konfirmasi Pasword</label><input type="password" class="form-control" placeholder="konfirmasi password" name="confirm" value=""></div>
                </div>
                <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button">Simpan</button></div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
