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

以下のリンクをクリックしてください。
https://example.com/

よろしくお願いします。
EOT;

// ヘッダ
$from = "From: info@あなたのドメイン"; 
$from .= "\nContent-Type: text/plain; charset=UTF-8";

// 送信
if (mb_send_mail($to, $subject, $message, $from)) {
    echo "メールを送信しました！";
} else {
    echo "メールの送信に失敗しました。";
}
?>
