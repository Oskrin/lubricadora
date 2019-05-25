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
	        $fecha = date('Y-m-d', time());           
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
		$resultado = $class->consulta("SELECT E.razon_social, E.nombre_comercial, E.ruc, G.clave_acceso, G.establecimiento, G.punto_emision, G.secuencial, G.numero_autorizacion, G.fecha_autorizacion, E.direccion_matriz, E.direccion_establecimiento, G.dir_partida, T.razon_social, TC.codigo, T.identificacion, E.contribuyente, E.obligacion, G.fecha_inicio, G.fecha_fin, G.placa, E.imagen FROM empresa E, guia_remision G, transportistas T, tipo_documento TC WHERE G.id_empresa = E.id AND G.id_transportista = T.id AND T.id_tipo_documento = TC.id AND G.id ='".$id."'");	
		while ($row = $class->fetch_array($resultado)) {
			$razonSocial = $row[0];
			$nombreComercial = $row[1];
			$ruc = $row[2];
			$claveAcceso = $row[3];
			$establecimiento = $row[4];
			$puntoEmision = $row[5];
			$secuencial = $row[6];
			$numeroAutorizacion = $row[7];
			$fechaAut = $row[8];
			$direcionMatriz = $row[9];
			$direccionEstablecimiento = $row[10];
			$dir_partida = $row[11];
			$razonSocialTransportista = $row[12];
			$tipoIdentificacionTransportista = $row[13];
			$rucTransportista = $row[14];
			$contribuyente = $row[15];
			$obligado = $row[16];
			$fecha_inicio = $row[17];
			$fecha_fin = $row[18];					
			$placa = $row[19];
			$imagen = $row[20];
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
		$pdf->Text(108, 23, utf8_decode("GUÍA REMISIÓN")); // Tipo comprobante
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
		$pdf->Text(130, 61, utf8_decode($ambiente)); // Ambiente
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(108, 67, utf8_decode('EMISIÓN:')); // Tipo de emision
		if ($emision == "Emisión Normal") {
			$pdf->SetFont('Arial','',10);
			$pdf->Text(130, 67, utf8_decode('NORMAL')); // Tipo de emision	
		}
		$pdf->Text(108, 75, utf8_decode('CLAVE DE ACCESO: ')); // Clave de acceso
		$code_number = $claveAcceso; // Código de barras		
		new barCodeGenrator($code_number,1,'temp.gif', 470, 60, true); /// img codigo barras	
		$pdf->Image('temp.gif',108,77,97,15);  
	
		$pdf->Rect(3, 101, 205, 27, 'D'); // infoGuiaRemision
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 105, utf8_decode('Identificación (Transportista):')); // Ruc transportista
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 105, utf8_decode($rucTransportista)); // Ruc transportista
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 110, utf8_decode('Razón Social / Nombres y Apellidos:')); // Razón Social
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 110, utf8_decode($razonSocialTransportista)); // Razón Social
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 115, utf8_decode('Placa:')); // Placa
		$pdf->SetFont('Arial','',9);
		$pdf->Text(45, 115, utf8_decode($placa)); // Placa
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 120, utf8_decode('Punto Partida:')); // Punto Partida
		$pdf->SetFont('Arial','',9);
		$pdf->Text(45, 120, utf8_decode($dir_partida)); // Punto Partida
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 125, utf8_decode('Fecha Inicio Trasporte:')); // Fecha Inicio
		$pdf->Text(90, 125, utf8_decode('Fecha Fin Trasporte:')); // Fecha fin
		$pdf->SetFont('Arial','',9);
		$pdf->Text(45, 125, utf8_decode($fecha_inicio)); // Fecha Inicio
		$pdf->Text(127, 125, utf8_decode($fecha_fin)); // Fecha fin

		$resultado = $class->consulta("SELECT C.identificacion, C.razon_social, G.dir_destinatario, G.motivo, G.doc_aduanero, G.establecimiento, G.ruta, T.codigo, F.secuencial, F.numero_autorizacion, F.fecha_emision  FROM guia_remision G, clientes C, factura_venta F, tipo_comprobante T WHERE G.id_cliente = C.id AND G.id_factura_venta = F.id AND F.id_tipo_comprobante = T.id AND G.id ='".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$identificacionDestinatario = $row[0];
			$razonSocialDestinatario = $row[1];
			$dirDestinatario = $row[2];
			$motivoTraslado = $row[3];
			$docAduaneroUnico = $row[4];
			$codEstabDestino = $row[5];	
			$ruta = $row[6];
			$codDocSustento = $row[7];
			$numDocSustento = $row[8];
			$numAutDocSustento = $row[9];
			$fechaEmisionDocSustento = $row[10];		
		}

		$pdf->Rect(3, 131, 205, 52, 'D'); // infoGuiaRemision
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 135, utf8_decode('Comprobante de Venta:')); // Comprobante
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 135, utf8_decode('FACTURA      '.$establecimiento.'-'.$puntoEmision.'-'.$numDocSustento));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(140, 135, utf8_decode('Fecha de Emisión:')); // Fecha de Emisión
		$pdf->SetFont('Arial','',9);
		$pdf->Text(170, 135, utf8_decode($fechaEmisionDocSustento)); // Fecha de Emisión
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 140, utf8_decode('Número Autorización:')); // Autorización
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 140, utf8_decode($numAutDocSustento));

		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 150, utf8_decode('Motivo Traslado:')); // Motivo
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 150, utf8_decode($motivoTraslado));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 155, utf8_decode('Destino (Punto Llegada):')); // Destino
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 155, utf8_decode($dirDestinatario));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 160, utf8_decode('Identificación (Destinatario):')); // Identificación
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 160, utf8_decode($identificacionDestinatario));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 165, utf8_decode('Razón Social / Nombres y Apellidos:')); // Razón Social
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 165, utf8_decode($razonSocialDestinatario));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 170, utf8_decode('Documento Aduanero:')); // Documento
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 170, utf8_decode($docAduaneroUnico));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 175, utf8_decode('Código Establecimiento Destino:')); // Establecimiento
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 175, utf8_decode($codEstabDestino));
		$pdf->SetFont('Arial','B',9);
		$pdf->Text(5, 180, utf8_decode('Ruta:')); // Ruta
		$pdf->SetFont('Arial','',9);
		$pdf->Text(65, 180, utf8_decode($ruta));

	    $pdf->SetY(185);
		$pdf->SetX(3);
		$pdf->multiCell(30, 6, utf8_decode('Cantidad'),1 );
		$pdf->SetY(185);
		$pdf->SetX(33);
		$pdf->multiCell(90, 6, utf8_decode('Descripción'),1 );
		$pdf->SetY(185);
		$pdf->SetX(123);
		$pdf->multiCell(40, 6, utf8_decode('Código'),1 );
		
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad FROM guia_remision G, detalle_guia_remision D, productos P WHERE D.id_guia_remision = G.id AND D.id_producto = P.id AND D.id_guia_remision = '".$id."'");
		while ($row = $class->fetch_array($resultado)) {
			$codigo = utf8_decode($row[0]);
			$descripcion = utf8_decode($row[1]);
			$cantidad = $row[2];

			$pdf->SetX(3);
	        $pdf->Cell(30, 6, utf8_decode($cantidad),1,0, 'C',0);        
	        $pdf->Cell(90, 6, utf8_decode($descripcion),1,0, 'L',0);
	        $pdf->Cell(40, 6, utf8_decode($codigo),1,0, 'L',0);                      
	        $pdf->Ln(6);
		}

		if(isset($_GET['id'])) {
			$pdf->Output();		
		} else {
			$pdf_file_contents = $pdf->Output("","S");		
			return $pdf_file_contents;
		}
	}
?>