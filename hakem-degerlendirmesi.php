<?php
require __DIR__."/config/header.php";
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0">Hakem Puanlama Ekranı</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <?php if (in_array($level,[2,3])){?>
            <div class="row">
            <!-- Small boxes (Stat box) -->
            <?php
            $getProjects = $db->qSql("SELECT * FROM projects")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($getProjects as $prj){
            ?>
                    <div class="d-inline-flex flex-row align-content-center justify-content-between col-12 col-md-6 mb-3">
                        <p class="m-0">Proje: <?=$prj['project_name']. "<br>Katılımcı: ". $prj['project_team'] . "<br>Başvuru No: ". $prj['project_ref_no'] ;?>
                            <br>Toplam Puan: <?=$db->getAveragePointsJudge($prj['id'])?></p>

                        <div>

                            <?php
                            echo ($db->didYouEvalJudge($prj['id']) == true)?'<div class="alert alert-success py-1 px-2 text-center" role="alert">Puanladınız</div>':'<div class="alert alert-danger py-1 px-2 text-center" role="alert">Puanlamadınız</div>';
                            ?>
                        <div>
                            <a href="/hakem-puanlama/<?=$prj['id']?>" class="btn btn-primary w-100">Puanla</a>
                        </div>

                        </div>
                    </div>
                    <?php } ?>
            </div>
            <!-- /.row -->
    <?php } else {
                echo "<h1>Bu sayfaya erişmek için yetkiniz bulunmamaktadır.</h1>";

            }?>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?php
require __DIR__."/config/footer.php";
?>