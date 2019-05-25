<?php
	function generarXML($id,$codDoc,$ambiente,$emision) {
		$class = new constante();
		$resultado = $class->consulta("SELECT E.ruc, E.razon_social, E.nombre_comercial, N.clave_acceso, N.establecimiento, N.punto_emision, N.secuencial, E.direccion_matriz, E.direccion_establecimiento, N.fecha_emision, N.fecha_autorizacion, N.numero_autorizacion, E.contribuyente, E.obligacion, TD.codigo, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, TC.codigo, F.secuencial, F.fecha_emision, N.motivo FROM empresa E, nota_credito N, factura_venta F, clientes C, tipo_documento TD, tipo_comprobante TC WHERE N.id_factura_venta = F.id AND F.id_cliente = C.id AND C.id_tipo_documento = TD.id AND F.id_tipo_comprobante = TC.id AND N.id_empresa = E.id AND N.id = '".$id."'");	
		while ($row = $class->fetch_array($resultado)) {
			$ruc = $row[0];
			$razonSocial = $row[1];
			$nombreComercial = $row[2];
			$claveAcceso = $row[3];
			$establecimiento = $row[4];
			$puntoEmision = $row[5];
			$secuencial = $row[6];
			$direcionMatriz = $row[7];
			$direccionEstablecimiento = $row[8];
			$fechaEmision = $row[9];
			$fechaAut = $row[10];
			$numeroAutorizacion = $row[11];
			$nroContribuyente = $row[12];
			$obligado = $row[13];
			$tipoIdentificacion = $row[14];
			$identificacion = $row[15];
			$cliente = $row[16];					
			$direcion = $row[17];
			$telefono = $row[18];
			$email = $row[19];
			$codigoModifica = $row[20];
			$comprobanteModifica = $row[21];
		    $fechaModifica = $row[22];
		    $razonModifica = $row[23]; 	
		}

		$ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
      	for ($i = 0; $i < $tam; $i++) {                 
        	$temp = $temp .'0';        
      	}
      	$secuencial = $temp .''. $secuencial ;
      	$s = "";
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$s .= "<notaCredito id=\"comprobante\" version=\"1.0.0\">\n";		
			$s .= "<infoTributaria>\n";
				$s .= "<ambiente>".$ambiente."</ambiente>\n";
				$s .= "<tipoEmision>".$emision."</tipoEmision>\n";
				$s .= "<razonSocial>".substr($razonSocial, 0,300) ."</razonSocial>\n";
				$s .= "<nombreComercial>".substr($nombreComercial, 0,300)."</nombreComercial>\n";
				$s .= "<ruc>".substr($ruc,0,13)."</ruc>\n";
				$s .= "<claveAcceso>".substr($claveAcceso,0,49)."</claveAcceso>\n";
				$s .= "<codDoc>".substr($codDoc,0,2)."</codDoc>\n";
				$s .= "<estab>".substr($establecimiento,0,3)."</estab>\n";
				$s .= "<ptoEmi>".substr($puntoEmision,0,3)."</ptoEmi>\n";
				$s .= "<secuencial>".substr($secuencial,0,9)."</secuencial>\n";
				$s .= "<dirMatriz>".substr($direcionMatriz,0,300)."</dirMatriz>\n";
			$s .= "</infoTributaria>\n";
			$s .= "<infoNotaCredito>\n";
				$s .= "<fechaEmision>".substr($fechaEmision,0,10)."</fechaEmision>\n";
				$s .= "<dirEstablecimiento>".substr($direccionEstablecimiento,0,300)."</dirEstablecimiento>\n";								
				$s .= "<tipoIdentificacionComprador>".substr($tipoIdentificacion,0,2)."</tipoIdentificacionComprador>\n";				
				$s .= "<razonSocialComprador>".substr(utf8_decode($cliente),0,300)."</razonSocialComprador>\n";
				$s .= "<identificacionComprador>".substr($identificacion,0,20)."</identificacionComprador>\n";

				if($nroContribuyente != '')
					$s .= "<contribuyenteEspecial>".substr($nroContribuyente,0,13)."</contribuyenteEspecial>\n";
					$s .= "<obligadoContabilidad>".$obligado."</obligadoContabilidad>\n";
					$s .= "<codDocModificado>".$codigoModifica."</codDocModificado>\n";
					$s .= "<numDocModificado>".$establecimiento.'-'.$puntoEmision.'-'.$comprobanteModifica."</numDocModificado>\n";
					$s .= "<fechaEmisionDocSustento>".$fechaModifica."</fechaEmisionDocSustento>\n";

				$totalSinImpuestos = 0;
				$valorModificacion = 0;

				$resultado = $class->consulta("SELECT subtotal, total_nota FROM nota_credito WHERE id = '".$id."'");
				while ($row = $class->fetch_array($resultado)) {
					$totalSinImpuestos = $row[0];
					$valorModificacion = $row[1];					
				}
				$s .= "<totalSinImpuestos>".number_format($totalSinImpuestos, 2, '.', '')."</totalSinImpuestos>\n";
				$s .= "<valorModificacion>".number_format($valorModificacion, 2, '.', '')."</valorModificacion>\n";
				$s .= "<moneda>DOLAR</moneda>\n";

				$s .= "<totalConImpuestos>\n";
				$resultado = $class->consulta("SELECT TTI.codigo, TI.codigo, SUM(CAST(D.total AS FLOAT)), TI.nombre_tarifa_impuesto FROM detalle_nota_credito D, nota_credito N, productos P, tarifa_impuesto TI, tipo_impuesto TTI WHERE D.id_nota_credito = N.id AND D.id_producto = P.id AND TI.id_tipo_impuesto = TTI.id AND P.id_porcentaje = TI.id AND D.id_nota_credito = '".$id."' GROUP BY TTI.codigo, TI.codigo, TI.nombre_tarifa_impuesto");
				while ($row = $class->fetch_array($resultado)) {
					$s .= "<totalImpuesto>\n";				    	
					$s .= "<codigo>".$row[0]."</codigo>\n";
				    $s .= "<codigoPorcentaje>".$row[1]."</codigoPorcentaje>\n";
				    $s .= "<baseImponible>".number_format($row[2] , 2, '.', '')."</baseImponible>\n";
				    $s .= "<valor>".number_format($row[2] * $row[3] / 100, 2, '.', '')."</valor>\n";
				    $s .= "</totalImpuesto>\n";
				}				  
			    $s .= "</totalConImpuestos>\n";			
			    $s .= "<motivo>".substr($razonModifica,0,299)."</motivo>\n";			   
			    	        
			$s .= "</infoNotaCredito>\n";
			$s .= "<detalles>\n";
				$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad, D.precio, D.descuento, D.total, TTI.codigo, TI.codigo, TI.nombre_tarifa_impuesto FROM detalle_nota_credito D, nota_credito N, productos P, tarifa_impuesto TI, tipo_impuesto TTI WHERE D.id_nota_credito = N.id AND D.id_producto = P.id AND TI.id_tipo_impuesto = TTI.id AND P.id_porcentaje = TI.id AND D.id_nota_credito = '".$id."'");
				while ($row = $class->fetch_array($resultado)) {
				    $s .= "<detalle>\n";
				    $s .= "<codigoInterno>".substr($row[0],0,25)."</codigoInterno>\n";
				    $s .= "<descripcion>".substr($row[1],0,300)."</descripcion>\n";
				    $s .= "<cantidad>".$row[2]."</cantidad>\n";
				    $s .= "<precioUnitario>".number_format($row[3], 2, '.', '')."</precioUnitario>\n";
				    $s .= "<descuento>".$row[4]."</descuento>\n";
				    $s .= "<precioTotalSinImpuesto>".number_format($row[5], 2, '.', '')."</precioTotalSinImpuesto>\n";
				    $s .= "<impuestos>\n";				    				   
			    	$s .= "<impuesto>\n";
				    $s .= "<codigo>".$row[6]."</codigo>\n";
				    $s .= "<codigoPorcentaje>".$row[7]."</codigoPorcentaje>\n";
				    $s .= "<tarifa>".$row[8]."</tarifa>\n";
				    $s .= "<baseImponible>".number_format($row[5], 2, '.', '')."</baseImponible>\n";
				    $s .= "<valor>".number_format($row[5] * $row[8] / 100, 2, '.', '')."</valor>\n";
				    $s .= "</impuesto>\n";					    			    			    
				    $s .= "</impuestos>\n";
				    $s .= "</detalle>\n";
				}
				
		  	$s .= "</detalles>\n";				
			$s .= "<infoAdicional>\n";				
				$s .= "<campoAdicional nombre=\"DIRECCION\">".' '.substr(utf8_decode($direcion),0,299)."</campoAdicional>\n";									
				$s .= "<campoAdicional nombre=\"TELEFONO\">".' '.utf8_decode(substr($telefono,0,299))."</campoAdicional>\n";	
				$s .= "<campoAdicional nombre=\"EMAIL\">".' '.utf8_decode(substr($email,0,299))."</campoAdicional>\n";
			$s .= "</infoAdicional>";	
		$s .="\n</notaCredito>";
		return $s;
	}

	function generarXMLCDATA($data) {				
      	$s = "";
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$s .= "<autorizacion>\n";
			$s .= "<estado>".$data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado."</estado>\n";
			$s .= "<numeroAutorizacion>".$data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion."</numeroAutorizacion>\n";
			$s .= "<fechaAutorizacion>".$data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion."</fechaAutorizacion>\n";
			$s .= "<comprobante><![CDATA[".$data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante."]]></comprobante>";
		 	$s .= "<mensajes/>\n";
		$s .= "</autorizacion>";
		return $s;
	}
?>