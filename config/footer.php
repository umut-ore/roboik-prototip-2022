<?php
?>
<footer class="main-footer">
    <strong>Tüm hakları sakldır - <?=date("Y")?> &copy;</strong>
    <div class="float-right d-none d-sm-inline-block">
        <b>Roboik</b> Yönetim Modülü
    </div>
</footer>

</div>
<!-- ./wrapper -->





<?php if (basename($_SERVER['REQUEST_URI']) == "profilim"){?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" integrity="sha512-ooSWpxJsiXe6t4+PPjCgYmVfr1NS5QXJACcR/FPpsdm6kqG1FmQ2SVyg2RXeVuCRBLr0lWHnWJP6Zs1Efvxzww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.min.js" integrity="sha512-V8cSoC5qfk40d43a+VhrTEPf8G9dfWlEJgvLSiq2T2BmgGRmZzB8dGe7XAABQrWj3sEfrR5xjYICTY4eJr76QQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let customAlert = function (message, type, insert) {
            let custId =  Math.floor(Math.random() * 1000);
            let alertObj = "<div class=\"alert alert-"+type+"\" style='margin: 0 15px 30px' role=\"alert\" id='alertToBeRemoved-"+custId+"'>" + message + "</div>";
            $(insert).prepend(alertObj).show("slow");
            setTimeout(
                function()
                {
                    $("#alertToBeRemoved-"+custId).remove();
                }, 2500);
            return true;
        }
        $(function () {
            const updateButton = $("#updateProfileImg");
            let image = $("#profile-photo");
            $("input:file#ppUpload").change(function(e) {
                const ppFileReset = function () {
                    let elem  = $('#ppUpload');
                    elem.wrap('<form>').closest('form').get(0).reset();
                    elem.unwrap();
                    $('#pp-file-span').text("Dosya Seçiniz.");
                }
                updateButton.on("click",function () {
                    let imageData = $('#profile-photo').cropper('getCroppedCanvas').toDataURL(e.target.files[0].type);
                    $.ajax({
                        method: "POST",
                        cache: false,
                        url: '/ajax.php',
                        dataType: "json",
                        data: { type: "updateProfileImg", img:imageData, uid: <?=$db->getUserId()?>},
                        success: function (data) {
                            console.log(data);
                            ppFileReset();
                            image.cropper('destroy');
                            if (data['status'] === true){
                                customAlert("Fotoğrafınız başarıyla güncellendi","success","#errorPP");
                                setTimeout(function () {
                                    window.location=window.location.href;
                                },2500);
                            } else {
                                customAlert("Fotoğrafınız güncellenirken bir hata oluştu, lütfen yönetici ile iletişime geçiniz.","danger","#errorPP");
                            }
                        },
                        complete: function (data){
                            console.log(data);
                        }
                    });
                });
                let fileProp = $("#ppUpload")[0].files[0];
                if (fileProp.type !== "image/jpeg" && fileProp.type !== "image/png"){
                    ppFileReset();
                    customAlert("Resim yüklenemedi. Lütfen bir png veya jpg yükleyip tekrar deneyiniz.","danger","#errorPP");
                } else {
                    $('#pp-file-span').text(fileProp.name);
                    let oFReader = new FileReader();


                    oFReader.readAsDataURL(this.files[0]);

                    oFReader.onload = function () {

                        // Destroy the old cropper instance
                        image.cropper('destroy');

                        // Replace url
                        image.attr('src', this.result);

                        // Start cropper
                        image.cropper({
                            aspectRatio: 1/1,
                            viewMode: 3,
                            dragMode: "move",
                            strict: true,
                            movable: true,
                            guides: false,
                            highlight: false,
                            dragCrop: false,
                            cropBoxMovable: false,
                            cropBoxResizable: false,
                            autoCropArea: 1,
                            background: false
                        });
                    };
                }

            });



        });
    </script>
<?php } else { ?>
<script>
    $("input:file.file").change(function(e){
        document.getElementById("file-custom-span").innerHTML=e.target.files[0].name;
    });
</script>
<?php } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.24/pagination/jPaginator/dataTables.jPaginator.min.js" integrity="sha512-AtIWgMIf/OMBUugbBWw/3X3U4je1OlHZp/BICQfna7+YGQQFDvqzo0RDSEiuQ9zRhm56JUy0tRsuxq3Lw/YQxQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-plugins/1.10.24/features/searchPane/dataTables.searchPane.min.js" integrity="sha512-Kk85UEiZJOPYBU+b9bvf85GYAxa+jfUzy0bHbknV5AZOzoN7WHuzLYmgvO7G3Y6nmMbiK/7y5MikgukuBETRaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    if($('#myTable').length > 0 )$('#dataTable').DataTable({
        dom: 'Pfrtip',
        "searching": true
    });
</script>
</body>
</html>

