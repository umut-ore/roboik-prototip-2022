<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require __DIR__."/netting/class.crud.php";
$db = new crud();
$prj = $db->qSql("SELECT * FROM projects")->fetchAll();
        foreach ($prj as $p){
            ?>
            <?php
            $first = 0;
            $second = 0;
            $third=0;
            if($db->getAveragePoints($p['id'],1,true) !== 0 ){($first = $db->getAveragePoints($p['id'],1,true)*125/1000);}
            if($db->getAveragePoints($p['id'],2,true) !== 0){$second = $db->getAveragePoints($p['id'],2,true)*225/1000;}
            if ($db->getAveragePointsJudge($p['id'],true) !== 0){$third = $db->getAveragePointsJudge($p['id'],true)*650/1000;}
            $allAvg = $first+$second+$third;
            $db->qSql("UPDATE projects SET project_avg = {$allAvg} WHERE id={$p['id']}");
        }
$prj = $db->qSql("SELECT * FROM projects ORDER BY project_avg DESC LIMIT 10")->fetchAll();
        $prjts = [];
foreach ($prj as $p){
    if($db->getAveragePoints($p['id'],1,true) !== 0 ){($first = $db->getAveragePoints($p['id'],1,true)*125/1000);}
    if($db->getAveragePoints($p['id'],2,true) !== 0){$second = $db->getAveragePoints($p['id'],2,true)*225/1000;}
    if ($db->getAveragePointsJudge($p['id'],true) !== 0){$third = $db->getAveragePointsJudge($p['id'],true)*650/1000;}
    $allAvg = $first+$second+$third;
    $a = [
        'id'=>$p['id'],
        'projectName'=>$p['project_name'],
        'projectTeam'=>$p['project_team'],
        'scoreOne'=>$db->getAveragePoints($p['id'],1,true),
        'scoreTwo'=>$db->getAveragePoints($p['id'],2,true),
        'scoreJudge'=>$db->getAveragePointsJudge($p['id'],true),
        'scoreAvg'=>$allAvg
    ];
    $prjts[] = $a;
}
echo json_encode($prjts)
?>