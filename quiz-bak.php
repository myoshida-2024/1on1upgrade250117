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
    <!-- quiz画面のレイアウト -->
<div id="quizSection">
  <!-- クイズフォームなど -->
</div>

<!-- グラフ埋め込み用iframe。最初はdisplay:noneで非表示にしておく例 -->
<iframe id="graphFrame" src="" width="800" height="600" style="display:none;border:1px solid black;"></iframe>

<script>
function checkAnswer() {
    // ...クイズ判定ロジック...

    // iframeの表示
    const iframe = document.getElementById('graphFrame');
    iframe.src = "pie-graph.php?quizDone=1";
    iframe.style.display = "block";
}
</script>

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

    <!-- クイズ結果表示エリア -->
    <div id="result" class="result"></div>
    <!-- アドバイス表示エリア -->
    <div id="advice" class="advice"></div>

    <?php
// session_start();

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
                
                resultDiv.innerHTML = `正解！<br>あなたの話した割合は ${speaker0ratio.toFixed(2)}% です。<br>1on1 では上司の話す割合は<br>20～40％がよいとされています。 `;
                resultDiv.classList.remove("blink");
                resultDiv.style.color = "green";
                window.location.href = "pie-graph.php?quizDone=1";
                exit;
            } else {
                resultDiv.innerHTML = `はずれ！<br>あなたの話した割合は ${speaker0ratio.toFixed(2)}% です。<br>1on1 では上司の話す割合は<br>20～40％がよいとされています。`;
                resultDiv.classList.remove("blink");
                resultDiv.style.color = "green";
                window.location.href = "pie-graph.php?quizDone=1";
                exit;
            }
       // iframeの表示
        const iframe = document.getElementById('graphFrame');
        iframe.src = "pie-graph.php?quizDone=1";
        iframe.style.display = "block";
    }
    
</script>

</body>
</html>
