<?php
require __DIR__."/config/header.php";
$video=false;
switch (preg_replace('/[0-9]+/', '', $_SERVER['REQUEST_URI'])){
    case "/proje-bilgi-dokumani-puanlama/":
        $page = "first_evaluation";
        $name="Proje Bilgi Dokümanı";
        $masterId=1;
        $file = "project_file";
        $test = strtotime($db->wread('settings','settings_key','pbd-last-day')->fetch()['settings_value'] . "+23 Hours 59 minutes 59 Seconds");
        if ($test>strtotime("now") == false){
            echo "<script>window.location.href='/'</script>";;
        }
        break;
    case "/proje-teslim-dokumani-puanlama/":
        $page = "second_evaluation";
        $video=true;
        $name="Proje Teslim Dokümanı";
        $masterId=2;
        $file = "project_file_two";
        $test = strtotime($db->wread('settings','settings_key','ptd-last-day')->fetch()['settings_value'] . "+23 Hours 59 minutes 59 Seconds");
        if ($test>strtotime("now") == false){
            echo "<script>window.location.href='/'</script>";
        }
        break;
}
if (isset($_POST['savePoints'])){
    $juryId = $db->getUserId();
    $projectId = str_replace(".php","",$_POST['projectId']);
    unset($_POST['projectId'],$_POST['savePoints']);
    $eval=serialize($_POST);
    $sqlString = "SELECT COUNT(*) FROM points2 WHERE jury_id = {$juryId} AND project_id = {$projectId} AND step = '$page'";
    $checkIfExists = $db->qSql($sqlString)->fetchColumn();
    if ($checkIfExists == 1){
        $dbSave = $db->qSql("UPDATE points2 SET evaluation = '{$eval}' WHERE jury_id = {$juryId} AND project_id = {$projectId} AND step = '$page'");
    }else{
        $dbSave = $db->insert("points2",[
                'project_id'=>$projectId,
            'jury_id' => $juryId,
            'evaluation' => $eval,
            'step' => $page
        ]);
    }
    $avg = $db->getAveragePoints($projectId,$masterId);
    $db->qSql("UPDATE points2 SET calc = {$avg} WHERE jury_id = {$juryId} AND project_id = {$projectId} AND step = '$page'");
}

