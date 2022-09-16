<?php
require __DIR__."/config/settings.php";
if (isset($_POST['type'])){
    if ($_POST['type']==="updateProfileImg"){

        $profile = $db->wread("users",'user_id',$_POST['uid'])->fetch(PDO::FETCH_ASSOC);
        if (!empty($profile['image'])){
            unlink(__DIR__."/img/users/".$profile['image']);
        }
        $newImg=$db->base64ToImage($_POST['img'],"users");
        $db->qSql("UPDATE users SET image='".$newImg."' WHERE user_id='".$_POST['uid']."'");
        $profile = $db->update("users",['user_id'=>$_POST['uid'],'picUploaded'=>1],['columns'=>'user_id']);
        echo json_encode(['status'=>true,'file'=>$newImg]);
    }
}



        if (isset($_POST['getModal'])){
            $data = $db->wread("projects","id",$_POST['id'])->fetch();
            ?>
            <div class="modal-header">
                <h5 class="modal-title">Tekrar puanlamak üzeresiniz.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?=$data['project_name']?> adlı, <?=$data['project_ref_no']?> referans numaralı projeyi tekrar puanlamak istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hayır</button>
                <a href="<?=$_POST['linking'].$_POST['id']?>" class="btn btn-primary">Evet</a>
            </div>
            <?php
        }


        ?>