<?php
	require_once("../../fpdf/rotation.php");
	require_once("../../fpdf/barcode.inc.php");
	require_once("../../admin/class.php");

	class PDF extends PDF_Rotate {   
	    var $widths;
	    var $aligns;       
	    function SetWidths($w){            
	        $this->widths = $w;
	    }  

	    function Header() {                         
	        $this->AddFont('Amble-Regular','','Amble-Regular.php');
	        $this->SetFont('Amble-Regular','',10);          
		}

	    function Footer() {            
	        $this->SetY(-10);            
	        $this->SetFont('Arial','I',8);            
	        $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C');
	    }  

	   	function RotatedImage($file, $x, $y, $w, $h, $angle) {            
	        $this->Rotate($angle, $x, $y);
	        $this->Image($file, $x, $y, $w, $h);
	        $this->Rotate(0);
	    }

	    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
		    $k = $this->k;
		    $hp = $this->h;
		    if($style == 'F')
		        $op='f';
		    elseif($style == 'FD' || $style == 'DF')
		        $op ='B';
		    else
		        $op ='S';
		    $MyArc = 4/3 * (sqrt(2) - 1);
		    $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
		    $xc = $x+$w-$r ;
		    $yc = $y+$r;
		    $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

		    $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		    $xc = $x+$w-$r ;
		    $yc = $y+$h-$r;
		    $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
		    $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		    $xc = $x+$r ;
		    $yc = $y+$h-$r;
		    $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
		    $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
		    $xc = $x+$r ;
		    $yc = $y+$r;
		    $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
		    $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		    $this->_out($op);
		}

		function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		    $h = $this->h;
		    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
		        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
		}       		         
	}			
	if(isset($_GET['id'])) {		
		$id = $_GET['id'];
		generarPDF($id);
	}

	function generarPDF($id) {
		$class = new constante();
		$resultado = $class->consulta("SELECT E.ruc, R.numero_comprobante, R.numero_autorizacion, R.fecha_emision, R.clave_acceso, E.razon_social, E.nombre_comercial, E.direccion_matriz, E.direccion_establecimiento, E.contribuyente, E.obligacion, P.razon_social, P.identificacion, P.direccion, P.telefono2, P.correo, TC.nombre_tipo_comprobante, R.secuencial, E.establecimiento, E.punto_emision, R.fecha_autorizacion, R.ambiente, R.emision, E.imagen FROM empresa E, retencion R, proveedores P, tipo_comprobante TC, tipo_documento TD WHERE R.id_proveedor = P.id AND P.id_tipo_documento = TD.id AND R.id_tipo_comprobante = TC.id AND R.id_empresa = E.id AND R.id = '".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$ruc = $row[0];
			$numeroComprobante = $row[1];
			$numeroAutorizacion = $row[2];
			$fechaEmision = $row[3];
			$claveAcceso = $row[4];
			$razonSocial = $row[5];
			$nombreComercial = $row[6];
			$direcionMatriz = $row[7];
			$direccionEstablecimiento = $row[8];
			$nroContribuyente = $row[9];
			$obligado = $row[10];
			$contribuyente = $row[11];
			$identificacion = $row[12];
			$direcion = $row[13];
			$telefono = $row[14];
			$email = $row[15];
			$tipoDocumento = $row[16];
			$secuencial = $row[17];
			$establecimiento = $row[18];
			$puntoEmision = $row[19];
			$fechaAut = $row[20];
			$ambiente = $row[21];
			$emision = $row[22];
			$imagen = $row[23];
		}	

		// parametro ambiente
		$resultado = $class->consulta("SELECT nombre_tipo_ambiente FROM tipo_ambiente WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		// parametro emision
		$resultado = $class->consulta("SELECT nombre_tipo_emision FROM tipo_emision WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
		}
		// fin

		$ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
	  	for ($i = 0; $i < $tam; $i++) {                 
	    	$temp = $temp .'0';        
	  	}
	  	$secuencial = $temp .''. $secuencial;

		$pdf = new PDF('P','mm','a4');
		$pdf->AddPage();
		$pdf->SetMargins(10,0,0,0);        
		$pdf->AliasNbPages();
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->AddFont('Amble-Regular','','Amble-Regular.php');
		$pdf->SetFont('Amble-Regular','',10); 

		$logo = $imagen;
		$pdf->Image('../../data/empresa/logo/'.$logo,25,15,50,20); // Img Empresa

		$pdf->SetLineWidth(0.2);
    	$pdf->SetFillColor(255,255,255);

    	$pdf->RoundedRect(3, 45, 100, 53, 3, 'DF');
		$pdf->Text(5, 50, utf8_decode($razonSocial));
		$pdf->SetFont('Arial','B',8);	
		$pdf->Text(5, 65, utf8_decode('Dirección Matriz:')); // Dirección Matriz
		$pdf->SetFont('Arial','',8);	
		$pdf->Text(30, 65, utf8_decode($direcionMatriz));
		$pdf->SetFont('Arial','B',8);	
		$pdf->Text(5, 75, utf8_decode('Dirección Sucursal:')); // Dirección Establecimiento
		$pdf->SetFont('Arial','',8);	
		$pdf->Text(34, 75, utf8_decode($direccionEstablecimiento));
		$pdf->SetFont('Arial','B',9);	
		$pdf->Text(5, 96, utf8_decode('OBLIGADO A LLEVAR CONTABILIDAD: '.$obligado)); // Obligado

		$pdf->RoundedRect(106, 8, 102, 90, 3, 'DF');
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(108, 15, 'R.U.C.:'); // ruc
		$pdf->SetFont('Arial','',12);
		$pdf->Text(125, 15, $ruc); // ruc
		$pdf->SetFont('Arial','B',12);		 	
		$pdf->Text(108, 23, utf8_decode("RETENCIÓN")); // Tipo comprobante
		$pdf->SetFont('Arial','',10);
		$pdf->Text(108, 31, 'No. '. $establecimiento.'-'.$puntoEmision.'-'.$secuencial); // Secuencial
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(108, 39, utf8_decode('NÚMERO DE AUTORIZACIÓN')); // N° Autorizacion
		$pdf->SetY(40);
		$pdf->SetX(107);	
		$pdf->SetFont('Arial','',10);
		$pdf->Multicell(100, 5, $numeroAutorizacion,0); // N° Autorización	
		$pdf->SetFont('Arial','B',10);	
		$pdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN')); // fecha y hora de autorizacion
		$pdf->SetFont('Arial','',10);
		$pdf->Text(108, 55, $fechaAut); // FECHA
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(108, 61, utf8_decode('AMBIENTE:')); // Ambiente
		$pdf->SetFont('Arial','',10);
		$pdf->Text(130, 61, utf8_decode($ambiente)); 
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(108, 67, utf8_decode('EMISIÓN:')); // Emision
		if ($emision == "Emisión Normal") {
			$pdf->SetFont('Arial','',10);
			$pdf->Text(130, 67, utf8_decode('NORMAL')); 
		}
		$pdf->Text(108, 75, utf8_decode('CLAVE DE ACCESO: ')); // Clave de acceso
		$code_number = $claveAcceso; // Código de barras		
		new barCodeGenrator($code_number,1,'temp.gif', 470, 60, true); /// img codigo barras	
		$pdf->Image('temp.gif',108,77,97,15);   	

		$pdf->Rect(3, 101, 205, 20 , 'D'); // 4 INFO TRIBUTARIA
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 105, utf8_decode('Razón Social / Nombres y Apellidos:')); // Nombre contribuyente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(62, 105, utf8_decode($contribuyente));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(150, 105, utf8_decode('RUC / CI:')); // Ruc contribuyente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(165, 105, utf8_decode($identificacion));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 117, utf8_decode('Fecha de Emisión:')); //fecha emision contribuyente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(35, 117, utf8_decode($fechaEmision));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(136, 117, utf8_decode('Guía de Remisión:' )); // guia remision 

		//////////////////detalles factura/////////////
	    $pdf->SetFont('Amble-Regular','',9);               
	    $pdf->SetY(123);
		$pdf->SetX(3);
		$pdf->multiCell(50, 10, utf8_decode('Comprobante'), 1);
		$pdf->SetY(123);
		$pdf->SetX(53);
		$pdf->multiCell(32, 10, utf8_decode('Número'), 1);
		$pdf->SetY(123);
		$pdf->SetX(85);
		$pdf->multiCell(20, 5, utf8_decode('Fecha Emisión'), 1);
		$pdf->SetY(123);
		$pdf->SetX(105);
		$pdf->multiCell(15, 5, utf8_decode('Ejercicio Fiscal'), 1);
		$pdf->SetY(123);
		$pdf->SetX(120);
		$pdf->multiCell(28, 5, utf8_decode('Base Imponible para la Retención'), 1);
		$pdf->SetY(123);
		$pdf->SetX(148);
		$pdf->multiCell(20, 10, utf8_decode('Impuesto'), 1);
		$pdf->SetY(123);
		$pdf->SetX(168);
		$pdf->multiCell(20, 5, utf8_decode('Porcentaje Retención'), 1);
		$pdf->SetY(123);
		$pdf->SetX(188);
		$pdf->multiCell(20, 5, utf8_decode('Valor Retenido'), 1);
		
		////DETALLES COMPROBANTE////
		$resultado = $class->consulta("SELECT R.mes, R.anio, D.base_imponible, TR.nombre_tipo_retencion, D.porcentaje, D.valor_retenido FROM retencion R, detalle_retencion D, tipo_retencion TR, tarifa_retencion T WHERE D.id_retencion = R.id AND D.id_tarifa_retencion = T.id AND D.id_tipo_retencion = TR.id AND R.id = '".$id."'");
		$x = 133;
		$y = 3;
		while ($row = $class->fetch_array($resultado)) {			
			$pdf->SetY($x);
			$pdf->SetX(3);
			$comprobante = utf8_decode($tipoDocumento);
			if(strlen($comprobante) > 25)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(50, $tam, $comprobante, 1);

			$pdf->SetY($x);
			$pdf->SetX(53);
			$numero = utf8_decode($numeroComprobante);
			if(strlen($numero) > 19)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(32, $tam, $numero, 1);

			$pdf->SetY($x);
			$pdf->SetX(85);
			$fechaEmision = utf8_decode($fechaEmision);
			if(strlen($fechaEmision) > 10)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(20, $tam, $fechaEmision, 1);

			$pdf->SetY($x);
			$pdf->SetX(105);
			$ejercicioFiscal = utf8_decode($row[0].'/'.$row[1]);
			if(strlen($ejercicioFiscal) > 10)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(15, $tam, $ejercicioFiscal, 1);
			
			$pdf->SetY($x);
			$pdf->SetX(120);
			$baseImponible = utf8_decode($row[2]);
			if(strlen($baseImponible) > 19)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(28, $tam, $baseImponible, 1);

			$pdf->SetY($x);
			$pdf->SetX(148);
			$impuesto = utf8_decode($row[3]);
			if(strlen($impuesto) > 15)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(20, $tam, $impuesto, 1);

			$pdf->SetY($x);
			$pdf->SetX(168);
			$porcentaje = utf8_decode($row[4]);
			if(strlen($porcentaje) > 10)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(20, $tam, $porcentaje, 1);

			$pdf->SetY($x);
			$pdf->SetX(188);
			$valorRetenido = utf8_decode($row[5]);
			if(strlen($valorRetenido) > 15)
				$tam = 5;
			else
				$tam = 10;	
			$pdf->multiCell(20, $tam, $valorRetenido, 1);	

			$x = $x + 10;
		}						
		/////////////////pie de pagina//////////	           	
		$pdf->Ln(5);
		$pdf->SetX(3);	
	    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 100, 55 , 'D'); // 3 INFO ADICIONAL
		$y =  $pdf->GetY();
		$x =  $pdf->GetX();	
		$pdf->Text($x + 5, $y + 5, utf8_decode('INFORMACIÓN ADICIONAL')); // informacion 		
		$pdf->SetY($y + 7);
		$pdf->SetX($x);
		$pdf->multiCell(100, 5, utf8_decode("Dirección:".$direcion ), 0);
		$pdf->SetY($y + 17);
		$pdf->SetX($x);
		$pdf->multiCell(100, 5, utf8_decode("Teléfono: ".$telefono ), 0);
		$pdf->SetY($y + 29);
		$pdf->SetX($x);
		$pdf->multiCell(100, 5, utf8_decode("Email: ".$email ), 0);

		if(isset($_GET['id'])) {
			$pdf->Output();		
		} else {
			$pdf_file_contents = $pdf->Output("","S");		
			return $pdf_file_contents;
		}
	}
?>