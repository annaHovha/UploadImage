<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $file = $_FILES["image"];
    $fileName = $file["name"];
    $fileTmp = $file["tmp_name"];
    $fileError = $file["error"];

    if ($fileError !== UPLOAD_ERR_OK) {
        echo "Error uploading file. Error code: $fileError";
        exit;
    }

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        echo 'Unsupported file type. Please upload a JPG, JPEG, PNG, or GIF file.';
        exit;
    }

    list($width, $height) = getimagesize($fileTmp);
    $newWidth = 200;
    $newHeight = ($height / $width) * $newWidth;
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    switch ($fileExt) {
        case 'jpg':
        case 'jpeg':
            $sourceImage = imagecreatefromjpeg($fileTmp);
            break;
        case 'png':
            $sourceImage = imagecreatefrompng($fileTmp);
            break;
        case 'gif':
            $sourceImage = imagecreatefromgif($fileTmp);
            break;
    }

    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $resizedFileName = "resized_" . $fileName;
    $resizedFilePath = "uploads/" . $resizedFileName;
    switch ($fileExt) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($resizedImage, $resizedFilePath);
            break;
        case 'png':
            imagepng($resizedImage, $resizedFilePath);
            break;
        case 'gif':
            imagegif($resizedImage, $resizedFilePath);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($sourceImage);

    echo 'Image uploaded and resized successfully. <br>';
    echo "Original Image:<br><img src='data:image/" . $fileExt . ";base64," . base64_encode(file_get_contents($fileTmp)) . "'><br>";
    echo "Resized Image:<br><img src='$resizedFilePath'>";
} else {
    echo 'Please upload an image file.';
}
