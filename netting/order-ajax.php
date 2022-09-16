<?php
require_once 'class.crud.php';
$db=new crud();

if (isset($_GET['order'])) {

    $sonuc=$db->orderUpdate($_GET['table'],$_POST['item'],"sort","id");

    // $returnMsg=array();
    $returnMsg= ['islemSonuc' => true, 'islemMsj' => $sonuc['status']];
    echo json_encode($returnMsg);
}
