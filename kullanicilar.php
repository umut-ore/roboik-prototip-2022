<?php
require __DIR__."/config/header.php";
if (isset($_GET['delete'])){
    $db->delete("phpauth_users","id",$_GET['delete']);
}
if (isset($_GET['active'])){
    $a = $db->wread("users","user_id",$_GET['active'])->fetch()['active'];
    $b = ($a == 0)?1:0;
    $db->qSql("UPDATE users SET active={$b} WHERE user_id={$_GET['active']} ");
}
if (isset($_POST['addUser'])){
    $db->registerUser($_POST);
    $d = $db->wread("phpauth_users","email",$_POST['email'])->fetch(PDO::FETCH_ASSOC);
    $db->insert("users",['user_id'=>$d['id'],'name'=>$_POST['name'],'role'=>$_POST['role'],'active'=>$_POST['active']]);
    echo "<script>window.location=window.location.href;</script>";
}
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) --><div class="content-header w-100 text-light px-4">
            <h1 class="m-0">Kullanıcı Ekle</h1>
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content p-3 w-100">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <form action="" method="post">
                    <div class="form-row text-left">
                        <div class="form-group col-12 col-md-4">
                            <label for="name">Adı Soyadı</label>
                            <input type="text" class="form-control" id="name" name="name" aria-describedby="name" required>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label for="email">E-Posta</label>
                            <input type="email" class="form-control" id="email" name="email" aria-describedby="email" required>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label for="password">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" aria-describedby="password" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="role">Kullanıcı Rolü</label>
                            <select class="form-control" id="role" name="role">
                                <option value="1" selected>Jüri Üyesi</option>
                                <option value="2">Hakem Üyesi</option>
                                <option value="3">Admin</option>
                           </select>
                        </div>
                        <div class="form-group col-4">
                            <label for="active">Kullanıcı Durumu</label>
                            <select class="form-control" id="active" name="active">
                                <option value="0" selected>Pasif</option>
                                <option value="1">Aktif</option>
                           </select>
                        </div>

                        <div class="form-group col-12 col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100" name="addUser">Ekle</button>
                        </div>
                    </div>
                </form>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <div class="content-header w-100 text-light px-4 mt-4">
            <h1 class="m-0">Kullanıcı Modülü</h1>
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content p-3 w-100">
            <div class="container-fluid">
                <h2>Kullanıcılar</h2>
                <table id="myTable" class="dataTable dataTables_scrollBody table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                    <tr>
                        <th style="width: 25px">#</th>
                        <th>Adı Soyadı</th>
                        <th>Organizasyon</th>
                        <th>Kullanıcı E-Posta</th>
                        <th>Kullanıcı Rolü</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $i=1;
                    $userList = $db->qSql("SELECT
	phpauth_users.id, 
	phpauth_users.email, 
	users.`name`, 
	users.`active`, 
	users.role,
       users.affiliation
FROM
	phpauth_users
	INNER JOIN
	users
	ON 
		phpauth_users.id = users.user_id")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($userList as $user){
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=$user['name']?></td>
                            <td><?=$user['affiliation']?></td>
                            <td><?=$user['email']?></td>
                            <td><?php if ($user['role']==3) echo "Yönetici"; elseif ($user['role'] == 2) echo "Hakem Üyesi"; elseif($user['role'] == 1)echo "Jüri Üyesi";?></td>
                            <td><a href="?active=<?=$user['id']?>" class="btn btn-<?=($user['active'] == 1)?'success':'danger'?>"><?=($user['active'] == 1)?'Aktif':'Pasif'?></a></td>
                            <td><a href="?delete=<?=$user['id']?>" class="btn btn-danger"><i class="fas fa-trash"></i></a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
<?php
require __DIR__."/config/footer.php";
?>