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
  <nav class="navbar navbar-default">LOGIN</nav>
</header>

<!-- lLOGIN login_act.php は認証処理用のPHPです。 -->
<form name="form1" action="login_act.php" method="post">
メールアドレス:<input type="text" name="lid">
PW:<input type="password" name="lpw" autocomplete="current-password">
</form>
<br><br>
<form style="margin-left: 100px;">
<!-- ボタンごとに value を変える -->
<button style="margin-right: 20px;" type="submit" name="action" value="analysis_start">分析開始 ログイン</button>
<button type="submit" name="action" value="analysis_result">分析結果確認 ログイン</button>

<!-- <input type="submit" value="ログイン"> -->
</form>
<br><br>

<form name="form1" action="user.php" method="post">
<input type="submit" value="ユーザ登録">
</form>

</body>
</html>