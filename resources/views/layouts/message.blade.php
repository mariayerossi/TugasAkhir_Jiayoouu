@php
$msg = "";
if (session()->has("success"))
{
    $status_class = "success";
    $msg = session()->get("success");
}
if (session()->has("error"))
{
    $status_class = "danger";
    $msg = session()->get("error");
}
@endphp
@if ($msg != "")
<div class="alert alert-{{$status_class}}
d-flex align-items-center" role="alert">
<div>
    {{$msg}}
</div>
</div>
@endif

{{-- Ini untuk Any Error! --}}
@if ($errors->any())
<div class="alert alert-danger
d-flex align-items-center" role="alert">
<div>
<ul>
@foreach ($errors->all() as $e)
    <li>{{ $e }}</li>
@endforeach
</ul>
</div>
</div>
@endif
