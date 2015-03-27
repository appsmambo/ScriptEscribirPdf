<?php
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
// pixel a milimetro
define('P2M', 0.264583333333334);
// inicio del contador
define('CONTADOR', 25);
// para rotar
define('DEGREES', 90);

if (!isset($_FILES['archivoPdf'])) {
	exit('ya fue, gg tim nub');
}

// info de las imagenes
$infoImagen = array();

$rotar = isset($_POST['rotar']) ? $_POST['rotar'] : '0';

// ruta temporal en el servidor
//$rutaTemporal = __DIR__ . '/temp/';
$rutaTemporal = __DIR__ . '/pdf/';


// para los archivos temporales
$aleatorio = '';
$serie = 'abcdefghijklmnopqrstuvwxyz0123456789';
$largo = strlen($serie);
for ($i = 0; $i < 8; $i++) {
	$aleatorio .= $serie[rand(0, $largo - 1)];
}

// archivo pdf temporal
$pdfTemporal = $rutaTemporal . $aleatorio . '.pdf';

// grabar el pdf en la ruta
move_uploaded_file($_FILES['archivoPdf']['tmp_name'], $pdfTemporal);

// obtener el numero de paginas del pdf
$pdftext = file_get_contents($pdfTemporal);
$totalPaginas = preg_match_all("/\/Page\W/", $pdftext,$dummy);

// generar jpg de cada pagina del pdf
for ($i = 0; $i < $totalPaginas; $i++) {
	$imagen = $rutaTemporal . $aleatorio . "-$i.jpg";
	exec('convert -density 300 '.$pdfTemporal.'['.$i.'] '.$imagen);
}

// rotar las imágenes
if ($rotar == 1) {
	for ($i = 0; $i < $totalPaginas; $i++) {
		$imagen = $rutaTemporal . $aleatorio . "-$i.jpg";
		// validar que archivo exista
		if (file_exists($imagen)) {
			$imagen_rotada = $rutaTemporal . $aleatorio . "-r-$i.jpg";
			$source = imagecreatefromjpeg($imagen);
			$rotate = imagerotate($source, DEGREES, 0);
			imagejpeg($rotate, $imagen_rotada);
		}
	}
}

// datos de la primera imagen
$imagen = $rutaTemporal . $aleatorio . "-0.jpg";
$image = new Imagick($imagen);
$infoImagen = $image->getImageGeometry();

// generar el nuevo PDF
require('scripts/fpdf/fpdf.php');

// el objeto FPDF
$pdf = new FPDF('P','mm',array($infoImagen['width']*P2M, $infoImagen['height']*P2M));
$pdf->AliasNbPages();

// recorrer las paginas
for ($i = 0; $i < $totalPaginas; $i++) {
	// si se rota
	if ($rotar == 1) {
		$imagen = $rutaTemporal . $aleatorio . "-r-$i.jpg";
	} else {
		$imagen = $rutaTemporal . $aleatorio . "-$i.jpg";
	}
	
	// validar que archivo exista
	if (file_exists($imagen)) {
		// para saber el tamaño del foliado
		list($width, $height) = getimagesize($imagen);
		$fontSize = 18;
		if ($width > 1000)
			$fontSize = 25;
		if ($width > 1500)
			$fontSize = 30;
		if ($width > 2000)
			$fontSize = 40;
		if ($width > 3000)
			$fontSize = 60;
		if ($width > 4000)
			$fontSize = 80;
		if ($width > 5000)
			$fontSize = 100;

		// agregar pagina al nuevo PDF
		$pdf->AddPage();

		// imprimir la imagen
		$pdf->Image($imagen, 0, 0);
		
		// imprimir el contador
		$contador = '00000' . (CONTADOR + $i);
		$contador = substr($contador, -4);
		$pdf->SetFont('Arial', 'B', $fontSize);
		$pdf->Cell(0, 42, $contador, 0, 0, 'R');
	}
}

// borrar los archivos temporales
//array_map('unlink', glob($rutaTemporal . $aleatorio . '*.*'));

// guardar el archivo
$archivo = $rutaTemporal . 'archivo.pdf';
$pdf->Output($archivo,'F');

// imprimir pdf en pantalla
$pdf->Output();