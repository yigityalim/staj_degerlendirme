<?php
session_start();
if (!$_SESSION) header('Location: giris.php');
$user = $_SESSION['user'];
$db = new mysqli('localhost', 'root', 'root', 'staj_degerlendirme');

if (isset($_POST['cikis'])) {
    session_destroy();
    $_SESSION = [];
    header('Location: giris.php');
}

$ogrenciSayisi = null;
$minimum = null;
$current = null;
$ogrenci = null;
$sekreter = null;
$isDisabledGeri = null;
$isDisabledIleri = null;
$isDisabledBasaGit = null;
$isDisabledSonaGit = null;
$staj_degerlendirme = null;

if ($user->tip == 'sekreter') {

    $ogrenciSayisi = $db->query('SELECT COUNT(id) AS count FROM ogrenci')->fetch_object()->count; # SELECT COUNT(id) AS count FROM ogrenci
    $minimum = $db->query('SELECT MIN(id) AS min FROM ogrenci')->fetch_object()->min; # SELECT MIN(id) AS min FROM ogrenci
    $current = $_SESSION['current'] ?? $minimum;
    $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $current")->fetch_object(); # SELECT * FROM ogrenci WHERE id = 1 LIMIT 1

    if (isset($_POST['onceki'])) {
        $current--;
        if ($current < $minimum) $current = $minimum;
        $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $current")->fetch_object();
        $_SESSION['current'] = $current;
    }
    if (isset($_POST['sonraki'])) {
        $current++;
        if ($current > $ogrenciSayisi) $current = $ogrenciSayisi;
        $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $current")->fetch_object();
        $_SESSION['current'] = $current;
    }
    if (isset($_POST['basa_git'])) {
        $current = $minimum;
        $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $current")->fetch_object();
        $_SESSION['current'] = $current;
    }
    if (isset($_POST['sona_git'])) {
        $current = $ogrenciSayisi;
        $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $current")->fetch_object();
        $_SESSION['current'] = $current;
    }

    $sekreter = $db->query("SELECT * FROM sekreter WHERE id = $user->id")->fetch_object(); # SELECT * FROM sekreter WHERE id = 1 LIMIT 1

    $isDisabledGeri = ($current == $minimum) ? 'disabled' : '';
    $isDisabledIleri = ($current == $ogrenciSayisi) ? 'disabled' : '';
    $isDisabledBasaGit = ($current == $minimum) ? 'disabled' : '';
    $isDisabledSonaGit = ($current == $ogrenciSayisi) ? 'disabled' : '';

    if (isset($_POST['kaydet'])) {
        $tc_kimlik = $_POST['tc_kimlik'];
        $ad = $_POST['ad'];
        $soyad = $_POST['soyad'];
        $ogrenci_no = $_POST['ogrenci_no'];
        $email = $_POST['email'];
        $sinif = $_POST['sinif'];
        $telefon = $_POST['telefon'];
        $bolum = $_POST['bolum'];
        $staj_kodu = $_POST['staj_kodu'];
        $staj_yeri = $_POST['staj_yeri'];
        $staj_baslama_tarihi = $_POST['staj_baslama_tarihi'];
        $staj_bitis_tarihi = $_POST['staj_bitis_tarihi'];
        $evrak_teslim = $_POST['evrak_teslim'];
        $basvuru_dilekcesi = $_POST['basvuru_dilekcesi'];
        $kabul_yazisi = $_POST['kabul_yazisi'];
        $mustehaklik = $_POST['mustehaklik'];
        $kimlik_fotokopisi = $_POST['kimlik_fotokopisi'];
        $staj_formu = $_POST['staj_formu'];
        $staj_raporu = $_POST['staj_raporu'];
        $aciklama = $_POST['aciklama'];
        $basari = $_POST['basari'];
        $id = $ogrenci->id;

        $query = $db->prepare('UPDATE ogrenci SET id = ?, sekreter_id = ?, tc_kimlik = ?, ad = ?, soyad = ?, ogrenci_no = ?, email = ?, sinif = ?, telefon = ?, bolum = ?, staj_kodu = ?, staj_yeri = ?, staj_baslama_tarihi = ?, staj_bitis_tarihi = ?, evrak_teslim = ?, basvuru_dilekcesi = ?, kabul_yazisi = ?, mustehaklik = ?, kimlik_fotokopisi = ?, staj_formu = ?, staj_raporu = ?, aciklama = ?, basari = ? WHERE id = ?');
        $query->bind_param('iisssssssssssssssssssssi', $ogrenci->id, $ogrenci->sekreter_id, $tc_kimlik, $ad, $soyad, $ogrenci_no, $email, $sinif, $telefon, $bolum, $staj_kodu, $staj_yeri, $staj_baslama_tarihi, $staj_bitis_tarihi, $evrak_teslim, $basvuru_dilekcesi, $kabul_yazisi, $mustehaklik, $kimlik_fotokopisi, $staj_formu, $staj_raporu, $aciklama, $basari, $id);
        $query->execute();
        $query->close();

    }
}

