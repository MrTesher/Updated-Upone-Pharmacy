<?php
Session_start();
session_destroy();
header("location: index.php");
exit();
?>
