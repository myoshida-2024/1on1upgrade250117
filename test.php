<?php 
include("funcs.php");
$db = db_conn(); // PHP内でdb_connを使用
// データを処理し、必要に応じてJavaScriptに渡す
$practicedata = [
    ['starttime' => 129354, 'y' => 50, 'duration' => 368, 'colorR' => 10, 'colorG' => 200, 'colorB' => 0],
    ['starttime' => 129722, 'y' => 50, 'duration' => 192, 'colorR' => 10, 'colorG' => 200, 'colorB' => 0],
    ['starttime' => 130282, 'y' => 100, 'duration' => 32, 'colorR' => 10, 'colorG' => 0, 'colorB' => 200]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
        console.log("488行目に到達");

        // PHPでデータをJSONにエンコードし、JavaScriptで使えるようにします。
        const practicedata = <?php echo json_encode($practicedata); ?>;
        
        practicedata.forEach((item) => {
            sendData(item.starttime, item.y, item.duration, item.colorR, item.colorG, item.colorB);
        });

       // sendData関数定義
       function sendData(starttime, y, duration, colorR, colorG, colorB) {
            console.log("sendData");
            var data = `starttime=${starttime}&y=${y}&duration=${duration}&colorR=${colorR}&colorG=${colorG}&colorB=${colorB}`;
            console.log("送信データ:", data); // データ内容を確認
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "write.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("データが送信されました");
                    console.log("サーバーからの応答: " + xhr.responseText);
                } else if (xhr.readyState === 4) {
                    console.log("リクエストに失敗しました。ステータス: " + xhr.status);
                }
            };
            
            xhr.send(data); // sendはxhr設定が完了した後に
        }
    </script>
</body>
</html>
