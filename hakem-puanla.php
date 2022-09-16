<?php
require __DIR__."/config/header.php";


 if (isset($_POST['savePoints'])){
    $juryId = $db->getUserId();
    $projectId = $_POST['projectId'];
    unset($_POST['projectId'],$_POST['savePoints']);
    $eval=serialize($_POST);
    $checkIfExists = $db->qSql("SELECT COUNT(*) FROM points_judge WHERE jury_id = {$juryId} AND project_id = {$projectId}")->fetchColumn();
    if ($checkIfExists == 1){
        $dbSave = $db->qSql("UPDATE points_judge SET evaluation = '{$eval}' WHERE jury_id = {$juryId} AND project_id = {$projectId}");
    }else{
        $dbSave = $db->insert("points_judge",[
                'project_id'=>$projectId,
            'jury_id' => $juryId,
            'evaluation' => $eval,
        ]);
    }
}

if (isset($_POST['saveTimes'])){
    $juryId = $db->getUserId();
    $projectId = $_POST['projectId'];
    unset($_POST['projectId'],$_POST['saveTimes']);
    $time=serialize($_POST);
    $checkIfExists = $db->qSql("SELECT COUNT(*) FROM points_judge WHERE jury_id = {$juryId} AND project_id = {$projectId}")->fetchColumn();
    if ($checkIfExists == 1){
        $dbSave = $db->qSql("UPDATE points_judge SET durations = '{$time}' WHERE jury_id = {$juryId} AND project_id = {$projectId}");
    }else{
        $dbSave = $db->insert("points_judge",[
                'project_id'=>$projectId,
            'jury_id' => $juryId,
            'durations' => $time,
        ]);
    }
}

