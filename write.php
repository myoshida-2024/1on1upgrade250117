<?php
session_start();
// エラーレポートを表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
// POSTデータ取得とデータ確認
var_dump("write.php", $_POST);

if (isset($_POST['label']) &&isset($_POST['starttime']) && isset($_POST['endtime']) ) {
  echo ("write.php");
  
  // POSTデータの変数代入
  $label = $_POST['label'];
  $starttime = $_POST['starttime']; // 必要に応じて変換
  $endtime = $_POST['endtime'];
} else {
  ob_flush();
  exit("必要なデータが送信されていません");
}

// デバッグ用のログ出力
error_log("write.phpで受け取ったデータ: label={$label}, starttime={$starttime}, endtime={$endtime}");


// DB接続
include("funcs.php");
$pdo = db_conn();

// データ登録SQL作成
$sql = "INSERT INTO speaker_result (id, label, starttime, endtime) 
        VALUES (NULL, :label, :starttime, :endtime);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':label', $label, PDO::PARAM_STR);
$stmt->bindValue(':starttime', $starttime, PDO::PARAM_INT);
$stmt->bindValue(':endtime', $endtime, PDO::PARAM_INT);
$status = $stmt->execute();

// データ登録処理後の確認
if($status == false){
  $error = $stmt->errorInfo();
  ob_flush();
  exit("SQL_ERROR: " . $error[2]);
} else {
  ob_flush();
  exit("データが正常に登録されました");
}
?>
