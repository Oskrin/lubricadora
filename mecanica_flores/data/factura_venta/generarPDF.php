<?php
	require_once("../../fpdf/rotation.php");
	require_once("../../fpdf/barcode.inc.php");
	require_once("../../admin/class.php");

	class PDF extends PDF_Rotate {   
	    var $widths;
	    var $aligns;       
	    function SetWidths($w) {            
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
		$resultado = $class->consulta("SELECT E.ruc, F.numero_autorizacion, F.fecha_emision, F.clave_acceso, E.razon_social, E.nombre_comercial, E.direccion_matriz, E.direccion_establecimiento, E.contribuyente, E.obligacion, C.razon_Social, C.identificacion, C.direccion, C.telefono1, C.correo, F.secuencial, F.establecimiento, F.punto_emision, F.fecha_autorizacion, TC.codigo, F.ambiente, F.emision, E.imagen FROM factura_venta F, clientes C, empresa E, tipo_documento TC WHERE F.id_cliente = C.id AND C.id_tipo_documento = TC.id AND F.id_empresa = E.id AND F.id = '".$id."'");	
		while ($row = $class->fetch_array($resultado)) {
			$ruc = $row[0];		
			$numeroAutorizacion = $row[1];
			$fechaEmision = $row[2];
			$claveAcceso = $row[3];
			$razonSocial = $row[4];
			$nombreComercial = $row[5];
			$direcionMatriz = $row[6];
			$direccionEstablecimiento = $row[7];
			$nroContribuyente = $row[8];
			$obligado = $row[9];
			$cliente = $row[10];
			$identificacion = $row[11];
			$direcion = $row[12];
			$telefono = $row[13];
			$email = $row[14];			
			$secuencial = $row[15];
			$establecimiento = $row[16];
			$puntoEmision = $row[17];
			$fechaAut = $row[18];
			$codigo = $row[19];
			$ambiente = $row[20];
			$emision = $row[21];
			$imagen = $row[22];
		}

		$ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
	  	for ($i = 0; $i < $tam; $i++) {                 
	    	$temp = $temp .'0';        
	  	}
	  	$secuencial = $temp .''. $secuencial;		

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
		$pdf->Text(108, 23, utf8_decode("FACTURA")); // Tipo comprobante
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
	 
		$pdf->Rect(3, 101, 205, 20 , 'D'); // INFO TRIBUTARIA
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 105, utf8_decode('Razón Social / Nombres y Apellidos:')); // Nombre cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(62, 105, utf8_decode($cliente));			     
	 	$pdf->SetFont('Arial','B',9);
		$pdf->Text(150, 105, utf8_decode('RUC / CI:')); // Ruc cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(165, 105, utf8_decode($identificacion));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 117, utf8_decode('Fecha de Emisión:')); //fecha emision cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(35, 117, utf8_decode($fechaEmision));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(136, 117, utf8_decode('Guía de Remisión: ')); //guia remision
		$pdf->SetFont('Arial','',9); 

		// detalles factura
	    $pdf->SetFont('Amble-Regular','',9);               
	    $pdf->SetY(123);
		$pdf->SetX(3);
		$pdf->multiCell(20, 5, utf8_decode('Cod. Principal'), 1);
		$pdf->SetY(123);
		$pdf->SetX(23);
		$pdf->multiCell(20, 5, utf8_decode('Cod. Auxiliar'), 1);
		$pdf->SetY(123);
		$pdf->SetX(43);
		$pdf->multiCell(15, 10, utf8_decode('Cantidad'), 1);
		$pdf->SetY(123);
		$pdf->SetX(58);
		$pdf->multiCell(95, 10, utf8_decode('Descripción'), 1);
		$pdf->SetY(123);
		$pdf->SetX(153);
		$pdf->multiCell(17, 5, utf8_decode('Precio Unitario'), 1);
		$pdf->SetY(123);
		$pdf->SetX(170);
		$pdf->multiCell(18, 10, utf8_decode('Descuento'), 1);
		$pdf->SetY(123);
		$pdf->SetX(188);
		$pdf->multiCell(20, 10, utf8_decode('Total'), 1);
		
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad, D.precio, D.descuento, D.total FROM factura_venta F, detalle_factura_venta D, productos P WHERE D.id_factura_venta = F.id AND D.id_producto = P.id  AND F.id = '".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$codigo = utf8_decode($row[0]);
			$codigoAuxiliar = '';
			$descripcion = utf8_decode($row[1]);
			$cantidad = $row[2];
			$precio = number_format($row[3], 2, '.', '');
			$descuento = $row[4];
			$total = number_format($row[5], 2, '.', '');

			$pdf->SetX(3);                  
	        $pdf->Cell(20, 6, utf8_decode($codigo),1,0, 'L',0);
	        $pdf->Cell(20, 6, utf8_decode($codigoAuxiliar),1,0, 'L',0);
	        $pdf->Cell(15, 6, utf8_decode($cantidad),1,0, 'C',0);        
	        $pdf->Cell(95, 6, utf8_decode($descripcion),1,0, 'L',0);                         
	        $pdf->Cell(17, 6, utf8_decode($precio),1,0, 'L',0);
	        $pdf->Cell(18, 6, utf8_decode($descuento),1,0, 'C',0); 
	        $pdf->Cell(20, 6, utf8_decode($total),1,0, 'L',0);                        
	        $pdf->Ln(6);
		}

		// pie de pagina           	
		if($pdf->getY() <= 220) {
			$pdf->Ln(5);
			$pdf->SetX(3);		   
		    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 100, 40 , 'D'); // 3 INFO ADICIONAL	   
			$y =  $pdf->GetY();
			$x =  $pdf->GetX();	
			$y1 =  $pdf->GetY();
			$x1 =  $pdf->GetX();	
			$pdf->Text($x + 5, $y + 5, utf8_decode('INFORMACIÓN ADICIONAL')); // informacion 		
			$pdf->SetY($y + 7);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Dirección:".$direcion ), 0);
			$pdf->SetY($y + 17);
			$pdf->SetX($x);
			$pdf->multiCell(100, 10, utf8_decode("Teléfono: ".$telefono ), 0);
			$pdf->SetY($y + 29);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Email: ".$email ), 0);

			$resultado = $class->consulta("SELECT F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_descuento, F.total_venta FROM factura_venta F WHERE F.id = '".$id."'");		
			while ($row = $class->fetch_array($resultado)) {
				$subtotal = $row[0];
				$tarifa = $row[1];
				$tarifa0 = $row[2];
				$iva = $row[3];
				$descuento = $row[4];
				$total = $row[5];	
			}

			$pdf->Ln(5);
			$pdf->SetX(108);
			$x1 = $x1 + 105;		   
		    $pdf->SetY($y1);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Subtotal 12%"), 1);	
			$pdf->SetY($y1);
			$pdf->SetX($x1+62);
			$pdf->multiCell(38, 6, number_format($tarifa, 2, '.', ''), 1);
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Subtotal 0%"), 1);	
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($tarifa0, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Descuento"), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($descuento, 2, '.', ''), 1);
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Iva"), 1);	
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($iva, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Propina"), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, utf8_decode("0.00"), 1);
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Total"), 1);	
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($total, 2, '.', ''), 1);	
		
			// FORMAS DE PAGO	           	
			$pdf->SetX(3);		   	    
			$y =  $pdf->GetY();
			$x =  $pdf->GetX();				
			$pdf->SetY($y + 7);
			$pdf->SetX($x);
			$pdf->multiCell(80, 6, utf8_decode("FORMAS DE PAGO"), 1);
			$pdf->SetY($y + 7);
			$pdf->SetX($x + 80);
			$pdf->multiCell( 20, 6, utf8_decode("VALOR"),1 );
			$resultado = $class->consulta("SELECT F.codigo, F.nombre_forma, P.valor, P.plazo, P.tiempo FROM formas_pagos_venta P, factura_venta V, formas_pago F WHERE P.id_factura_venta = V.id AND P.id_forma_pago = F.id AND V.id = '".$id."'");
			while ($row = $class->fetch_array($resultado)) {
				$pdf->SetY($y + 13);
				$pdf->SetX($x);
				$pdf->multiCell(80, 6, utf8_decode($row[1]), 1);
				$pdf->SetY($y + 13);
				$pdf->SetX($x + 80);
				$pdf->multiCell(20, 6, utf8_decode($row[2]), 1);
			}
		} else {
			$pdf->AddPage();
			$pdf->Ln(5);
			$pdf->SetX(3);		   
		    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 100, 40 , 'D'); // 3 INFO ADICIONAL	   
			$y =  $pdf->GetY();
			$x =  $pdf->GetX();	
			$y1 =  $pdf->GetY();
			$x1 =  $pdf->GetX();	
			$pdf->Text($x + 5, $y + 5, utf8_decode('INFORMACIÓN ADICIONAL')); // informacion 		
			$pdf->SetY($y + 7);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Dirección:".$direcion), 0);
			$pdf->SetY($y + 17);
			$pdf->SetX($x);
			$pdf->multiCell(100, 10, utf8_decode("Teléfono: ".$telefono), 0);
			$pdf->SetY($y + 29);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Email: ".$email), 0);		

			$resultado = $class->consulta("SELECT F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_descuento, F.total_venta FROM factura_venta F WHERE F.id = '".$id."'");		
			while ($row = $class->fetch_array($resultado)) {
				$subtotal = $row[0];
				$tarifa = $row[1];
				$tarifa0 = $row[2];
				$iva = $row[3];
				$descuento = $row[4];
				$total = $row[5];	
			}

			$pdf->Ln(5);
			$pdf->SetX(108);
			$x1 = $x1 + 105;		   
		    $pdf->SetY($y1);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Subtotal 12%"), 1);	
			$pdf->SetY($y1);
			$pdf->SetX($x1+62);
			$pdf->multiCell(38, 6, number_format($tarifa, 2, '.', ''), 1);
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Subtotal 0%"), 1);	
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($tarifa0, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Descuento"), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($descuento, 2, '.', ''), 1);
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Iva"), 1);	
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($iva, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Propina"), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, utf8_decode("0.00"), 1);
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1);
			$pdf->multiCell(62, 6, utf8_decode("Total"), 1);	
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1 + 62);
			$pdf->multiCell(38, 6, number_format($total, 2, '.', ''), 1);	
		
			// FORMAS DE PAGO	           	
			$pdf->SetX(3);		   	    
			$y =  $pdf->GetY();
			$x =  $pdf->GetX();				
			$pdf->SetY($y + 7);
			$pdf->SetX($x);
			$pdf->multiCell(80, 6, utf8_decode("FORMAS DE PAGO"), 1);
			$pdf->SetY($y + 7);
			$pdf->SetX($x + 80);
			$pdf->multiCell( 20, 6, utf8_decode("VALOR"),1 );
			$resultado = $class->consulta("SELECT F.codigo, F.nombre_forma, P.valor, P.plazo, P.tiempo FROM formas_pagos_venta P, factura_venta V, formas_pago F WHERE P.id_factura_venta = V.id AND P.id_forma_pago = F.id AND V.id = '".$id."'");
			while ($row = $class->fetch_array($resultado)) {
				$pdf->SetY($y + 13);
				$pdf->SetX($x);
				$pdf->multiCell(80, 6, utf8_decode($row[1]),1);
				$pdf->SetY($y + 13);
				$pdf->SetX($x + 80);
				$pdf->multiCell( 20, 6, utf8_decode($row[2]),1);
			}
		}
		if(isset($_GET['id'])) {
			$pdf->Output();		
		} else {
			$pdf_file_contents = $pdf->Output("","S");		
			return $pdf_file_contents;
		}
	}
?>