// -------------------------------------- AKADEMİSYEN --------------------------------------
if ($user->tip == 'akademisyen') {

    $ogrenciSayisi = null;
    $minimum = null;
    $current = null;
    $ogrenci = null;
    $sekreter = null;
    $isDisabledGeri = null;
    $isDisabledIleri = null;
    $isDisabledBasaGit = null;
    $isDisabledSonaGit = null;

    $staj_degerlendirme = $db->query("SELECT * FROM staj_degerlendirme WHERE akademisyen_id = $user->id")->fetch_object(); # SELECT * FROM staj_degerlendirme WHERE akademisyen_id = 1 LIMIT 1
    $ogrenci = $db->query("SELECT * FROM ogrenci WHERE id = $staj_degerlendirme->ogrenci_id")->fetch_object(); # SELECT * FROM ogrenci WHERE id = 1 LIMIT 1

    // 19 tane soru soru1 gibi name değerleri ile post olarak getiriliyor. bunu kısa yoldan bir değişkene ata ve gelen değerlerin ortalamasını al
    $sorular = [];
    $ortalama = 0;
    if (isset($_POST['degerlendirme_guncelle'])) {
        for ($i = 1; $i <= 19; $i++) {
            $sorular['soru' . $i] = $_POST['soru' . $i];
        }
        $ortalama = round(array_sum($sorular) / count($sorular), 2) * 100;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Anasayfa</title
</head>
<body>
<div class="container p-1">
    <?php if ($user->tip == 'sekreter'): ?>
        <div class="h-100 mx-auto d-flex flex-column gap-4 justify-content-center">
            <h1 class="fw-bold col-4 mt-4">Öğrenci Bilgileri</h1>
            <form action="" method="post" class="d-flex gap-4">
                <div class="col-4">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="id" class="fw-bold input-group-text">Öğrenci ID:</label>
                                <input id="id" readonly type="text" class="form-control"
                                       value="<?= $ogrenci->id; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="sekreter_id" class="fw-bold input-group-text">Sekreter ID: </label>
                                <input id="sekreter_id" readonly type="text" class="form-control"
                                       value="<?= $ogrenci->sekreter_id; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="tc_kimlik" class="fw-bold input-group-text">TC Kimlik: </label>
                                <input id="tc_kimlik" type="text" class="form-control" name="tc_kimlik"
                                       value="<?= $ogrenci->tc_kimlik;; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="ad" class="fw-bold input-group-text">Ad: </label>
                                <input id="ad" type="text" class="form-control" value="<?= $ogrenci->ad; ?>" name="ad"
                                >
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="soyad" class="fw-bold input-group-text">Soyad: </label>
                                <input id="soyad" type="text" class="form-control" value="<?= $ogrenci->soyad; ?>"
                                       name="soyad">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="ogrenci_no" class="fw-bold input-group-text">Öğrenci Numarası: </label>
                                <input id="ogrenci_no" type="number" class="form-control" name="ogrenci_no"
                                       value="<?= $ogrenci->ogrenci_no; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="email" class="fw-bold input-group-text">Email: </label>
                                <input id="email" type="email" class="form-control" value="<?= $ogrenci->email; ?>"
                                       name="email">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="sinif" class="fw-bold input-group-text">Sınıf: </label>
                                <input id="sinif" type="text" class="form-control" value="<?= $ogrenci->sinif; ?>"
                                       name="sinif">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="telefon" class="fw-bold input-group-text">Telefon: </label>
                                <input id="telefon" type="text" class="form-control" name="telefon"
                                       value="<?= $ogrenci->telefon; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="bolum" class="fw-bold input-group-text">Bölüm: </label>
                                <input id="bolum" type="text" class="form-control" value="<?= $ogrenci->bolum; ?>"
                                       name="bolum">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="aciklama" class="fw-bold input-group-text">Açıklama:</label>
                                <textarea rows="1" id="aciklama" name="aciklama"
                                          class="form-control"><?= $ogrenci->aciklama; ?></textarea>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="basari" class="fw-bold input-group-text">Başarı Durumu:</label>
                                <select id="basari" class="form-select" name="basari">
                                    <option value="1" <?= ($ogrenci->basari == 1) ? 'selected' : ''; ?>>Başarılı
                                    </option>
                                    <option value="0" <?= ($ogrenci->basari == 0) ? 'selected' : ''; ?>>Başarısız
                                    </option>
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-4">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_kodu" class="fw-bold input-group-text">Staj Kodu:</label>
                                <input id="staj_kodu" type="text" class="form-control" name="staj_kodu"
                                       value="<?= $ogrenci->staj_kodu; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_yeri" class="fw-bold input-group-text">Staj yeri
                                    Tarihi:</label>
                                <input id="staj_yeri" type="tel" class="form-control" name="staj_yeri"
                                       value="<?= $ogrenci->staj_yeri; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_baslama_tarihi" class="fw-bold input-group-text">Staj Başlama
                                    Tarihi:</label>
                                <input id="staj_baslama_tarihi" type="text" class="form-control"
                                       name="staj_baslama_tarihi"
                                       value="<?= $ogrenci->staj_baslama_tarihi; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_bitis_tarihi" class="fw-bold input-group-text">Staj Bitiş
                                    Tarihi:</label>
                                <input id="staj_bitis_tarihi" type="text" class="form-control" name="staj_bitis_tarihi"
                                       value="<?= $ogrenci->staj_bitis_tarihi; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="evrak_teslim" class="fw-bold input-group-text">Evrak Teslim:</label>
                                <input id="evrak_teslim" type="number" class="form-control" name="evrak_teslim"
                                       value="<?= $ogrenci->evrak_teslim ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="basvuru_dilekcesi" class="fw-bold input-group-text">Başvuru
                                    Dilekçesi:</label>
                                <input id="basvuru_dilekcesi" type="number" class="form-control"
                                       name="basvuru_dilekcesi"
                                       value="<?= $ogrenci->basvuru_dilekcesi; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="kabul_yazisi" class="fw-bold input-group-text">Kabul Yazısı: </label>
                                <input id="kabul_yazisi" type="number" class="form-control" pattern="[0-1{1}]"
                                       name="kabul_yazisi"
                                       value="<?= $ogrenci->kabul_yazisi; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="mustehaklik" class="fw-bold input-group-text">Müstehaklık: </label>
                                <input id="mustehaklik" type="number" class="form-control" name="mustehaklik"
                                       value="<?= $ogrenci->mustehaklik; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="kimlik_fotokopisi" class="fw-bold input-group-text">Kimlik
                                    Fotokopisi: </label>
                                <input id="kimlik_fotokopisi" type="number" class="form-control"
                                       name="kimlik_fotokopisi"
                                       value="<?= $ogrenci->kimlik_fotokopisi; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_formu" class="fw-bold input-group-text">Staj Formu: </label>
                                <input id="staj_formu" type="number" class="form-control" name="staj_formu"
                                       value="<?= $ogrenci->staj_formu; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="staj_raporu" class="fw-bold input-group-text">Staj Raporu: </label>
                                <input id="staj_raporu" type="number" class="form-control" name="staj_raporu"
                                       value="<?= $ogrenci->staj_raporu; ?>">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <label for="sekreter" class="fw-bold input-group-text">Sekreter: </label>
                                <input readonly id="sekreter" type="text" class="form-control"
                                       value="<?= $sekreter->ad; ?> <?= $sekreter->soyad; ?>">
                            </div>
                        </li>
                    </ul>
                    <button class="btn btn-primary mt-4 w-100" type="submit" name="kaydet">Kaydet</button>
                </div>
            </form>
            <div class="d-flex gap-4">
                <form method="post" action="">
                    <input <?= $isDisabledGeri ?> class="btn btn-secondary" type="submit" name="basa_git"
                                                  value="Başa Git">
                    <input <?= $isDisabledGeri ?> class="btn btn-secondary" type="submit" name="onceki" value="Önceki">
                    <input <?= $isDisabledIleri ?> class="btn btn-secondary" type="submit" name="sonraki"
                                                   value="Sonraki">
                    <input <?= $isDisabledIleri ?> class="btn btn-secondary" type="submit" name="sona_git"
                                                   value="Sona Git">
                </form>
                <form action="" method="post">
                    <input class="btn btn-danger" name="cikis" type="submit" value="Çıkış Yap">
                </form>
            </div>
        </div>
    <?php elseif ($user->tip == 'akademisyen'): ?>
        <div class="p-4 h-100 mx-auto d-flex flex-column gap-4 justify-content-center">
            <h1 class="text-start fw-bold">Staj Değerlendirme</h1>
            <form method="post" action="" class="">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="ad">Ad</label>
                            <input id="ad" readonly class="form-control" type="text" value="<?= $ogrenci->ad ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="soyad">Soyad</label>
                            <input id="soyad" readonly class="form-control" type="text" value="<?= $ogrenci->soyad ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="ad">Sınıf</label>
                            <input id="ad" readonly class="form-control" type="text"
                                   value="<?= $ogrenci->sinif ?>">
                        </div>
                    </div>
                </div>
                <div class="row my-4">
                    <div class="col">
                        <label for="isyeri_degerlendirmesi">İşyeri Değerlendirmesi:</label>
                        <select class="form-select" id="isyeri_degerlendirmesi" name="isyeri_degerlendirmesi">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="sekil_bicim_yazidili">Şekil - Biçim - Yazı dili:</label>
                        <select class="form-select" id="sekil_bicim_yazidili" name="sekil_bicim_yazidili">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="soru1">Soru 1: (4 puan)</label>
                            <select class="form-select" id="soru1" name="soru1">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru2">Soru 2: (6 puan)</label>
                            <select class="form-select" id="soru2" name="soru2">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru3">Soru 3: (5 puan)</label>
                            <select class="form-select" id="soru3" name="soru3">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru4">Soru 4: (5 puan)</label>
                            <select class="form-select" id="soru4" name="soru4">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru5">Soru 5: (8 puan)</label>
                            <select class="form-select" id="soru5" name="soru5">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru6">Soru 6: (3 puan)</label>
                            <select class="form-select" id="soru6" name="soru6">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru7">Soru 7: (8 puan)</label>
                            <select class="form-select" id="soru7" name="soru7">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru8">Soru 8: (3 puan)</label>
                            <select class="form-select" id="soru8" name="soru8">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru9">Soru 9: (3 puan)</label>
                            <select class="form-select" id="soru9" name="soru9">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru19">Soru 19: (3 puan)</label>
                            <select class="form-select" id="soru19" name="soru19">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="soru10">Soru 10: (4 puan)</label>
                            <select class="form-select" id="soru10" name="soru10">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru11">Soru 11: (6 puan)</label>
                            <select class="form-select" id="soru11" name="soru11">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru12">Soru 12: (5 puan)</label>
                            <select class="form-select" id="soru12" name="soru12">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru13">Soru 13: (4 puan)</label>
                            <select class="form-select" id="soru13" name="soru13">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru14">Soru 14: (6 puan)</label>
                            <select class="form-select" id="soru14" name="soru14">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru15">Soru 15: (5 puan)</label>
                            <select class="form-select" id="soru15" name="soru15">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru16">Soru 16: (6 puan)</label>
                            <select class="form-select" id="soru16" name="soru16">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru17">Soru 17: (5 puan)</label>
                            <select class="form-select" id="soru17" name="soru17">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="soru18">Soru 18: (5 puan)</label>
                            <select class="form-select" id="soru18" name="soru18">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="basari">Başarı Durumu</label>
                            <input readonly class="form-control" id="basari"
                                   value="<?= $staj_degerlendirme->notu > 60 ? 'Başarılı' : 'Başarısız' ?>">
                        </div>
                    </div>
                </div>
                <input name="degerlendirme_guncelle" type="submit" class="mt-4 btn btn-primary" value="Gönder">
            </form>
            <form action="" method="post">
                <input name="cikis" value="Çıkış Yap" type="submit" class="btn btn-danger">
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>