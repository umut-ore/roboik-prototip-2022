<?php
require __DIR__."/config/header.php";

if (isset($_POST['add'])){
    $values = $_POST;
    if (isset($_FILES['project_file'])){
        echo "test";
        if ($_FILES['project_file']['type'] == "application/pdf") {
            echo "test2";
            $source_file = $_FILES['project_file']['tmp_name'];
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".pdf";
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_file' => $randName];
        }
        var_dump($values);
        var_dump($_POST);
    }
    if (isset($_FILES['project_file_two'])){
        if ($_FILES['project_file_two']['type'] == "application/pdf") {
            $source_file = $_FILES['project_file_two']['tmp_name'];
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".pdf";
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_file_two' => $randName];
        }
    }
    if (isset($_FILES['project_video_one'])){
        if (strpos($_FILES['project_video_one']['type'], 'video') !== false) {
            $source_file = $_FILES['project_video_one']['tmp_name'];
            $ext = pathinfo($_FILES['project_video_one']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_one' => $randName];
        }
    }
    if (isset($_FILES['project_video_two'])){
        if (strpos($_FILES['project_video_two']['type'], 'video') !== false) {
            $source_file = $_FILES['project_video_two']['tmp_name'];
            $ext = pathinfo($_FILES['project_video_two']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_two' => $randName];
        }
    }
    if (isset($_FILES['project_video_three'])){
        if (strpos($_FILES['project_video_three']['type'], 'video') !== false) {
            $source_file = $_FILES['project_video_three']['tmp_name'];
            $ext = pathinfo($_FILES['project_video_three']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_three' => $randName];
        }
    }
    $a = $db->insert("projects",$values,['form_name'=>'add']);
    echo "<script>
                    
                            window.history.replaceState( null, null, '".str_replace('.php','',$_SERVER['PHP_SELF'])."' );";

    if ($a['status']){
        echo "toastr.success('Proje başarıyla yüklendi.')";

    }else{
        echo "toastr.danger('Bir hata oluştu');";
    }
    echo "
              </script>";
}
if (isset($_POST['addSecond'])){
    //$values = $_POST;
    $values=[];
    if (isset($_FILES[$_POST['id'].'_project_file_two'])){
        if ($_FILES[$_POST['id'].'_project_file_two']['type'] == "application/pdf") {
            $source_file = $_FILES[$_POST['id'].'_project_file_two']['tmp_name'];
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".pdf";
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_file_two' => $randName];
        }
    }
    if (isset($_FILES[$_POST['id'].'_project_video_one'])){
        if (strpos($_FILES[$_POST['id'].'_project_video_one']['type'], 'video') !== false) {
            $source_file = $_FILES[$_POST['id'].'_project_video_one']['tmp_name'];
            $ext = pathinfo($_FILES[$_POST['id'].'_project_video_one']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_one' => $randName];
        }
    }
    if (isset($_FILES[$_POST['id'].'_project_video_two'])){
        if (strpos($_FILES[$_POST['id'].'_project_video_two']['type'], 'video') !== false) {
            $source_file = $_FILES[$_POST['id'].'_project_video_two']['tmp_name'];
            $ext = pathinfo($_FILES[$_POST['id'].'_project_video_two']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_two' => $randName];
        }
    }
    if (isset($_FILES[$_POST['id'].'_project_video_three'])){
        if (strpos($_FILES[$_POST['id'].'_project_video_three']['type'], 'video') !== false) {
            $source_file = $_FILES[$_POST['id'].'_project_video_three']['tmp_name'];
            $ext = pathinfo($_FILES[$_POST['id'].'_project_video_three']['name'], PATHINFO_EXTENSION);
            $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".".$ext;
            $dest_file = __DIR__."/files/".$randName;
            move_uploaded_file( $source_file, $dest_file);
            $values+=['project_video_three' => $randName];
        }
    }
    $values += ['id'=>$_POST['id']];
    $a = $db->update("projects",$values,['form_name'=>'add','columns'=>'id']);
    if ($a['status'] == true){
        if (!empty($_POST[$_POST['id'].'_project_file_two_prev'])) unlink( __DIR__."/files/".$_POST[$_POST['id'].'_project_file_two_prev']);
        if (!empty($_POST[$_POST['id'].'_project_video_one'])) unlink( __DIR__."/files/".$_POST[$_POST['id'].'_project_video_one']);
        if (!empty($_POST[$_POST['id'].'_project_video_two'])) unlink( __DIR__."/files/".$_POST[$_POST['id'].'_project_video_two']);
        if (!empty($_POST[$_POST['id'].'_project_video_three'])) unlink( __DIR__."/files/".$_POST[$_POST['id'].'_project_video_three']);
    }
    echo "<script>
                    
                            window.history.replaceState( null, null, '".str_replace('.php','',$_SERVER['PHP_SELF'])."' );";

    if ($a['status']){
        echo "toastr.success('Proje başarıyla yüklendi.')";

    }else{
        echo "toastr.danger('Bir hata oluştu');";
    }
    echo "
              </script>";
}

?>
   <?php if (isset($_GET['delete'])){
       $data = $db->wread("projects","id",$_GET['delete'])->fetch();
       $fields = ['project_file','project_file_two','project_video_one','project_video_two','project_video_three'];
       foreach ($fields as $f){
           if (!empty($data[$f])){
               unlink(__DIR__."/files/".$data[$f]);
           }
       }
       $status = $db->delete("projects","id",$_GET['delete']);
       if ($status){
           echo "<script>window.history.replaceState( null, null, '".str_replace('.php','',$_SERVER['PHP_SELF'])."' );";

           if ($status['status']){
               echo "toastr.success('{$data['project_name']} projesi başarıyla silindi.')";

           }else{
               echo "toastr.danger('Bir hata oluştu');";
           }
           echo "
              </script>";
       }
    }
    elseif (isset($_GET['add'])) { ?>
        <div class="content-wrapper">
            <div class="content-header w-100 text-light px-4">
                <h1 class="m-0 fa-pull-left">Proje Yükleme Ekranı</h1>
            </div>
            <section class="content p-3 w-100">
                <div class="container-fluid">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="project_name">Proje Adı</label>
                                    <input type="text" class="form-control" id="project_name" name="project_name">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="project_team">Takım</label>
                                    <input type="text" class="form-control" id="project_team" name="project_team">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="project_ref_no">Referans Numarası</label>
                                    <input type="text" class="form-control" id="project_ref_no" name="project_ref_no">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label>Proje Bilgi Dökümanı</label>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="project_file" name="project_file">
                                    <label class="custom-file-label" for="project_file">Proje Bilgi Dökümanı Seçiniz...</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label>Proje Teslim Dökümanı</label>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="project_file_two" name="project_file_two">
                                    <label class="custom-file-label" for="project_file_two">Proje Teslim Dökümanı Seçiniz...</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label>Proje Teslim Videosu (1)</label>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="project_video_one" name="project_video_one">
                                    <label class="custom-file-label" for="project_video_one">Proje Teslim Videosu (1) Seçiniz...</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label>Proje Teslim Videosu (2)</label>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="project_video_two" name="project_video_two">
                                    <label class="custom-file-label" for="project_video_two">Proje Teslim Videosu (2) Seçiniz...</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label>Proje Teslim Videosu (3)</label>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="project_video_three" name="project_video_three">
                                    <label class="custom-file-label" for="project_video_three">Proje Teslim Videosu (3) Seçiniz...</label>
                                </div>
                            </div>
                            <div class="col-12"><button class="btn btn-success w-100" name="add">Kayıt Et</button></div>

                            <script>
                                $("input[type=file]").on("change",function () {
                                    let filename = $(this).val().replace(/C:\\fakepath\\/i, '');
                                    $("label[for="+$(this).attr('id')+"]").text(filename.substr(0,30));
                                });
                            </script>
                        </div>
                    </form>
                </div>
            </section>
        </div>
   <?php } else { ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header w-100 text-light px-4">
            <h1 class="m-0 fa-pull-left">Proje Yönetim Sistemi</h1>
            <a href="?add=true" class="btn btn-success fa-pull-right">Yeni Proje Ekle</a>
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content p-3 w-100">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <?php
                    $getProjects = $db->read("projects")->fetchAll();
                    foreach ($getProjects as $prj){
                        ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card w-100 h-100">
                                <div class="card-body h-100 flex-row d-flex flex-wrap">
                                    <h5 class="card-title w-100"><?=$prj['project_name']?></h5>
                                    <h5 class="card-title mb-3 w-100"><?=$prj['project_ref_no']?></h5>
                                    <p class="card-text"><?=$prj['project_team']?></p>
                                    <div class="w-100">
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div class="w-100 mt-3">
                                                <div class="custom-file mb-3">
                                                    <input type="file" class="custom-file-input" id="<?=$prj['id']?>_project_file_two" name="<?=$prj['id']?>_project_file_two">
                                                    <label class="custom-file-label" for="<?=$prj['id']?>_project_file_two"><?=(is_null($prj['project_file_two']))?"Proje Teslim Dokümanı Seçiniz...":$prj['project_file_two']?></label>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="custom-file mb-3">
                                                    <input type="file" class="custom-file-input" id="<?=$prj['id']?>_project_video_one" name="<?=$prj['id']?>_project_video_one">
                                                    <label class="custom-file-label" for="<?=$prj['id']?>_project_video_one"><?=(is_null($prj['project_video_one']))?"Proje Teslim Videosu (1) Seçiniz...":$prj['project_video_one']?></label>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="custom-file mb-3">
                                                    <input type="file" class="custom-file-input" id="<?=$prj['id']?>_project_video_two" name="<?=$prj['id']?>_project_video_two">
                                                    <label class="custom-file-label" for="<?=$prj['id']?>_project_video_two"><?=(is_null($prj['project_video_two']))?"Proje Teslim Videosu (2) Seçiniz...":$prj['project_video_two']?></label>
                                                </div>
                                            </div>
                                            <div class="w-100 mt-3">
                                                <div class="custom-file mb-3">
                                                    <input type="file" class="custom-file-input" id="<?=$prj['id']?>_project_video_three" name="<?=$prj['id']?>_project_video_three">
                                                    <label class="custom-file-label" for="<?=$prj['id']?>_project_video_three"><?=(is_null($prj['project_video_three']))?"Proje Teslim Videosu (3) Seçiniz...":$prj['project_video_three']?></label>
                                                </div>
                                            </div>
                                            <script>
                                                $("input[type=file]").on("change",function () {
                                                    let filename = $(this).val().replace(/C:\\fakepath\\/i, '');
                                                    $("label[for="+$(this).attr('id')+"]").text(filename.substr(0,30));
                                                });
                                            </script>
                                            <div class="w-100 mt-3">
                                                <input type="hidden" class="d-none" name="<?=$prj['id']?>_project_file_two_prev" value="<?=(is_null($prj['project_file_two']))?"":$prj['project_file_two']?>">
                                                <input type="hidden" class="d-none" name="<?=$prj['id']?>_project_video_one_prev" value="<?=(is_null($prj['project_video_one']))?"":$prj['project_video_one']?>">
                                                <input type="hidden" class="d-none" name="<?=$prj['id']?>_project_video_two_prev" value="<?=(is_null($prj['project_video_two']))?"":$prj['project_video_two']?>">
                                                <input type="hidden" class="d-none" name="id" value="<?=$prj['id']?>">
                                                <button type="submit" class="btn btn-primary w-100" name="addSecond">Ekle</button>
                                            </div>
                                        </form>
                                    </div>
                                    <a href="?delete=<?=$prj['id']?>" class="btn btn-danger w-100 mt-auto">Sil</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
   <?php } ?>
<?php
require __DIR__."/config/footer.php";
?>