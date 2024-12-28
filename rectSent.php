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
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@next/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.5.0/frappe-gantt.css">
</head>
<body>
    <!-- キャンバスとスライダー -->
    <canvas id="myCanvas" width="800" height="600" style="border:1px solid #000000;"></canvas>
    <canvas id="myPieChart" width="400" height="400"></canvas>
    <input type="range" id="slider" min="<?php echo $firstX; ?>" max="5000" value="<?php echo $firstX; ?>" style="width: 800px;" />

    <?php
    // PHPでデータを取得し、JavaScriptに渡す準備をする
    include("funcs.php");
    $pdo = db_conn();
    $firstX = 0;

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


    // 感情データベースから情報を取得
    $sql = "SELECT starttime, endtime, energy, stress, concentration FROM sentiment_result";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON形式に変換
    $rectangleData = json_encode($results, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if (!$rectangleData) {
        die("JSONエンコードエラー: " . json_last_error_msg());
    }
        
    ?>

    <script>
        // PHPから取得したデータをJavaScriptで利用
        const rectangleData = <?php echo $rectangleData; ?>;
        const speakerData = <?php echo $speakerData; ?>;
        const firstX = <?php echo json_encode($firstX); ?>;

        console.log("rectangleData:", rectangleData);
        console.log("speakerData:", speakerData);
        console.log("firstX:", firstX);

        // 円グラフのデータ作成
        
        // 結果を格納する行列（配列の配列）
        const resultMatrix = [];
        const uniqueEntries = new Set(); // 重複チェック用のセットを初期化

        // データを行列に追加
        speakerData.forEach(item => {
        const { label, starttime, endtime } = item;
        const uniqueKey = `${label}-${starttime}-${endtime}`; // 一意のキーを作成
        
        if (!uniqueEntries.has(uniqueKey)) { // 重複していない場合のみ追加
        uniqueEntries.add(uniqueKey);
        const duration = endtime - starttime;
        resultMatrix.push([label, starttime, endtime, duration]);
        }
        });
        console.log(resultMatrix);

        //話者ごとの合計、沈黙の合計
// 各話者の話している時間を計算する変数
let speaker0Time = 0;
let speaker1Time = 0;

// resultMatrix をループして時間を計算
resultMatrix.forEach(row => {
    const [label, starttime, endtime] = row; // 各行のデータを分解
    const duration = endtime - starttime;

    if (label === "speaker0") {
        speaker0Time += duration; // speaker0の時間を加算
    } else if (label === "speaker1") {
        speaker1Time += duration; // speaker1の時間を加算
    }
});

// 結果をコンソールに出力
console.log(`Speaker0 の話している合計時間: ${speaker0Time}`);
console.log(`Speaker1 の話している合計時間: ${speaker1Time}`);

let totalGapTime = 0; // 合計時間の初期化

for (let i = 0; i < resultMatrix.length - 1; i++) {
    const currentEndTime = resultMatrix[i][2]; // n番目のendtime
    const nextStartTime = resultMatrix[i + 1][1]; // n+1番目のstarttime

    const gap = nextStartTime - currentEndTime; // 間隔を計算
    if (gap > 0) { // 正の間隔のみ加算
        totalGapTime += gap;
    }
}
console.log(`合計の間隔時間: ${totalGapTime}`);

  const ctx = document.getElementById("myPieChart").getContext("2d");
  const myPieChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: ["Speaker 0", "Speaker 1", "Silence"],
      datasets: [
        {
          backgroundColor: ["red", "blue", "lightgray"],
          data: [speaker1Time, speaker1Time, totalGapTime], // サンプルデータ
        },
      ],
    },
    options: {},
  });
</script>


        // キャンバスの設定
        const canvas = document.getElementById('myCanvas');
        const ctx3 = canvas.getContext('2d');
        const slider = document.getElementById('slider');
        // const rate = 0.02;

        const maxCanvasWidth = 800;
        const maxCanvasHeight = 100; // 1つのグラフの高さ
        const maxDataValue = 80000; // x 軸のデータ最大値
        const maxValue = 100; // energy,stress,concentration の最大値
        const speakerH = 20;

        const xScale =  maxCanvasWidth / maxDataValue;
        const yScale = maxCanvasHeight / maxValue;

            // 四角形を描画する関数(speaker用)
            function drawSpeaker(label, starttime, endtime) {
            
            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = speakerH * yScale; // 高さは定数

    
            if (label == "speaker0"){
                ctx3.fillStyle = "red";
                
                ctx3.fillRect(scaledStart, maxCanvasHeight, scaledEnd - scaledStart, scaledheight);
            } else {
                ctx3.fillStyle = "blue";
                
                ctx3.fillRect(scaledStart, maxCanvasHeight*2, scaledEnd - scaledStart, scaledheight);
            }
            
            
        }

        // 四角形を描画する関数(energy用)
        function drawRectangle_energy(starttime, endtime, energy) {
            
            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = energy * yScale; // エネルギーを高さとして使用

            ctx3.fillStyle = "orange";
            ctx3.fillRect(scaledStart, maxCanvasHeight*3, scaledEnd - scaledStart, (-1) * scaledheight);
            
        }
 
        // 四角形を描画する関数(stress用)
        function drawRectangle_stress(starttime, endtime, stress) {
            
            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = stress * yScale; // ストレスを高さとして使用

            ctx3.fillStyle = "gray";
            ctx3.fillRect(scaledStart, maxCanvasHeight *4 , scaledEnd - scaledStart, (-1) * scaledheight);
            
        }

          // 四角形を描画する関数(concentration用)
         function drawRectangle_concentration(starttime,endtime, concentration) {

            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = concentration * yScale; // 集中を高さとして使用


            ctx3.fillStyle = "lightblue";
            ctx3.fillRect(scaledStart, maxCanvasHeight *5 , scaledEnd - scaledStart, (-1) * scaledheight);
            
        }

        // 四角形を再描画する関数
        function redrawRectangles(offset) {
          
            ctx3.clearRect(0, 0, canvas.width, canvas.height); // キャンバスのクリア
            // speakerのグラフを再描画
            speakerData.forEach(item => {
                const { label, starttime, endtime } = item;
                drawSpeaker(label, starttime - offset, endtime - offset);   
            });
            // energyのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, energy } = item;
                const validEnergy = energy !== undefined && energy !== null ? energy : 0; // デフォルト値0
                drawRectangle_energy(starttime - offset, endtime - offset, validEnergy);
                
            });
            //stressのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, stress } = item;
                const validStress = stress !== undefined && stress !== null ? stress : 0; // デフォルト値0
                drawRectangle_stress(starttime - offset, endtime - offset, validStress);
                
            });

            //concentrationのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, concentration } = item;
                const validconcentration = concentration !== undefined && concentration !== null ? concentration : 0; // デフォルト値0
                drawRectangle_concentration(starttime - offset, endtime - offset, validconcentration);
               
            });
        }

        // 初期描画
        redrawRectangles(firstX);

        // スライダーのイベントリスナー
        slider.addEventListener('input', (event) => {
            const offset = event.target.value;
            redrawRectangles(offset);
        });
    </scrip>
</body>
</html>
