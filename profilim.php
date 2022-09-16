<?php
require __DIR__."/config/header.php";
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php
        if (isset($_POST['updateProfile'])){
            $errorArray = array();
            if ($db->checkInput($_POST['name']) && mb_strlen($_POST['name']) < 5){
                array_push($errorArray , "İsim alanı çok kısa.");
            }
            if ($db->checkInput($_POST['affiliation']) && mb_strlen($_POST['affiliation']) < 5){
                array_push($errorArray , "Organizasyon alanı çok kısa.");
            }
            if (!$db->checkInput($_POST['dob'])){
                array_push($errorArray , "Tarih seçilmedi.");
            }elseif (new DateTime($_POST['dob']) > new DateTime("now - 18 years")) array_push($errorArray , "Tarih yanlış seçildi");
            if (count($errorArray) == 0){
                $values=[
                    'user_id' => $db->getUserId($_COOKIE['phpauth_session_cookie']),
                    'name' => $_POST['name'],
                    'dob'=> $_POST['dob'],
                    'affiliation' => $_POST['affiliation']
                ];
                $test = $db->update("users",$values,['columns'=>'user_id']);
                if($test['status'] == true){
                    $val=[
                        'user_id' => $db->getUserId($_COOKIE['phpauth_session_cookie']),
                        'profileFilled' => 1,
                    ];
                    $db->update('users',$val,['columns'=>'user_id']);
                }
            } else {
                foreach ($errorArray as $errorItem){
            ?>
                    <div class="alert alert-danger w-100 text-center" role="alert">
                        <?=$errorItem?>
                    </div>
        <?php }}}
        if (isset($_POST['updatePass'])){

            $a = $db->changePass($_POST['oldPass'],$_POST['newPass']);
            if ($a['error']==false){
                ?>
                <div class="alert alert-success w-100 text-center" role="alert">
                    <?=$a['message']?>
                </div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger w-100 text-center" role="alert">
                    Şifreniz hatalı lütfen tekrar deneyiniz.
                </div>
                <?php
            }
        }
        ?>
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0">Profilim</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <form action="" method="post">
                <div class="form-row text-left">
                    <div class="form-group col-12 col-md-5">
                        <label for="name">Adınız - Soyadınız</label>
                        <input type="text" class="form-control" id="name" name="name" aria-describedby="name" required value="<?=$profile['name']?>">
                    </div>
                    <div class="form-group col-12 col-md-5">
                        <label for="affiliation">Organizasyon</label>
                        <input type="text" class="form-control" name="affiliation" id="affiliation" required value="<?=$profile['affiliation']?>">
                    </div>
                    <div class="form-group col-12 col-md-2">
                        <label for="updateProfile">&nbsp;</label> <br>
                        <button type="submit" class="btn btn-primary w-100" name="updateProfile" id="updateProfile">Profili Güncelle</button>
                    </div>
                </div>

            </form>
            <form action="" method="post">
                <div class="form-row text-left">
                    <div class="form-group col-12 col-md-5">
                        <label for="oldPass">Eski Şifre</label>
                        <input type="text" class="form-control" id="oldPass" name="oldPass" aria-describedby="oldPass">
                    </div>
                    <div class="form-group col-12 col-md-5">
                        <label for="newPass">Yeni Şifre</label>
                        <input type="text" class="form-control" id="newPass" name="newPass" aria-describedby="newPass">
                    </div>
                    <div class="form-group col-12 col-md-2">
                        <label for="updatePass">&nbsp;</label> <br>
                        <button type="submit" class="btn btn-primary w-100" name="updatePass" id="updatePass">Şifreyi Güncelle</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
<?php
require __DIR__."/config/footer.php";
?>