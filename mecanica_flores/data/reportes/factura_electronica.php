<?php
	require('../../fpdf/rotation.php');
	require('../../fpdf/barcode.inc.php');
	include_once('../../admin/class.php');

	$class = new constante();	
	// $pFile = "archivos/".$_GET['user']."/".$_GET['id'].".".$_GET['ext'];
	$pFile = "../repositorio/archivos/0904201701200106505555011017912512371.xml";
	$slPath = $pFile;
	$rlFile = fopen($slPath, 'r');
	$xmlData = '';
	$ilLong = filesize($slPath);
	$slData = fread($rlFile, $ilLong);
	$xmlData = new SimpleXMLElement($slData);

	if(!is_object($xmlData)) {
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $slData);
		$xmlAut = new SimpleXMLElement($xmlString);					
		
		$nroAut = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		$fechAut = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		$ambi = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

		$xmlAut = utf8_decode($xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);									
	} else {		
		$nroAut = $xmlData->numeroAutorizacion;	
		$fechAut = $xmlData->fechaAutorizacion;	
		$ambi = utf8_decode($xmlData->ambiente);	
		$xmlAut = $class->uncdata($xmlData->comprobante);	
	}					

	$xmlAut =  new SimpleXMLElement($xmlAut);								
	
	if($xmlAut->infoTributaria->tipoEmision == 1) {
		$emi = 'Normal';	
	} else {
		$emi = 'Indisponibilidad del Sistema';	
	}
	/////---xml data--- ///////
	class PDF extends PDF_Rotate {   
        var $widths;
        var $aligns;       
        function SetWidths($w) {            
            $this->widths=$w;
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
    }

    $pdf = new PDF('P','mm','a4');
    $pdf->AddPage();
    $pdf->SetMargins(10,0,0,0);        
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddFont('Amble-Regular','','Amble-Regular.php');                  	   	
   	///////////base de datos////////
	if($xmlAut->infoTributaria->codDoc == '01') {
		$doc = "FACTURA";		 	
		// $pdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
	    $pdf->Text(5, 30, substr(utf8_decode($xmlAut->infoTributaria->razonSocial),0,48).'..');//NOMBRE proveedor

	    $pdf->Rect(3, 48, 100, 50 , 'D');
		$pdf->SetY(50);
		$pdf->SetX(4);
		$pdf->SetFont('Arial','',11);	
	    $pdf->multiCell(95, 5, $xmlAut->infoTributaria->razonSocial,0 ); // Razon social
		$pdf->SetY(62);
		$pdf->SetX(4);
		$pdf->SetFont('Arial','',9);	
		$pdf->multiCell(98, 5, 'Dir Matriz: ' .utf8_decode($xmlAut->infoTributaria->dirMatriz),0); // Direccion Matriz
		$pdf->SetX(4);	
		$pdf->multiCell(98, 5, 'Dir Sucursal: ' .utf8_decode($xmlAut->infoFactura->dirEstablecimiento),0); // Direccion	Sucursal
		$pdf->Text(5, 90, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial)); // Contribuyente
		$pdf->Text(5, 95, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad)); // Obligado llevar contabilidad

		$est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
		
	    $pdf->Rect(106, 8, 100, 90 , 'D');
	    $pdf->SetFont('Arial','',11);
	    $pdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc); // Ruc Empresa
	    $pdf->SetFont('Arial','B',13);
	    $pdf->Text(108, 22, $doc); // Documento
	    $pdf->SetFont('Arial','',9);
	    $pdf->Text(108, 29, 'No. ' . $est); // Establecimiento
	    $pdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN:')); // Autorizacion
	    $pdf->Text(108, 43, $nroAut);//nro autorizacion
	    $pdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN')); // Fecha y hora de autorizacion
	    $pdf->Text(108, 57, $fechAut); // Fecha autorizacion
	    $pdf->Text(108, 64, utf8_decode('AMBIENTE: '. $ambi)); // Ambiente
	    $pdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi)); // Emisión
	    $pdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: ')); // Clave de Acceso
	    $code_number = $xmlAut->infoTributaria->claveAcceso; 		
		new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true); // Codigo barras		
		$pdf->Image('temp.gif',108,82,96,15);
    
	    // INFO TRIBUTARIA
	    $pdf->SetFont('Arial','',9);
	    $pdf->Rect(3, 101, 203, 20 , 'D');
	    $pdf->SetY(103);
		$pdf->SetX(4);
		$pdf->multiCell(130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoFactura->razonSocialComprador ),0); // Nombres Clientes	
	    $pdf->Text(135, 108, utf8_decode('RUC / CI: ' . $xmlAut->infoFactura->identificacionComprador)); // Ruc cliente
	    $pdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoFactura->fechaEmision)); // Fecha de emision cliente
	    $pdf->Text(136, 117, utf8_decode('Guía de Remisión: ' .$xmlAut->infoFactura->guiaRemision)); // Guia remision 

	    if(is_object($xmlAut->detalles->detalle[0]->detallesAdicionales->detAdicional[2])) {
			print_r($xmlAut->detalles->detalle[0]->detallesAdicionales->detAdicional[1]->attributes()->nombre);	
		}
		
	    //////////////////detalles factura/////////////
	    $pdf->SetFont('Arial','',8);               
	    $pdf->SetY(125);
		$pdf->SetX(3);
		$pdf->multiCell(20, 10, utf8_decode('Cod. Principal'),1);
		$pdf->SetY(125);
		$pdf->SetX(23);
		$pdf->multiCell(21, 10, utf8_decode('Cod. Auxiliar'),1);
		$pdf->SetY(125);
		$pdf->SetX(44);
		$pdf->multiCell(12, 10, utf8_decode('Cant.'),1);
		$pdf->SetY(125);
		$pdf->SetX(56);
		$pdf->multiCell(57, 10, utf8_decode('Descripción'),1);
		$pdf->SetY(125);
		$pdf->SetX(113);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(125);
		$pdf->SetX(128);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(125);
		$pdf->SetX(143);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(125);
		$pdf->SetX(158);
		$pdf->multiCell(16, 5, utf8_decode('Precio Unitario'),1);
		$pdf->SetY(125);
		$pdf->SetX(174);
		$pdf->multiCell(16, 10, utf8_decode('Descuento'),1);
		$pdf->SetY(125);
		$pdf->SetX(190);
		$pdf->multiCell(15, 5, utf8_decode('Precio Total'),1);            
	 
		for ($i = 0; $i < sizeof($xmlAut->detalles->detalle); $i++) { 
			$pdf->SetX(3);
			$pdf->Cell(20, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoPrincipal),1,0, 'C',0);                 	
			$pdf->Cell(21, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoAuxiliar),1,0, 'C',0);
			$pdf->Cell(12, 6, utf8_decode($xmlAut->detalles->detalle[$i]->cantidad),1,0, 'C',0); 
			$pdf->Cell(57, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->descripcion),0,36),1,0, 'L',0); 	

			if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[0])) {
				$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[0]->attributes()),0,9),1,0, 'C',0);
			} else {
				$pdf->Cell(15, 6, '',1,0, 'C',0);                 									
			}
			if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[1])) {
				$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[1]->attributes()),0,9),1,0, 'C',0);                 					
			} else {
				$pdf->Cell(15, 6, '',1,0, 'C',0);                 									
			}
			if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[2])) {				
				$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[2]->attributes()),0,9),1,0, 'C',0);                 	
			} else {
				$pdf->Cell(15, 6, '',1,0, 'C',0);                 						
			}			
			$pdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioUnitario),1,0, 'C',0);                 	
			$pdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->descuento),1,0, 'C',0);                 	
			$pdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioTotalSinImpuesto),1,1, 'C',0);                 	                
		}

		$pdf->SetFont('Arial','B',8);            	
		$pdf->Ln(5);
		$pdf->SetX(3);
	    // $pdf->Rect($pdf->GetX(), $pdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
	    // $pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
		$y = $pdf->GetY();
		$x = $pdf->GetX();
		$y_1 = $pdf->GetY();
		$x_1 = $pdf->GetX();
		$pdf->Text($x_1 + 3, $y_1 + 3, utf8_decode('INFORMACIÓN ADICIONAL')); // Informacion adicional	
		$pdf->Ln(5);
		$y = $y + 6;		
		$tam = 0;
		$tam =  $tam + 6;
		$pdf->SetFont('Arial','',8);

		for ($i = 0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) { 		
			$pdf->SetX(5);
			$pdf->MultiCell(105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0 );            
			$y = $y + 6;
			$tam =  $tam + 6;
		}	

		// $pdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
		$y_1 = $y_1;
		$pdf->SetY($y_1);
		$posx = 135;	               							
		$tam = sizeof($xmlAut->infoFactura->totalConImpuestos->totalImpuesto);

		// subtotal diferente 0
		$cont = 0;
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('SUBOTOTAL 14%'),1,0, 'R',0);
		for($i = 0; $i < $tam; $i++) {
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 3) {
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'R',0);
				$cont = 1;
			}		
		}

		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);
		}
		// fin	

		// subtotal 0
		$cont = 0;	
		$pdf->SetX($posx);	
		$pdf->Cell(50, 5, utf8_decode('SUBOTOTAL 0%'),1,0, 'R',0);                 											
		for($i = 0; $i < $tam; $i++) {
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 0) {
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'R',0);
				$cont = 1;
			}			
		}
		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);
		}
		// fin

		// subtotal no sujeto iva
		$cont = 0;
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('SUBTOTAL No sujeto de IVA'),1,0, 'R',0);         				
		for($i = 0; $i < $tam; $i++){
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 6 ) {
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'R',0);
				$cont = 1;
			}			
		}		
		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);	
		}
		// fin

		// subtotal exento de iva
		$cont = 0;
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('SUBTOTAL Exento de IVA'),1,0, 'R',0);         								
		for($i = 0; $i < $tam; $i++){
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 7) {
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'L',0);
				$cont = 1;
			}			
		}
		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);
		}
		// fin

		// subtotal sin impuestos
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('SUBTOTAL SIN IMPUESTOS'),1,0, 'R',0);         										
		$pdf->Cell(20, 5, utf8_decode($xmlAut->infoFactura->totalSinImpuestos),1,1, 'R',0);																	
		// fin

		// descuentos
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('DESCUENTOS'),1,0, 'R',0);         														
		$pdf->Cell(20, 5, utf8_decode($xmlAut->infoFactura->totalDescuento),1,1, 'R',0);													
		// fin

		// ice
		$cont = 0;
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('ICE'),1,0, 'R',0);         																
		for($i = 0; $i < $tam; $i++) {
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje >= 3000 && $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje < 4000 ){
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'L',0);
				$cont = 1;
			}			
		}
				
		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);
		}					
		// fin

		// iva diferente 0
		$cont = 0;
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('IVA 14%'),1,0, 'R',0);         																
		for($i = 0; $i < $tam; $i++) {
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 3) {
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'R',0);												   	
				$cont = 1;
			}			
		}

		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);													
		}
		// fin					

		// irbpnr
		$cont = 0;
		$pdf->SetX($posx);	
		$pdf->Cell(50, 5, utf8_decode('IRBPNR'),1,0, 'R',0);         			
		for($i = 0; $i < $tam; $i++) {
			if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje >= 5000){
				$pdf->Cell(20, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'R',0);											
				$cont = 1;
			}			
		}

		if($cont == 0) {
			$pdf->Cell(20, 5, '0.00',1,1, 'R',0);																
		}
		// fin

		// propina
		$pdf->SetX($posx);		
		$pdf->Cell(50, 5, utf8_decode('PROPINA'),1,0, 'R',0);         					
		$pdf->Cell(20, 5,utf8_decode($xmlAut->infoFactura->propina),1,1, 'R',0);
		// fin												   								    							 		
		
		// valor total
		$pdf->SetX($posx);
		$pdf->Cell(50, 5, utf8_decode('VALOR TOTAL'),1,0, 'R',0);         							
		$pdf->Cell(20, 5,utf8_decode($xmlAut->infoFactura->importeTotal),1,1, 'R',0);
		// fin				 
	}

	if($xmlAut->infoTributaria->codDoc == '04') {
		$doc = "NOTA DE CRÉBITO";		 	
		$pdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
	    $pdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
	    // $pdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
	    //////////////////////1/////////////////////////

	    $pdf->Rect(3, 53, 100, 45, 'D');//2 datos personales	
		$pdf->SetY(55);
		$pdf->SetX(4);	
	    $pdf->multiCell(98, 5, $xmlAut->infoTributaria->razonSocial,0);//NOMBRE proveedor	
		$pdf->SetY(66);
		$pdf->SetX(4);	
		$pdf->multiCell( 98, 5, utf8_decode('Dir Matriz: ' .$xmlAut->infoTributaria->dirMatriz),0);//	 direccion	
		$pdf->Text(5, 86, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial));//contribuyente
		$pdf->Text(5, 93, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad));//obligado

		$est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
		
	    $pdf->Rect(106, 8, 102, 90, 'D');//3 DATOS EMPRESA
	    $pdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc);//ruc
	    $pdf->Text(108, 22, $doc);//tipo comprobante
	    $pdf->Text(108, 29, 'No. ' . $est);//tipo comprobante
	    $pdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN'));//nro autorizacion TEXT
	    $pdf->Text(108, 43, $nroAut);//nro autorizacion
	    $pdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN'));//fecha y hora de autorizacion TEXT
	    $pdf->Text(108, 57, $fechAut);//nro autorizacion
	    $pdf->Text(108, 64, utf8_decode('AMBIENTE: '. $ambi));//ambiente
	    $pdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi));//tipo de emision
	    $pdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: '));//tipo de emision
	    $code_number = $xmlAut->infoTributaria->claveAcceso;//////cpdigo de barras		
		new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true);///img codigo barras		
		$pdf->Image('temp.gif',108,82,96,15);     	
	    /////////////////////////////Datos Factura///////////////////
	    $pdf->Rect(3, 101, 205, 44, 'D');////3 INFO TRIBUTARIA
	    $pdf->SetY(101);
		$pdf->SetX(3);
		$pdf->multiCell(130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoNotaCredito->razonSocialComprador),0);//NOMBRE cliente	
	    $pdf->Text(135, 105, utf8_decode('Identificación: ' . $xmlAut->infoNotaCredito->identificacionComprador ));//ruc cliente
	    $pdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoNotaCredito->fechaEmision ));//fecha de emision cliente	    
		$pdf->Line(5,122,205,122);
		//01 factura ver en la base de datos $xmlAut->infoNotaCredito->codDocModificado
		$pdf->Text(5, 128, utf8_decode('Comprobante que se modifica: ' .  'FACTURA'));//
		$pdf->Text(150, 128, utf8_decode($xmlAut->infoNotaCredito->numDocModificado ));//
	    $pdf->Text(5, 136, utf8_decode('Fecha Emisión (Comprobante a modificar): ' . $xmlAut->infoNotaCredito->fechaEmisionDocSustento));//
	    $pdf->Text(5, 143, utf8_decode('Razón de Modificación: ' . $xmlAut->infoNotaCredito->motivo));//
	   	
	   	 //////////////////detalles factura/////////////
	    $pdf->SetFont('Amble-Regular','',8);               
	    $pdf->SetY(145);
		$pdf->SetX(3);
		$pdf->multiCell(15, 10, utf8_decode('Código'),1);
		$pdf->SetY(145);
		$pdf->SetX(18);
		$pdf->multiCell(13, 5, utf8_decode('Código Auxiliar'),1);
		$pdf->SetY(145);
		$pdf->SetX(31);
		$pdf->multiCell(14, 10, utf8_decode('Cantidad'),1);
		$pdf->SetY(145);
		$pdf->SetX(45);
		$pdf->multiCell(71, 10, utf8_decode('Descripción'),1);
		$pdf->SetY(145);
		$pdf->SetX(116);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(145);
		$pdf->SetX(131);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(145);
		$pdf->SetX(146);
		$pdf->multiCell(15, 5, utf8_decode('Detalle Adicional'),1);
		$pdf->SetY(145);
		$pdf->SetX(161);
		$pdf->multiCell(16, 10, utf8_decode('Descuento'),1);
		$pdf->SetY(145);
		$pdf->SetX(177);
		$pdf->multiCell(16, 5, utf8_decode('Precio Unitario'),1);
		$pdf->SetY(145);
		$pdf->SetX(193);
		$pdf->multiCell(15, 5, utf8_decode('Precio Total'),1);             
		$desc = 0;

	  	for ($i=0; $i < sizeof($xmlAut->detalles->detalle); $i++) { 
			$pdf->SetX(3);
			$pdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoInterno),1,0, 'C',0);                 	
			$pdf->Cell(13, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoAdicional),1,0, 'C',0);
			$pdf->Cell(14, 6, utf8_decode($xmlAut->detalles->detalle[$i]->cantidad),1,0, 'C',0); 
			$pdf->Cell(71, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->descripcion),0,46),1,0, 'L',0);                 	
			$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                 	
			$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                 	
			$pdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                 	
			$pdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioUnitario),1,0, 'C',0);                 	
			$pdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->descuento),1,0, 'C',0);                 	
			$pdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioTotalSinImpuesto),1,1, 'C',0);                 	                
			$desc = $desc + $xmlAut->detalles->detalle[$i]->descuento;
		}

		/////////////////pie de pagina//////////
		$pdf->SetFont('Amble-Regular','',9);            	
		$pdf->Ln(4);
		$pdf->SetX(3);
		
	    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
	    $pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
		$y = $pdf->GetY();
		$x = $pdf->GetX();
		$y_1 = $pdf->GetY();
		$x_1 = $pdf->GetX();
		$pdf->Text($x_1 + 3, $y_1 + 3, utf8_decode('INFORMACIÓN ADICIONAL'));//informacion adicional	
		$pdf->Ln(3);
		$y = $y + 6;		
		$tam = 0;
		$tam =  $tam + 6;
		for ($i=0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) { 		
			$pdf->SetX(5);
			$pdf->MultiCell( 105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0 );            
			$y = $y + 6;
			$tam =  $tam + 6;
		}	
		////////////////////////////////////////////////////
		$pdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
		$y_1 = $y_1;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL 14%'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->baseImponible,1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL 0%'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[0]->baseImponible,1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL No sujeto de IVA'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[2]->baseImponible,1, 'C' );
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL Exento IVA'),1 );
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[3]->baseImponible,1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL SIN IMPUESTOS'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, utf8_decode($xmlAut->infoNotaCredito->totalSinImpuestos),1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('TOTAL DESCUENTOS'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell( 23, 5, $desc,1 , 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('ICE'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		if($xmlAut->infoNotaCredito->ice == '') {
			$ice = '0.00';
		} else {
			$ice = $xmlAut->infoNotaCredito->ice;
		}

		$pdf->multiCell(23, 5, utf8_decode($ice),1 ,'C' );
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('IVA 14%'),1 );
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5,  $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->valor,1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('IRBPNR'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[5]->valor,1, 'C');
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('VALOR TOTAL'),1 );
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, utf8_decode($xmlAut->infoNotaCredito->valorModificacion),1, 'C');
	}

	if($xmlAut->infoTributaria->codDoc == '05') {
		$doc = "NOTA DE DÉBITO";		 	
		$pdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
	    $pdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
	    $pdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
	    ///////////////////////////////////////////////

	    $pdf->Rect(3, 53, 100, 45 , 'D');//2 datos personales	
		$pdf->SetY(55);
		$pdf->SetX(4);	
	    $pdf->multiCell(98, 5, $xmlAut->infoTributaria->razonSocial,0);//NOMBRE proveedor	
		$pdf->SetY(66);
		$pdf->SetX(4);	
		$pdf->multiCell(98, 5, utf8_decode('Dir Matriz: ' .$xmlAut->infoTributaria->dirMatriz),0);//	 direccion	
		$pdf->Text(5, 86, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial));//contribuyente
		$pdf->Text(5, 93, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad));//obligado

		$est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
		
	    $pdf->Rect(106, 8, 102, 90, 'D');//3 DATOS EMPRESA
	    $pdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc);//ruc
	    $pdf->Text(108, 22, $doc);//tipo comprobante
	    $pdf->Text(108, 29, 'No. ' . $est);//tipo comprobante
	    $pdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN'));//nro autorizacion TEXT
	    $pdf->Text(108, 43, $nroAut);//nro autorizacion
	    $pdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN'));//fecha y hora de autorizacion TEXT
	    $pdf->Text(108, 57, $fechAut);//nro autorizacion
	    $pdf->Text(108, 64, utf8_decode('AMBIENTE: '. $ambi));//ambiente
	    $pdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi));//tipo de emision
	    $pdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: '));//tipo de emision
	    $code_number = $xmlAut->infoTributaria->claveAcceso;//////cpdigo de barras		
		new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true);///img codigo barras		
		$pdf->Image('temp.gif',108,82,96,15);    
		 	
	    /////////////////////////////Datos Factura///////////////////
	    $pdf->Rect(3, 101, 205, 40, 'D');////3 INFO TRIBUTARIA
	    $pdf->SetY(101);
		$pdf->SetX(3);
		$pdf->multiCell(130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoNotaDebito->razonSocialComprador),0);//NOMBRE cliente	
	    $pdf->Text(135, 105, utf8_decode('Identificación: ' . $xmlAut->infoNotaDebito->identificacionComprador));//ruc cliente
	    $pdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoNotaDebito->fechaEmision));//fecha de emision cliente	    
		$pdf->Line(5,122,205,122);
		//01 factura ver en la base de datos $xmlAut->infoNotaDebito->codDocModificado
		$pdf->Text(5, 128, utf8_decode('Comprobante que se modifica: ' .  'FACTURA'));//ruc cliente
		$pdf->Text(150, 128, utf8_decode($xmlAut->infoNotaDebito->numDocModificado));//ruc cliente
	    $pdf->Text(5, 136, utf8_decode('Fecha Emisión: ' . $xmlAut->infoNotaDebito->fechaEmisionDocSustento));//ruc cliente
	    //detalles nota debito
	    $pdf->SetFont('Amble-Regular','',12);               
	    $pdf->SetY(141);
		$pdf->SetX(3);
		$pdf->multiCell(127, 8, utf8_decode('RAZÓN DE LA MODIFICACIÓN'),1,'C');
		$pdf->SetY(141);
		$pdf->SetX(130);
		$pdf->multiCell(78, 8, utf8_decode('VALOR DE LA MODIFICACIÓN'),1, 'C');
		$pdf->SetFont('Amble-Regular','',9);               
		for ($i = 0; $i < sizeof($xmlAut->motivos->motivo); $i++) { 
			$pdf->SetX(3);
			$pdf->Cell(127, 6, utf8_decode($xmlAut->motivos->motivo[$i]->razon),1,0, 'L',0);                 	
			$pdf->Cell(78, 6, utf8_decode($xmlAut->motivos->motivo[$i]->valor),1,0, 'R',0);			               	                		
		}

		$pdf->SetFont('Amble-Regular','',9);            	
		$pdf->Ln(8);
		$pdf->SetX(3);
		
	    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
	    $pdf->Rect($pdf->GetX() + 115, $pdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
		$y = $pdf->GetY();
		$x = $pdf->GetX();
		$y_1 = $pdf->GetY();
		$x_1 = $pdf->GetX();
		$pdf->Text('', '', utf8_decode('INFORMACIÓN ADICIONAL'));//informacion adicional	
		$pdf->Ln(3);
		$y = $y + 6;		
		$tam = 0;
		$tam =  $tam + 6;
		for ($i = 0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) { 		
			$pdf->SetX(5);
			$pdf->MultiCell(105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0);            
			$y = $y + 6;
			$tam =  $tam + 6;
		}	

		$pdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
		$y_1 = $y_1;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL 12 %'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, $xmlAut->infoNotaDebito->totalSinImpuestos,1);
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL 0 %'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, '0.00',1 );
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL No sujeto de IVA'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, '0.00',1 );
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL Exento IVA'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, utf8_decode($xmlAut->infoNotaDebito->totalSinImpuestos),1);
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell(70, 5, utf8_decode('SUBOTOTAL SIN IMPUESTOS'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell(23, 5, utf8_decode($xmlAut->infoNotaDebito->totalSinImpuestos),1);
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);		
		$pdf->multiCell(70, 5, utf8_decode('VALOR ICE'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		if($xmlAut->infoNotaDebito->ice == '') {
			$ice = '0.00';
		} else {
			$ice = $xmlAut->infoNotaDebito->ice;
		}

		$pdf->multiCell( 23, 5, utf8_decode($ice),1);
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);
		$pdf->multiCell( 70, 5, utf8_decode('IVA 12 %'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell( 23, 5, $xmlAut->infoNotaDebito->impuestos->impuesto->valor,1);//REVISAR CON OTROS DATOS
		$y_1 = $y_1 + 5;
		$pdf->SetY($y_1);
		$pdf->SetX(115);				
		$pdf->multiCell( 70, 5, utf8_decode('VALOR TOTAL'),1);
		$pdf->SetY($y_1);
		$pdf->SetX(115 + 70);
		$pdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaDebito->valorTotal),1);
	}
    $pdf->Output();
?>