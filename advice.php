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
        .blink {
            animation: blink 1s steps(2, start) infinite;
        }
        @keyframes blink {
            to {
                visibility: hidden;
            }
        }
    </style>
<body>
    <h1>Quiz</h1>
    <p>あなたの話した割合を選んでください：</p>
    <form id="quizForm">
        <!-- 選択肢を横並びに -->
        <div class="options-container">
            <div class="option">
                <input type="radio" name="answer" value="0-20" id="option1">
                <label for="option1">0～20%</label>
            </div>
            <div class="option">
                <input type="radio" name="answer" value="20-40" id="option2">
                <label for="option2">20～40%</label>
            </div>
            <div class="option">
                <input type="radio" name="answer" value="40-60" id="option3">
                <label for="option3">40～60%</label>
            </div>
            <div class="option">
                <input type="radio" name="answer" value="60-100" id="option4">
                <label for="option4">60～100%</label>
            </div>
        </div>
        <br>
        <button type="button" onclick="checkAnswer()">回答する</button>
    </form>

    <div id="result" class="result"></div>
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
        // PHPから$speaker0ratioを受け取る
        const speaker0ratio = <?php echo json_encode($speaker0ratio); ?>;

        function checkAnswer() {
            // 選択肢の値を取得
            const form = document.getElementById("quizForm");
            const selectedOption = form.answer.value;

            if (!selectedOption) {
                alert("回答を選択してください！");
                return;
            }

            // 選択肢を範囲に変換
            let [min, max] = selectedOption.split("-").map(Number);

            // 結果の判定
            const resultDiv = document.getElementById("result");
            if (speaker0ratio >= min && speaker0ratio <= max) {
                
                resultDiv.innerHTML = `正解！<br>あなたの話した割合は ${speaker0ratio.toFixed(2)}% です。<br>1on1 では上司の話す割合は20～40％がよいとされています。 `;
                resultDiv.classList.remove("blink");
                resultDiv.style.color = "green";
            } else {
                resultDiv.innerHTML = `はずれ！<br>あなたの話した割合は ${speaker0ratio.toFixed(2)}% です。<br>1on1 では上司の話す割合は20～40％がよいとされています。`;
                resultDiv.classList.remove("blink");
                resultDiv.style.color = "green";
            }
        }
    </script>
</body>
</html>

<?php

// 発声割合クイズ



// 発声割合アドバイス
if ($speaker0ratio <= 40 & $speaker0ratio >= 20) {
    echo '<div style="text-align: center;">';
    echo '<img src="img/advice-good.jpg" alt="Good Advice" style="width: 300px; height: auto;"><br>';
    echo '<div style="background-color: #f0f0f0; padding: 10px; margin-top: 10px; border-radius: 5px; color: #333;">';
    echo '<strong>傾聴</strong><br>しっかり相手の話を聞けているようです。<br>これからも傾聴をつづけていきましょう。';
    echo '</div>';
    echo '</div>';
} else if ($speaker0ratio > 40) {
    echo '<div style="text-align: center;">';
    echo '<img src="img/advice-bad.jpg" alt="Bad Advice" style="width: 300px; height: auto;"><br>';
    echo '<div style="background-color: #f0f0f0; padding: 10px; margin-top: 10px; border-radius: 5px; color: #333;">';
    echo '<strong>傾聴</strong><br>あなたが話している割合が多めです。<br>相手の話をゆっくり聞いてみましょう。';
    echo '</div>';
    echo '</div>';
} else if ($speaker0ratio <20){
    echo '<div style="text-align: center;">';
    echo '<img src="img/advice-bad.jpg" alt="Bad Advice" style="width: 300px; height: auto;"><br>';
    echo '<div style="background-color: #f0f0f0; padding: 10px; margin-top: 10px; border-radius: 5px; color: #333;">';
    echo '<strong>質問</strong><br>あなたが話している割合がとても少ないようです。<br>関心をもって質問してみましょう。
                 <br>また、アドバイスを求められたら経験を共有してみましょう。';
    echo '</div>';
    echo '</div>';
}


?>

