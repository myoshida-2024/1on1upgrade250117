<?php
// 文字化け対策（日本語メール）
mb_internal_encoding("UTF-8");

// 送信先アドレス
$to = "yoshida-m@mva.biglobe.ne.jp";

// 件名（日本語の場合はmb_encode_mimeheaderでエンコード推奨）
$subject = mb_encode_mimeheader("テスト送信", "UTF-8");

// メール本文
$message = <<<EOT
こんにちは。

1on1 upgradeアプリの結果がでました。

以下のリンクをクリックして結果を見てみてください。
https://forest-people.sakura.ne.jp/1on1upgrade/login.php

EOT;

// ヘッダ
$from = "From: info@forest-people.sakura.ne.jp"; 
// $from .= "\nContent-Type: text/plain; charset=UTF-8";

// 送信
if (mb_send_mail($to, $subject, $message, $from)) {
    echo "メールを送信しました！";
} else {
    echo "メールの送信に失敗しました。";
}
?>
