<?php
session_start();

// Hapus captcha lama dulu
unset($_SESSION['captcha']);

$width = 130;
$height = 45;

$image = imagecreate($width, $height);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

// Buat kode captcha
$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $code;

// Noise garis
for ($i = 0; $i < 6; $i++) {
    $noise_color = imagecolorallocate($image, rand(150,255), rand(150,255), rand(150,255));
    imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $noise_color);
}

// Tulis text
imagestring($image, 5, 30, 15, $code, $text_color);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
exit;
