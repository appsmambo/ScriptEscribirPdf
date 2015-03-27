<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Documento sin título</title>
	</head>
	<body>
		<table width="642" border="0">
			<tr>
				<td>Subir primer PDF</td>
			</tr>
			<tr>
				<td>
					<form action="subir_primer_pdf.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
						<input type="file" name="archivoPdf" id="pdf1" />
						<br />
						<br />
						<input type="checkbox" name="rotar" value="1" /> Rotar páginas
						<br />
						<br />
						<input type="submit" name="button" id="button" value="Subir Primer PDF" />
					</form>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	</body>
</html>