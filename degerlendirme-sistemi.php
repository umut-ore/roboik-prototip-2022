<?php
require __DIR__."/config/header.php";
switch ($_GET['page']){
    case 1:
        $name="Proje Bilgi Dokümanı";
        break;
    case 2:
        $name="Proje Teslim Dokümanı";
        break;
}
function resetCurrentEval(){
    $db = new crud();
    $db->dumpDB();
    if ($_GET['page'] == 1){
        $db->qSql("TRUNCATE TABLE points");
    }
    if ($_GET['page'] == 2){
        $db->qSql("TRUNCATE TABLE points2");
    }
}
if (isset($_GET['delete'])){
    resetCurrentEval();
    if ($db->delete("evaluation_groups","evaluation_group_id",$_GET['delete'])['status']){
        $groupDelete = true;
    } else {
        $groupDelete = false;
    }
}
if (isset($_GET['deleteEval'])){
    resetCurrentEval();
    if ($db->delete("evaluation","evaluation_id",$_GET['deleteEval'])['status']){
        $evalDelete = true;
    } else {
        $evalDelete = false;
    }
}
if (isset($_POST['addGroup'])){
    resetCurrentEval();
    if ($x = $db->insert("evaluation_groups",['evaluation_group_value'=>$_POST['value'],'evaluation_group_percentage'=>$_POST['percentage'],'evaluation_group_master_id'=>$_GET['page']])['status']){
        $groupAdd = true;
    } else {
        $groupAdd = false;
    }
    var_dump($x);
}
if (isset($_POST['addQ'])){
    resetCurrentEval();
    if ($db->insert("evaluation",[ 'evaluation_question'=>$_POST['valueQadd'], 'evaluation_short_key'=>'qs-'.mt_rand(10000,99999),'evaluation_group_id'=>$_POST['groupQadd']] )['status']){
        $qAdd = true;
    } else {
        $qAdd = false;
    }
}
?>
    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper ">
        <?php if (isset($_GET['edit'])){?>
        <div class="content-header text-light px-4 w-100">
            <h1 class="m-0"><?=$name?> Başlığını Düzenle</h1>
        </div>
        <section class="content w-100 mb-3">
            <form action="" method="post">
                <?php

                if (isset($groupEdit)){
                    if ($groupEdit)echo '<div class="alert alert-success" role="alert">Başarıyla Düzenlendi</div>'; else echo '<div class="alert alert-danger" role="alert">Bir Hata Oluştu</div>';
                }

                if (isset($_POST['editGroup'])){
                    resetCurrentEval();
                    if ($db->qSql("UPDATE evaluation_groups SET evaluation_group_value = '{$_POST['value2']}' , evaluation_group_percentage='{$_POST['percentage2']}' WHERE evaluation_group_id = {$_GET['edit']}")){
                        $groupEdit = true;
                    } else {
                        $groupEdit = false;
                    }
                }$g = $db->wread("evaluation_groups","evaluation_group_id",$_GET['edit'])->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="value">Başlık:</label>
                        <input type="text" class="form-control" id="value" name="value2" value="<?=$g['evaluation_group_value']?>">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="percentage">Yüzdelik Oran (1-100):</label>
                        <input type="text" class="form-control" id="percentage" name="percentage2" value="<?=$g['evaluation_group_percentage']?>">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-warning w-100" name="editGroup">Düzenle</button>
                    </div>

                </div>
        </section>
<?php } ?>
        <!-- Content Header (Page header) -->
        <div class="alert alert-danger mb-5"><h5><b>DİKKAT!</b>: Puanlama yapıldıktan sonra bu sayfada değişiklik yapmayınız. Sistem sağlığı için puanlama tablolarını sıfırlayacaktır.</h5></div>
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0"><?=$name?> Sistemi Ayarları</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
               <div class="col-12 col-md-6">
                    <h2>Değerlendirme Başlığı Ekle</h2>
                   <form action="" method="post">
                       <?php
                       if (isset($groupAdd)){
                           if ($groupAdd)echo '<div class="alert alert-success" role="alert">Başarıyla Eklendi</div>'; else echo '<div class="alert alert-danger" role="alert">Bir Hata Oluştu</div>';
                       }
                       if (isset($groupDelete)){
                           if ($groupDelete)echo '<div class="alert alert-success" role="alert">Başarıyla Silindi</div>'; else echo '<div class="alert alert-danger" role="alert">Bir Hata Oluştu</div>';
                       }
                       ?>
                       <div class="form-row">
                           <div class="form-group col-12">
                               <label for="value">Başlık:</label>
                               <input type="text" class="form-control" id="value" name="value">
                           </div>
                           <div class="form-group col-12 col-md-6">
                               <label for="percentage">Yüzdelik Oran (1-100):</label>
                               <input type="text" class="form-control" id="percentage" name="percentage">
                           </div>
                           <div class="form-group col-12 col-md-6">
                               <label>&nbsp;</label>
                               <button type="submit" class="btn btn-primary w-100" name="addGroup">Ekle</button>
                           </div>

                       </div>
                   </form>
                   <div style="max-height: 500px;overflow-y: auto">
                       <?php
                       $groups = $db->wread("evaluation_groups","evaluation_group_master_id",$_GET['page'])->fetchAll();
                       foreach ($groups as $g){?>
                           <div class="w-100 border-bottom border-secondary d-inline-flex flex-row align-items-center">
                               <p class="m-0"><?=$g['evaluation_group_value']." - %".$g['evaluation_group_percentage']?></p>
                               <div class="ml-auto py-3">
                                   <a href="?edit=<?=$g['evaluation_group_id']?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                   <a href="?delete=<?=$g['evaluation_group_id']?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                               </div>
                           </div>
                        <?php } ?>
                   </div>
               </div>
               <div class="col-12 col-md-6">
                    <h2>Değerlendirme Sorusu Ekle</h2>
                   <form action="" method="post">
                       <div class="form-row">
                           <?php
                           if (isset($qAdd)){
                               if ($qAdd)echo '<div class="alert alert-success" role="alert">Başarıyla Eklendi</div>'; else echo '<div class="alert alert-danger" role="alert">Bir Hata Oluştu</div>';
                           }
                           if (isset($evalDelete)){
                               if ($evalDelete)echo '<div class="alert alert-success" role="alert">Başarıyla Silindi</div>'; else echo '<div class="alert alert-danger" role="alert">Bir Hata Oluştu</div>';
                           }?>
                           <div class="form-group col-12">
                               <label for="value">Başlık:</label>
                               <input type="text" class="form-control" id="value" name="valueQadd">
                           </div>
                           <div class="form-group col-12">
                               <label for="percentage">Grup</label>
                               <select class="form-control" id="group" name="groupQadd">
                                   <?php
                                   $groups = $db->wread("evaluation_groups","evaluation_group_master_id",$_GET['page'])->fetchAll();
                                   foreach ($groups as $g){?>
                                   <option value="<?=$g['evaluation_group_id']?>"><?=$g['evaluation_group_value']." - ".$g['evaluation_group_percentage']." %"?></option>
                                    <?php } ?>
                               </select>
                           </div>
                           <div class="form-group col-12">
                               <label>&nbsp;</label>
                               <button type="submit" class="btn btn-primary w-100" name="addQ">Ekle</button>
                           </div>

                       </div>
                   </form>
                   <div style="max-height: 500px;overflow-y: auto">
                       <?php
                       $groups = $db->wread("evaluation_groups","evaluation_group_master_id",$_GET['page'])->fetchAll();
                       foreach ($groups as $g){?>
                           <div class="w-100 border-bottom border-secondary">
                               <p class="m-0"><?=$g['evaluation_group_value']?></p>
                               <ul>
                                   <?php
                                   $evals = $db->wread("evaluation", "evaluation_group_id" , $g['evaluation_group_id'])->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($evals as $e){?>
                                    <li class="d-inline-flex flex-row align-items-center w-100 py-3"><p class="m-0"><?=$e['evaluation_question']?></p>
                                    <div style="width: 40px;flex-shrink: 0" class="ml-auto mr-0">
                                        <a href="?deleteEval=<?=$e['evaluation_id']?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                    </div></li>
                                    <?php }?>
                               </ul>
                           </div>
                       <?php } ?>
                   </div>
               </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?php
require __DIR__."/config/footer.php";
?>