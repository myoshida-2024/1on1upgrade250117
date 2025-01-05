<?php
try {
    // データベース接続
  
    include("funcs.php");
    $pdo = db_conn();

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 既存の画像ファイルを指定
    $filePath = 'img/advice2.png';
    $filename = basename($filePath); // ファイル名
    $fileData = file_get_contents($filePath); // ファイルのバイナリデータ

    // データベースに挿入
    $sql = "INSERT INTO advice (filename, file_url) VALUES (:filename, :file_url)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':filename', $filename);
    $stmt->bindParam(':file_url', $filePath); // URLとして保存
    $stmt->execute();

    echo "既存のファイルがデータベースに保存されました。";
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
}
