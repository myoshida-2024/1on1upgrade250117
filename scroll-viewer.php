<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time-Slider View</title>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #imageContainer {
            overflow-x: ;
            width: 100%; /* 表示領域をページ幅に調整 */
            height: 300px; /* 縦サイズ固定 */
            position: relative;
            border: 1px solid black;
        }
        img {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%; /* 縦サイズを表示領域に合わせる */
            width: auto; /* 横幅を自動調整 */
            image-rendering: pixelated; /* ピクセル単位で表示 */
            image-rendering: -moz-crisp-edges; /* Firefox対応 */
            image-rendering: -webkit-optimize-contrast; /* Safari対応 */
        }
        #sliderContainer {
            margin: 20px 0;
            width: 80%; /* スライダーの幅 */
        }
    </style>
</head>
<body>
    <h1>Rectangle Chart Viewer</h1>
    <div id="imageContainer">
        <img id="chartImage" src="img/rectangle_chart.png" alt="Rectangle Chart">
    </div>
    <div id="sliderContainer">
        <input type="range" id="timeSlider" min="0" max="100" value="0">
    </div>
    <script>
        const chartImage = document.getElementById("chartImage");
        const timeSlider = document.getElementById("timeSlider");
        const imageContainer = document.getElementById("imageContainer");

        // スライダーと画像の初期設定
        chartImage.onload = () => {
            const imageWidth = chartImage.naturalWidth; // 元の横幅
            console.log ("imageWidth", imageWidth);
            const scale = imageContainer.offsetHeight / chartImage.naturalHeight; // 縦サイズに基づく縮小スケール
            const scaledWidth = imageWidth * scale; // 縮小後の横幅
            const containerWidth = imageContainer.offsetWidth; // 表示領域の幅

            // スライダーの最大値を設定
            timeSlider.max = Math.max(0, scaledWidth - containerWidth);
            // timeSlider.max = Math.max(0, (imageWidth * scale) - containerWidth);

            // 画像のスケールを適用
            chartImage.style.height = `${imageContainer.offsetHeight}px`;
            chartImage.style.width = `${scaledWidth}px`;
        };

        // スライダーの値に応じて画像をスクロール
        timeSlider.addEventListener("input", () => {
            const offset = -timeSlider.value; // スライダーの値を負にして左方向にスクロール
            chartImage.style.left = `${offset}px`;
        });
    </script>
</body>
</html>
