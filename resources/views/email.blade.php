<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: black;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #d7d7d7;
            border-radius: 10px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            /* border: 10px solid rgb(130, 130, 130); */
            padding: 20px;
        }

        .header {
            background-color: #00796b;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: white;
            color: black;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00796b;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #005c51;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="{{$message->embed(public_path().'/assets/img/logo_white.png')}}" width="40%">
        <h2>{{$judul}}</h2>
    </div>
    <div class="content">
        <p><b>Halo {{$nama_user}},</b></p>
        <p>{!! $isi !!}</p>
        {{-- <a href="{{$url}}" class="btn" style="margin: 20px auto; display: block; text-align: center;">{{$button}}</a> --}}
    </div>
    <div class="footer">
        <p>Terima kasih telah menggunakan layanan kami. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi kami.</p>
        <p>&copy; 2023 Sportiva. Hak Cipta Dilindungi.</p>
    </div>
</div>

</body>
</html>
