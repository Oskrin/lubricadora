<?php
	function generarXML($id,$codDoc,$ambiente,$emision) {
		$class = new constante();
		$resultado = $class->consulta("SELECT E.razon_social, E.nombre_comercial, E.ruc, G.clave_acceso, G.establecimiento, G.punto_emision, G.secuencial, E.direccion_matriz, E.direccion_establecimiento, G.dir_partida, T.razon_social, TC.codigo, T.identificacion, E.contribuyente, E.obligacion, G.fecha_inicio, G.fecha_fin, G.placa FROM empresa E, guia_remision G, transportistas T, tipo_documento TC WHERE G.id_empresa = E.id AND G.id_transportista = T.id AND T.id_tipo_documento = TC.id AND G.id ='".$id."'");	
		while ($row = $class->fetch_array($resultado)) {
			$razonSocial = $row[0];
			$nombreComercial = $row[1];
			$ruc = $row[2];
			$claveAcceso = $row[3];
			$establecimiento = $row[4];
			$puntoEmision = $row[5];
			$secuencial = $row[6];
			$direcionMatriz = $row[7];
			$direccionEstablecimiento = $row[8];
			$dir_partida = $row[9];
			$razonSocialTransportista = $row[10];
			$tipoIdentificacionTransportista = $row[11];
			$rucTransportista = $row[12];
			$contribuyente = $row[13];
			$obligacion = $row[14];
			$fecha_inicio = $row[15];
			$fecha_fin = $row[16];					
			$placa = $row[17];
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
		$s .= "<guiaRemision id=\"comprobante\" version=\"1.1.0\">\n";		
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

			$s .= "<infoGuiaRemision>\n";
				$s .= "<dirEstablecimiento>".substr($direccionEstablecimiento,0,300)."</dirEstablecimiento>\n";
				$s .= "<dirPartida>".$dir_partida."</dirPartida>\n";
				$s .= "<razonSocialTransportista>".$razonSocialTransportista."</razonSocialTransportista>\n";
				$s .= "<tipoIdentificacionTransportista>".$tipoIdentificacionTransportista."</tipoIdentificacionTransportista>\n";
				$s .= "<rucTransportista>".$rucTransportista."</rucTransportista>\n";
				
				if($contribuyente != '')
				$s .= "<contribuyenteEspecial>".substr($contribuyente,0,13)."</contribuyenteEspecial>\n";
				$s .= "<obligadoContabilidad>".$obligacion."</obligadoContabilidad>\n";
				$s .= "<fechaIniTransporte>".$fecha_inicio."</fechaIniTransporte>\n";
				$s .= "<fechaFinTransporte>".$fecha_fin."</fechaFinTransporte>\n";
				$s .= "<placa>".$fecha_fin."</placa>\n";
			$s .= "</infoGuiaRemision>\n";

			$s .= "<destinatarios>\n";
				$s .= "<destinatario>\n";
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
					$s .= "<identificacionDestinatario>".$identificacionDestinatario."</identificacionDestinatario>\n";
					$s .= "<razonSocialDestinatario>".$razonSocialDestinatario."</razonSocialDestinatario>\n";
					$s .= "<dirDestinatario>".$dirDestinatario."</dirDestinatario>\n";
					$s .= "<motivoTraslado>".$motivoTraslado."</motivoTraslado>\n";
					$s .= "<docAduaneroUnico>".$docAduaneroUnico."</docAduaneroUnico>\n";
					$s .= "<codEstabDestino>".$codEstabDestino."</codEstabDestino>\n";
					$s .= "<ruta>".$ruta."</ruta>\n";
					$s .= "<codDocSustento>".$codDocSustento."</codDocSustento>\n";
					$s .= "<numDocSustento>".$establecimiento.'-'.$puntoEmision.'-'.$numDocSustento."</numDocSustento>\n";
					$s .= "<numAutDocSustento>".$numAutDocSustento."</numAutDocSustento>\n";
					$s .= "<fechaEmisionDocSustento>".$fechaEmisionDocSustento."</fechaEmisionDocSustento>\n";
					$s .= "<detalles>\n";
						$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.cantidad FROM guia_remision G, detalle_guia_remision D, productos P WHERE D.id_guia_remision = G.id AND D.id_producto = P.id AND D.id_guia_remision = '".$id."'");
						while ($row = $class->fetch_array($resultado)) {
							$s .= "<detalle>\n";
						    $s .= "<codigoInterno>".substr($row[0],0,25)."</codigoInterno>\n";
						    $s .= "<descripcion>".substr($row[1],0,50)."</descripcion>\n";
						    $s .= "<cantidad>".$row[2]."</cantidad>\n";
						    $s .= "</detalle>\n";	
						}
					$s .= "</detalles>\n";
				$s .= "</destinatario>\n";
			$s .= "</destinatarios>";	
		$s .="\n</guiaRemision>";
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