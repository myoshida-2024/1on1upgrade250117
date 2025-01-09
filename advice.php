<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speaker Ratio Quiz</title>
    <style>
        .options-container {
            display: flex; /* Flexboxで横並びに */
            justify-content: space-around; /* 均等配置 */
            margin-top: 20px;
        }
        .option {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        .option input {
            margin-right: 5px; /* ラジオボタンとラベルの間に余白 */
        }
        .blink {
            animation: blink 1s steps(2, start) infinite;
        }
        @keyframes blink {
            to {
                visibility: hidden;
            }
        }
        .result {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        .advice {
        text-align: center; /* アドバイス全体を中央揃え */
        }
        .advice img {
        display: block; /* 画像を中央揃え */
        margin: 0 auto; /* 画像を中央揃え */
        width: 300px;
        height: auto;
        margin-bottom: 10px; /* テキストとの間に余白 */
        }
        .advice-text {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            color: #333;
            display: inline-block; /* テキストを中央揃えで囲む */
        }
    </style>
<body>
<div id="advice" class="advice"></div>
    <?php
session_start();

// セッションデータの取得
$speaker0Time = $_SESSION['speaker0Time'] ?? 0;
$speaker1Time = $_SESSION['speaker1Time'] ?? 0;
$totalGapTime = $_SESSION['totalGapTime'] ?? 0;

// echo "Speaker 0 Time: $speaker0Time<br>";
// echo "Speaker 1 Time: $speaker1Time<br>";
// echo "Total Gap Time: $totalGapTime<br>";

$speaker0ratio = $speaker0Time / ($speaker0Time + $speaker1Time)*100;

?>

    <script>
        // 発声割合クイズ
        // PHPから$speaker0ratioを受け取る
        const speaker0ratio = <?php echo json_encode($speaker0ratio); ?>;
       
        // アドバイス表示
        showAdvice(speaker0ratio);
    
    function showAdvice(ratio) {
        const adviceDiv = document.getElementById("advice");
        adviceDiv.innerHTML = ""; // 初期化

// 発声割合アドバイス
if (ratio >= 20 && ratio <= 40) {
                adviceDiv.innerHTML = `
                    <img src="img/advice-good.jpg" alt="Good Advice">
                    <div class="advice-text">
                        <strong>傾聴</strong><br>
                        しっかり相手の話を聞けているようです。<br>
                        これからも傾聴をつづけていきましょう。
                    </div>
                `;
            } else if (ratio > 40) {
                adviceDiv.innerHTML = `
                    <img src="img/advice-bad.jpg" alt="Bad Advice">
                    <div class="advice-text">
                        <strong>傾聴</strong><br>
                        あなたが話している割合が多めです。<br>
                        相手の話をゆっくり聞いてみましょう。
                    </div>
                `;
            } else if (ratio < 20) {
                adviceDiv.innerHTML = `
                    <img src="img/advice-bad.jpg" alt="Bad Advice">
                    <div class="advice-text">
                        <strong>質問</strong><br>
                        あなたが話している割合がとても少ないようです。<br>
                        関心をもって質問してみましょう。<br>
                        また、アドバイスを求められたら経験を共有してみましょう。
                    </div>
                `;
            }
        }

</script>
</body>
</html>
