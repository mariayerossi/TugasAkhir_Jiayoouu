@extends('layouts.sidebarNavbar_tempat')

@section('content')
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
    <h3 class="text-center mb-5">Notifikasi</h3>
    @if (!$notif->isEmpty())
        @foreach ($notif as $item)
            <a href="{{$item->link_notifikasi}}" id="notificationLink{{$item->id_notifikasi}}">
                <div class="card h-70 mb-3" style="background-color: {{$item->status_notifikasi === 'Dibaca' ? 'white' : 'rgb(239, 239, 239)'}};">
                    <div class="card-body">
                        <div class="row p-3">
                            @php
                                $tanggalAwal3 = $item->waktu_notifikasi;
                                $tanggalObjek3 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal3);
                                $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
                                $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY HH:mm');
                            @endphp
                            <h6 class="mb-3">{{$item->keterangan_notifikasi}}</h6>
                            <label style="font-size:14px">{{$tanggalBaru3}}</label>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <h1>Tidak ada notifikasi</h1>
    @endif
</div>
<script>
    $(document).ready(function () {
        // Click event for the notification links
        $('a[id^="notificationLink"]').on('click', function (e) {
            e.preventDefault(); // Prevent the default behavior of the link
            
            // Extract notification ID from the link's id attribute
            var notificationId = $(this).attr('id').replace('notificationLink', '');
            var hlmTujuan = $(this).attr('href');
            
            var formData = {
                _token: '{{ csrf_token() }}', // Laravel CSRF token
                notificationId: notificationId,
                tujuan: hlmTujuan
            };

            // AJAX request to update the status
            $.ajax({
                url: '/notifikasi/editStatusDibaca/' + notificationId, // Replace with your backend endpoint
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    console.log('Notification status updated successfully.');
                    if (data.redirect) { // Change 'response' to 'data'
                        window.location.href = data.redirect; // Change 'response' to 'data'
                    }
                },
                error: function (error) {
                    console.error('Error updating notification status:', error);
                }
            });
        });
    });
</script>
@endsection