<?php
error_reporting(E_ALL);
// pixel a milimetro
define('P2M', 0.264583333333334);
// inicio del contador
define('CONTADOR', 25);

if (!isset($_FILES['archivoPdf'])) {
	exit('ya fue, gg tim nub');
}

// info de las imagenes
$infoImagen = array();

// ruta temporal en el servidor
$rutaTemporal = __DIR__ . '/temp/';

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

// el objeto FPDF
$pdf = new FPDF('P','mm',array($infoImagen['width']*P2M, $infoImagen['height']*P2M));
$pdf->AliasNbPages();

// recorrer las paginas
for ($i = 0; $i < $totalPaginas; $i++) {
	// agregar pagina al nuevo PDF
	$pdf->AddPage();

	// imprimir la imagen
	$imagen = $rutaTemporal . $aleatorio . "-$i.jpg";
	$pdf->Image($imagen, 0, 0);
	
	// imprimir el contador
	$contador = '00000' . (CONTADOR + $i);
	$contador = substr($contador, -4);
	$pdf->SetFont('Arial', 'B', 35);
	$pdf->Cell(0, 35, $contador, 0, 0, 'R');
}

// borrar los archivos temporales
array_map('unlink', glob($rutaTemporal . $aleatorio . '*.*'));

// guardar el archivo
$archivo = $rutaTemporal . 'archivo.pdf';
$pdf->Output($archivo,'F');

// imprimir pdf en pantalla
$pdf->Output();
