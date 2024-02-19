<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Berhasil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 650px;
            margin: 50px auto;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 160px;
            height: 60px;
            margin-right: 10px;
        }
        h2 {
            margin-top: 20px;
        }
        .action-check {
            font-size: 36px;
            color: #007466;
        }
        .login-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007466;
            border: 2px solid #007466;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .login-link:hover {
            background-color: #007466;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="https://sportiva.my.id/" class="logo">
            <img src="{{ asset('assets/img/logo.png')}}" alt="Logo">
        </a>
        <hr>
        <div class="action-check"></div>
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16" style="color: green">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
          </svg> 
        <h2>🎉Yeay! Anda Berhasil Melakukan Verifikasi Email!🎉</h2>
        <h4>Silahkan login untuk mengakses berbagai fitur eksklusif dan mulai menikmati pengalaman pengguna kami yang lengkap!</h4>
        <a href="https://sportiva.my.id/login/" class="login-link">Ke Halaman Login</a>
    </div>
</body>
</html>
