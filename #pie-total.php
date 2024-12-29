<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"
        integrity="sha512-VMsZqo0ar06BMtg0tPsdgRADvl0kDHpTbugCBBrL55KmucH6hP9zWdLIWY//OTfMnzz6xWQRxQqsUFefwHuHyg=="
        crossorigin="anonymous"></script>
    <style>
        .chart-container {
            display: flex;
            justify-content: space-around; /* グラフを横並びに配置 */
            align-items: center;
            flex-wrap: nowrap; /* 折り返しを防止 */
            width: 100%;
            margin: 20px auto; /* 上下中央揃え */
        }
        canvas {
            width:90vw;
            max-width: 300px;
            height: auto;
            border: 1px solid black; /* デバッグ用 */
        }
    </style>
</head>
<body>
    <?php
    include("funcs.php");
    $pdo = db_conn();

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
    ?>

    <div class="chart-container">
        <canvas id="chartWithGap"></canvas>
        <canvas id="chartWithoutGap"></canvas>
    </div>

    <script>
        const speaker0Time = <?php echo $speaker0Time; ?>;
        const speaker1Time = <?php echo $speaker1Time; ?>;
        const totalGapTime = <?php echo $totalGapTime; ?>;

        console.log("Speaker0 Time:", speaker0Time);
        console.log("Speaker1 Time:", speaker1Time);
        console.log("Total Gap Time:", totalGapTime);

        const ctxWithGap = document.getElementById("chartWithGap").getContext("2d");
        const ctxWithoutGap = document.getElementById("chartWithoutGap").getContext("2d");

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
                maintainAspectRatio: false,
            },
        });

        new Chart(ctxWithoutGap, {
            type: "pie",
            data: {
                labels: ["Speaker 0", "Speaker 1"],
                datasets: [{
                    backgroundColor: ["red", "blue"],
                    data: [speaker0Time, speaker1Time],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    </script>
</body>
</html>
