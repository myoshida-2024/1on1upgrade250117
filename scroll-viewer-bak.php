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
            overflow: hidden;
            width: 100%;
            height: 300px; /* 縦サイズ固定 */
            position: relative;
            border: 1px solid black;
        }
        img {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%; /* 縦サイズを表示領域に合わせる */
            transform: scaleX(0.1); /* 横幅を1/10に縮小 */
            transform-origin: top left; /* 縮小の基準点を左上に設定 */
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

        // スライダーの最大値を画像の縮小後の横幅に依存させる
        chartImage.onload = () => {
            const imageWidth = chartImage.naturalWidth * 0.1; // 横幅を1/10に縮小
            const containerWidth = imageContainer.offsetWidth; // 表示領域の幅
            timeSlider.max = Math.max(0, imageWidth - containerWidth); // スライダーの範囲を設定
        };

        // スライダーを動かすと画像をスクロール
        timeSlider.addEventListener("input", () => {
            const offset = -timeSlider.value; // スライダーの値をオフセットとして使用
            chartImage.style.left = `${offset}px`; // 画像を移動
        });
    </script>
</body>
</html>
