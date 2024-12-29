<?php
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['image']) && isset($data['filename'])) {
    $imageData = $data['image'];
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedImage = base64_decode($imageData);

    $filePath = __DIR__ . '/' . $data['filename'];
    file_put_contents($filePath, $decodedImage);

    echo "画像が保存されました: " . $data['filename'];
} else {
    echo "画像データまたはファイル名が見つかりません";
}
?>
