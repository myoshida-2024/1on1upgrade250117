<?php
session_start();

$to = $_SESSION["lid"];

echo "宛先アドレス". $to;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// PHPMailer ファイルのインクルード
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';



$userPHPMailer = true; // 念の為、切り替え出来るように



// $mail->SMTPDebug = 3; // 0=OFF,1=CLIENT,2=CLIENT+SERVER,3=VERBOSE

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

try {
    $mail = new PHPMailer(true);
    // サーバー設定
    $mail->isSMTP();
    $mail->Host = 'forest-people.sakura.ne.jp'; //  SMTP サーバー
    $mail->Port = 587; //  SMTP サーバー
    $mail->SMTPAuth = true;
    $mail->Username = 'info@forest-people.sakura.ne.jp'; // アカウント
    $mail->Password = 'infomy1010'; // パスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    // 送信者情報
    $mail->setFrom('info@forest-people.sakura.ne.jp', 'Forest People');
    $mail->addAddress($to, $to);

    // メール内容
    $mail->isHTML(true);
    $mail->Subject = 'テストメール';
    $mail->Body = '<p>これはテストメールです。</p>';
 
// メールフォームの送信処理
if (!$userPHPMailer) {
    // デフォルトmail関数で送る例
    mail($to, 'テストメール', 'これはテストメールです。', "From: info@forest-people.sakura.ne.jp");
    // mail($to, $subject, $message, $header); // デフォルトの送信方法
    echo "mail()関数で送信しました";
} else {
    $mail->send(); // SMTPによる送信
    echo 'メールが送信されました！';
    }
} catch (Exception $e) {
    echo "メール送信に失敗しました: {$mail->ErrorInfo}";

 }

?>
