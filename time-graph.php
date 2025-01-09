<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            margin-bottom: 50px;
            text-align: center;
        }
        /* Canvasを1/4に縮小して表示 (transform) */
        #rectangleChart {
            display: block;
            margin: 0 auto; 
            transform: scale(0.125, 0.25);     /* 1/4 表示に縮小 */
            transform-origin: top left;   /* 左上を基準に拡縮 */
        }
        canvas {
            display: block;
            margin: 0 auto;
            border: 1px solid black;
        }
        #canvasContainer {
            width: 800px; /* または画面幅に合わせるなどお好み */
            height: 300px; /* 縦は適宜 */
            overflow-x: auto; /* 横スクロールを有効に */
            overflow-y: hidden; /* 縦スクロール不要なら隠す */
            border: 1px solid gray; /* 枠線 (オプション) */
            margin-bottom: 50px; /* 下マージン(オプション) */
            position: relative; /* transformと組み合わせる場合に必要なことも */
}
    </style>
</head>
<body>
<div id="canvasContainer">
    <canvas id="rectangleChart" width="800" height="1000"></canvas>
</div>
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

<!-- <h2>四角形のグラフ</h2> -->
<!-- <canvas id="rectangleChart" width="800" height="1000"></canvas> -->
<!-- ここは内部的に幅800、高さ1000の大きさで描画 -->
<!-- ただしCSSの transform で表示を1/4に縮小 -->

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const rectangleData = <?php echo json_encode($rectangleData); ?>;
        const speakerData = <?php echo $speakerData; ?>;

        const rectCtx = document.getElementById("rectangleChart").getContext("2d");
        const canvas = document.getElementById("rectangleChart");
           
        // 四角形グラフ描画
        // データ範囲を取得
        // スケール計算を統一
        const minstarttime = Math.min(...rectangleData.map(d => d.starttime), ...speakerData.map(d => d.starttime));
        const maxendtime = Math.max(...rectangleData.map(d => d.endtime), ...speakerData.map(d => d.endtime));

        // キャンバス幅を調整(内部座標は大きいまま)
        const canvasWidth = maxendtime - minstarttime;
        canvas.width = canvasWidth;
        console.log ("canvasWidth", canvasWidth);

        // スケール計算
        // const xScale = canvas.width / (maxendtime - starttime);
        const xScale = 1;  // ここでは1のまま (内部的に大きい)
        const yScale = 1000 / 100; // 固定スケール
        const maxCanvasHeight = 1000;


// X軸ラベルを描画する関数
function drawXAxisLabels(ctx, minstarttime, maxendtime, xScale, canvasHeight) {
    const labelInterval = 1000; // ラベルの間隔
    const yOffset = canvasHeight - 30; // ラベルのY座標位置（下部）
    ctx.fillStyle = "black";
    ctx.font = "48px Arial";

    // 開始時間から終了時間まで、100単位ごとにラベルを描画
    for (let x = minstarttime; x <= maxendtime; x += labelInterval) {
        const xPosition = (x - minstarttime) * xScale; // ラベルのX座標位置
        const label = (x / 1000).toFixed(1); // ラベルの値を計算（1000単位）
        ctx.fillText(label, xPosition, yOffset); // ラベルを描画
    }
}

// speaker用四角形を描画する関数
function drawSpeaker(label, starttime, endtime) {
    const xStart = (starttime - minstarttime) * xScale;
    const xEnd = (endtime - minstarttime) * xScale;
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
        drawXAxisLabels(rectCtx, minstarttime, maxendtime, xScale, canvas.height);

        }

        drawGraphs();

    });
</script>
</body>
</html>

