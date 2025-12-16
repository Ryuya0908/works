<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header('Location: auth_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['followee'])) {
    $follower = $_SESSION['username'];
    $followee = $_POST['followee'];
    if ($follower !== $followee) {
        // すでにフォローしていないかチェック
        $check = $mysqli->prepare('SELECT id FROM follows WHERE follower = ? AND followee = ?');
        $check->bind_param('ss', $follower, $followee);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            $stmt = $mysqli->prepare('INSERT INTO follows (follower, followee) VALUES (?, ?)');
            $stmt->bind_param('ss', $follower, $followee);
            $stmt->execute();
            $stmt->close();
        }
        $check->close();
    }
}
// 元のページにリダイレクト
$redirect = $_SERVER['HTTP_REFERER'] ?? 'board.php';
header('Location: ' . $redirect);
exit;
?>
