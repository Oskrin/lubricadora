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
		$resultado = $class->consulta("SELECT ruc, razon_social, direccion_matriz, direccion_establecimiento, contribuyente, obligacion, establecimiento, punto_emision, token, imagen FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ruc = $row[0];
			$razonSocial = $row[1];
			$direcionMatriz = $row[2];
			$direccionEstablecimiento = $row[3];
			$nroContribuyente = $row[4];
			$obligado = $row[5];
			$establecimiento = $row[6];
			$puntoEmision = $row[7];
			$token = $row[8];
			$imagen = $row[9];
		}

		$resultado = $class->consulta("SELECT P.id, P.fecha_emision, P.secuencial, P.id_cliente, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, P.estado FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$fechaEmision = $row[1];
			$secuencial = $row[2];
			$identificacion = $row[4];
			$cliente = $row[5];
			$direccion = $row[6];
			$telefono = $row[7];
			$email = $row[8];
		}

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
		$pdf->Image('../../data/empresa/logo/'.$logo,25,25,40,20); // Img Empresa

		$pdf->SetLineWidth(0.2);
    	$pdf->SetFillColor(255,255,255);

    	$pdf->SetFont('Arial','B',8);
    	$pdf->Text(20, 60, utf8_decode($razonSocial));

    	$pdf->RoundedRect(106, 15, 95, 50, 3, 'DF');
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(108, 22, 'R.U.C.:'); // ruc
		$pdf->SetFont('Arial','',10);
		$pdf->Text(140, 22, $ruc); // ruc
		$pdf->SetFont('Arial','B',10);		 	
		$pdf->Text(108, 29, utf8_decode("PROFORMA No")); 
		$pdf->SetFont('Arial','',10);
		$pdf->Text(140, 29, $establecimiento.'-'.$puntoEmision.'-'.$secuencial); // Secuencial
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(108, 36, utf8_decode('Dirección Matriz:')); // Dirección Matriz
		$pdf->SetFont('Arial','',9);	
		$pdf->Text(140, 36, utf8_decode($direcionMatriz));
		$pdf->SetFont('Arial','B',9);	
		$pdf->Text(108, 43, utf8_decode('Dirección Sucursal:')); // Dirección Establecimiento
		$pdf->SetFont('Arial','',9);	
		$pdf->Text(140, 43, utf8_decode($direccionEstablecimiento));
		  	
		$pdf->Rect(8, 70, 193, 20 , 'D'); // INFO TRIBUTARIA
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(10, 76, utf8_decode('Razón Social / Nombres y Apellidos:')); // Nombre cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(67, 76, utf8_decode($cliente));			     
	 	$pdf->SetFont('Arial','B',9);
		$pdf->Text(155, 76, utf8_decode('RUC / CI:')); // Ruc cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(170, 76, utf8_decode($identificacion));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(10, 83, utf8_decode('Fecha de Emisión:')); //fecha emision cliente
		$pdf->SetFont('Arial','',9);
		$pdf->Text(40, 83, utf8_decode($fechaEmision));
		$pdf->SetFont('Arial','',9); 

		// detalles factura
	    $pdf->SetFont('Amble-Regular','',9);               
	    $pdf->SetY(94);
		$pdf->SetX(8);
		$pdf->multiCell(40, 6, utf8_decode('Código'), 1, 'C');
		$pdf->SetY(94);
		$pdf->SetX(48);
		$pdf->multiCell(73, 6, utf8_decode('Descripción'), 1, 'C');
		$pdf->SetY(94);
		$pdf->SetX(121);
		$pdf->multiCell(20, 6, utf8_decode('Cantidad'), 1, 'C');
		$pdf->SetY(94);
		$pdf->SetX(141);
		$pdf->multiCell(20, 6, utf8_decode('PVP'), 1, 'C');
		$pdf->SetY(94);
		$pdf->SetX(161);
		$pdf->multiCell(20, 6, utf8_decode('Descuento'), 1, 'C');
		$pdf->SetY(94);
		$pdf->SetX(181);
		$pdf->multiCell(20, 6, utf8_decode('Total'), 1, 'C');
		
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad, D.precio, D.descuento, D.total FROM detalle_proforma D, proforma F, productos P WHERE D.id_proforma = F.id AND D.id_producto = P.id AND D.id_proforma = '".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$codigo = utf8_decode($row[0]);
			$descripcion = utf8_decode($row[1]);
			$cantidad = $row[2];
			$precio = number_format($row[3], 2, '.', '');
			$descuento = $row[4];
			$total = number_format($row[5], 2, '.', '');

			$pdf->SetX(8);                  
	        $pdf->Cell(40, 6, utf8_decode($codigo),1,0, 'L',0);
	        $pdf->Cell(73, 6, utf8_decode($descripcion),1,0, 'L',0);
	        $pdf->Cell(20, 6, utf8_decode($cantidad),1,0, 'C',0);        
	        $pdf->Cell(20, 6, utf8_decode($precio),1,0, 'C',0);                         
	        $pdf->Cell(20, 6, utf8_decode($descuento),1,0, 'C',0);
	        $pdf->Cell(20, 6, utf8_decode($total),1,0, 'C',0);                       
	        $pdf->Ln(6);
		}

		// pie de pagina           	
		if($pdf->getY() <= 220) {
			$pdf->Ln(5);
			$pdf->SetX(8);		   
		    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 105, 30, 'D'); // 3 INFO ADICIONAL	   
			$y =  $pdf->GetY();
			$x =  $pdf->GetX();	
			$y1 =  $pdf->GetY();
			$x1 =  $pdf->GetX();	
			$pdf->Text($x + 35, $y + 5, utf8_decode('INFORMACIÓN ADICIONAL')); // informacion 		
			$pdf->SetY($y + 7);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Dirección:".$direccion ), 0);
			$pdf->SetY($y + 13);
			$pdf->SetX($x);
			$pdf->multiCell(100, 10, utf8_decode("Teléfono: ".$telefono ), 0);
			$pdf->SetY($y + 23);
			$pdf->SetX($x);
			$pdf->multiCell(100, 5, utf8_decode("Email: ".$email ), 0);

			$resultado = $class->consulta("SELECT P.subtotal, P.tarifa, P.tarifa0, P.iva, P.total_descuento, P.total_proforma, P.estado FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$id."'");		
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
			$x1 = $x1 + 110;		   
		    $pdf->SetY($y1);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Subtotal 12%"), 1);	
			$pdf->SetY($y1);
			$pdf->SetX($x1+50);
			$pdf->multiCell(33, 6, number_format($tarifa, 2, '.', ''), 1, 'R');
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Subtotal 0%"), 1);	
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($tarifa0, 2, '.', ''), 1, 'R');	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Descuento"), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($descuento, 2, '.', ''), 1, 'R');
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Iva"), 1);	
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($iva, 2, '.', ''), 1, 'R');
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Total"), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($total, 2, '.', ''), 1, 'R');	
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
			$pdf->multiCell(100, 5, utf8_decode("Dirección:".$direccion), 0);
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
			$pdf->multiCell(50, 6, utf8_decode("Subtotal 12%"), 1);	
			$pdf->SetY($y1);
			$pdf->SetX($x1+50);
			$pdf->multiCell(33, 6, number_format($tarifa, 2, '.', ''), 1);
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Subtotal 0%"), 1);	
			$pdf->SetY($y1 + 6);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($tarifa0, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Descuento"), 1);	
			$pdf->SetY($y1 + 12);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($descuento, 2, '.', ''), 1);
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Iva"), 1);	
			$pdf->SetY($y1 + 18);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($iva, 2, '.', ''), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Propina"), 1);	
			$pdf->SetY($y1 + 24);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, utf8_decode("0.00"), 1);
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1);
			$pdf->multiCell(50, 6, utf8_decode("Total"), 1);	
			$pdf->SetY($y1 + 30);
			$pdf->SetX($x1 + 50);
			$pdf->multiCell(33, 6, number_format($total, 2, '.', ''), 1);	
		
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