$projectId = str_replace(".php","",basename($_GET['id']));
$juryId = $db->getUserId();
$checkIfExists = $db->qSql("SELECT COUNT(*) FROM points2 WHERE jury_id = {$juryId} AND project_id = {$projectId} AND step = '$page'")->fetchColumn();
if ($checkIfExists == 1){
    $oldPoints = unserialize($db->qSql("SELECT evaluation FROM points2 WHERE jury_id = {$juryId} AND project_id = {$projectId} AND step = '$page'")->fetchColumn());
}
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0"><?=$name?> Puanlama Ekranı</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
                        <div class="container-fluid">
                            <!-- Small boxes (Stat box) -->

                            <?php
                            $d = str_replace(".php","",$_GET['id']);
                            $prj = $db->qSql("SELECT * FROM projects WHERE
                    projects.id = '{$d}' ")->fetch(PDO::FETCH_ASSOC);


                        ?>      <div class="mb-3 border-bottom border-secondary">
                                <h3><?=$prj['project_name']." - ".$prj['project_team']?></h3>
                        </div>
                        <div class="row" style="position: relative">
                        <div class="col-12 col-md-8" style="height: auto;display: flex;flex-direction: row;flex-wrap: wrap;">
                            <?php
                            if ($video){
                            ?><div class="row mb-3 w-100 justify-content-around">
                            <?php
                            if (!empty($prj['project_video_one'])){?>
                                <div class="col-4" style="height: fit-content;">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video controls class="embed-responsive-item">
                                            <?php
                                            $mime1 = finfo_file(finfo_open(FILEINFO_MIME_TYPE), __DIR__."/files/".$prj['project_video_one']);
                                            ?>
                                            <source src="/files/<?=$prj['project_video_one']?>" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                                <?php
                            }
                                if (!empty($prj['project_video_two'])){?>
                                <div class="col-4" style="height: fit-content;">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video controls class="embed-responsive-item">
                                            <?php
                                            $mime2 = finfo_file(finfo_open(FILEINFO_MIME_TYPE), __DIR__."/files/".$prj['project_video_two']);
                                            ?>
                                            <source src="/files/<?=$prj['project_video_two']?>" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                            <?php }
                                if (!empty($prj['project_video_three'])){?>
                                <div class="col-4" style="height: fit-content;">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video controls class="embed-responsive-item">
                                            <?php
                                            $mime3 = finfo_file(finfo_open(FILEINFO_MIME_TYPE), __DIR__."/files/".$prj['project_video_three']);
                                            ?>
                                            <source src="/files/<?=$prj['project_video_three']?>" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                            <?php } ?>
                    </div>
                            <?php
                            } ?>
                            <iframe class="w-100" style="height: 70vh;" src="/files/<?=$prj[$file]?>"></iframe>
                        </div>
                        <div class="col-12 col-md-4" style="height: 100%">
                            <div class="alert alert-danger position-sticky" role="alert" id="displayCurrentPoints">
                                Proje için puanınız: <span></span>
                            </div>
                            <div style="height: 85vh; overflow-y: auto; overflow-x: hidden">

                                <form action="" method="post">
                                    <input type="hidden" name="projectId" value="<?=str_replace(".php","",basename($_GET['id']))?>">
                                    <?php
                                    $query = $db->qSql("
                            SELECT
	evaluation.evaluation_id, 
	evaluation.evaluation_question, 
	evaluation.evaluation_group_id, 
	evaluation_groups.evaluation_group_id AS eval_groups_id, 
	evaluation_groups.evaluation_group_value, 
	evaluation_groups.evaluation_group_percentage, 
	evaluation.evaluation_short_key
FROM
	evaluation
	INNER JOIN
	evaluation_groups
	ON 
		evaluation.evaluation_group_id = evaluation_groups.evaluation_group_id WHERE evaluation_groups.evaluation_group_master_id = $masterId")->fetchAll(PDO::FETCH_ASSOC);
                                    $FormBuilder = array();
                                    foreach ($query as $q){
                                        $FormBuilder[$q['eval_groups_id']][$q['evaluation_id']] = ['key'=>$q['evaluation_short_key'],'q'=>$q['evaluation_question']];
                                        $FormBuilder[$q['eval_groups_id']]['groupId'] = $q['evaluation_group_id'];
                                    }
                                    foreach ($FormBuilder as $fb){
                                        $grp = $db->wread("evaluation_groups","evaluation_group_id",$fb['groupId'])->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <h3 class="mt-3"><?=$grp['evaluation_group_value'] . " - ". $grp['evaluation_group_percentage'] . "%"?></h3>
                                        <div class="row groupsOfEval" data-gid="<?=$fb['groupId']?>" data-percentage="<?=$grp['evaluation_group_percentage']?>">
                                            <?php
                                            $curGID=$fb['groupId'];
                                            unset($fb['groupId']);
                                            foreach ($fb as $item){
                                                ?>
                                                <div class="col-12 col-md-9"><?=$item['q']?></div>
                                                <div class="col-12 col-md-3"><input type="number" name="<?=$item['key']?>" min="0" max="10"  value="<?=(isset($oldPoints))?$oldPoints[$item['key']]:""?>"></div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" style="display: none;" value="" name="savePoints">
                                    <a class="btn btn-primary w-100 mt-3" href="javascript:void(0)" id="savePoints">Puanlamayı Kaydet</a>
                                </form>
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
<script>
    $(".groupsOfEval input").on("change",function () {
        if (0 > $(this).val() || $(this).val() > 11){
            $(this).val("");
            $(this).focus();
            alert("Değerlendirme 0 dan küük ve 10 dan büyük olamaz");
        }
        getsPoints();
    });
    $("#savePoints").on("click",function () {
        let test = getsPoints();
        if (isNaN(test)){
            alert("Lütfen puanlamanızı kontrol ediniz");
        } else {
            $('#savePoints').closest("form").submit();
        }
    });
    $(function () {
        getsPoints();

    });
    function getsPoints() {
        let object = {};
        let subtotal = parseFloat(0);
        $(".groupsOfEval").each(
            function () {
                //console.log($(this).attr('data-gid') + " - " + $(this).attr('data-percentage'));
                let gid = $(this).attr('data-gid');
                object[gid] = {
                    percentage:  parseInt($(this).attr('data-percentage')),
                    valuex: []
                };
                $(".groupsOfEval[data-gid='"+gid.toString()+"']  input").each(
                    function (){
                        object[gid].valuex.push(parseInt(this.value));
                    }
                );
            }
        );

        for (const [key, value] of Object.entries(object)) {
            object[key].valuex.forEach(function (value) {
                 subtotal += parseFloat(parseFloat(parseFloat(value) / parseFloat(10)) * parseFloat(parseFloat(object[key].percentage) / parseFloat(object[key].valuex.length)));
            });

        }$("#displayCurrentPoints span").html(subtotal.toFixed(2));
        return subtotal;
    }
</script>
