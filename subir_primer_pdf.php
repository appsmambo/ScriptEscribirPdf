<?php
error_reporting(E_ALL);
// pixel a milimetro
define('P2M', 0.264583333333334);
// inicio del contador
define('CONTADOR', 25);

if (!isset($_FILES['archivoPdf'])) {
	exit('ya fue, gg tim nub');
}

$rotar = isset($_POST['rotar']) ? $_POST['rotar'] : '0';

// info de las imagenes
$infoImagen = array();

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



// datos de la primera imagen
$imagen = $rutaTemporal . $aleatorio . "-0.jpg";
$image = new Imagick($imagen);
$infoImagen = $image->getImageGeometry();

// generar el nuevo PDF
require('scripts/fpdf/fpdf.php');

class PDF extends FPDF {
	const DPI = 96;
	const MM_IN_INCH = 25.4;
	const A4_HEIGHT = 297;
	const A4_WIDTH = 210;
	// tweak these values (in pixels)
	const MAX_WIDTH = 800;
	const MAX_HEIGHT = 500;

	function pixelsToMM($val) {
		return $val * self::MM_IN_INCH / self::DPI;
	}

	function resizeToFit($imgFilename) {
		list($width, $height) = getimagesize($imgFilename);

		$widthScale = self::MAX_WIDTH / $width;
		$heightScale = self::MAX_HEIGHT / $height;

		$scale = min($widthScale, $heightScale);

		return array(
			round($this->pixelsToMM($scale * $width)),
			round($this->pixelsToMM($scale * $height))
		);
	}

	function centreImage($img) {
		list($width, $height) = $this->resizeToFit($img);

		// you will probably want to swap the width/height
		// around depending on the page's orientation
		$this->Image(
			$img, (self::A4_WIDTH - $height) / 2,
			(self::A4_HEIGHT - $width) / 2,
			$height,
			$width
		);
	}
}

// el objeto FPDF
$pdf = new PDF();
$pdf->AliasNbPages();

// recorrer las paginas
for ($i = 0; $i < $totalPaginas; $i++) {
	$imagen = $rutaTemporal . $aleatorio . "-$i.jpg";

	// validar que archivo exista
	if (file_exists($imagen)) {
		// agregar pagina al nuevo PDF
		$pdf->AddPage('P');

		// imprimir la imagen
		$pdf->centreImage($imagen);
		
		// imprimir el contador
		$contador = '00000' . (CONTADOR + $i);
		$contador = substr($contador, -4);
		$pdf->SetFont('Arial', 'B', 35);
		$pdf->Cell(0, 35, $contador, 0, 0, 'R');
	}
}

// borrar los archivos temporales
array_map('unlink', glob($rutaTemporal . $aleatorio . '*.*'));

// guardar el archivo
$archivo = $rutaTemporal . 'archivo.pdf';
$pdf->Output($archivo,'F');

// imprimir pdf en pantalla
$pdf->Output();