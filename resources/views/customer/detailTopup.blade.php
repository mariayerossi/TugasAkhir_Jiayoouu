@extends('layouts.navbar_customer')

<!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
<script type="text/javascript"
src="https://app.sandbox.midtrans.com/snap/snap.js"
data-client-key={{config("midtrans.client_key")}}></script>

@section('content')
<style>
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }

    .h-custom {
        height: calc(100% - 73px);
    }

    @media (max-width: 450px) {
        .h-custom {
            height: 100%;
        }
    }
</style>
<section class="vh-100">
    <div class="container-fluid h-custom">
        @include("layouts.message")
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="{{ asset('assets/img/topup2.png') }}" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form action="/customer/saldo/topup" method="post" id="afterPayment">
                    @csrf

                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0">Detail Top Up</p>
                    </div>

                    <!-- Amount input -->
                    <div class="form-outline mb-4">
                        <h4>Nominal: Rp {{number_format($isi["transaction_details"]["gross_amount"], 0, ',', '.')}}</h4>
                        @php
                            date_default_timezone_set("Asia/Jakarta");
                            $tanggal = date("d-m-Y H:i:s");
                            $tanggalObjek1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal);
                            $carbonDate1 = \Carbon\Carbon::parse($tanggalObjek1)->locale('id');
                            $tanggalBaru1 = $carbonDate1->isoFormat('D MMMM YYYY HH:mm');
                        @endphp
                        <h6 class="mt-4">Nama: {{Session::get("dataRole")->nama_user}}</h6>
                        <h6 class="mt-2">Tanggal Top Up: {{$tanggalBaru1}}</h6>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <input type="hidden" name="jumlah" value="{{$isi["transaction_details"]["gross_amount"]}}">
                        <button type="submit" class="btn btn-success" id="pay-button">Top Up Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#pay-button').click(function(e) {
            e.preventDefault();

            var formData = $('#afterPayment').serialize();

            $.ajax({
                type: "POST",
                url: "/customer/saldo/after_midtrans",
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Anda bisa menambahkan tindakan tambahan setelah berhasil seperti mengalihkan halaman, menampilkan pesan sukses, dll.
                },
                error: function(error) {
                    console.log(error);
                    // Tampilkan pesan kesalahan jika ada.
                }
            });
        });
    });
</script>

<script type="text/javascript">
    // For example trigger on button clicked, or any time you need
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        event.preventDefault();
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
      window.snap.pay('{{$snapToken}}', {
        onSuccess: function(result){
          /* You may add your own implementation here */
          document.getElementById('afterPayment').submit();
        //   alert("payment success!"); console.log(result);
            swal("Success!", "Pembayaran Berhasil!", "success");
        },
        onPending: function(result){
          /* You may add your own implementation here */
        //   alert("wating your payment!"); console.log(result);
          swal("Warning!", "Menunggu Pembayaran!", "warning");
        },
        onError: function(result){
          /* You may add your own implementation here */
        //   alert("payment failed!"); console.log(result);
          swal("Error!", "Pembayaran Gagal!", "error");
        },
        onClose: function(){
          /* You may add your own implementation here */
        //   alert('you closed the popup without finishing the payment');
        swal("Warning!", "Anda menutup popup tanpa menyelesaikan pembayaran!", "warning");
        }
      })
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection