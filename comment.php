<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header('Location: auth_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['comment_text'])) {
    $user_name = $_SESSION['username'];
    $post_id = (int)$_POST['post_id'];
    $comment_text = trim($_POST['comment_text']);
    if ($comment_text !== '') {
        $stmt = $mysqli->prepare('INSERT INTO comments (post_id, user_name, comment_text) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $post_id, $user_name, $comment_text);
        $stmt->execute();
        $stmt->close();
    }
}
// 元のページにリダイレクト
$redirect = $_SERVER['HTTP_REFERER'] ?? 'board.php';
header('Location: ' . $redirect);
exit;
?>
