<?php
session_start();
require_once 'db_connect.php';

// ユーザー名取得
$user_name = isset($_GET['user']) ? $_GET['user'] : '';
if ($user_name === '') {
    echo 'ユーザーが指定されていません。';
    exit;
}

// ユーザー情報取得
// 注: 現在の users テーブルに oshi_icon 等のカラムが存在しない場合、
// 元のクエリだと "Unknown column" エラーになります。まずは最低限の username を取得するようにします。
$stmt = $mysqli->prepare('SELECT username FROM users WHERE username = ?');
$stmt->bind_param('s', $user_name);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo 'ユーザーが見つかりません。';
    exit;
}

// 参加コミュニティ取得
$cstmt = $mysqli->prepare('SELECT c.id, c.titles FROM communities c INNER JOIN community_members m ON c.id = m.community_id WHERE m.user_name = ?');
$cstmt->bind_param('s', $user_name);
$cstmt->execute();
$communities = $cstmt->get_result();
$cstmt->close();

$my_profile = (isset($_SESSION['username']) && $_SESSION['username'] === $user_name);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>のプロフィール</title>
    <link rel="stylesheet" href="css/diagnosis.css">
    <link rel="stylesheet" href="css/broad.css">
    <style>
        .profile-icon { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .profile-section { margin-bottom: 20px; }
        .community-btn { margin: 5px; }
    </style>
</head>
<body>
<div class="frame">
    <h1><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>のプロフィール</h1>
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($user['oshi_icon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="推しアイコン" class="profile-icon"><br>
        <strong>推しキャラ名:</strong> <?php echo htmlspecialchars($user['oshi_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
        <strong>アニメ名:</strong> <?php echo htmlspecialchars($user['anime_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
        <?php if ($my_profile): ?>
            <a href="Character_entry.php" class="button">推しの追加へ</a>
        <?php endif; ?>
    </div>
    <div class="profile-section">
        <h2>参加しているコミュニティ</h2>
        <?php if ($communities && $communities->num_rows > 0): ?>
            <?php while ($c = $communities->fetch_assoc()): ?>
                <a href="board.php?id=<?php echo $c['id']; ?>" class="community-btn button"><?php echo htmlspecialchars($c['titles'], ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>参加中のコミュニティはありません。</p>
        <?php endif; ?>
    </div>
    <a href="community.php">← コミュニティ一覧に戻る</a>
</div>
</body>
</html>
<?php $mysqli->close(); ?>
