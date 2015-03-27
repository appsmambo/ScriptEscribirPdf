<?php
ini_set('memory_limit', '512M');

$degrees = 90;

//define image path
$filename = 'temp/archivo.jpg';

// Load the image
$source = imagecreatefromjpeg($filename);

// Rotate
$rotate = imagerotate($source, $degrees, 0);

imagejpeg($rotate, 'temp/nuevo.jpg');
//and save it on your server...
//file_put_contents('temp/nuevo.jpg', $rotate);