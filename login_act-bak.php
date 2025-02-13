<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();
// echo "login_act セッションID: " . session_id();

//POST値
$lid = $_POST["lid"]; //lid
$lpw = $_POST["lpw"]; //lpw
$action = $_POST['action'];

//1.  DB接続します
include("funcs.php");
$pdo = db_conn();

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
  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["kanri_flg"] = $val['kanri_flg'];
  // $_SESSION["username"]  = $val['username'];
  $_SESSION["username"]  = $username;
  // $_SESSION["lid"]  = $val['lid'];
  $_SESSION["lid"] = $lid; // メールアドレスをセッションに保存
  //Login成功時（select.phpへ）
  // ボタンの値でリダイレクト先を振り分け
  if($action === "analysis_start"){
    // 「分析開始」ボタン
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

?>