$projectId = str_replace(".php","",$_GET['id']);
$juryId = $db->getUserId();
$checkIfExists = $db->qSql("SELECT COUNT(*) FROM points_judge WHERE jury_id = {$juryId} AND project_id = {$projectId}")->fetchColumn();
if ($checkIfExists == 1){
    $oldPoints = unserialize($db->qSql("SELECT evaluation FROM points_judge WHERE jury_id = {$juryId} AND project_id = {$projectId}")->fetchColumn());
    $oldTimes = unserialize($db->qSql("SELECT durations FROM points_judge WHERE jury_id = {$juryId} AND project_id = {$projectId}")->fetchColumn());
}

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
            <!-- Small boxes (Stat box) -->
            <?php
            $prj = $db->qSql("SELECT * FROM projects WHERE
	projects.id = '{$_GET['id']}' ")->fetch(PDO::FETCH_ASSOC);


            ?>      <div class="mb-3 border-bottom border-secondary">
                    <h3><?=$prj['project_name']." - ".$prj['project_team']?></h3>
            </div>
                    <div class="row">
                        <div class="col-12">
                            <!--<div class="alert alert-info" role="alert" id="displayCurrentPoints">
                                Proje için puanınız: <span></span>
                            </div>-->
                            <form action="" method="post">
                                <input type="hidden" name="projectId" value="<?=$projectId?>">
                                <?php
                                $a = $db->read("evaluation_groups_judge")->fetchAll();
                                foreach ($a as $b){
                                    if ($b['evaluation_group_judge_static']==0){
                                    ?>
                                    <div class=" row justify-content-between pb-3">
                                        <div class="col-md-3 col-12">
                                            <?=$b['evaluation_group_judge_value']?>
                                        </div>
                                        <div class="col-md-3 col-12"> Parkur Süresi: <input type="number" id="total-duration-<?=$b['evaluation_group_judge_id']?>" disabled value="<?=$b['evaluation_group_judge_duration']?>"> Saniye
                                        </div>
                                        <div class="col-md-3 col-12">
                                            Geçirilen Süre: <input type="number" onchange="durationChangeHandler(<?=$b['evaluation_group_judge_id']?>)" name="<?=$b['evaluation_group_judge_id']?>" id="spent-duration-<?=$b['evaluation_group_judge_id']?>" value="<?=(isset($oldTimes))?$oldTimes[$b['evaluation_group_judge_id']]:0?>">
                                        </div>
                                        <div class="col-md-3 col-12">
                                            Kalan Süre: <input type="number" disabled id="left-duration-<?=$b['evaluation_group_judge_id']?>" value="<?=(isset($oldTimes))?$b['evaluation_group_judge_duration']-$oldTimes[$b['evaluation_group_judge_id']]:$b['evaluation_group_judge_duration']?>">
                                        </div>

                                    </div>

                                <?php
                                }else{
                                        echo "<input type='number' disabled value='1'  name='{$b['evaluation_group_judge_id']}' style='display: none'>";
                                    }
                                }
                                ?>
                                <script>
                                    const durationChangeHandler = (id) => {
                                        const total = $('#total-duration-'+id);
                                        const spent = $('#spent-duration-'+id);
                                        const left = $('#left-duration-'+id);

                                        left.val(+total.val() - spent.val());
                                    }
                                </script>
                                <button class="btn btn-primary w-100 mt-3" type="submit" name="saveTimes">Puanlamayı Kaydet</button>
                            </form>

                            <form action="" method="post">
                                <input type="hidden" name="projectId" value="<?=$projectId?>">
                            <?php
                            $query = $db->qSql("
                            SELECT
	evaluation_judge.evaluation_judge_id, 
	evaluation_judge.evaluation_judge_question, 
	evaluation_judge.evaluation_judge_group_id, 
	evaluation_groups_judge.evaluation_group_judge_id AS eval_groups_id, 
	evaluation_groups_judge.evaluation_group_judge_value, 
	evaluation_groups_judge.evaluation_group_judge_static, 
	evaluation_judge.evaluation_judge_point, 
	evaluation_judge.evaluation_judge_negative, 
	evaluation_judge.evaluation_judge_short_key
FROM
	evaluation_judge
	INNER JOIN
	evaluation_groups_judge
	ON 
		evaluation_judge.evaluation_judge_group_id = evaluation_groups_judge.evaluation_group_judge_id")->fetchAll(PDO::FETCH_ASSOC);
                            $FormBuilder = array();
                            foreach ($query as $q){
                                $FormBuilder[$q['eval_groups_id']][$q['evaluation_judge_id']] = ['key'=>$q['evaluation_judge_short_key'],'q'=>$q['evaluation_judge_question'],'negative'=>$q['evaluation_judge_negative'],'point'=>$q['evaluation_judge_point'],'static'=>$q['evaluation_group_judge_static']];
                                $FormBuilder[$q['eval_groups_id']]['groupId'] = $q['evaluation_judge_group_id'];
                            }
                            foreach ($FormBuilder as $fb){
                                $grp = $db->wread("evaluation_groups_judge","evaluation_group_judge_id",$fb['groupId'])->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <div>
                                    <h3 class="mt-3"><?=$grp['evaluation_group_judge_value']?>  </h3>
                                </div>
                                <div class="row groupsOfEval" data-gid="<?=$fb['groupId']?>"">
                                    <?php
                                    $curGID=$fb['groupId'];
                                    unset($fb['groupId']);
                                    $valX = 0;
                                    foreach ($fb as $item){
                                        //if ($item['static'] != 1) if ($item['negative'] != 1) $valX += (isset($oldPoints))?((isset($oldPoints[$item['key']]))?$oldPoints[$item['key']]:0):0;  else $valX -= (isset($oldPoints))?((isset($oldPoints[$item['key']]))?$oldPoints[$item['key']]:0):0;
                                        ?>
                                    <div class="col-12 col-md-9"><?=$item['q']?></div>
                                    <div class="col-12 col-md-3 text-right"><input type="number" data-init="<?=($fb[array_keys($fb)[0]]['static'] != 1)?'pointsChangeHandler':($fb[array_keys($fb)[0]]['negative'] == 1?'punPointsHandler':'addPointsHandler')?>(<?=$curGID?>)" onchange="<?=($fb[array_keys($fb)[0]]['static'] != 1)?'pointsChangeHandler':($fb[array_keys($fb)[0]]['negative'] == 1?'punPointsHandler':'addPointsHandler')?>(<?=$curGID?>)" class="group-of-points-<?=$curGID?>" name="<?=$item['key']?>" min="0"  data-size='<?=$item['point']?>' data-negative='<?=$item['negative']?>' value="<?=(isset($oldPoints))?((isset($oldPoints[$item['key']]))?$oldPoints[$item['key']]:0):0?>"></div>
                                    <?php } ?>
                                    <?php
                                    if ($fb[array_keys($fb)[0]]['static'] != 1){
                                    ?>
                                        <div class="col-12 col-md-7 offset-md-5 mt-3 text-right">
                                            Görevden alınan toplam puan X (1 + (İlgili Parkurda Kalan Süre/İlgili Parkurun Toplam Süresi))
                                            <br>
                                            Görevin Puanı: <input type="number" value="0" id="points-of-<?=$curGID?>" disabled>
                                        </div>
                                <?php } else {
                                        if ($fb[array_keys($fb)[0]]['negative'] == 1){
                                        ?>
                                <div class="col-12 col-md-7 offset-md-5 mt-3 text-right">
                                    Toplam Ceza Puanı : <input type="number" value="0" id="punPoints" disabled>
                                </div>
                                <?php
                                        } else {
                                            ?>
                                            <div class="col-12 col-md-7 offset-md-5 mt-3 text-right">
                                                Toplam Ek Puanı : <input type="number" value="0" id="addPoints" disabled>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        <script>
                            const pointsChangeHandler = (gid) => {
                              const inputs = $('.group-of-points-'+gid);
                              let points = [];
                                points = inputs.map(function(idx, elem) {
                                    if (elem.dataset.negative == true){
                                        return -elem.value;
                                    } else {
                                       return +elem.value;
                                    }

                                }).get();
                                let sum = 0;

                                for (let i = 0; i < points.length; i++) {
                                    sum += points[i];
                                }
                                let totalDuration = $('#total-duration-'+gid).val();
                                let leftDuration = $('#left-duration-'+gid).val();
                                let calc = $('#points-of-'+gid)
                                calc.val(parseFloat(sum*(1+(leftDuration/totalDuration))).toFixed(2));
                                totalPointsCalc();
                            }

                            const punPointsHandler = (gid) => {
                                const inputs = $('.group-of-points-'+gid);
                                let points = [];
                                points = inputs.map(function(idx, elem) {
                                    return -elem.value;
                                }).get();
                                let sum = 0;

                                for (let i = 0; i < points.length; i++) {
                                    sum += points[i];
                                }

                                $('#punPoints').val(parseFloat(sum).toFixed(2));
                                totalPointsCalc();
                            }

                            const addPointsHandler = (gid) => {
                                const inputs = $('.group-of-points-'+gid);
                                let points = [];
                                points = inputs.map(function(idx, elem) {
                                    return +elem.value;
                                }).get();
                                let sum = 0;

                                for (let i = 0; i < points.length; i++) {
                                    sum += points[i];
                                }

                                $('#addPoints').val(parseFloat(sum).toFixed(2));
                                totalPointsCalc();
                            }


                            const totalPointsCalc = () => {
                                const inputs = $("input[id*='points-of-']");
                                let points = [];
                                points = inputs.map(function(idx, elem) {
                                    return +elem.value;
                                }).get();
                                let sum = 0;

                                for (let i = 0; i < points.length; i++) {
                                    sum += points[i];
                                }
                                let punPoint = $('#punPoints').val();
                                let addPoint = $('#addPoints').val();

                                $('#totalPoints').text(parseFloat(sum+parseInt(punPoint)+parseInt(addPoint)).toFixed(2));
                            }
                            $(function () {
                                $('[data-init]').each(function(){
                                    eval($(this).data('init'));
                                });
                            })
                        </script>
                                <div class="alert alert-warning">
                                    Toplam Puan: <span id="totalPoints"></span>
                                </div>
                                <button class="btn btn-primary w-100 mt-3" type="submit" name="savePoints">Puanlamayı Kaydet</button>
                            </form>
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
        getsPoints();
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
