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
    <canvas id="myCanvas" width="800" height="300" style="border:1px solid #000000;"></canvas>
    <input type="range" id="slider" min="<?php echo $firstX; ?>" max="5000" value="<?php echo $firstX; ?>" style="width: 800px;" />

    <?php
    // PHPでデータを取得し、JavaScriptに渡す準備をする
    include("funcs.php");
    $pdo = db_conn();

    // データベースから情報を取得
    $sql = "SELECT starttime, endtime, energy, stress, concentration FROM sentiment_result";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON形式に変換
    $rectangleData = json_encode($results, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if (!$rectangleData) {
        die("JSONエンコードエラー: " . json_last_error_msg());
    }
    
    // 最初の四角形の x 値を取得して min 値に設定

    $firstX = isset($results[0]['starttime']) ? $results[0]['starttime'] * 0.02 : 0;
    ?>

    <script>
        // PHPから取得したデータをJavaScriptで利用
        const rectangleData = <?php echo $rectangleData; ?>;
        const firstX = <?php echo json_encode($firstX); ?>;

        console.log("rectangleData:", rectangleData);
        console.log("firstX:", firstX);

        // キャンバスの設定
        const canvas = document.getElementById('myCanvas');
        const ctx3 = canvas.getContext('2d');
        const slider = document.getElementById('slider');
        // const rate = 0.02;

        const maxCanvasWidth = 800;
      const maxCanvasHeight = 100; // 1つのグラフの高さ
      const maxDataValue = 80000; // x 軸のデータ最大値
      const maxValue = 100; // energy,stress,concentration の最大値

      const xScale = maxCanvasWidth / maxDataValue;
      const yScale = maxCanvasHeight / maxValue;


        // 四角形を描画する関数(energy用)
        function drawRectangle_energy(starttime, endtime, energy) {
            console.log("drawRectangle_energy");
            console.log(starttime, endtime, (-1) * energy);

            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = energy * yScale; // エネルギーを高さとして使用

            console.log("Scaled values:", scaledStart, scaledEnd, scaledheight);
            ctx3.fillStyle = "orange";
            ctx3.fillRect(scaledStart, maxCanvasHeight, scaledEnd - scaledStart, (-1) * scaledheight);
            
        }
 
        // 四角形を描画する関数(stress用)
        function drawRectangle_stress(starttime, endtime, stress) {
            console.log("drawRectangle_stress");
            console.log(starttime, endtime, (-1) * stress);

            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = stress * yScale; // ストレスを高さとして使用

            console.log("Scaled values:", scaledStart, scaledEnd, scaledheight);
            ctx3.fillStyle = "gray";
            ctx3.fillRect(scaledStart, maxCanvasHeight *2 , scaledEnd - scaledStart, (-1) * scaledheight);
            
        }

          // 四角形を描画する関数(concentration用)
         function drawRectangle_concentration(starttime,endtime, concentration) {
            console.log("drawRectangle_concentration");
            console.log(starttime, endtime, (-1) * concentration);

            const scaledStart = starttime * xScale; // スケーリングされた開始位置
            const scaledEnd = endtime * xScale; // スケーリングされた終了位置
            const scaledheight = concentration * yScale; // 集中を高さとして使用

            console.log("Scaled values:", scaledStart, scaledEnd, scaledheight);
            ctx3.fillStyle = "blue";
            ctx3.fillRect(scaledStart, maxCanvasHeight *3 , scaledEnd - scaledStart, (-1) * scaledheight);
            
        }

        // 四角形を再描画する関数
        function redrawRectangles(offset) {
          console.log("redrawRectangles called with offset:", offset); // 呼び出しを確認
            ctx3.clearRect(0, 0, canvas.width, canvas.height); // キャンバスのクリア
            
            // energyのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, energy } = item;
                console.log ("redrawRectangles");
                console.log ((starttime * xScale) - offset, (endtime * xScale) - offset, energy);
                const validEnergy = energy !== undefined && energy !== null ? energy : 0; // デフォルト値0
                drawRectangle_energy(starttime - offset, endtime - offset, validEnergy);
                // drawRectangle_energy((starttime * xScale) - offset, (endtime * xScale) - offset, energy);
            });
            //stressのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, stress } = item;
                const validStress = stress !== undefined && stress !== null ? stress : 0; // デフォルト値0
                drawRectangle_stress(starttime - offset, endtime - offset, validStress);
                // drawRectangle_energy((starttime * xScale) - offset, (endtime * xScale) - offset, energy);
            });

            //concentrationのグラフを再描画
            rectangleData.forEach(item => {
                const { starttime, endtime, concentration } = item;
                const validconcentration = concentration !== undefined && concentration !== null ? concentration : 0; // デフォルト値0
                drawRectangle_concentration(starttime - offset, endtime - offset, validconcentration);
                // drawRectangle_energy((starttime * xScale) - offset, (endtime * xScale) - offset, energy);
            });
        }

        // 初期描画
        redrawRectangles(firstX);

        // スライダーのイベントリスナー
        slider.addEventListener('input', (event) => {
            const offset = event.target.value;
            redrawRectangles(offset);
        });
    </script>
</body>
</html>
