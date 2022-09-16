<?php /** @noinspection ALL */
/** @noinspection SpellCheckingInspection */
require_once 'dbconfig.php';
require_once 'vendor/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
class crud
{
    private PDO $db;
    private string          $DBHost             = DBHOST;
    private string          $DBUser             = DBUSER;
    private string          $DBPass             = DBPWD;
    private string          $DBName             = DBNAME;
    private PHPAuthConfig   $config;
    private PHPAuth         $auth;
    function __construct()
    {
        try {
            $this->db = new PDO("mysql:host=$this->DBHost;dbname=$this->DBName;charset=utf8", $this->DBUser, $this->DBPass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->config = new PHPAuthConfig($this->db,'','','tr_TR');
            $this->auth = new PHPAuth($this->db, $this->config);
        } catch (Exception $error) {
            die("Bağlantı Başarısız: " . $error->getMessage());
        }
    }
    public function dumpDB(){
        try {
            $dump = new IMysqldump\Mysqldump("mysql:host=$this->DBHost;dbname=$this->DBName;charset=utf8", $this->DBUser, $this->DBPass);
            $name = date('Y-m-d__H-i-s',strtotime('now'))."__".mt_rand(1000,9999)."__mysqldump.sql";
            $dump->start(__DIR__."/dbBackups/".$name);
            return $name;
        } catch (\Exception $e) {
            exit();
        }
    }
    public function checkInput($input){
        if (isset($input) && !is_null($input) && !empty($input)){
            return true;
        } else {
            return false;
        }
    }
    public function loginCheck($hash){
        if (!$this->auth->isLogged()) {
            return ['status'=>false];
        }else{
            $_COOKIE['uid'] = $this->auth->getUID($hash);
            return ['status'=>true,'uid'=>$this->auth->getUID($hash)];
        }
    }
    public function logoutUser($hash){
        $this->auth->logout($hash);
        unset($_COOKIE['uid']);
    }
    public function changePass($curPass,$newPass){
        return $this->auth->changePassword( $this->auth->getCurrentUser()['id'],$curPass,$newPass,$newPass);
    }
    public function registerUser($postData){
        try {
            $email = $postData['email'];
            $password = $postData['password'];
            $repeatpassword = $postData['password'];
            $result = $this->auth->register($email, $password, $repeatpassword);
            return ['status' => true,'msg'=>$result];
        } catch (Exception $exception){
            return ['status' => false];

        }
    }
    public function getSessionEmail(){
        return $this->wread("phpauth_users",'id',$this->auth->getCurrentUser()['id'])->fetch()['email'];
    }
    public function getUserId(){
        return $this->auth->getCurrentUser()['id'];
    }
    public function loginUser($postData){

        try {
            $email = $postData['email'];
            $password = $postData['password'];
            $rememberMe = (isset($postData['remember']))?1:0;
            $result = $this->auth->login($email, $password,$rememberMe);
            if ($result['error']==false)$status=true; else $status=false;
            return ['status'=>$status,'err'=>$result];
        } catch (Exception $exception){

        }
    }
    public function adminsLogin($admins_username, $admins_pass)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE admins_username=? AND admins_status=1");
            $stmt->execute(array(htmlspecialchars($admins_username)));
            if ($stmt->rowCount()==1) {
                $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
                $er_pwd = $admins_pass;                     //PASSWORD FROM POST
                $db_pwd = $stmt['admins_pass'];                    //ENCRYPTED PASSWORD FROM DB
                $er_pwd_encode = urlencode($er_pwd);            // URL ENCODED POST PASSWORD
                $password = crypt($er_pwd_encode,$db_pwd);
                $query = $this->db->prepare("SELECT * FROM admins WHERE admins_username=? AND admins_pass=?");
                $query->execute([$admins_username,$password]);
                if (password_verify($er_pwd_encode, $db_pwd)) {
                    if ($stmt['admins_file'] != ""){
                        $file = $stmt['admins_file'];
                    }else{
                        $file = "user.jpg";
                    }
                    $_SESSION["admins"] = [
                        "admins_username" => $admins_username,
                        "admins_namesurname" => $stmt['admins_namesurname'],
                        "admins_file" => $file,
                        "admins_id" => $stmt['admins_id'],
                        "admins_level" => $stmt['admins_level']
                    ];
                    return ['status' => TRUE];
                } else {
                    return ['status' => FALSE];
                }
            }
        } catch (Exception $error) {
            return ['status' => FALSE, "error" => $error->getMessage()];
        }
        return true;
    }
    public function read($table,$options=[]) {
        try {
            if (isset($options['columns_name']) && empty($options['limit']) && !isset($options['today'])){
                $stmt = $this->db->prepare("SELECT * FROM $table ORDER BY {$options['columns_name']} {$options['columns_sort']}");
            } elseif(isset($options['columns_name']) && isset($options['limit']) && !isset($options['today'])){
                $stmt = $this->db->prepare("SELECT * FROM $table ORDER BY {$options['columns_name']} {$options['columns_sort']} LIMIT {$options['limit']}");
            } elseif(isset($options['columns_name']) && isset($options['limit']) && isset($options['today'])){
                $date = $options['date'];
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$options['today']} {$options['operator']} \"{$date}\" ORDER BY {$options['columns_name']} {$options['columns_sort']} LIMIT {$options['limit']}");
            }else {
                $stmt = $this->db->prepare("SELECT * FROM $table");
            }
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            return ['status' => FALSE, 'error' => $e->getMessage()];
        }
    }
    public function wread($table,$columns,$values,$options=[]) {
        try {
            if (isset($options['columns_name']) && empty($options['limit']) && !isset($options['today'])){
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE $columns=? ORDER BY {$options['columns_name']} {$options['columns_sort']}");
            } elseif(isset($options['columns_name']) && isset($options['limit']) && !isset($options['today'])){
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE $columns=? ORDER BY {$options['columns_name']} {$options['columns_sort']} LIMIT {$options['limit']}");
            } elseif(isset($options['columns_name']) && isset($options['limit']) && isset($options['today']) && isset($options['offset'])){
                $date = $options['date'];
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$options['today']} {$options['operator']} \"{$date}\" AND $columns=? ORDER BY {$options['columns_name']} {$options['columns_sort']} LIMIT {$options['limit']} OFFSET {$options['offset']}");
            } elseif(isset($options['columns_name']) && isset($options['limit']) && isset($options['today'])){
                $date = $options['date'];
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$options['today']} {$options['operator']} \"{$date}\" AND $columns=? ORDER BY {$options['columns_name']} {$options['columns_sort']} LIMIT {$options['limit']}");
            }else {
                $stmt = $this->db->prepare("SELECT * FROM $table WHERE $columns=?");
            }
            $stmt->execute([htmlspecialchars($values)]);
            return $stmt;
        } catch (Exception $e) {
            return ['status' => FALSE, 'error' => $e->getMessage()];
        }
    }
    public function addValue($value): string
    {
        return implode(',',array_map(function ($item){return $item.'=?';},array_keys($value)));
    }
    public function base64ToImage($base64_string,$dir,$file_delete=null): string
    {
        $data = explode(',', $base64_string);
        $ext = "";
        $data[0]=str_replace(";base64","",substr($data[0],"5"));
        switch ($data[0]){
            case "image/png" :
                $ext = "png";
                break;
            case "image/jpeg" :
                $ext = "jpg";
                break;
            default:
                echo "none";
                break;
        }
        $filename = uniqid().".".$ext;
        $file = fopen(__DIR__."/../img/$dir/$filename", "wb");
        fwrite($file, base64_decode($data[1]));
        fclose($file);
        if ($file_delete != null){
            unlink(__DIR__."/../img/$dir/$file_delete");
        }
        return $filename;
    }
    public function insert($table,$values,$options=[]): array
    {
        try {
            if (!empty($_POST['file'])){
                $base64 = $_POST['file'];
                unset($values[$options['file_name']]);
                $filename = $this->base64ToImage($base64,$options['dir']);
                $values+=[$options['file_name'] => $filename];
                unset($values['file']);
            } else {
                unset($values['file']);
            }
            if ( isset( $_FILES['pdfFile'] ) ) {
                if ($_FILES['pdfFile']['type'] == "application/pdf") {
                    $source_file = $_FILES['pdfFile']['tmp_name'];
                    $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".pdf";
                    $dest_file = __DIR__."/../files/".$randName;
                    move_uploaded_file( $source_file, $dest_file);
                    $values+=[$options['file_name'] => $randName];

                }
            }




            if (isset($options['form_name']))unset($values[$options['form_name']]);
            if (isset($options['pass'])){
                $values[$options['pass']] = password_hash($values[$options['pass']],PASSWORD_BCRYPT);
            }
            if (isset($options['url'])){
                $url = $table."_url";
                $values[$url] = $options['url'];
            }
            $stmt=$this->db->prepare("INSERT INTO {$table} SET {$this->addValue($values)}");
            $stmt->execute(array_values($values));
            return ['status'=>true];
        } catch (Exception $e){
            return['status'=>FALSE,'error'=>$e->getMessage()];
        }
    }
    public function update($table,$values,$options=[]): array
    {
        try {

            if (!empty($_POST['file'])){
                $base64 = $_POST['file'];
                unset($values[$options['file_name']]);
                $filename = $this->base64ToImage($base64,$options['dir'],$values[$options['file_delete']]);
                $values+=[$options['file_name'] => $filename];
                unset($values['file']);
            } elseif(isset($_POST['setting_delete'])) {
                $values+=[$options['file_name'] => ""];
                unlink("../img/{$options['dir']}/{$values[$options['file_delete']]}");
                unset($values['file']);
            } elseif (!isset($_POST['setting_delete'])){
                unset($values['file']);
            }

            if ( isset( $_FILES['pdfFile'] ) ) {
                if ($_FILES['pdfFile']['type'] == "application/pdf") {
                    $source_file = $_FILES['pdfFile']['tmp_name'];
                    $randName=date("Y_m_d-H_i_s-").mt_rand(1000,9999).".pdf";
                    $dest_file = __DIR__."/../files/".$randName;
                    move_uploaded_file( $source_file, $dest_file);
                    $values+=[$options['file_name'] => $randName];
                    if (isset($options['unset']) && !empty($options['unset'])){
                        unlink(__DIR__."/../files/".$options['unset']);
                    }
                }
            }
            if (isset($options['file_delete'])) unset($values[$options['file_delete']]);
            if (isset($options['form_name']))unset($values[$options['form_name']]);
            $id=$values[$options['columns']];
            if (isset($options['columns']))unset($values[$options['columns']]);
            if (isset($options['pass'])){
                if (!empty($values[$options['pass']])){
                    $values[$options['pass']] = password_hash($values[$options['pass']],PASSWORD_BCRYPT);
                } else {
                    unset($values[$options['admins_pass']],$options['pass'],$values['admins_pass']);
                }
            }
            if (isset($options['url'])){
                $url = $table."_url";
                $values[$url] = $options['url'];
            }
            if (isset($options['deleteKey'])){
                unset($values[$options['deleteKey']]);
                $stmt=$this->db->prepare("UPDATE $table SET {$this->addValue($values)} WHERE {$options['columns']}={$id}");
                $stmt->execute(array_values($values));
                return ['status'=>true,"delete"=>true];
            } else {
                $stmt=$this->db->prepare("UPDATE $table SET {$this->addValue($values)} WHERE {$options['columns']}={$id}");
                $stmt->execute(array_values($values));
                return ['status'=>true];
            }
        } catch (Exception $e){
            return['status'=>FALSE,'error'=>$e->getMessage()];
        }
    }
    public function delete($table,$column,$value,$filename=null): array
    {
        try {
            if (!empty($filename)){
                unlink("../img/$table/$filename");
            }
            $stmt=$this->db->prepare("DELETE FROM $table WHERE {$column}={$value}");
            $stmt->execute();
            return ['status'=>true];
        }catch (Exception $e){
            return ['status' => FALSE, 'error' => $e->getMessage()];
        }
    }
    public function qSql($sql){
        try {
            $stmt=$this->db->prepare($sql);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e){
            return ['status'=>FALSE,'error'=>$e->getMessage()];
        }
    }
    public function orderUpdate($table,$values,$columns,$orderId): array
    {
        try {
            foreach ($values as $key => $value){
                $stmt=$this->db->prepare("UPDATE $table SET $columns=? WHERE $orderId=?");
                $stmt->execute([$key,$value]);
            }
            return ['status'=>TRUE];
        } catch (Exception $e){
            return ['status'=>FALSE,'error'=>$e->getMessage()];
        }
    }
    public function didYouEvaluate($id,$table){
        $tName;
        switch ($table){
            case 1:
                $tName = "first_evaluation";
                $tableName="points";
                break;
            case 2:
                $tName = "second_evaluation";
                $tableName="points2";
                break;
            case 3:
                $tName = "judge_evaluation";
                break;
            default:
                $tName = "first_evaluation";
                $tableName="points";
                break;
        }
        $aP = $this->qSql("SELECT
	{$tableName}.evaluation
FROM
	projects
	INNER JOIN
	{$tableName}
	ON 
		projects.id = {$tableName}.project_id
		WHERE projects.id={$id} AND {$tableName}.step='{$tName}' AND {$tableName}.jury_id={$this->getUserId()}")->fetchAll();
        return (count($aP) == 1)?true:false;
    }
    public function didYouEvalJudge($id){
        $aP = $this->qSql("SELECT
	points_judge.evaluation
FROM
	projects
	INNER JOIN
	points_judge
	ON 
		projects.id = points_judge.project_id
		WHERE projects.id={$id} AND points_judge.jury_id={$this->getUserId()}")->fetchAll();
        return (count($aP) == 1)?true:false;
    }
    public function getAveragePoints($id,$table,$all=false){
        $tName;
        switch ($table){
            case 1:
                $tName = "first_evaluation";
                $tableName = "points";
                break;
            case 2:
                $tName = "second_evaluation";
                $tableName = "points2";
                break;
            case 3:
                $tName = "judge_evaluation";
                break;
            default:
                $tName = "first_evaluation";
                $tableName = "points";
                break;
        }
        $aPQS = "SELECT
	{$tableName}.evaluation
FROM
	projects
	INNER JOIN
	{$tableName}
	ON 
		projects.id = {$tableName}.project_id
		WHERE projects.id={$id} AND {$tableName}.step='{$tName}'";
        if (!$all){
            $aPQS .= " AND {$tableName}.jury_id={$this->getUserId()}";
        }
        $aP = $this->qSql($aPQS)->fetchAll();
        $juryCount=count($aP);
        if ($juryCount == 0){
            return "Henüz Puanlanmadı";
        }
        $totalOfAll = 0;
        foreach ($aP as $points){
            $points = unserialize($points['evaluation']);
            $getBindings = $this->qSql("SELECT
	evaluation.evaluation_question, 
	evaluation.evaluation_short_key, 
	evaluation_groups.evaluation_group_percentage, 
	evaluation_groups.evaluation_group_value
FROM
	evaluation_groups
	INNER JOIN
	evaluation
	ON 
		evaluation_groups.evaluation_group_id = evaluation.evaluation_group_id WHERE evaluation_groups.evaluation_group_master_id=$table")->fetchAll();
            $subTotal = 0;
            foreach ($getBindings as $gb){
                $getSize = $this->qSql("SELECT
	count(*)
FROM
	evaluation_groups
	INNER JOIN
	evaluation
	ON 
		evaluation_groups.evaluation_group_id = evaluation.evaluation_group_id
		WHERE evaluation_groups.evaluation_group_percentage = {$gb['evaluation_group_percentage']} AND evaluation_groups.evaluation_group_master_id = $table")->fetchColumn();
                $subTotal += ($points[$gb['evaluation_short_key']]/10)*($gb['evaluation_group_percentage']/$getSize);
            }
            $totalOfAll += $subTotal;
        }
        $returning = number_format($totalOfAll/$juryCount, 2,'.',',');
        return (($returning=="0.00"||$returning=="nan")?"Henüz Puanlanmadı":$returning);

    }
    public function getAveragePointsJudge($id,$all=false){
        $aPQS = "SELECT
	points_judge.*, 
	projects.*
FROM
	points_judge
	INNER JOIN
	projects
	ON 
		points_judge.project_id = projects.id
		WHERE projects.id='{$id}'";

        if (!$all){
            $aPQS .= " AND points_judge.jury_id='{$this->getUserId()}'";
        }
        $allData = $this->qSql($aPQS)->fetchAll();
//Puan Grupları
        $pg= $this->read('evaluation_groups_judge')->fetchAll();
        $pointsGroup = [];
        $ptlGrouped = [];
        foreach ($pg as $x){
            $pointsGroup[$x['evaluation_group_judge_id']] = ['id'=>$x['evaluation_group_judge_id'],'duration'=>$x['evaluation_group_judge_duration'],'static'=>$x['evaluation_group_judge_static'],'negative'=>$x['evaluation_group_judge_negative']];
            $ptlGrouped[$x['evaluation_group_judge_id']] = [];
        }

// Puan Tanım listesi
        $ptl = $this->read('evaluation_judge')->fetchAll();

        $i = 0;
        foreach ($ptl as $x){
            $ptlGrouped[$x['evaluation_judge_group_id']][$i] = ['key'=>$x['evaluation_judge_short_key'],'negative'=>$x['evaluation_judge_negative']];
            $i++;
        }

        $Ctotal=0;
        $u = 0 ;
        foreach ($allData as $y){
            $eval = unserialize($y['evaluation']);
            $times = unserialize($y['durations']);
            $evals=[];
            foreach ($eval as $keyH=>$valH){
                $evals[$keyH] = $valH;
            }
            $Xtotal=0;
            foreach ($pointsGroup as $pgx){
                $valX = 0;
                if ($pgx['static'] == 0){
                    foreach ($ptlGrouped[$pgx['id']] as $ptx){
                        if ($ptx['negative'] == 0){
                            $valX += $evals[$ptx['key']];
                        } else {
                            $valX -= $evals[$ptx['key']];
                        }
                    }
                    $parkurKalanSure = $pointsGroup[$pgx['id']]['duration'] - $times[$pgx['id']];
                    $parkurVerilenSure = 0+$pointsGroup[$pgx['id']]['duration'];
                    $Xtotal += $valX * (1 + ($parkurKalanSure / $parkurVerilenSure));
                } else {
                    foreach ($ptlGrouped[$pgx['id']] as $ptxs){
                        if ($pgx['negative'] == 1){
                            $valX -= $evals[$ptxs['key']];
                        } else {
                            $valX += $evals[$ptxs['key']];
                        }
                    }
                    $Xtotal += $valX;
                }
            }
            $u++;
            $Ctotal += $Xtotal;
        }
        $rtn = ($u!=0)?$Ctotal/$u:0;
        $returning = number_format($rtn, 2,'.',',');
        return ($returning=="nan"?"Henüz Puanlanmadı":$returning);

    }
    public function seoUrl($s): string{
        $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',','&');
        $eng = array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','-','-','','ve');
        $s = str_replace($tr,$eng,$s);
        $s = strtolower($s);
        $s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('|-+|', '-', $s);
        $s = preg_replace('/#/', '', $s);
        $s = str_replace('.', '', $s);
        $s = trim($s, '-');
        return $s;
    }
    public function returnErr($status,$successMsg,$errMsg): string{
        $a = "<script>";
        if ($status){
            $a .= "toastr.success('$successMsg');";

        }else{
            $a .= "toastr.error('$errMsg');";
        }
        $a .= "$(function() {
          window.history.replaceState( null, null, '".str_replace('.php','',$_SERVER['PHP_SELF'])."' );
        });";


        $a .= "</script>";
        return $a;
    }
}