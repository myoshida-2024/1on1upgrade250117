<?php
session_start();

$to = $_SESSION["lid"];

echo "宛先アドレス". $to;

// PHPMailer ファイルのインクルード
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';
require_once 'vendor/autoload.php'; // Composerのオートローダーを読み込み

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Dotenv\Dotenv; // Dotenvクラスを使用するための宣言

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// echo '現在のディレクトリ: ' . __DIR__ . '<br>';
// if (file_exists(__DIR__ . '/.env')) {
    // echo '.env ファイルが見つかりました。<br>';
// } else {
    // echo '.env ファイルが見つかりません。<br>';
// }

// if (getenv('MAIL_PASSWORD')) {
    // echo 'MAIL_PASSWORD の値: ' . getenv('MAIL_PASSWORD') . '<br>';
// } else {
    // echo 'MAIL_PASSWORD が読み取れませんでした。<br>';
// }

// var_dump(getenv('MAIL_PASSWORD')); // 環境変数を確認

if (isset($_ENV['MAIL_PASSWORD'])) {
    echo 'MAIL_PASSWORD の値: ' . $_ENV['MAIL_PASSWORD'] . '<br>';
} else {
    echo 'MAIL_PASSWORD が $_ENV から読み取れませんでした。<br>';
}

// var_dump($_ENV['MAIL_PASSWORD'] ?? 'Not set'); // $_ENVも確認

// echo '環境変数 MAIL_PASSWORD の値: ' . getenv('MAIL_PASSWORD') . "<br>";

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
    // $mail->Password = 'infomy1010'; // パスワード
    // $mail->Password = getenv('MAIL_PASSWORD');
    $mail->Password = $_ENV['MAIL_PASSWORD'] ; // パスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

     // デバッグモード
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // デバッグ用
    // $mail->Debugoutput = 'html';
    // echo getenv('MAIL_PASSWORD');

    // 送信者情報
    $mail->setFrom('info@forest-people.sakura.ne.jp', 'Forest People');
    $mail->addAddress($to);

    // メール内容
    $mail->isHTML(true);
    $mail->Subject = '1on1 analysis result';
    $mail->Body = '<p>1on1分析の結果が出ました。<br>
<a href="https://forest-people.sakura.ne.jp/1on1upgrade/login.php">
こちらのリンク</a>からログインしてください。</p>';

// メールフォームの送信処理

    // デフォルトmail関数で送る例
    // mail($to, $Subject, $Body, "From: info@forest-people.sakura.ne.jp");
    // mail($to, $subject, $message, $header); // デフォルトの送信方法
    // echo "mail()関数で送信しました";

    $mail->send(); // SMTPによる送信
    echo 'メールが送信されました！';

} catch (Exception $e) {
    echo "メール送信に失敗しました: {$mail->ErrorInfo}";
 }

?>
