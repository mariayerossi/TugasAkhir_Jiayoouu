@extends('layouts.sidebarNavbar_pemilik')

@section('content')
@if (!$notif->isEmpty())
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
</div>
@else
<h1>Tidak ada notifikasi</h1>
@endif
@endsection