<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	ini_set('max_execution_time', 240); //240 segundos = 4 minutos
	include 'generarXML.php';		
	include 'firma/firma.php';	
	include 'firma/xades.php';
	include_once('../../admin/correolocal.php');
    //include_once('../../admin/correoweb.php');
    $fecha = $class->fecha_hora();
	$fecha_corta = $class->fecha();
	$defaultMail = mailDefecto;
	$codDoc = '06'; // tipo documento
	error_reporting(0);

	//guardar guia remision
	if (isset($_POST['btn_guardar']) == "Guardar") {
		// contador guia
		$id = 0;
		$resultado = $class->consulta("SELECT max(id) FROM guia_remision");
		while ($row = $class->fetch_array($resultado)) {
			$id = $row[0];
		}
		$id++;
		// fin

		// modificar secuencia_comprobantes
       	$resultado = $class->consulta("SELECT MAX(secuencia_guia_remision) FROM secuencia_comprobantes GROUP BY id");
        	while ($row = $class->fetch_array($resultado)) {
            $secuencial = $row[0];
        }

        $secuencial = $secuencial + 1;
        $class->consulta("UPDATE secuencia_comprobantes SET secuencia_guia_remision = '".$secuencial."'");
        // fin

        // completar serie
        $ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
      	for ($i = 0; $i < $tam; $i++) {                 
        	$temp = $temp .'0';        
      	}
      	$secuencial = $temp .''. $secuencial;
      	// fin

		// parametros empresa
		$resultado = $class->consulta("SELECT ruc, token, clave, establecimiento, punto_emision FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ruc = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$establecimiento = $row[3];
			$punto_emision = $row[4];
		}
		// fin

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		// parametro emision
		$resultado = $class->consulta("SELECT codigo FROM tipo_emision WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
		}
		// fin

		// generar clave
		$clave = $class->generarClave($_POST['fecha_emision'],$codDoc,$ruc,$ambiente,$establecimiento.''.$punto_emision,$secuencial,$_POST['fecha_emision'],$emision);
		// fin

		// guardar guia_remision
		$class->consulta("INSERT INTO guia_remision VALUES  (	'".$id."',
																'".$_SESSION['empresa']['id']."',
																'".$_SESSION['user']['id']."',
																'".$_POST['id_transportista']."',
																'".$_POST['fecha_emision']."',
																'',
																'',
																'',
																'".$clave."',
																'".$establecimiento."',
																'".$punto_emision."',
																'".$secuencial."',
																'".$ambiente."',
																'".$emision."',
																'',
																'4',
																'".$_POST['fecha_inicio']."',
																'".$_POST['fecha_fin']."',
																'".$_POST['placa']."',
																'".$_POST['dir_partida']."',
																'".$_POST['id_cliente']."',
																'".$_POST['dir_destino']."',
																'".$_POST['motivo']."',
																'".$_POST['ruta']."',
																'".$_POST['doc_aduanero']."',
																'".$_POST['id_factura_venta']."',
																'9', 
																'".$fecha."')");
		// fin

        // auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Guía Remisión','INSERT','".'Creación Guía Remisión:'.$secuencial."','','','$id','$fecha')");

		// datos detalle guia
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    // Fin

	    // descomponer detalle guia
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle guia remision
			$id_detalle_guia_remision = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_guia_remision");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_guia_remision = $row[0];
			}
			$id_detalle_guia_remision++;
			// fin

			$class->consulta("INSERT INTO detalle_guia_remision VALUES (	'".$id_detalle_guia_remision."',
																			'".$id."',
																			'".$arreglo1[$i]."',
																			'".$arreglo2[$i]."',
																			'".$arreglo3[$i]."',
																			'1', 
																			'".$fecha."')");

			// modificar productos
           	$resultado = $class->consulta("SELECT * FROM productos WHERE id = '".$arreglo1[$i]."'");
           	while ($row = $class->fetch_array($resultado)) {
                $stock = $row['stock'];
            }

            $cal = $stock - $arreglo2[$i];
            $class->consulta("UPDATE productos SET stock = '$cal' WHERE id = '".$arreglo1[$i]."'");
            // fin

            // consultar movimientos
           	$resultado = $class->consulta("SELECT * FROM movimientos WHERE id_producto = '".$arreglo1[$i]."'");
           	while ($row = $class->fetch_array($resultado)) {
                $salida = $row[5];
            }

            $cal2 = $salida + $arreglo2[$i]; 
            $class->consulta("UPDATE movimientos SET salidas = '$cal2', saldo = '$cal' WHERE id_producto = '".$arreglo1[$i]."'");
            // fin

            // contador kardex
			$id_kardex = 0;
			$resultado = $class->consulta("SELECT max(id) FROM kardex");
			while ($row = $class->fetch_array($resultado)) {
				$id_kardex = $row[0];
			}
			$id_kardex++;
			// fin

			$multi = $arreglo2[$i] * $arreglo3[$i];
			// guardar kardex
			$class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
															'".$arreglo1[$i]."',
															'".$fecha_corta."',
															'".'G.R:'.$secuencial."',
															'".$arreglo2[$i]."',
															'".$arreglo3[$i]."',
															'".$multi."',
															'".$cal."',
															'',
															'',
															'12', 
															'".$fecha."')");
			// fin
	    }

	    $xml = generarXML($id, $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'guiaRemision', $pass, $token, $ambiente); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado, $ambiente, $clave,'','guiaRemision', $pass, $token,'0'); // Envio Archivo XML Validar

				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado'];

					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);
														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$id."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		       				$doc->loadXML($dataFile); // xml	 
		       				if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
			        			//if(trim($email) == '' && $email != '') {
			        			//	$resultado = $class->consulta("UPDATE guia_remision SET estado = '1' WHERE id = '".$id."'");			
								//	if($resultado) {
								//		$data = 1; // datos actualizados
								//	} else {
								//		$data = 4; // error al momento de guadar
								//	}
								//} else {
									$data = 3; // error al momento de enviar el correo
					    			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$id."'"); 
				    			//}	
		        			} else {
		        				$data = 2; // error al generar los documentos
			        			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$id."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$id."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$id."'");
						}
					}
				}
			}
		}

		print_r(json_encode(array('estado' => $data, 'id' => $id)));
	}
	// fin

	// anular guia remision
	if (isset($_POST['btn_anular']) == "Anular") {
		$fecha_emision = $_POST['fecha_emision'];

		$class->consulta("UPDATE guia_remision SET fecha_anulacion = '$fecha_emision', estado = '10'  WHERE id = '".$_POST['id_guia']."'");

		// datos detalle guia
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    // Fin

	    // descomponer detalle guia
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// modificar productos
           	$resultado = $class->consulta("SELECT * FROM productos WHERE id = '".$arreglo1[$i]."'");
           	while ($row = $class->fetch_array($resultado)) {
                $stock = $row['stock'];
            }

            $cal = $stock + $arreglo2[$i];
            $class->consulta("UPDATE productos SET stock = '$cal' WHERE id = '".$arreglo1[$i]."'");
            // fin
	    }

	    $data = 1;
		echo $data;
	}
	// fin

	//cargar ultimo secuencial
	if (isset($_POST['cargar_secuencial'])) {
		$resultado = $class->consulta("SELECT MAX(secuencia_guia_remision) FROM secuencia_comprobantes GROUP BY id ORDER BY id asc");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('serie' => $row[0]);
		}

		print_r(json_encode($data));
	}
	//fin

	// LLenar info
	if (isset($_POST['llenar_infomacion'])) {
		$resultado = $class->consulta("SELECT ruc, razon_social, direccion_matriz, direccion_establecimiento, contribuyente, obligacion, establecimiento, punto_emision, token, imagen FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('ruc' => $row[0],'razon_social' => $row[1],'matriz' => $row[2],'sucursal' => $row[3],'contribuyente' => $row[4],'obligacion' => $row[5],'establecimiento' => $row[6], 'emision' => $row[7], 'token' => $row[8], 'imagen' => $row[9]);
		}

		print_r(json_encode($data));
	}
	// fin

	// llenar documento sustento
	if (isset($_POST['llenar_documento_sustento'])) {
		$resultado = $class->consulta("SELECT secuencial, numero_autorizacion FROM factura_venta F WHERE id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'secuencial' => $row[0],
							'numero_autorizacion' => $row[1]);
		}

		print_r(json_encode($data));
	}
	//fin

	// llenar cabezera factura venta
	if (isset($_POST['llenar_cabezera_factura'])) {
		$resultado = $class->consulta("SELECT F.id, F.fecha_emision, F.secuencial,  F.id_cliente, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, F.tipo_precio, F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_descuento, F.total_venta, F.efectivo, F.cambio, F.estado FROM factura_venta F, clientes C WHERE F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id_factura' => $row[0],
							'fecha_emision' => $row[1],
							'secuencial' => $row[2],
							'id_cliente' => $row[3],
							'identificacion' => $row[4],
							'razon_social' => $row[5],
							'direccion' => $row[6],
							'telefono2' => $row[7],
							'correo' => $row[8],
							'tipo_precio' => $row[9],
							'subtotal' => $row[10],
							'tarifa' => $row[11],
							'tarifa0' => $row[12],
							'iva' => $row[13],
							'descuento' => $row[14],
							'total_pagar' => $row[15],
							'efectivo' => $row[16],
							'cambio' => $row[17],
							'estado' => $row[18]);
		}
		
		print_r(json_encode($data));
	}
	//fin

	// llenar detalle factura venta
	if (isset($_POST['llenar_detalle_factura'])) {
		$resultado = $class->consulta("SELECT D.id_producto, U.codigo, U.descripcion, D.cantidad, D.precio, D.descuento, D.total, P.nombre_tarifa_impuesto, U.incluye_iva, D.pendientes FROM detalle_factura_venta D, factura_venta F, productos U, tarifa_impuesto P  WHERE D.id_producto = U.id AND D.id_factura_venta = F.id AND U.id_porcentaje = P.id AND F.id = '".$_POST['id']."' ORDER BY D.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$arr_data[] = $row['0'];
		    $arr_data[] = $row['1'];
		    $arr_data[] = $row['2'];
		    $arr_data[] = $row['3'];
		    $arr_data[] = $row['4'];
		    $arr_data[] = $row['5'];
		    $arr_data[] = $row['6'];
		    $arr_data[] = $row['7'];
		    $arr_data[] = $row['8'];
		    $arr_data[] = $row['9'];
		}

		echo json_encode($arr_data);
	}
	//fin

	// reenviarCorreo
	if (isset($_POST['reenviarCorreo']) == "reenviarCorreo") {
		$resultado = $class->consulta("SELECT C.correo, C.nombre_comercial FROM guia_remision G, clientes C WHERE G.id_cliente = C.id AND G.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {						
			$email = $row[0];
			$nombre = $row[1];
			$total = "SIN MONTO";
		} 
		
		if($email != "") {
			include 'generarPDF.php';

			$data = correo($fecha,$total,$_POST['aut'].'.xml',$_POST['aut'].'.pdf', $nombre, $email,'comprobantes/'.$_POST['aut'].'.xml',generarPDF($_POST['id']),1);

			if($data == 1) {
				$resultado = $class->consulta("UPDATE guia_remision set estado = '1' where id = '".$_POST['id']."'");

				if($resultado) {
					$data = 1; // comprobantes enviados
				} else {
					$data = 4; // error 
				}
			}
		} else {
			$data = 2; // sin correo		
		}			
		
		echo $data;
	}
	// fin

	// generarArchivos
	if (isset($_POST['generarArchivos']) == "generarArchivos") {
		$resultado = $class->consulta("SELECT G.emision, E.token, E.clave, G.clave_acceso, C.correo, C.nombre_comercial FROM guia_remision G, empresa E, clientes C WHERE G.id_empresa = E.id AND G.id_cliente = C.id AND G.id ='".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$respuesta = consultarComprobante($ambiente, $clave);	

		if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {
			if($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado == 'AUTORIZADO') {		
	    	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;
	            $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

	            $dataFile = generarXMLCDATA($respuesta);		                
	            $doc = new DOMDocument('1.0', 'UTF-8');
				$doc->loadXML($dataFile); // xml	 
				if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
	    			$data = 3; // comprobante autorizado
					$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	    			
				} else {
					$data = 2; // error al generar los documentos
			        $class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");			                	
				} 
			} else {
				$data = 7; // Error en el service web rechazado
				$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");		
			}     
		} else {
			$data = 7; // Error en el service web rechazado
			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
		}

		echo $data;
	}
	// fin

	// errorWebService
	if (isset($_POST['errorWebService']) == "errorWebService") {
		$resultado = $class->consulta("SELECT G.emision, E.token, E.clave, G.clave_acceso, C.correo, C.nombre_comercial FROM guia_remision G, empresa E, clientes C WHERE G.id_empresa = E.id AND G.id_cliente = C.id AND G.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'guiaRemision', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {												
				$respuesta = consultarComprobante($ambiente, $clave);

				if($respuesta->RespuestaAutorizacionComprobante->numeroComprobantes == 0) {	
					if(isset($respWeb['RespuestaRecepcionComprobante']['estado']) == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);

						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado)) {
							if($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado == 'AUTORIZADO') {
			            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
			        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
			    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;
				                $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

				                $dataFile = generarXMLCDATA($respuesta);		                
				                $doc = new DOMDocument('1.0', 'UTF-8');
			        			$doc->loadXML($dataFile); // xml	 
			        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
									$data = 3; // comprobante autorizado
					    			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
			        			} else {
			        				$data = 2; // error al generar los documentos
			        				$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");                	
			        			}
		        			} else {
		        				$data = 7; // Error en el service web rechazado
								$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
		        			}      
						} else {
							$data = 7; // Error en el service web rechazado
							$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
						}
					} else {
						$data = 8; // Error en el service web
						$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				} else {
					if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {
						
	            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
	        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
	    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;
		                $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

		                $dataFile = generarXMLCDATA($respuesta);		                
		                $doc = new DOMDocument('1.0', 'UTF-8');
	        			$doc->loadXML($dataFile); // xml	 
	        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
			    			$data = 3; // comprobante autorizado
					    	$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
	        			} else {
	        				$data = 2; // error al generar los documentos
			        		$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
	        			}      
					} else {
						$data = 7; // Error en el service web rechazado
						$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				}				
			}
		}

		echo $data;
	}
	// fin

	// generarFirma
	if (isset($_POST['generarFirma']) == "generarFirma") {
		$resultado = $class->consulta("SELECT G.emision, E.token, E.clave, G.clave_acceso, C.correo, C.nombre_comercial FROM guia_remision G, empresa E, clientes C WHERE G.id_empresa = E.id AND G.id_cliente = C.id AND G.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'guiaRemision', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado, $ambiente, $clave,'','guiaRemision',$pass,$token,'0'); // Envio Archivo XML Validar 
				
				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
								$data = 3; // comprobante autorizado
					    		$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
		        			} else {
		        				// error al gererar archivos
		        				$data = 2;
			        			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
						}
					}
				}
			}
		}

		echo $data;
	}
	// fin 

	// volverValidar
	if (isset($_POST['volverValidar']) == "volverValidar") {
		$resultado = $class->consulta("SELECT G.emision, E.token, E.clave, G.clave_acceso, C.correo, C.nombre_comercial FROM guia_remision G, empresa E, clientes C WHERE G.id_empresa = E.id AND G.id_cliente = C.id AND G.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'guiaRemision', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // archivo token
		} else {
			if($firmado == 6) {
				$data = 6; // contrasenia incorrecta
			} else {
				$respWeb = webService($firmado,$ambiente,$clave,'','guiaRemision',$pass,$token,'0'); // Envio Archivo XML Validar 

				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE guia_remision SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
								$data = 3; // comprobante autorizado
					    		$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
		        			} else {
		        				// error al gererar archivos
		        				$data = 2;
			        			$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE guia_remision SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
						}
					}
				}
			}
		}

		echo $data;
	}
	// fin

	// consultar factura
	if(isset($_POST['cargar_tabla'])){
		$resultado = $class->consulta("SELECT F.id, U.nombres_completos, C.razon_social, F.secuencial, F.total_venta, F.fecha_creacion FROM factura_venta  F, clientes C, usuarios U WHERE F.id_usuario = U.id AND F.id_cliente = C.id AND F.fecha_emision BETWEEN '$_POST[fecha_inicio]' AND '$_POST[fecha_fin]' ORDER BY F.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'id' => $row[0],
								'vendedor' => $row[1],
								'cliente' => $row[2],
								'secuencial' => $row[3],
								'total_venta' => $row[4],
								'fecha_creacion' => $row[5]
								);
		}

		echo $lista = json_encode($lista);
	}
	// fin

	// consultar detalles
	if(isset($_POST['cargar_tabla_detalle'])){
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.precio, D.cantidad, D.total FROM detalle_factura_venta D, factura_venta F, productos P WHERE D.id_factura_venta = F.id AND D.id_producto = P.id AND F.id = '$_POST[id]' ORDER BY D.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'codigo' => $row[0],
								'descripcion' => $row[1],
								'precio' => $row[2],
								'cantidad' => $row[3],
								'total' => $row[4]
								);
		}

		echo $lista = json_encode($lista);
	}
	// fin 
?>