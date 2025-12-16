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
        // 既存ユーザー確認
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'そのユーザー名は既に使われています。';
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $ins->bind_param('ss', $username, $hash);
            if ($ins->execute()) {
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['username'] = $username;
                header('Location: community.php');
                exit;
            } else {
                $errors[] = '登録に失敗しました。';
            }
            $ins->close();
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>ユーザー登録</title></head>
<body>
<h1>ユーザー登録</h1>
<?php foreach ($errors as $e) echo '<p style="color:red">'.htmlspecialchars($e).'</p>'; ?>
<form method="post">
    <label>ユーザー名: <input name="username" required></label><br>
    <label>パスワード: <input name="password" type="password" required></label><br>
    <button type="submit">登録</button>
</form>
<p><a href="auth_login.php">ログイン</a> | <a href="community.php">戻る</a></p>
</body>
</html>