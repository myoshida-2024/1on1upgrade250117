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
    <!-- キャンバス -->
    <canvas id="myCanvas" width="800" height="600" style="border:1px solid #000000;"></canvas>
    
    <?php
    // PHPでデータを取得し、JavaScriptに渡す準備をする
    include("funcs.php");
    $pdo = db_conn();

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
  
    ?>

    <script>
        // PHPから取得したデータをJavaScriptで利用
        
        const speakerData = <?php echo $speakerData; ?>;
        console.log("speakerData:", speakerData);
        

        // キャンバスの設定
        const canvas = document.getElementById('myCanvas');
        const ctx3 = canvas.getContext('2d');
       
        // const rate = 0.02;

        const maxCanvasWidth = 800;
        const maxCanvasHeight = 100; // 1つのグラフの高さ
        const maxDataValue = 80000; // x 軸のデータ最大値
        const maxValue = 100; // energy,stress,concentration の最大値
        const speakerH = 20;

        const xScale =  maxCanvasWidth / maxDataValue;
        const yScale = maxCanvasHeight / maxValue;

        const resultMatrix = [];

        speakerData.forEach(item => {
            const { label, starttime, endtime } = item;
            const duration = endtime - starttime; // 差を計算
            resultMatrix.push([label, starttime, endtime, duration]);
             
        });

        // starttime（インデックス1）を基準にソート
        resultMatrix.sort((a, b) => a[1] - b[1]); // インデックス1はstarttime
        console.log(resultMatrix);


            // 四角形を描画する関数(speaker用)
            function drawPie(label, starttime, endtime) {
            
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

        // 四角形を再描画する関数
        // function redrawPie() {
          
            // ctx3.clearRect(0, 0, canvas.width, canvas.height); // キャンバスのクリア
            // speakerのグラフを再描画
            // speakerData.forEach(item => {
                // const { label, starttime, endtime } = item;
                // drawSpeaker(label, starttime - offset, endtime - offset);   
            // });
            
        // }

       
    </script>
</body>
</html>
