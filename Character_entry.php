<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>推し追加画面</title>
</head>
<body>
    <h1>テスト</h1>
    <form action="diagnosis.php" method="POST">
        <?php
        session_start();
        if (isset($_SESSION['username'])){
            echo '<form action="diagnosis.php" method="POST">';
            echo '<p>ようこそ、' . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . 'さん！</p>';
            echo '<button type="submit">推し診断へ</button>';
            echo '</form>';
        }
        else{
            echo '<p>ログインしてください。</p>';
        }
        ?>
        <button type="submit" formaction="profile.php">登録する</button>
    </form>
</body>
</html>