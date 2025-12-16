<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header('Location: auth_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $user_name = $_SESSION['username'];
    $post_id = (int)$_POST['post_id'];
    if (isset($_POST['unlike']) && $_POST['unlike'] == '1') {
        // いいね解除
        $stmt = $mysqli->prepare('DELETE FROM likes WHERE post_id = ? AND user_name = ?');
        $stmt->bind_param('is', $post_id, $user_name);
        $stmt->execute();
        $stmt->close();
    } else {
        // すでにいいねしていないかチェック
        $check = $mysqli->prepare('SELECT id FROM likes WHERE post_id = ? AND user_name = ?');
        $check->bind_param('is', $post_id, $user_name);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            $stmt = $mysqli->prepare('INSERT INTO likes (post_id, user_name) VALUES (?, ?)');
            $stmt->bind_param('is', $post_id, $user_name);
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
