<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>統合グラフ保存</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            margin-bottom: 50px;
            text-align: center;
        }
        canvas {
            display: block;
            margin: 0 auto;
            border: 1px solid black;
        }
    </style>
</head>
<body>
<?php
session_start();
include("funcs.php");
$pdo = db_conn();

// speakerデータ取得
$sql = "SELECT label, starttime, endtime FROM speaker_result";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$speakerResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

$speaker0Time = 0;
$speaker1Time = 0;
$totalGapTime = 0;

foreach ($speakerResults as $i => $row) {
    $label = $row['label'];
    $starttime = (int)$row['starttime'];
    $endtime = (int)$row['endtime'];

    $duration = $endtime - $starttime;
    if ($label === "speaker0") {
        $speaker0Time += $duration;
    } elseif ($label === "speaker1") {
        $speaker1Time += $duration;
    }

    if ($i > 0) {
        $prevEndTime = $speakerResults[$i - 1]['endtime'];
        $gap = $starttime - $prevEndTime;
        if ($gap > 0) {
            $totalGapTime += $gap;
        }
    }
}
// セッションにデータを保存
$_SESSION['speaker0Time'] = $speaker0Time;
$_SESSION['speaker1Time'] = $speaker1Time;
$_SESSION['totalGapTime'] = $totalGapTime;


// advice.php を呼び出して内容を表示
include "advice.php";

// 話者分析データベースから情報を取得
$sql = "SELECT label, starttime, endtime FROM speaker_result";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON形式に変換
$speakerData = json_encode($results, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
if (!$speakerData) {
die("JSONエンコードエラー: " . json_last_error_msg());
}  
// 四角形グラフ用データ取得
$sql = "SELECT starttime, endtime, energy, stress, concentration FROM sentiment_result";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rectangleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>統合グラフ保存ページ</h1>

<div class="chart-container">
    <h2>円グラフ（ギャップを含む）</h2>
    <canvas id="chartWithGap" width="400" height="400"></canvas>
    <h2>円グラフ（ギャップを含まない）</h2>
    <canvas id="chartWithoutGap" width="400" height="400"></canvas>
</div>

<h2>四角形のグラフ</h2>
<canvas id="rectangleChart" width="800" height="1000"></canvas>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const speaker0Time = <?php echo $speaker0Time; ?>;
        const speaker1Time = <?php echo $speaker1Time; ?>;
        const totalGapTime = <?php echo $totalGapTime; ?>;
        const rectangleData = <?php echo json_encode($rectangleData); ?>;

        const ctxWithGap = document.getElementById("chartWithGap").getContext("2d");
        const ctxWithoutGap = document.getElementById("chartWithoutGap").getContext("2d");
        const rectCtx = document.getElementById("rectangleChart").getContext("2d");
        const canvas = document.getElementById("rectangleChart");
    

        // 円グラフ描画
        let myPieChartWithGap = new Chart(ctxWithGap, {
            type: "pie",
            data: {
                labels: ["Speaker 0", "Speaker 1", "Silence"],
                datasets: [{
                    backgroundColor: ["red", "blue", "lightgray"],
                    data: [speaker0Time, speaker1Time, totalGapTime],
                }],
            },
            options: {
                responsive: false,
                animation: { duration: 0 },
            },
        });

        let myPieChartWithoutGap = new Chart(ctxWithoutGap, {
            type: "pie",
            data: {
                labels: ["Speaker 0", "Speaker 1"],
                datasets: [{
                    backgroundColor: ["red", "blue"],
                    data: [speaker0Time, speaker1Time],
                }],
            },
            options: {
                responsive: false,
                animation: { duration: 0 },
            },
        });

        // 四角形グラフ描画
        // データ範囲を取得
        // スケール計算を統一
        const speakerData = <?php echo $speakerData; ?>;
        const startTime = Math.min(...rectangleData.map(d => d.starttime), ...speakerData.map(d => d.starttime));
        const endTime = Math.max(...rectangleData.map(d => d.endtime), ...speakerData.map(d => d.endtime));

        // キャンバス幅を調整
        const canvasWidth = Math.max(800, endTime - startTime);
        canvas.width = canvasWidth;

        // スケール計算
        const xScale = canvas.width / (endTime - startTime);
        const yScale = 1000 / 100; // 固定スケール
        const maxCanvasHeight = 1000;


// X軸ラベルを描画する関数
function drawXAxisLabels(ctx, startTime, endTime, xScale, canvasHeight) {
    const labelInterval = 100; // ラベルの間隔
    const yOffset = canvasHeight - 30; // ラベルのY座標位置（下部）
    ctx.fillStyle = "black";
    ctx.font = "16px Arial";

    // 開始時間から終了時間まで、100単位ごとにラベルを描画
    for (let x = startTime; x <= endTime; x += labelInterval) {
        const xPosition = (x - startTime) * xScale; // ラベルのX座標位置
        const label = (x / 1000).toFixed(1); // ラベルの値を計算（1000単位）
        ctx.fillText(label, xPosition, yOffset); // ラベルを描画
    }
}

// speaker用四角形を描画する関数
function drawSpeaker(label, starttime, endtime) {
    const xStart = (starttime - startTime) * xScale;
    const xEnd = (endtime - startTime) * xScale;
    const rectHeight = 20; // 固定高さ
    const yOffset = label === "speaker0" ? 50 : 80; // Y座標のオフセット

    rectCtx.fillStyle = label === "speaker0" ? "red" : "blue";
    rectCtx.fillRect(xStart, maxCanvasHeight - yOffset - rectHeight, xEnd - xStart, rectHeight);
}

        function drawRectangle(ctx, starttime, endtime, value, color, yOffset) {
            const xStart = starttime * xScale;
            const xEnd = endtime * xScale;
            const rectHeight = value * yScale;

            ctx.fillStyle = color;
            ctx.fillRect(xStart, maxCanvasHeight - rectHeight - yOffset, xEnd - xStart, rectHeight);
        }

        function drawGraphs() {

            // Speakerデータの描画
            speakerData.forEach(({ label, starttime, endtime }) => {
            drawSpeaker(label, starttime, endtime);
        });
        // Rectangleデータの描画
            rectangleData.forEach((item) => {
                const { starttime, endtime, energy, stress, concentration } = item;

                drawRectangle(rectCtx, starttime, endtime, energy, 'orange', 200);
                drawRectangle(rectCtx, starttime, endtime, stress, 'gray', 400);
                drawRectangle(rectCtx, starttime, endtime, concentration, 'lightblue', 600);
            });
        
        // X軸ラベルを描画
        drawXAxisLabels(rectCtx, startTime, endTime, xScale, canvas.height);

        }

        drawGraphs();

        // 画像として保存する関数
        function saveCanvasAsImage(canvasId, filename) {
            const canvas = document.getElementById(canvasId);
            const imageData = canvas.toDataURL("image/png");
            fetch("save_image.php", {
                method: "POST",
                body: JSON.stringify({ image: imageData, filename }),
                headers: { "Content-Type": "application/json" },
            })
            .then((response) => response.text())
            .then((data) => console.log(data))
            .catch((error) => console.error("エラー:", error));
        }

        // 円グラフ2つを1つの画像に保存
        saveCanvasAsImage("chartWithGap", "img/pie_chart_with_gap.png");
        saveCanvasAsImage("chartWithoutGap", "img/pie_chart_without_gap.png");

        // 四角形グラフを1つの画像に保存
        saveCanvasAsImage("rectangleChart", "img/rectangle_chart.png");
    });
</script>
</body>
</html>

