<?php
require __DIR__."/netting/class.crud.php";
$db = new crud();
if (isset($_COOKIE['phpauth_session_cookie'])) {
    if ($db->loginCheck($_COOKIE['phpauth_session_cookie'])['status'] == false) {
        $_SESSION['loginErrors'] = "Lütfen sisteme erişmek için giriş yapınız";
        header('Location: /');
        exit();
    }
}else{
    $_SESSION['loginErrors'] = "Lütfen sisteme erişmek için giriş yapınız";
    header('Location: /');
    exit();
}
$name = $db->dumpDB();
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\" {$name} \"");
readfile(__DIR__."/netting/dbBackups/".$name);