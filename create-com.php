<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/diagnosis.css">
    <title>コミュニティの作成</title>
</head>
<body>
    <div class="frame">
    <h1>コミュニティの作成</h1>
    <form action="community.php" method="POST">
        <label for="titles">コミュニティ名:</label>
        <input type="text" id="titles" name="titles" required>
        <br><br>
        <label for="descriptions">コミュニティ説明:</label>
        <textarea id="descriptions" name="descriptions" rows="4" cols="50" required></textarea>
        <br><br>
        <button type="submit">作成</button>
    </form>
    </div>
</body>
</html>