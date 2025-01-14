<?php
session_start();

//POST値
$lid = $_POST["lid"]; //lid
$lpw = $_POST["lpw"]; //lpw
$action = $_POST['action'];

// 1) DB接続
include("funcs.php");
$pdo = db_conn();

// 2) 1行目を取得
//    - テーブルに1行しかないなら "LIMIT 1" で十分
$sql_select = "SELECT id, newest_1on1_id FROM 1on1_record LIMIT 1";
$stmt_select = $pdo->prepare($sql_select);
$status_select = $stmt_select->execute();

if ($status_select == false) {
    sql_error($stmt_select); // funcs.php 側のエラー処理関数
}




//2. データ登録SQL作成
//* PasswordがHash化→条件はlidのみ！！
$stmt = $pdo->prepare("SELECT * FROM gs_user_table WHERE lid=:lid AND life_flg=0"); 
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();         //1レコードだけ取得する方法
//$count = $stmt->fetchColumn(); //何回(何個？)とれたかカウント SELECT COUNT(*)で使用可能()


//5.該当１レコードがあればSESSIONに値を代入
//入力したPasswordと暗号化されたPasswordを比較！[戻り値：true,false]
$pw = password_verify($lpw, $val["lpw"]); //$lpw = password_hash($lpw, PASSWORD_DEFAULT);   //パスワードハッシュ化
if($pw){ 
  //Login成功時


  // ======================================= id_1on1
// fetch して1行目を取得
$record = $stmt_select->fetch(PDO::FETCH_ASSOC);
if (!$record) {
    exit("No row found in 1on1_record (table is empty?)");
}
// 例: newest_1on1_id に +1
$new_val = $record["newest_1on1_id"] + 1;

// 同じ行の id を使って UPDATE
$id = $record["id"];

// 3) UPDATE
$sql_update = "UPDATE 1on1_record 
               SET newest_1on1_id = :new_val
               WHERE id = :id";
$stmt_update = $pdo->prepare($sql_update);
$stmt_update->bindValue(':new_val', $new_val, PDO::PARAM_INT);
$stmt_update->bindValue(':id', $id, PDO::PARAM_INT);
$status_update = $stmt_update->execute();

if ($status_update == false) {
    sql_error($stmt_update);
} else {
    echo "newest_1on1_id updated successfully: $new_val";
}
// =======================================

  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["new1on1_id"] = $new_val;
  $_SESSION["kanri_flg"] = $val['kanri_flg'];
  $_SESSION["username"]  = $val['username'];

  // $_SESSION["lid"]  = $val['lid'];
  $_SESSION["lid"] = $lid; // メールアドレスをセッションに保存
  //Login成功時（select.phpへ）
  // ボタンの値でリダイレクト先を振り分け
  if($action === "analysis_start"){
    // 「分析開始」ボタン


  $sql = "UPDATE gs_user_table 
  SET id_1on1 = :new_val
    WHERE lid = :lid";   // ここで同じlidの行を更新
  $stmt = $pdo->prepare($sql);
  // :new_val → newest_1on1_id + 1 した値
  $stmt->bindValue(':new_val', $new_val, PDO::PARAM_INT);
  // :lid → ログインID（メールアドレス）
  $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);

  $status_user_update = $stmt->execute();
  if($status_user_update == false){
    sql_error($stmt); // エラー時にはfuncs.phpのsql_error()を呼ぶ
  }


    redirect("index.php");
  } elseif($action === "analysis_result"){
    // 「分析結果確認」ボタン
    redirect("pie-graph.php");
}else{
  //Login失敗時(login.phpへ)
  redirect("login.php");

}
}else{
   //=== ログイン失敗時 ===
   redirect("login.php");
}
exit();
