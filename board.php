<?php
session_start();
require_once 'db_connect.php';

$community_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// コミュニティ情報を取得
$stmt = $mysqli->prepare('SELECT id, titles, descriptions FROM communities WHERE id = ?');
$stmt->bind_param('i', $community_id);
$stmt->execute();
$community = $stmt->get_result()->fetch_assoc();

if (!$community) {
    echo 'コミュニティが見つかりません。<br><a href="community.php">戻る</a>';
    exit;
}

// POSTで投稿が送信された場合
// POSTで投稿が送信された場合（ログインユーザーのユーザー名を使う）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_content'])) {
    $post_content = trim($_POST['post_content']);
    // ログインしていればセッションの username を使う
    $user_name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

    if ($user_name === null) {
        echo '<p>投稿するにはログインしてください。<a href="auth_login.php">ログイン</a></p>';
    } else {
        if (!empty($post_content)) {
            // postsテーブルに投稿を保存
            $pstmt = $mysqli->prepare('INSERT INTO posts (community_id, user_name, post_content) VALUES (?, ?, ?)');
            $pstmt->bind_param('iss', $community_id, $user_name, $post_content);
            if ($pstmt->execute()) {
                echo '<p>投稿しました！</p>';
            } else {
                echo '<p>投稿エラー: ' . $pstmt->error . '</p>';
            }
            $pstmt->close();
        }
    }
}

// 投稿一覧を取得
$plist = $mysqli->prepare('SELECT id, user_name, post_content, created_at FROM posts WHERE community_id = ? ORDER BY created_at DESC');
$plist->bind_param('i', $community_id);
$plist->execute();
$posts = $plist->get_result();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($community['titles'], ENT_QUOTES, 'UTF-8'); ?> - 掲示板</title>
    <link rel="stylesheet" href="css/diagnosis.css">
    <link rel="stylesheet" href="css/broad.css">
</head>
<body>
    <div class="frame">
    <h1><?php echo htmlspecialchars($community['titles'], ENT_QUOTES, 'UTF-8'); ?> - 掲示板</h1>
    <p><?php echo htmlspecialchars($community['descriptions'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><a href="community.php">← コミュニティ一覧に戻る</a></p>
    
    <hr>
    <h2>投稿一覧</h2>
    
    <?php
    if ($posts && $posts->num_rows > 0) {
        while ($p = $posts->fetch_assoc()) {
            echo '<div class="post">';
            // ユーザー名をプロフィールページへのリンクに変更
            $profile_url = 'profile.php?user=' . urlencode($p['user_name']);
            echo '<div class="post-user"><a href="' . $profile_url . '">' . htmlspecialchars($p['user_name'], ENT_QUOTES, 'UTF-8') . '</a>';
            // フォローボタン（自分以外のユーザーのみ）
            if (isset($_SESSION['username']) && $_SESSION['username'] !== $p['user_name']) {
                echo ' <form action="follow.php" method="post" style="display:inline;">';
                echo '<input type="hidden" name="followee" value="' . htmlspecialchars($p['user_name'], ENT_QUOTES, 'UTF-8') . '">';
                echo '<button type="submit">フォロー</button>';
                echo '</form>';
            }
            echo '</div>';
            echo '<div class="post-date">' . $p['created_at'] . '</div>';
            echo '<div class="post-content">' . nl2br(htmlspecialchars($p['post_content'], ENT_QUOTES, 'UTF-8')) . '</div>';
            // いいね数取得
            $like_stmt = $mysqli->prepare('SELECT COUNT(*) as cnt FROM likes WHERE post_id = ?');
            $like_stmt->bind_param('i', $p['id']);
            $like_stmt->execute();
            $like_result = $like_stmt->get_result();
            $like_count = ($like_result && $row = $like_result->fetch_assoc()) ? (int)$row['cnt'] : 0;
            $like_stmt->close();

            // いいね済みかどうか
            $liked = false;
            if (isset($_SESSION['username'])) {
                $like_check = $mysqli->prepare('SELECT id FROM likes WHERE post_id = ? AND user_name = ?');
                $like_check->bind_param('is', $p['id'], $_SESSION['username']);
                $like_check->execute();
                $like_check->store_result();
                $liked = $like_check->num_rows > 0;
                $like_check->close();
            }
            // いいねボタン
            if (isset($_SESSION['username'])) {
                echo '<form action="like.php" method="post" style="display:inline;vertical-align:middle;">';
                echo '<input type="hidden" name="post_id" value="' . $p['id'] . '">';
                if ($liked) {
                    echo '<button type="submit" name="unlike" value="1" style="background:none;border:none;color:#e25555;font-size:1.5em;cursor:pointer;vertical-align:middle;line-height:1;">&#9829;</button>';
                } else {
                    echo '<button type="submit" style="background:none;border:none;color:#e25555;font-size:1.5em;cursor:pointer;vertical-align:middle;line-height:1;">&#9829;</button>';
                }
                echo '<span style="margin-left:4px;font-size:1em;color:#e25555;vertical-align:middle;line-height:1;">' . $like_count . '</span>';
                echo '</form>';
            } else {
                echo '<span style="color:#e25555;font-size:1.5em;vertical-align:middle;line-height:1;">&#9829;<span style="margin-left:4px;font-size:1em;vertical-align:middle;line-height:1;">' . $like_count . '</span></span>';
            }
            // コメント一覧表示（簡易）
            require_once 'db_connect.php';
            $comment_stmt = $mysqli->prepare('SELECT user_name, comment_text, created_at FROM comments WHERE post_id = ? ORDER BY created_at ASC');
            $comment_stmt->bind_param('i', $p['id']);
            $comment_stmt->execute();
            $comments = $comment_stmt->get_result();
            while ($cm = $comments->fetch_assoc()) {
                echo '<div class="comment" style="margin-left:2em;padding:2px 0;">[' . htmlspecialchars($cm['user_name'], ENT_QUOTES, 'UTF-8') . '] ' . nl2br(htmlspecialchars($cm['comment_text'], ENT_QUOTES, 'UTF-8')) . ' <span style="font-size:0.8em;">(' . $cm['created_at'] . ')</span></div>';
            }
            $comment_stmt->close();
            // コメント投稿フォーム
            if (isset($_SESSION['username'])) {
                echo '<form action="comment.php" method="post">';
                echo '<input type="hidden" name="post_id" value="' . $p['id'] . '">';
                echo '<textarea name="comment_text" rows="2" cols="40" required placeholder="コメントを書く"></textarea>';
                echo '<button type="submit">コメント送信</button>';
                echo '</form>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>まだ投稿がありません。</p>';
    }
    ?>
    
    <hr>
    <h2>新しい投稿</h2>
    <?php if (isset($_SESSION['username'])): ?>
        <p>ログイン中: <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> (<a href="auth_logout.php">ログアウト</a>)</p>
        <form action="board.php?id=<?php echo $community_id; ?>" method="post">
            <div>
                <label for="post_content">投稿内容:</label><br>
                <textarea id="post_content" name="post_content" rows="6" cols="50" required placeholder="ここに投稿内容を入力"></textarea>
            </div>
            <br>
            <button type="submit">投稿する</button>
        </form>
    <?php else: ?>
        <p>投稿するにはログインしてください。<a href="auth_login.php">ログイン</a> または <a href="auth_register.php">新規登録</a></p>
    <?php endif; ?>
    
    <?php $mysqli->close(); ?>
    </div>
</body>
</html>
