<?php
require __DIR__."/config/header.php";
switch ($_GET['page']){
    case 1:
        $linking = "proje-bilgi-dokumani-puanlama/";
        $name="Proje Bilgi Dokümanı";
        $timer = "pbd-last-day";
        break;
    case 2:
        $linking = 'proje-teslim-dokumani-puanlama/';
        $name="Proje Teslim Dokümanı";
        $timer = "ptd-last-day";
        break;
    case 3:
        $linking = "hakem-degerlendirmesi-puanlama/";
        $name="Hakem";
        break;
}
$test = strtotime($db->wread('settings','settings_key',$timer)->fetch()['settings_value'] . "+23 Hours 59 minutes 59 Seconds");
$timer2 = ($test>strtotime("now"));
if ($timer2 == false) {
    echo "<script>window.location.replace('/')</script>";
}

?>
    <div class="modal" tabindex="-1" id="quest" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <?php if ($timer2){ ?>
        <div class="alert alert-info" role="alert">
            <?=date("d.m.Y - H:i",$test)?> Tarihine kadar puanlamanızı tamamlamanız  gerekmektedir. Bu tarihden sonra sistem puanlamaya kapatılacaktır.
        </div>
    <?php } ?>
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0"><?=$name?> Puanlama Ekranı</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <?php if (in_array($level,[1,3])){?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Proje Referans Numarası</th>
                    <th>Proje Adı</th>
                    <th>Proje Takımı</th>
                    <th>Proje Puanı</th>
                    <th>Link</th>
                </tr>
                </thead>
                <tbody>
            <!-- Small boxes (Stat box) -->
            <?php
            $prjList = $db->qSql("SELECT * FROM projects")->fetchAll();
            $getProjects = array();
            $i = 0;
            foreach ($prjList as $prj){
                $getPoints = $db->qSql("SELECT * FROM points WHERE project_id='".$prj['id']."' AND jury_id = {$db->getUserId()}")->fetchAll();
                $getProjects[$i] = $prj;
                if (count($getPoints)==1){
                    $getProjects[$i]['score'] = $getPoints[0]['calc'];
                }else{
                    $getProjects[$i]['score'] = 0;
                }
                $i++;
            }

            class projectList {
                var string $project_name ;
                var string $project_team ;
                var string $project_ref_no ;
                var int $id ;
                var float $score ;
                public function __construct($data) {
                    $this->project_name = $data['project_name'];
                    $this->project_team = $data['project_team'];
                    $this->project_ref_no = $data['project_ref_no'];
                    $this->id = $data['id'];
                    $this->score = $data['score'];
                }
            }
            function data2Object($data) {
                $class_object = new projectList($data);
                return $class_object;
            }
            function comparator($object1, $object2) {
                return $object1->score < $object2->score;
            }
            $school_data = array_map('data2Object', $getProjects);
            usort($school_data, 'comparator');
            foreach ($school_data as $prj){
                if($_GET['page']==1){

            ?>
                    <tr>
                        <td><?=$prj->project_ref_no?></td>
                        <td><?=$prj->project_name?></td>
                        <td><?=$prj->project_team?></td>
                        <td><?=($db->didYouEvaluate($prj->id,$_GET['page']) == true)?'<div class="alert alert-success py-1 px-2 text-center" role="alert">'.$db->getAveragePoints($prj->id,$_GET['page']).'</div>':'<div class="alert alert-danger py-1 px-2 text-center" role="alert">Puanlamadınız</div>';
                            ?></td>
                        <?php if ( $db->didYouEvaluate($prj->id,$_GET['page']) == true ){?>
                        <td><a href="javascript:openEval('<?=$prj->id?>')" class="btn btn-primary w-100">Puanla</a></td>
                        <?php } else { ?>
                            <td><a href="<?=$linking.$prj->id?>" class="btn btn-primary w-100">Puanla</a></td>
                        <?php } ?>
                    </tr>
                
                    <?php
                } else {
                    $px=$db->qSql("SELECT id from projects WHERE project_avg!=0 ORDER BY project_avg DESC LIMIT 10")->fetchAll();
                    $ids = [];
                    foreach ($px as $x){
                        $ids[] = $x['id'];
                    }
                    if (in_array($prj->id,$ids)){
                    ?>
                    <tr>
                        <td><?=$prj->project_ref_no?></td>
                        <td><?=$prj->project_name?></td>
                        <td><?=$prj->project_team?></td>
                        <td><?=($db->didYouEvaluate($prj->id,$_GET['page']) == true)?'<div class="alert alert-success py-1 px-2 text-center" role="alert">'.$db->getAveragePoints($prj->id,$_GET['page']).'</div>':'<div class="alert alert-danger py-1 px-2 text-center" role="alert">Puanlamadınız</div>';
                            ?></td>
                        <?php if ( $db->didYouEvaluate($prj->id,$_GET['page']) == true ){?>
                            <td><a href="javascript:openEval('<?=$prj->id?>')" class="btn btn-primary w-100">Puanla</a></td>
                        <?php } else { ?>
                            <td><a href="<?=$linking.$prj->id?>" class="btn btn-primary w-100">Puanla</a></td>
                        <?php } ?>
                    </tr>

                    <?php
                    }
                }
            }  ?>


                </tbody>
            </table>
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

<script>
    function openEval(id) {
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: {id: id,getModal: true,linking: '<?=$linking?>'},
            success: function(response){
                // Add response in Modal body
                $('.modal-content').html(response);

                // Display Modal
                $('#quest').modal('show');
            }
        });
    }
</script>
