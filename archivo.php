<?php
ini_set('memory_limit', '256M');

//define image path
$filename = 'temp/archivo.jpg';

// Load the image
$source = imagecreatefromjpeg($filename);

// Rotate
$rotate = imagerotate($source, $degrees, 0);

//and save it on your server...
file_put_contents('temp/nuevo.jpg', $rotate);