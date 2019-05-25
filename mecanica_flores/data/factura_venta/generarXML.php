<?php
	function generarXML($id,$codDoc,$ambiente,$emision) {
		$class = new constante();
		$resultado = $class->consulta("SELECT E.ruc, E.razon_social, E.nombre_comercial, F.clave_acceso, F.establecimiento, F.punto_emision, F.secuencial, E.direccion_matriz, E.direccion_establecimiento, F.fecha_emision, F.fecha_autorizacion, F.numero_autorizacion, E.contribuyente, E.obligacion, TC.codigo, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo FROM empresa E, factura_venta F, clientes C, tipo_documento TC WHERE F.id_cliente = C.id AND C.id_tipo_documento = TC.id AND F.id_empresa = E.id AND F.id ='".$id."'");	
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
		}					
		$ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
      	for ($i = 0; $i < $tam; $i++) {                 
        	$temp = $temp .'0';        
      	}
      	$secuencial = $temp .''. $secuencial;
      	$s = "";
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$s .= "<factura id=\"comprobante\" version=\"1.1.0\">\n";		
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
			$s .= "<infoFactura>\n";
				$s .= "<fechaEmision>".substr($fechaEmision,0,10)."</fechaEmision>\n";
				$s .= "<dirEstablecimiento>".substr($direccionEstablecimiento,0,300)."</dirEstablecimiento>\n";
				
				if($nroContribuyente != '')
					$s .= "<contribuyenteEspecial>".substr($nroContribuyente,0,13)."</contribuyenteEspecial>\n";
					$s .= "<obligadoContabilidad>".$obligado."</obligadoContabilidad>\n";
					$s .= "<tipoIdentificacionComprador>".substr($tipoIdentificacion,0,2)."</tipoIdentificacionComprador>\n";				
					$s .= "<razonSocialComprador>".substr($cliente,0,300)."</razonSocialComprador>\n";
					$s .= "<identificacionComprador>".substr($identificacion,0,20)."</identificacionComprador>\n";
					$s .= "<direccionComprador>".substr($direcion,0,300)."</direccionComprador>\n";

				$totalSinImpuestos = 0;
				$totalDescuento = 0;
				$total = 0;

				$resultado = $class->consulta("SELECT subtotal, total_descuento, total_venta FROM factura_venta WHERE id = '".$id."'");
				while ($row = $class->fetch_array($resultado)) {
					$totalSinImpuestos = $row['subtotal'];
					$totalDescuento = $row['total_descuento'];
					$total = $row['total_venta'];	
				}

				$s .= "<totalSinImpuestos>".number_format($totalSinImpuestos, 2, '.', '')."</totalSinImpuestos>\n";
				$s .= "<totalDescuento>".number_format($totalDescuento, 2, '.', '')."</totalDescuento>\n";
				$s .= "<totalConImpuestos>\n";

				$resultado = $class->consulta("SELECT TTI.codigo, TI.codigo, SUM(CAST(D.total AS FLOAT)), TI.nombre_tarifa_impuesto FROM detalle_factura_venta D, factura_venta F, productos P, tarifa_impuesto TI, tipo_impuesto TTI WHERE D.id_factura_venta = F.id AND D.id_producto = P.id AND TI.id_tipo_impuesto = TTI.id AND P.id_porcentaje = TI.id AND D.id_factura_venta = '".$id."' GROUP BY TTI.codigo, TI.codigo, TI.nombre_tarifa_impuesto");
				while ($row = $class->fetch_array($resultado)) {
					$s .= "<totalImpuesto>\n";				    	
					$s .= "<codigo>".$row[0]."</codigo>\n";
				    $s .= "<codigoPorcentaje>".$row[1]."</codigoPorcentaje>\n";
				    $s .= "<baseImponible>".number_format($row[2], 2, '.', '')."</baseImponible>\n";
				    $s .= "<tarifa>".$row[3]."</tarifa>\n";
				    $s .= "<valor>".number_format($row[2] * $row[3] / 100, 2, '.', '')."</valor>\n";
				    $s .= "</totalImpuesto>\n";
				}  
			    $s .= "</totalConImpuestos>\n";
				$s .= "<propina>0.00</propina>\n";
			    $s .= "<importeTotal>".number_format($total, 2, '.', '')."</importeTotal>\n";
			    $s .= "<moneda>DOLAR</moneda>\n";
			    $s .= "<pagos>\n";

			    $resultado = $class->consulta("SELECT F.codigo, F.nombre_forma, P.valor, P.plazo, P.tiempo FROM formas_pagos_venta P, factura_venta V, formas_pago F WHERE P.id_factura_venta = V.id AND P.id_forma_pago = F.id AND V.id = '".$id."'");
			    while ($row = $class->fetch_array($resultado)) {
			    	$s .= "<pago>\n";
		            $s .= "<formaPago>".$row[0]."</formaPago>\n";
		            $s .= "<total>".number_format($row[2], 2, '.', '')."</total>\n";
		            if($row[3] != '')
		            $s .= "<plazo>".$row[3]."</plazo>\n";
		        	if($row[4] != '')
		            $s .= "<unidadTiempo>".$row[4]."</unidadTiempo>\n";
		        	$s .= "</pago>\n";	
			    }
		        $s .= "</pagos>\n";
		        
			$s .= "</infoFactura>\n";
			$s .= "<detalles>\n";
			$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad, D.precio, D.descuento, D.total, TTI.codigo, TI.codigo, TI.nombre_tarifa_impuesto FROM detalle_factura_venta D, factura_venta F, productos P, tarifa_impuesto TI, tipo_impuesto TTI WHERE D.id_factura_venta = F.id AND D.id_producto = P.id AND TI.id_tipo_impuesto = TTI.id AND P.id_porcentaje = TI.id AND D.id_factura_venta = '".$id."'");
			while ($row = $class->fetch_array($resultado)) {
				$s .= "<detalle>\n";
			    $s .= "<codigoPrincipal>".substr($row[0],0,25)."</codigoPrincipal>\n";
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
				$s .= "<campoAdicional nombre=\"DIRECCION\">".' '.substr($direcion,0,299)."</campoAdicional>\n";
				$s .= "<campoAdicional nombre=\"TELEFONO\">".' '.utf8_decode(substr($telefono,0,299))."</campoAdicional>\n";	
				$s .= "<campoAdicional nombre=\"EMAIL\">".' '.utf8_decode(substr($email,0,299))."</campoAdicional>\n";
			$s .= "</infoAdicional>";	
		$s .="\n</factura>";
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