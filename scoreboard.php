<?php
require __DIR__."/config/header.php";
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0">Puan Tablosu - Scoreboard</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
               <div class="col-12">




                   <?php
                   $prj = $db->read("projects")->fetchAll();
                   foreach ($prj as $p){
                       ?>
                       <?php
                       $allAvg = ($db->getAveragePoints($p['id'],1,true)*125/1000)+($db->getAveragePoints($p['id'],2,true)*225/1000)+($db->getAveragePointsJudge($p['id'],true)*650/1000);
                       $db->qSql("UPDATE projects SET project_avg = {$allAvg} WHERE id={$p['id']}");
                   }
                   ?>




                   <table>
                    <thead>
                    <th class="pr-3">Proje Referans No:</th>
                    <th class="pr-3">Proje Adı</th>
                    <th class="pr-3">Proje Takımı</th>
                    <th class="pr-3">Proje Bilgi Dokümanı Puanı</th>
                    <th class="pr-3">Proje Teslim Dokümanı Puanı</th>
                    <th class="pr-3">Hakem Puanı</th>
                    <th class="pr-3">Toplam Puan</th>
                    </thead>
                     <?php
                        $prj = $db->qSql("SELECT * FROM projects ORDER BY project_avg DESC ")->fetchAll();
                        foreach ($prj as $p){
                        ?>
                        <tr>
                            <td class="pr-3"><?=$p['project_ref_no']?></td>
                            <td class="pr-3"><?=$p['project_name']?></td>
                            <td class="pr-3"><?=$p['project_team']?></td>
                            <td class="pr-3"><?=$db->getAveragePoints($p['id'],1,true)?></td>
                            <td class="pr-3"><?=$db->getAveragePoints($p['id'],2,true)?> </td>
                            <td class="pr-3"><?=$db->getAveragePointsJudge($p['id'],true)?> <br></td>
                            <td class="pr-3"><?=$p['project_avg']?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
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