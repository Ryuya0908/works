<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="diagnosis.js"></script>
    <link rel="stylesheet" href="css/diagnosis.css">
    <title>推し活診断</title>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        echo '<p>ログインしてください。<a href="auth_login.php">ログイン</a></p>';
        exit;
    }else{
        $username = $_SESSION['username'];
    }
    ?>
    <div class="frame">
    <h1>推し活×SNS</h1>
    <h2>ようこそ推し診断へ！！</h2>
    <form action="Character_entry.php" method="POST">
    <ul>
        <li>質問１ 性別</li>
        <input type="radio" name="gender" value="男" id="men"><label for="men">男</label>
        <input type="radio" name="gender" value="女" id="girl"><label for="girl">女</label>
        <input type="radio" name="gender" value="不明" id="unknown"><label for="unknown">不明</label>
        <input type="radio" name="gender" value="人外" id="monster"><label for="monster">人外</label>
        <li>質問２ 年齢</li>
        <?php
        for($i=0;$i<100;$i+=10){
            echo '<input type="radio" name="age" value='.$i.' id='.$i.'><label for='.$i.'>'.$i.'</label>';
        }
        ?>
        <input type="radio" name="age" value="不明" id="unknown"><label for="unknown">不明</label>
        <li>質問３</li>
    </ul>
    <button type="submit" name="SUB1">診断する</button>
    <button type="submit" formaction="community.php">コミュニティ</button>
    <?php
    if (isset($_SESSION['username']))
        echo '<form action="profile.php" method="POST">';
        echo '<button type="submit" formaction="profile.php">プロフィール</button>';
        echo '</form>';
    ?>
    </form>
    </div>
</body>
</html>