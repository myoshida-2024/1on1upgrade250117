<script>
import React, { useRef, useEffect, useState } from "react";

const HorizontalScrollViewer = () => {
    const imageRef = useRef(null);
    const [scrollMax, setScrollMax] = useState(0);

    useEffect(() => {
        if (imageRef.current) {
            const img = imageRef.current;
            const containerWidth = img.parentElement.offsetWidth; // 表示領域の幅
            const imageWidth = img.naturalWidth; // 元の画像の幅
            setScrollMax(imageWidth - containerWidth); // スクロール可能な最大値
        }
    }, []);

    const handleScroll = (e) => {
        const offset = e.target.value;
        imageRef.current.style.transform = `translateX(${-offset}px)`;
    };

    return (
        <div style={{ display: "flex", flexDirection: "column", alignItems: "center" }}>
            <h1>Horizontal Scroll Viewer</h1>
            <div
                style={{
                    overflow: "hidden",
                    width: "80%", // 表示領域の幅
                    height: "300px", // 表示領域の高さ
                    border: "1px solid black",
                    position: "relative",
                }}
            >
                <img
                    ref={imageRef}
                    src="img/rectangle_chart.png"
                    alt="Horizontal Image"
                    style={{
                        position: "absolute",
                        top: 0,
                        left: 0,
                        height: "100%",
                        width: "auto",
                        transition: "transform 0.2s ease",
                    }}
                />
            </div>
            <input
                type="range"
                min="0"
                max={scrollMax}
                step="1"
                defaultValue="0"
                onInput={handleScroll}
                style={{ width: "80%", marginTop: "20px" }}
            />
        </div>
    );
};

export default HorizontalScrollViewer;
</script>