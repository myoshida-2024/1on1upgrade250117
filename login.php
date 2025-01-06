<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<!-- <link rel="stylesheet" href="css/main.css" /> -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
<title>ログイン</title>
</head>
<body>

<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">LOGIN</a>
      </div>
    </div>
  </nav>
</header>

<?php
session_start();
// echo "login.php セッションID: " . session_id();
?>

<!-- lLOGIN login_act.php は認証処理用のPHPです。 -->
<form action="login_act.php" method="post">
<div>
    メールアドレス:<input type="text" name="lid" required>
    <?php $_SESSION["lid"] = "lid";       ?>   
  </div>
  <div>
    PW:<input type="password" name="lpw" autocomplete="current-password" required>
  </div>
<br><br>
<div>
<!-- ボタンごとに value を変える -->
<button style="margin-right: 20px;" type="submit" name="action" value="analysis_start">分析開始 ログイン</button>
<button type="submit" name="action" value="analysis_result">分析結果確認 ログイン</button>
</div>
<!-- <input type="submit" value="ログイン"> -->
</form>
<br><br>

<form action="user.php" method="post" style="display: flex; align-items: center;">
  <span style="margin-right: 10px;">アカウント未登録の場合</span>
  <input type="submit" value="ユーザ登録">
</form>

</body>
</html>