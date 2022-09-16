<?php
require __DIR__."/config/header.php";
function resetCurrentEvalJudge(){
    $db = new crud();
    //$db->qSql("TRUNCATE TABLE points_judge");
}
if (isset($_GET['delete'])){
    resetCurrentEvalJudge();
    $status = $db->delete("evaluation_groups_judge","evaluation_group_judge_id",$_GET['delete']);
    echo $db->returnErr($status,"Başlık başarı ile silindi","Başlık silinirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
}
if (isset($_GET['deleteEval'])){
    resetCurrentEvalJudge();
    $status = $db->delete("evaluation_judge","evaluation_judge_id",$_GET['deleteEval'])['status'];
    echo $db->returnErr($status,"Değerlendirme kriteri başarı ile silindi","Değerlendirme kriteri silinirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
}
if (isset($_POST['addGroup'])){
    resetCurrentEvalJudge();
    $status = $db->insert("evaluation_groups_judge",['evaluation_group_judge_value'=>$_POST['value'],'evaluation_group_judge_duration'=>$_POST['duration']])['status'];
    echo $db->returnErr($status,"Başlık başarı ile eklendi","Başlık eklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
}
if (isset($_POST['addQ'])){
    resetCurrentEvalJudge();
    $status = $db->insert("evaluation_judge",[ 'evaluation_judge_question'=>$_POST['valueQadd'],'evaluation_judge_point'=>$_POST['points'],'evaluation_judge_negative'=>$_POST['negative'], 'evaluation_judge_short_key'=>'qs-'.mt_rand(10000,99999),'evaluation_judge_group_id'=>$_POST['groupQadd']] )['status'];
    echo $db->returnErr($status,"Değerlendirme kriteri başarı ile eklendi","Değerlendirme kriteri eklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
}
?>
    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper ">
        <?php if (isset($_GET['edit'])){?>
        <div class="content-header text-light px-4 w-100">
            <h1 class="m-0">Hakem Puanlama Başlığını Düzenle</h1>
        </div>
        <section class="content w-100 mb-3">
            <form action="" method="post">
                <?php

                if (isset($_POST['editGroup'])){
                    resetCurrentEvalJudge();
                    $status = $db->qSql("UPDATE evaluation_groups_judge SET evaluation_group_judge_value = '{$_POST['value2']}' , evaluation_group_judge_duration = '{$_POST['duration2']}'  WHERE evaluation_group_judge_id = {$_GET['edit']}");
                    echo $db->returnErr($status,"Başlık başarı ile düzenlendi","Başlık düzenlenirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
                }$g = $db->wread("evaluation_groups_judge","evaluation_group_judge_id",$_GET['edit'])->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="form-row">
                    <div class="form-group col-12 col-md-6">
                        <label for="value">Başlık:</label>
                        <input type="text" class="form-control" id="value" name="value2" value="<?=$g['evaluation_group_judge_value']?>">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="duration">İlgili Parkurda Geçirilen Süre (Saniye Cinsi):</label>
                        <input type="text" class="form-control" id="duration" name="duration2" value="<?=$g['evaluation_group_judge_duration']?>">
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
            <h1 class="m-0">Hakem Puanlama Sistemi Ayarları</h1>
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

                       <div class="form-row">
                           <div class="form-group col-12">
                               <label for="value">Başlık:</label>
                               <input type="text" class="form-control" id="value" name="value">
                           </div>
                           <div class="form-group col-12 col-md-6">
                               <label for="duration">İlgili Parkurda Geçirilen Süre  (Saniye Cinsi):</label>
                               <input type="text" class="form-control" id="duration" name="duration">
                           </div>
                           <div class="form-group col-12 col-md-6">
                               <label>&nbsp;</label>
                               <button type="submit" class="btn btn-primary w-100" name="addGroup">Ekle</button>
                           </div>

                       </div>
                   </form>
                   <div style="max-height: 500px;overflow-y: auto">
                       <?php
                       $groups = $db->read("evaluation_groups_judge")->fetchAll();
                       foreach ($groups as $g){?>
                           <div class="w-100 border-bottom border-secondary d-inline-flex flex-row align-items-center">
                               <p class="m-0"><?=$g['evaluation_group_judge_value']?></p>
                               <div class="ml-auto py-3">
                                   <a href="?edit=<?=$g['evaluation_group_judge_id']?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                   <a href="?delete=<?=$g['evaluation_group_judge_id']?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
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
                               echo $db->returnErr($qAdd,"Değerlendirme kriteri başarı ile eklendi","Değerlendirme kriteri eklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");
                           }
                           if (isset($evalDelete)){
                               echo $db->returnErr($evalDelete,"Değerlendirme kriteri başarı ile silindi","Değerlendirme kriteri silinirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.");                           }?>
                           <div class="form-group col-12">
                               <label for="value">Başlık:</label>
                               <input type="text" class="form-control" id="value" name="valueQadd">
                           </div>
                           <div class="form-group col-12">
                               <label for="points">Değeri:</label>
                               <input type="text" class="form-control" id="points" name="points">
                           </div>
                           <div class="form-group col-12">
                               <div class="form-check">
                                   <input class="form-check-input" type="radio" name="negative" id="negative1" value="0" checked>
                                   <label class="form-check-label" for="negative1">
                                       Pozitif Değerlendirme
                                   </label>
                               </div>
                               <div class="form-check">
                                   <input class="form-check-input" type="radio" name="negative" id="negative2" value="1">
                                   <label class="form-check-label" for="negative2">
                                       Negatif Değerlendirme
                                   </label>
                               </div>

                           </div>
                           <div class="form-group col-12">
                               <label for="percentage">Grup</label>
                               <select class="form-control" id="group" name="groupQadd">
                                   <?php
                                   $groups = $db->read("evaluation_groups_judge")->fetchAll();
                                   foreach ($groups as $g){?>
                                   <option value="<?=$g['evaluation_group_judge_id']?>"><?=$g['evaluation_group_judge_value']." (Süre: {$g['evaluation_group_judge_duration']} Saniye)"?></option>
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
                       $groups = $db->read("evaluation_groups_judge")->fetchAll();
                       foreach ($groups as $g){?>
                           <div class="w-100 border-bottom border-secondary">
                               <p class="m-0"><?=$g['evaluation_group_judge_value']." (Süre: {$g['evaluation_group_judge_duration']} Saniye)"?></p>
                               <ul>
                                   <?php
                                   $evals = $db->wread("evaluation_judge", "evaluation_judge_group_id" , $g['evaluation_group_judge_id'])->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($evals as $e){?>
                                    <li class="d-inline-flex flex-row align-items-center w-100 py-3"><p class="m-0"><?php


                                            if ($e['evaluation_judge_negative'] == 0) {
                                                echo "<span style='color: green'>";
                                                echo $e['evaluation_judge_question'];
                                                echo " Puan Değeri: +";
                                                echo $e['evaluation_judge_point'];
                                                echo "</span>";
                                            } else {
                                                echo "<span style='color:red'>";
                                                echo $e['evaluation_judge_question'];
                                                echo " Puan Değeri: -";
                                                echo $e['evaluation_judge_point'];
                                                echo "</span>";
                                            }

                                            ?></p>
                                    <div style="width: 40px;flex-shrink: 0" class="ml-auto mr-0">
                                        <a href="?deleteEval=<?=$e['evaluation_judge_id']?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
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