<?php
session_start();

$id_1on1 = $_SESSION["new1on1_id"] ;

// エラーレポートの設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データがPOSTされているか確認
if (isset($_POST['starttime'], $_POST['endtime'], $_POST['energy'], $_POST['stress'], $_POST['concentration'])) {
    $starttime = (int)$_POST['starttime'];
    $endtime = (int)$_POST['endtime'];
    $energy = (int)$_POST['energy'];
    $stress = (int)$_POST['stress'];
    $concentration = (int)$_POST['concentration'];

    // デバッグ用のログ
    error_log("受信データ: starttime=$starttime, endtime=$endtime , energy=$energy , stress=$stress , concentration=$concentration");

    // DB接続
    include("funcs.php");
    $pdo = db_conn();

    // データ登録SQL
    $sql = "INSERT INTO sentiment_result (id_1on1, id, starttime, endtime, energy, stress, concentration ) 
            VALUES (:id_1on1, NULL, :starttime, :endtime , :energy, :stress, :concentration)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_1on1', $id_1on1, PDO::PARAM_INT);
    $stmt->bindValue(':starttime', $starttime, PDO::PARAM_INT);
    $stmt->bindValue(':endtime', $endtime, PDO::PARAM_INT);
    $stmt->bindValue(':energy', $energy, PDO::PARAM_INT);
    $stmt->bindValue(':stress', $stress, PDO::PARAM_INT);
    $stmt->bindValue(':concentration', $concentration, PDO::PARAM_INT);

    // SQL実行と結果チェック
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "データが正常に登録されました"]);
    } else {
        $error = $stmt->errorInfo();
        echo json_encode(["status" => "error", "message" => "SQLエラー: " . $error[2]]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "必要なデータが送信されていません"]);
}
