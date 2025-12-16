<?php
require_once 'db_connect.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($username === '' || $password === '') {
        $errors[] = 'ユーザー名とパスワードを入力してください。';
    } else {
        $stmt = $mysqli->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            // get_result を使って可読性を上げる
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                $id = $row['id'];
                $hash = isset($row['password_hash']) ? $row['password_hash'] : null;
                if ($hash === null || $hash === '') {
                    $errors[] = 'アカウントのパスワード情報が不正です。';
                } elseif (password_verify($password, $hash)) {
                    // ログイン成功
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    header('Location: community.php');
                    exit;
                } else {
                    $errors[] = 'パスワードが違います。';
                }
            } else {
                $errors[] = 'ユーザーが見つかりません。';
            }
            $stmt->close();
        } else {
            $errors[] = 'DBステートメントの準備に失敗しました。';
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>ログイン</title></head>
<body>
<h1>ログイン</h1>
<?php foreach ($errors as $e) echo '<p style="color:red">'.htmlspecialchars($e).'</p>'; ?>
<form method="post">
    <label>ユーザー名: <input name="username" required></label><br>
    <label>パスワード: <input name="password" type="password" required></label><br>
    <button type="submit">ログイン</button>
</form>
<p><a href="auth_register.php">新規登録</a> | <a href="community.php">戻る</a></p>
</body>
</html>