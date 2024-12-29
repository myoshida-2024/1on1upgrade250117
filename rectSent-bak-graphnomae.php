<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>統合グラフ表示</title>
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
include("funcs.php");
$pdo = db_conn();

// 円グラフ用データ取得
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

// 四角形グラフ用データ取得
$sql = "SELECT starttime, endtime, energy, stress, concentration FROM sentiment_result";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rectangleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>統合グラフ表示ページ</h1>

<div class="chart-container">
    <h2>円グラフ（ギャップを含む）</h2>
    <canvas id="chartWithGap" width="400" height="400"></canvas>
    <h2>円グラフ（ギャップを含まない）</h2>
    <canvas id="chartWithoutGap" width="400" height="400"></canvas>
</div>

<h2>四角形のグラフ</h2>
<canvas id="rectangleChart" width="800" height="600"></canvas>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const speaker0Time = <?php echo $speaker0Time; ?>;
        const speaker1Time = <?php echo $speaker1Time; ?>;
        const totalGapTime = <?php echo $totalGapTime; ?>;
        const rectangleData = <?php echo json_encode($rectangleData); ?>;

        console.log("Speaker0 Time:", speaker0Time);
        console.log("Speaker1 Time:", speaker1Time);
        console.log("Total Gap Time:", totalGapTime);
        console.log("Rectangle Data:", rectangleData);

        const ctxWithGap = document.getElementById("chartWithGap").getContext("2d");
        const ctxWithoutGap = document.getElementById("chartWithoutGap").getContext("2d");
        const rectCtx = document.getElementById("rectangleChart").getContext("2d");

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
                animation: {
                    duration: 0,
                },
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
                animation: {
                    duration: 0,
                },
            },
        });

        // 四角グラフ描画
        const xScale = 800 / 10000; // サンプルスケール
        const yScale = 600 / 100; // サンプルスケール
        const maxCanvasHeight = 600;

        function drawRectangle(ctx, starttime, endtime, value, color, yOffset) {
            const xStart = starttime * xScale;
            const xEnd = endtime * xScale;
            const rectHeight = value * yScale;

            ctx.fillStyle = color;
            ctx.fillRect(xStart, maxCanvasHeight - rectHeight - yOffset, xEnd - xStart, rectHeight);
        }

        function drawGraphs() {
            rectangleData.forEach((item) => {
                const { starttime, endtime, energy, stress, concentration } = item;

                drawRectangle(rectCtx, starttime, endtime, energy, 'orange', 200); // エネルギー
                drawRectangle(rectCtx, starttime, endtime, stress, 'gray', 400);  // ストレス
                drawRectangle(rectCtx, starttime, endtime, concentration, 'blue', 600); // 集中度
            });
        }

        drawGraphs();
    });
</script>
</body>
</html>
