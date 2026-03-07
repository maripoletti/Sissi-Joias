<?php

declare(strict_types= 1);

class imageUpload {
    private string $uploadDir;

    public function __construct(?string $dir = null) {
        if (!$dir) {
            $dir = realpath(__DIR__ . '/../../www/uploads') ?: __DIR__ . '/../../www/uploads';
        }
        $this->uploadDir = $dir;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    public function image(array $file): ?string {
        if (empty($file['tmp_name'])) return null;

        $nomeOriginal = $file['name'];
        $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $permitidos = ['jpg','jpeg','png','webp'];

        if(!in_array($ext, $permitidos)) return null;

        $novoNome = uniqid() . '.' . $ext;
        $destino = $this->uploadDir . '/' . $novoNome;

        list($width, $height) = getimagesize($file['tmp_name']);
        $max = 300;

        if($width > $height) {
            $newWidth = $max;
            $newHeight = intval($height * $max/$width);
        } else {
            $newHeight = $max;
            $newWidth = intval($width * $max/$height);
        }

        switch($ext) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($file['tmp_name']);
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImg, $img, 0,0,0,0, $newWidth, $newHeight, $width, $height);
                imagejpeg($newImg, $destino, 70);
                break;
            case 'png':
                $img = imagecreatefrompng($file['tmp_name']);
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                imagecopyresampled($newImg, $img, 0,0,0,0, $newWidth, $newHeight, $width, $height);
                imagepng($newImg, $destino, 6);
                break;
            case 'webp':
                $img = imagecreatefromwebp($file['tmp_name']);
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImg, $img, 0,0,0,0 , $newWidth, $newHeight, $width, $height);
                imagewebp($newImg, $destino, 70);
                break;
        }

        return '/uploads/' . $novoNome;
    }
}