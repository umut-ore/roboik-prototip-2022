<?php
require __DIR__.'/config/settings.php';
if (isset($_COOKIE['phpauth_session_cookie']))$db->logoutUser($_COOKIE['phpauth_session_cookie']);
header('Location:/');