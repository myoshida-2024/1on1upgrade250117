<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// PHPMailer ファイルのインクルード
// require 'lib/PHPMailer.php';
// require 'lib/SMTP.php';
// require 'lib/Exception.php';

require '/home/forest-people/www/1on1upgrade/lib/PHPMailer.php';
require '/home/forest-people/www/1on1upgrade/lib/SMTP.php';
require '/home/forest-people/www/1on1upgrade/lib/Exception.php';

try {
    $mail = new PHPMailer(true);

    // SMTP設定
    $mail->isSMTP();
    $mail->Host       = 'forest-people.sakura.ne.jp';
    $mail->Port       = 587;
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@forest-people.sakura.ne.jp'; // さくらメールのユーザー名
    $mail->Password   = 'infomy1010';                   // パスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    // 送信先
    $mail->setFrom('info@forest-people.sakura.ne.jp', 'Forest People');
    $mail->addAddress('gglyoshida17@gmail.com'); // テスト用アドレス

    $mail->Subject = 'テストメール';
    $mail->Body    = 'これはテストメールです';
    $mail->send();

    echo "送信成功";
} catch (Exception $e) {
    echo "送信失敗: " . $mail->ErrorInfo;
}
