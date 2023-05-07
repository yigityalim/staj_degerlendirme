<?php
session_start();
if ($_SESSION) header('Location: index.php');

$db = new mysqli('localhost', 'root', 'root', 'staj_degerlendirme');
if ($db->connect_error) die($db->connect_error);


if ($_POST) {
    $sekreter = $db->query('SELECT * FROM sekreter')->fetch_object();
    if ($sekreter->email == $_POST['email'] && $sekreter->sifre == $_POST['password']) {
        $_SESSION['user'] = $sekreter;
        header('Location: index.php');
    }

    $akademisyen = $db->query('SELECT * FROM akademisyen')->fetch_object();
    if ($akademisyen->email == $_POST['email'] && $akademisyen->sifre == $_POST['password']) {
        $_SESSION['user'] = $akademisyen;
        header('Location: index.php');
    }
}


?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body class="">
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-6 col-lg-8 col-xl-10">
            <div class="card shadow-lg">
                <div class="card-body">
                    <form class="d-flex flex-column gap-4" action="" method="post">
                        <h4 class="text-start mb-4 fw-bolder fs-1">Giriş Yap</h4>
                        <div class="form-group">
                            <label for="email">E-posta Adresi</label>
                            <input name="email" type="email" class="form-control" id="email" placeholder="E-posta adresinizi girin">
                        </div>
                        <div class="form-group">
                            <label for="password">Şifre</label>
                            <input name="password" type="password" class="form-control" id="password" placeholder="Şifrenizi girin">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Gönder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
