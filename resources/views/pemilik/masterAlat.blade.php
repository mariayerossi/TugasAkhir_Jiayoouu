@extends('layouts.sidebarNavbar_pemilik')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-5">Tambah Alat Olahraga</h3>
    @include("layouts.message")
    
    <form action="" method="post">
        @csrf
        <h6>Nama Alat Olahraga :</h6>
    </form>
</div>
@endsection