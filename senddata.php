<?php
session_start();

$id_1on1 = $_SESSION["new1on1_id"] ;

// JSONレスポンスのContent-Type設定
header('Content-Type: application/json; charset=UTF-8');

// エラーレポートの設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データがPOSTされているか確認
if (isset($_POST['label'], $_POST['starttime'], $_POST['endtime'])) {
    $label = $_POST['label'];
    $starttime = (int)$_POST['starttime'];
    $endtime = (int)$_POST['endtime'];

    // デバッグ用のログ
    error_log("受信データ: label=$label, starttime=$starttime, endtime=$endtime");

    // DB接続
    include("funcs.php");
    $pdo = db_conn();

    // データ登録SQL
    $sql = "INSERT INTO speaker_result (id_1on1, id, label, starttime, endtime) 
            VALUES (:id_1on1, NULL, :label, :starttime, :endtime)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_1on1', $id_1on1, PDO::PARAM_INT);
    $stmt->bindValue(':label', $label, PDO::PARAM_STR);
    $stmt->bindValue(':starttime', $starttime, PDO::PARAM_INT);
    $stmt->bindValue(':endtime', $endtime, PDO::PARAM_INT);

    // SQL実行と結果チェック
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "データが正常に登録されました"]);
        exit;
    } else {
        $error = $stmt->errorInfo();
        echo json_encode(["status" => "error", "message" => "SQLエラー: " . $error[2]]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "必要なデータが送信されていません"]);
    exit;
}
