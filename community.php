<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コミュニティ</title>
    <link rel="stylesheet" href="css/diagnosis.css">
</head>
<body>
    <div class="frame">
    <?php
    require_once 'db_connect.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titles'])) {
        $titles = trim($_POST['titles']);
        $descriptions = isset($_POST['descriptions']) ? trim($_POST['descriptions']) : '';
        
        if (!empty($titles)) {
            $stmt = $mysqli->prepare('INSERT INTO communities (titles, descriptions) VALUES (?, ?)');
            if ($stmt) {
                $stmt->bind_param('ss', $titles, $descriptions);
                if ($stmt->execute()) {
                    $community_id = $stmt->insert_id;
                    echo '<p><strong>' . htmlspecialchars($titles, ENT_QUOTES, 'UTF-8') . '</strong> のコミュニティを作成しました。</p>';
                    echo '<p>' . htmlspecialchars($descriptions, ENT_QUOTES, 'UTF-8') . '</p>';
                    echo '<p><a href="community.php">戻る</a></p>';
                } else {
                    echo '<p>エラー: ' . $stmt->error . '</p>';
                }
                $stmt->close();
            }
        } else {
            echo '<p>コミュニティ名を入力してください。</p>';
        }
    } else {
        echo '<h2>作成済みコミュニティ一覧</h2>';
        $result = $mysqli->query('SELECT id, titles, descriptions, created_at FROM communities ORDER BY created_at DESC');
        
        if ($result && $result->num_rows > 0) {
            echo '<ul>';
            while ($row = $result->fetch_assoc()) {
                echo '<li>';
                echo '<strong>' . htmlspecialchars($row['titles'], ENT_QUOTES, 'UTF-8') . '</strong><br>';
                echo htmlspecialchars($row['descriptions'], ENT_QUOTES, 'UTF-8') . '<br>';
                echo '作成日時: ' . $row['created_at'] . '<br>';
                echo '<a href="board.php?id=' . $row['id'] . '">掲示板を見る</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>まだコミュニティが作成されていません。</p>';
        }
        
        echo '<hr>';
        echo '<p><a href="create-com.php">新しいコミュニティを作成する</a></p>';
    }
    
    $mysqli->close();
    ?>
    </div>
</body>
</html>