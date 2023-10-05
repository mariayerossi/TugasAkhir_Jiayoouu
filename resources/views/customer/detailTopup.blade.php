@extends('layouts.navbar_customer')

<!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
<script type="text/javascript"
src="https://app.sandbox.midtrans.com/snap/snap.js"
data-client-key={{config("midtrans.client_key")}}></script>

@section('content')
<form action="" method="post" id="afterPayment">
    @csrf
    <input type="hidden" name="jumlah" value="{{$isi["transaction_details"]["gross_amount"]}}">
    <button type="submit" class="btn btn-success" id="pay-button">Top Up sekarang</button>
</form>

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