{{-- opsi pertama --}}

{{-- @php
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
@endif --}}

{{-- --------------------------------------------------------------------------------------------- --}}

{{-- opsi kedua --}}

@php
    $msg = "";
    $status_class = "";

    if (session()->has("success")) {
        $status_class = "success";
        $msg = session()->get("success");
        session()->forget("success");
    }

    if (session()->has("error")) {
        $status_class = "error"; // Update to match SweetAlert class
        $msg = session()->get("error");
        session()->forget("error");
    }
@endphp

@if ($msg != "")
    <script>
        // Display SweetAlert for success or error with "OK" button
        swal({
            type: '{{ $status_class }}',
            title: '{{ $msg }}',
            buttons: {
                confirm: {
                    text: 'OK',
                    value: true,
                    visible: true,
                    className: 'swal-button',
                    closeModal: true,
                }
            },
        }, function(isConfirm) {
                if (isConfirm) {
                    //HAPUS SESSION NYA
                    $.ajax({
                        type: 'POST',
                        url: '/hapusMsg',
                        data: {
                            "_token": '{{ csrf_token() }}',
                        },
                        success: function (data) {
                            console.log("Session message deleted");
                            window.location.reload();
                        },
                        error: function (data) {
                            console.error("Error deleting session message");
                        }
                    });
                }
            });
    </script>
@endif

{{-- @if ($errors->any())
    <script>
        // Display SweetAlert for validation errors with "Yes" and "No" buttons
        swal({
            icon: 'error',
            title: 'Validation Error!',
            html: `<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            buttons: {
                confirm: {
                    text: 'Yes',
                    value: true,
                    visible: true,
                    className: 'swal-button',
                    closeModal: true,
                },
                cancel: {
                    text: 'No',
                    value: false,
                    visible: true,
                    className: 'swal-button',
                    closeModal: true,
                }
            },
        });
    </script>
@endif --}}