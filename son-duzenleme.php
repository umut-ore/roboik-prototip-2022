<?php
require __DIR__."/config/header.php";
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
    <div class="content-header w-100 text-light px-4">
            <h1 class="m-0">Son Düzenleme Tarih Ayarı</h1>
    </div>
        <!-- /.content-header -->
    <!-- Main content -->
    <section class="content p-3 w-100">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <form action="" method="post">
                        <div class="form-row text-left">
                            <div class="form-group col">
                                <?php
                                if (isset($_POST['pbdEdit'])){
                                    $x = $db->update("settings",[
                                        'settings_value'=>date('d.m.Y',strtotime($_POST['pbdLastDay'])),
                                        'settings_key'=>'"pbd-last-day"'
                                    ],['columns'=>'settings_key']);

                                }
                                ?>
                                <label for="pbdLastDay">Proje Bilgi Dokümanı Son Tarihi</label>
                                <input type="date" class="form-control" id="pbdLastDay" name="pbdLastDay" aria-describedby="pbdLastDay" required value="<?=date('Y-m-d',strtotime($db->wread('settings','settings_key','pbd-last-day')->fetch()['settings_value']))?>">
                            </div>
                            <div class="form-group col-4">
                                <div class="py-3"></div>
                                <button type="submit" class="btn btn-primary w-100" name="pbdEdit">Tarihi Güncelle</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6">
                    <form action="" method="post">
                        <div class="form-row text-left">
                            <div class="form-group col">
                                    <?php
                                    if (isset($_POST['ptdEdit'])){
                                        $x = $db->update("settings",[
                                            'settings_value'=>date('d.m.Y',strtotime($_POST['ptdLastDay'])),
                                            'settings_key'=>'"ptd-last-day"'
                                        ],['columns'=>'settings_key']);

                                    }
                                    ?>
                                    <label for="ptdLastDay">Proje Teslim Dokümanı Son Tarihi</label>
                                    <input type="date" class="form-control" id="ptdLastDay" name="ptdLastDay" aria-describedby="ptdLastDay" required value="<?=date('Y-m-d',strtotime($db->wread('settings','settings_key','ptd-last-day')->fetch()['settings_value']))?>">
                            </div>
                        <div class="form-group col-4">
                            <div class="py-3"></div>
                            <button type="submit" class="btn btn-primary w-100" name="ptdEdit">Tarihi Güncelle</button>
                        </div>

                        </div>
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