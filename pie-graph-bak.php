<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* 全体を横並びレイアウト */
        .row {
            display: flex;
            justify-content: center; /* 中央寄せ */
            gap: 20px; /* キャンバス同士の隙間 */
        }
        /* キャンバスを小さく（例：幅・高さ200px） */
        .chart-canvas {
            width: 300px;
            height: 300px;
            border: 1px solid black; /* 任意で表示枠 */
        }
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

// quiz.php を呼び出して内容を表示
include "quiz.php";

// クイズ完了パラメータをチェック
if (isset($_GET['quizDone']) && $_GET['quizDone'] == 1) {
    // ======= グラフ描画コードをここに =======
    ?>
    <div class="row">
        <!-- 2つのCanvasを横並びに -->
        <canvas id="chartWithoutGap" class="chart-canvas"></canvas>
        <canvas id="chartWithGap" class="chart-canvas"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const speaker0Time = <?php echo $speaker0Time; ?>;
            const speaker1Time = <?php echo $speaker1Time; ?>;
            const totalGapTime = <?php echo $totalGapTime; ?>;
            
            const ctxWithoutGap = document.getElementById("chartWithoutGap").getContext("2d");
            const ctxWithGap = document.getElementById("chartWithGap").getContext("2d");

            // 円グラフ(ギャップあり)
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

            // 円グラフ(ギャップなし)
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
        });
    </script>
    <?php
} else {
    // クイズが完了していない場合の表示など
    // echo "<p>クイズが完了していません。完了するとグラフが表示されます。</p>";
}
?>
</body>
</html>
