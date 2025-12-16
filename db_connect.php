<?php
$host = 'localhost';
$db   = 'community_db';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('DB接続エラー: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
