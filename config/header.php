<?php
date_default_timezone_set('Europe/Istanbul');
require __DIR__."/settings.php";
if (isset($_COOKIE['phpauth_session_cookie'])) {
    if ($db->loginCheck($_COOKIE['phpauth_session_cookie'])['status'] == false) {
        $_SESSION['loginErrors'] = "Lütfen sisteme erişmek için giriş yapınız";
        header('Location: /');
        exit();
    }
}else{
    $_SESSION['loginErrors'] = "Lütfen sisteme erişmek için giriş yapınız";
    header('Location: /');
    exit();
}


$profileEmail=$db->getSessionEmail();

$profile = $db->wread("users",'user_id',$db->getUserId())->fetch(PDO::FETCH_ASSOC);
/*if ($profile['picUploaded'] == 1 && $profile['profileFilled'] == 1){
    $profileComplete = 1;
}else{
    $profileComplete = 0;
}
if($profileComplete == 0 && basename($_SERVER['REQUEST_URI']) != "profilim"){
    header('Location: /profilim');
}*/

$level = $profile['role'];
$active = $profile['active'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roboik 2022</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.3/skins/flat/blue.min.css" integrity="sha512-NFzPiFD5sIrKyFzW9/n3DgL45vt0/5SL5KbQXsHyf63cQOXR5jjWBvU9mY3A80LOGPJSGApK8rNwk++RwZAS6Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <?php if (basename($_SERVER['REQUEST_URI']) == "profilim")echo'<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" integrity="sha512-0SPWAwpC/17yYyZ/4HSllgaK7/gg9OlVozq8K7rf3J8LvCjYEEIfzzpnA2/SSjpGIunCSD18r3UhvDcu/xncWA==" crossorigin="anonymous" referrerpolicy="no-referrer" />';?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css" integrity="sha512-PT0RvABaDhDQugEbpNMwgYBCnGCiTZMh9yOzUsJHDgl/dMhD9yjHAwoumnUk3JydV3QTcIkNDuN40CJxik5+WQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.24/features/searchPane/dataTables.searchPane.min.css" integrity="sha512-+chPPIWAPOnflvaqE560afWEVzepWCsyY7rlFEFs6ABvrgRR8tQ3ydc84C7ZzgGhNxk9fjcauXOcT/rMQYriaA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js" integrity="sha512-wV7Yj1alIZDqZFCUQJy85VN+qvEIly93fIQAN7iqDFCPEucLCeNFz4r35FCo9s6WrpdDQPi80xbljXB8Bjtvcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
    <link rel="stylesheet" href="/dist/main.css">
    <link rel="icon" href="/img/logo.png">

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php /* ?>
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center" style="background: navy">
        <img class="animation__shake" src="/img/logo.png" alt="AdminLTELogo" height="60" width="60">
    </div>
<?php */ ?>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
    </nav>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: navy">
        <!-- Brand Logo -->
        <a href="/ana-sayfa" class="brand-link">
            <img src="/img/logo.png" alt="Roboik Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">ROBOİK</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block"><?=$profile['name']?></a>
                </div>
            </div>


            <!-- Sidebar Menu -->
            <nav class="mt-2 mb-auto d-flex flex-wrap flex-row" style="min-height: calc(100% - 83px)">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <?php /*GENERAL LINKS*/?>
                    <li class="nav-header">Kullanıcı Ayarları</li>
                    <li class="nav-item">
                        <a href="/profilim" class="nav-link">
                            <i class="nav-icon fas fa-user-edit"></i>
                            <p>Profili Düzenle</p>
                        </a>
                    </li>
                    <?php
                    /*Users - Jury (1)*/
                    /* Show Only to Jury and Admins */
                    if (in_array($level,[1,3])  && $active == 1){
                    ?>

                        <li class="nav-header">Jüri Sistemi</li>
                        <?php
                        $test = strtotime($db->wread('settings','settings_key','pbd-last-day')->fetch()['settings_value'] . "+23 Hours 59 minutes 59 Seconds");
                        if ($test>strtotime("now")) {
                        ?>
                            <li class="nav-item">
                                <a href="/proje-bilgi-dokumani" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Proje Bilgi Dokümanı Puanlama
                                    </p>
                                </a>
                            </li>
                        <?php
                        }
                        ?>

                        <?php
                        $test2 = strtotime($db->wread('settings','settings_key','ptd-last-day')->fetch()['settings_value'] . "+23 Hours 59 minutes 59 Seconds");
                        if ($test2>strtotime("now")) {
                        ?>
                            <li class="nav-item">
                                <a href="/proje-teslim-dokumani" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Proje Teslim Dokümanı Puanlama
                                    </p>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    <?php
                    }
                    /* Users - Judges (2) */
                    /* Show Only to Judges and Admins */
                    if (in_array($level,[2,3]) && $active == 1){


                    ?>

                        <li class="nav-header">Hakem Sistemi</li>
                        <li class="nav-item">
                            <a href="/hakem-degerlendirmesi" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Puanlama Sistemi
                                </p>
                            </a>
                        </li>
                    <?php
                    }
                    /*Users - Admins (3)*/
                    /* Show Only to Admins  */
                    if ($level >= 3){
                    ?>

                        <li class="nav-header">Yönetim Sistemi</li>
                        <li class="nav-item">
                            <a href="/scoreboard" class="nav-link">
                                <i class="nav-icon fas fa-calendar-check"></i>
                                <p>
                                    Puan Tablosu (Scoreboard)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/son-duzenleme" class="nav-link">
                                <i class="nav-icon fas fa-calendar-check"></i>
                                <p>
                                    Son Düzenleme Tarih Ayarı
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/proje-yukleme" class="nav-link">
                                <i class="nav-icon fas fa-calendar-check"></i>
                                <p>
                                    Proje Yükleme Sistemi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/kullanicilar" class="nav-link">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>
                                    Kullanıcı İşlemleri
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/proje-bilgi-dokumani-ayar" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    PBD Ayar İşlemleri
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/proje-teslim-dokumani-ayar" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    PTD Ayar İşlemleri
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/degerlendirme-sistemi-hakem" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Hakem Ayar İşlemleri
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/createBackup" class="nav-link">
                                <i class="nav-icon fas fa-download"></i>
                                <p>
                                    Sistem Yedeği Oluştur ve İndir
                                </p>
                            </a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>

                <a class="btn btn-danger mt-auto mb-2 w-100" href="/cikis-yap">Çıkış Yap</a>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>




