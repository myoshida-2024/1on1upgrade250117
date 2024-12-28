<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rectangle Drawing</title>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"
        integrity="sha512-VMsZqo0ar06BMtg0tPsdgRADvl0kDHpTbugCBBrL55KmucH6hP9zWdLIWY//OTfMnzz6xWQRxQqsUFefwHuHyg=="
        crossorigin="anonymous"></script>
    <style>
        .chart-container {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        canvas {
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>
    <canvas id="myCanvas" width="100" height="100" style="width: 100px; height: 100px;"></canvas>

    <?php
    // PHPでデータを取得し、計算を実行
    include("funcs.php");
    $pdo = db_conn();
    $firstX = 0;

    // 話者分析データベースから情報を取得
    $sql = "SELECT label, starttime, endtime FROM speaker_result";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $speakerResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 話者ごとの時間を計算
    $speaker0Time = 0;
    $speaker1Time = 0;
    $totalGapTime = 0;
    $resultMatrix = [];

    foreach ($speakerResults as $i => $row) {
        $label = $row['label'];
        $starttime = (int)$row['starttime'];
        $endtime = (int)$row['endtime'];

        // 行列にデータを追加
        $resultMatrix[] = [$label, $starttime, $endtime];

        // 話者ごとの合計時間を計算
        $duration = $endtime - $starttime;
        if ($label === "speaker0") {
            $speaker0Time += $duration;
        } elseif ($label === "speaker1") {
            $speaker1Time += $duration;
        }

        // 沈黙の合計時間を計算
        if ($i > 0) {
            $prevEndTime = $speakerResults[$i - 1]['endtime'];
            $gap = $starttime - $prevEndTime;
            if ($gap > 0) {
                $totalGapTime += $gap;
            }
        }
    }
    ?>
    <div class="chart-container">
       <canvas id="chartWithGap"></canvas>
    <canvas id="chartWithoutGap"></canvas>
   </div>

    <script>
        // PHPで計算したデータを埋め込む
        const speaker0Time = <?php echo $speaker0Time; ?>;
        const speaker1Time = <?php echo $speaker1Time; ?>;
        const totalGapTime = <?php echo $totalGapTime; ?>;

        console.log("Speaker0 Time:", speaker0Time);
        console.log("Speaker1 Time:", speaker1Time);
        console.log("Total Gap Time:", totalGapTime);

        // 円グラフ1: totalGapTimeを含む
        const ctxWithGap = document.getElementById("chartWithGap").getContext("2d");
        new Chart(ctxWithGap, {
            type: "pie",
            data: {
                labels: ["Speaker 0", "Speaker 1", "Silence"],
                datasets: [{
                    backgroundColor: ["red", "blue", "lightgray"],
                    data: [speaker0Time, speaker1Time, totalGapTime],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // アスペクト比を無視
            }
            });

        // 円グラフ2: totalGapTimeを含まない
        const ctxWithoutGap = document.getElementById("chartWithoutGap").getContext("2d");

        new Chart(ctxWithoutGap, {
            type: "pie",
            data: {
                labels: ["Speaker 0", "Speaker 1"],
                datasets: [{
                    backgroundColor: ["red", "blue"],
                    data: [speaker0Time, speaker1Time],
                }],
            },
            options: { },
        });
    </script>
</body>
</html>
