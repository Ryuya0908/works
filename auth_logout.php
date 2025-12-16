<?php
session_start();
session_unset();
session_destroy();
header('Location: community.php');
exit;
?>