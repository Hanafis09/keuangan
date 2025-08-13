<?php
session_start();
unset($_SESSION['neraca_user']);
unset($_SESSION['neraca_nama']);
session_write_close();
header('Location: neraca_login.php');
exit;
