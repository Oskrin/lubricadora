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
	$codDoc = '01'; // tipo documento
	error_reporting(0);

	//guardar factura venta
	if (isset($_POST['btn_guardar']) == "Guardar") {
		// contador factura
		$id = 0;
		$resultado = $class->consulta("SELECT max(id) FROM factura_venta");
		while ($row = $class->fetch_array($resultado)) {
			$id = $row[0];
		}
		$id++;
		// fin

		// modificar secuencia_comprobantes
       	$resultado = $class->consulta("SELECT MAX(secuencia_factura) FROM secuencia_comprobantes GROUP BY id");
       	while ($row = $class->fetch_array($resultado)) {
            $secuencial = $row[0];
        }

        $secuencial = $secuencial + 1;
        $class->consulta("UPDATE secuencia_comprobantes SET secuencia_factura = '".$secuencial."'");
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

      	// valor cambio
      	$valor = number_format($_POST['efectivo'], 2, '.', '');

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


		if ($_POST['id_cliente'] != "") {
			// guardar factura normal
			$class->consulta("INSERT INTO factura_venta VALUES  (	'".$id."',
																	'".$_SESSION['empresa']['id']."',
																	'".$_SESSION['user']['id']."',
																	'".$_POST['id_cliente']."',
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
																	'1',
																	'".$_POST['select_tipo_precio']."',
																	'".$_POST['subtotal']."',
																	'".$_POST['tarifa']."',
																	'".$_POST['tarifa_0']."',
																	'".$_POST['iva']."',
																	'".$_POST['otros']."',
																	'".$_POST['total_pagar']."',
																	'".$valor."',
																	'".$_POST['cambio']."',
																	'9', 
																	'".$fecha."')");
			// fin
		} else {
			// contador clientes
			$id_cliente = 0;
			$resultado = $class->consulta("SELECT max(id) FROM clientes");
			while ($row = $class->fetch_array($resultado)) {
				$id_cliente = $row[0];
			}
			$id_cliente++;
			// fin
			$dirFoto = "defaul.jpg";

			if (strlen($_POST['ruc']) == 10) {
				// guardar cliente cedula
				$class->consulta("INSERT INTO clientes VALUES  (	'".$id_cliente."',
																	'2',
																	'".$_POST['ruc']."',
																	'".$_POST['razon_social']."',
																	'".$_POST['nombre_comercial']."',
																	'',
																	'".$_POST['telefono']."',
																	'',
																	'".$_POST['direccion']."',
																	'".$_POST['correo']."',
																	'0.00',
																	'".$dirFoto."',
																	'',
																	'1', 
																	'".$fecha."')");
			} else {
				if (strlen($_POST['ruc']) == 13) {
					// guardar cliente ruc
					$class->consulta("INSERT INTO clientes VALUES  (	'".$id_cliente."',
																		'1',
																		'".$_POST['ruc']."',
																		'".$_POST['razon_social']."',
																		'".$_POST['nombre_comercial']."',
																		'',
																		'".$_POST['telefono']."',
																		'',
																		'".$_POST['direccion']."',
																		'".$_POST['correo']."',
																		'0.00',
																		'".$dirFoto."',
																		'',
																		'1', 
																		'".$fecha."')");
				// fin
				}	
			}
			
			// guardar factura nuevo cliente
			$class->consulta("INSERT INTO factura_venta VALUES  (	'".$id."',
																	'".$_SESSION['empresa']['id']."',
																	'".$_SESSION['user']['id']."',
																	'".$id_cliente."',
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
																	'1',
																	'".$_POST['select_tipo_precio']."',
																	'".$_POST['subtotal']."',
																	'".$_POST['tarifa']."',
																	'".$_POST['tarifa_0']."',
																	'".$_POST['iva']."',
																	'".$_POST['otros']."',
																	'".$_POST['total_pagar']."',
																	'".$valor."',
																	'".$_POST['cambio']."',
																	'9', 
																	'".$fecha."')");
			// fin	
		}

		// modificar proformas
        if ($_POST['id_proforma'] != "") {
        	$class->consulta("UPDATE proforma SET estado = '0' WHERE id = '".$_POST['id_proforma']."'");
        }
        // fin

        // auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Factura Venta','INSERT','".'Creación Factura Venta:'.$secuencial."','','','$id','$fecha')");

		// datos detalle factura
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    $campo4 = $_POST['campo4'];
	    $campo5 = $_POST['campo5'];
	    $campo6 = $_POST['campo6'];
	    // Fin

	    // descomponer detalle factura
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $arreglo4 = explode('|', $campo4);
	    $arreglo5 = explode('|', $campo5);
	    $arreglo6 = explode('|', $campo6);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle factura
			$id_detalle_factura = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_factura_venta");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_factura = $row[0];
			}
			$id_detalle_factura++;
			// fin

			$class->consulta("INSERT INTO detalle_factura_venta VALUES (	'".$id_detalle_factura."',
																			'".$id."',
																			'".$arreglo1[$i]."',
																			'".$arreglo2[$i]."',
																			'".$arreglo3[$i]."',
																			'".$arreglo4[$i]."',
																			'".$arreglo5[$i]."',
																			'".$arreglo6[$i]."',
																			'1', 
																			'".$fecha."')");

			$resultado = $class->consulta("SELECT T.nombre_tipo_producto FROM productos P, tipo_producto T WHERE P.id_tipo_producto = T.id AND P.id = '".$arreglo1[$i]."'");
           	while ($row = $class->fetch_array($resultado)) {
                $tipo = $row[0];
            }

            if ($tipo == "OTROS") {
            	// modificar productos
	           	$resultado = $class->consulta("SELECT P.stock, P.disponibles, U.cantidad FROM productos P, unidades_medida U WHERE P.id_unidad_medida = U.id AND P.id = '".$arreglo1[$i]."'");
	           	while ($row = $class->fetch_array($resultado)) {
	                $stock = $row[0];
	                $disponibles = $row[1];
	                $canti = $row[2];
	            }

		        if ($disponibles != 0) {
		            $cal = $stock - $arreglo2[$i];
		            $mul = $arreglo2[$i] * $canti;
		            $res = $disponibles - $mul;
		            
		            $class->consulta("UPDATE productos SET stock = '$cal', disponibles = '$res' WHERE id = '".$arreglo1[$i]."'");
		        } else {
		            if ($disponibles == 0) {
		            	$resultado = $class->consulta("SELECT P.id, P.disponibles FROM productos P WHERE P.id = '".$arreglo6[$i]."'");
					    while ($row = $class->fetch_array($resultado)) {
					        $id_pro = $row[0];
					        $disponibles = $row[1];
					    }

					    $resultado = $class->consulta("SELECT U.cantidad FROM productos P, unidades_medida U WHERE P.id_unidad_medida = U.id AND P.id = '".$arreglo1[$i]."'");
					    while ($row = $class->fetch_array($resultado)) {
					        $canti = $row[0];
					    }

					    $mul = $arreglo2[$i] * $canti;
		                $res = $disponibles - $mul; 

			            $class->consulta("UPDATE productos SET disponibles = '$res' WHERE id = '".$id_pro."'");
		            }
		        }
            } else {
            	if ($tipo == "PRODUCTO") {
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
		        }
            }

            // contador kardex
			//$id_kardex = 0;
			//$resultado = $class->consulta("SELECT max(id) FROM kardex");
			//while ($row = $class->fetch_array($resultado)) {
			//	$id_kardex = $row[0];
			//}
			//$id_kardex++;
			// fin

			// guardar kardex
			//$class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
			//												'".$arreglo1[$i]."',
			//												'".$fecha_corta."',
			//												'".'F.V:'.$secuencial."',
			//												'".$arreglo2[$i]."',
			//												'".$arreglo3[$i]."',
			//												'".$arreglo5[$i]."',
			//												'".$cal."',
			//												'',
			//												'',
			//												'2', 
			//S												'".$fecha."')");
			// fin
	    }

	    // datos detalle pagos
		$campo7 = $_POST['campo7'];
	    $campo8 = $_POST['campo8'];
	    $campo9 = $_POST['campo9'];
	    $campo10 = $_POST['campo10'];

	    // descomponer detalle pagos
		$arreglo7 = explode('|', $campo7);
	    $arreglo8 = explode('|', $campo8);
	    $arreglo9 = explode('|', $campo9);
	    $arreglo10 = explode('|', $campo10);
	    $nelem2 = count($arreglo7);
	    // fin

	    for ($j = 1; $j < $nelem2; $j++) {
	    	// contador detalle pagos
			$id_detalle_pagos = 0;
			$resultado = $class->consulta("SELECT max(id) FROM formas_pagos_venta");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_pagos = $row[0];
			}
			$id_detalle_pagos++;
			// fin

			// contador cuentas_cobrar
			$id_cuentas_cobrar = 0;
			$resultado = $class->consulta("SELECT max(id) FROM cuentas_cobrar");
			while ($row = $class->fetch_array($resultado)) {
				$id_cuentas_cobrar = $row[0];
			}
			$id_cuentas_cobrar++;
			// fin

			$class->consulta("INSERT INTO formas_pagos_venta VALUES (	'".$id_detalle_pagos."',
																		'".$id."',
																		'".$arreglo7[$j]."',
																		'".$arreglo8[$j]."',
																		'".$arreglo9[$j]."',
																		'".$arreglo10[$j]."',
																		'1', 
																		'".$fecha."')");

			if($arreglo9[$j] == "Días") {
				$class->consulta("INSERT INTO cuentas_cobrar VALUES (	'".$id_cuentas_cobrar."',
																		'".$_SESSION['empresa']['id']."',
																		'".$_SESSION['user']['id']."',
																		'".$id."',
																		'".$_POST['fecha_emision']."',
																		'".$arreglo9[$j]."',
																		'".$arreglo10[$j]."', 
																		'".$arreglo8[$j]."',
																		'".$arreglo8[$j]."',
																		'1', 
																		'".$fecha."')");
				// contador detalle cobrar
				$id_detalle_cobrar = 0;
				$resultado = $class->consulta("SELECT max(id) FROM detalle_cuentas_cobrar");
				while ($row = $class->fetch_array($resultado)) {
					$id_detalle_cobrar = $row[0];
				}
				$id_detalle_cobrar++;
				// fin

				// max secuencial
				$resultado = $class->consulta("SELECT MAX(secuencial) FROM detalle_cuentas_cobrar GROUP BY id ORDER BY id asc");
				while ($row = $class->fetch_array($resultado)) {
					$secuencial_cuentas = $row[0];
				}
				$secuencial_cuentas = $secuencial_cuentas + 1;

				$ceros = 9;
				$temp = '';
				$tam = $ceros - strlen($secuencial_cuentas);
		      	for ($i = 0; $i < $tam; $i++) {                 
		        	$temp = $temp .'0';        
		      	}
		      	$secuencial_cuentas = $temp .''. $secuencial_cuentas;
		      	// fin

		      	$fecha = date('Y-m-j');
				$fecha_pago = strtotime('+'.$arreglo10[$j].' day', strtotime($fecha));
				$fecha_pago = date('Y-m-j',$fecha_pago);

		      	$class->consulta("INSERT INTO detalle_cuentas_cobrar VALUES (	'".$id_detalle_cobrar."',
																				'".$id_cuentas_cobrar."',
																				'".$secuencial_cuentas."',
																				'".$fecha_pago."',
																				'".'FACTURA Nº '.$secuencial.' - Cuota Diferida'."',
																				'".$arreglo8[$j]."',
																				'".$arreglo8[$j]."',
																				'',
																				'',
																				'',
																				'1', 
																				'".$fecha."')");
			} else {
				if($arreglo9[$j] == "Meses") {
					$class->consulta("INSERT INTO cuentas_cobrar VALUES (	'".$id_cuentas_cobrar."',
																			'".$_SESSION['empresa']['id']."',
																			'".$_SESSION['user']['id']."',
																			'".$id."',
																			'".$_POST['fecha_emision']."',
																			'".$arreglo9[$j]."',
																			'".$arreglo10[$j]."',
																			'".$arreglo8[$j]."',
																			'".$arreglo8[$j]."',
																			'1', 
																			'".$fecha."')");

					$meses  = $arreglo10[$j];
					for ($k = 1; $k <= $meses; $k++) {
						// contador detalle cobrar
						$id_detalle_cobrar = 0;
						$resultado = $class->consulta("SELECT max(id) FROM detalle_cuentas_cobrar");
						while ($row = $class->fetch_array($resultado)) {
							$id_detalle_cobrar = $row[0];
						}
						$id_detalle_cobrar++;
						// fin

						// max secuencial
						$resultado = $class->consulta("SELECT MAX(secuencial) FROM detalle_cuentas_cobrar GROUP BY id ORDER BY id asc");
						while ($row = $class->fetch_array($resultado)) {
							$secuencial_cuentas = $row[0];
						}
						$secuencial_cuentas = $secuencial_cuentas + 1;

						$ceros = 9;
						$temp = '';
						$tam = $ceros - strlen($secuencial_cuentas);
				      	for ($i = 0; $i < $tam; $i++) {                 
				        	$temp = $temp .'0';        
				      	}
				      	$secuencial_cuentas = $temp .''. $secuencial_cuentas;
				      	// fin

				      	$fecha = date('Y-m-j');
						$fecha_pago = strtotime('+'.$k.' month', strtotime($fecha));
						$fecha_pago = date('Y-m-j',$fecha_pago);

						$cuota = $arreglo8[$j] / $meses;
						$cuotas = number_format($cuota, 2, '.', '');

				      	$class->consulta("INSERT INTO detalle_cuentas_cobrar VALUES (	'".$id_detalle_cobrar."',
																						'".$id_cuentas_cobrar."',
																						'".$secuencial_cuentas."',
																						'".$fecha_pago."',
																						'".'FACTURA Nº '.$secuencial.' - Cuota '.$k."',
																						'".$cuotas."',
																						'".$cuotas."',
																						'',
																						'',
																						'',
																						'1', 
																						'".$fecha."')");	
					}	
				} else {
					if($arreglo9[$j] == "Años") {
						$class->consulta("INSERT INTO cuentas_cobrar VALUES (	'".$id_cuentas_cobrar."',
																			'".$_SESSION['empresa']['id']."',
																			'".$_SESSION['user']['id']."',
																			'".$id."',
																			'".$_POST['fecha_emision']."',
																			'".$arreglo9[$j]."',
																			'".$arreglo10[$j]."',
																			'".$arreglo8[$j]."',
																			'".$arreglo8[$j]."',
																			'1', 
																			'".$fecha."')");

						$meses  = $arreglo10[$j] * 12;
						for ($k = 1; $k <= $meses; $k++) {
							// contador detalle cobrar
							$id_detalle_cobrar = 0;
							$resultado = $class->consulta("SELECT max(id) FROM detalle_cuentas_cobrar");
							while ($row = $class->fetch_array($resultado)) {
								$id_detalle_cobrar = $row[0];
							}
							$id_detalle_cobrar++;
							// fin

							// max secuencial
							$resultado = $class->consulta("SELECT MAX(secuencial) FROM detalle_cuentas_cobrar GROUP BY id ORDER BY id asc");
							while ($row = $class->fetch_array($resultado)) {
								$secuencial_cuentas = $row[0];
							}
							$secuencial_cuentas = $secuencial_cuentas + 1;

							$ceros = 9;
							$temp = '';
							$tam = $ceros - strlen($secuencial_cuentas);
					      	for ($i = 0; $i < $tam; $i++) {                 
					        	$temp = $temp .'0';        
					      	}
					      	$secuencial_cuentas = $temp .''. $secuencial_cuentas;
					      	// fin

					      	$fecha = date('Y-m-j');
							$fecha_pago = strtotime('+'.$k.' month', strtotime($fecha));
							$fecha_pago = date('Y-m-j',$fecha_pago);

							$cuota = $arreglo8[$j] / $meses;
							$cuotas = number_format($cuota, 2, '.', '');

					      	$class->consulta("INSERT INTO detalle_cuentas_cobrar VALUES (	'".$id_detalle_cobrar."',
																							'".$id_cuentas_cobrar."',
																							'".$secuencial_cuentas."',
																							'".$fecha_pago."',
																							'".'FACTURA Nº '.$secuencial.' - Cuota '.$k."',
																							'".$cuotas."',
																							'".$cuotas."',
																							'',
																							'',
																							'',
																							'1', 
																							'".$fecha."')");	
						}
					}
				}	
			}
	    }

	    //$xml = generarXML($id, $codDoc, $ambiente, $emision); // generar xml
			
		//$firmado = generarFirma($xml, $clave, 'factura', $pass, $token, $ambiente); // firmar xml

		//if($firmado == 5) {
		//	$data = 5; // ARCHIVO NO EXISTE
		//} else {
		//	if($firmado == 6) {
		//		$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
		//	} else {
		//		$respWeb = webService($firmado, $ambiente, $clave,'','factura', $pass, $token,'0'); // Envio Archivo XML Validar 

		//		if($respWeb) {
		//			$estado = $respWeb['RespuestaRecepcionComprobante']['estado'];

		//			if($estado == 'RECIBIDA') {
		//				$respuesta = consultarComprobante($ambiente, $clave);
														
		//				if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		//            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		//        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		//    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

		//	                $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$id."'");

		//	                $dataFile = generarXMLCDATA($respuesta);		                
		//	                $doc = new DOMDocument('1.0', 'UTF-8');
		//      				$doc->loadXML($dataFile); // xml	 
		//       				if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
		//        				$email = $_POST['correo'];
		//        				$total = $_POST['total_pagar'];
		//        				$nombre = $_POST['razon_social'];

		        				// include 'generarPDF.php';
			        			// $data = correo($fecha,$total,$numeroAutorizacion.'.xml',$numeroAutorizacion.'.pdf',$nombre,$email,'comprobantes/'.$numeroAutorizacion.'.xml',generarPDF($id),1);

		//	        			if(trim($email) == '' && $email != '') {
		//	        				$resultado = $class->consulta("UPDATE factura_venta SET estado = '1' WHERE id = '".$id."'");			
		//							if($resultado) {
		//								$data = 1; // datos actualizados
		//							} else {
		//								$data = 4; // error al momento de guadar
		//							}
		//						} else {
		//							$data = 3; // error al momento de enviar el correo
		//			    			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$id."'"); 
		//		    			}	
		//        			} else {
		//        				$data = 2; // error al generar los documentos
		//	        			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$id."'");               	
		//        			}      
		//				} else {
		//					if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
		//						$data = 7; // Error en el service web
		//						$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$id."'");
		//					}
		//				}
		//			} else {
		//				if($estado == 'DEVUELTA') {
		//					$data = 8; // Error en el service web
		//					$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$id."'");
		//				}
		//			}
		//		}
		//	}
		//}
		
		//print_r(json_encode(array('estado' => $data, 'id' => $id)));
		echo $id;
	}
	// fin

	// anular factura venta
	if (isset($_POST['btn_anular']) == "Anular") {
		$fecha_emision = $_POST['fecha_emision'];

		$class->consulta("UPDATE factura_venta SET fecha_anulacion = '$fecha_emision', estado = '10'  WHERE id = '".$_POST['id_factura']."'");

		// datos detalle factura
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    // Fin

	    // descomponer detalle factura
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
		$resultado = $class->consulta("SELECT MAX(secuencia_factura) FROM secuencia_comprobantes GROUP BY id ORDER BY id asc");
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

	// LLenar forma pago
	if (isset($_POST['llenar_forma_pago'])) {
		$resultado = $class->consulta("SELECT id, codigo ,nombre_forma, principal FROM formas_pago WHERE estado = '1' order by id asc");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['codigo'].' - '.$row['nombre_forma'].'</option>';	
			} else {
				print '<option value="'.$row['id'].'">'.$row['codigo'].' - '.$row['nombre_forma'].'</option>';	
			}
		}
	}
	// fin

	// LLenar forma pago campos
	if (isset($_POST['llenar_campos_pago'])) {
		$resultado = $class->consulta("SELECT id, codigo ,nombre_forma FROM formas_pago WHERE id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('id' => $row[0], 'codigo' => $row[1] , 'descripcion' => $row[2]);
		}

		print_r(json_encode($data));
	}
	// fin

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

	// llenar detalle pagos
	if (isset($_POST['llenar_detalle_pagos'])) {
		$resultado = $class->consulta("SELECT A.id_forma_pago, F.codigo, F.nombre_forma, A.valor, A.tiempo, A.plazo FROM formas_pagos_venta A, factura_venta V, formas_pago F WHERE A.id_factura_venta = V.id AND A.id_forma_pago = F.id AND V.id = '".$_POST['id']."' ORDER BY A.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$arr_data[] = $row['0'];
		    $arr_data[] = $row['1'];
		    $arr_data[] = $row['2'];
		    $arr_data[] = $row['3'];
		    $arr_data[] = $row['4'];
		    $arr_data[] = $row['5'];
		}

		echo json_encode($arr_data);
	}
	//fin

	// llenar cabezera proforma
	if (isset($_POST['llenar_cabezera_proforma'])) {
		$resultado = $class->consulta("SELECT P.id, P.id_cliente, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, P.tipo_precio, P.subtotal, P.tarifa, P.tarifa0, P.iva, P.total_descuento, P.total_proforma FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id_proforma' => $row[0],
							'id_cliente' => $row[1],
							'identificacion' => $row[2],
							'razon_social' => $row[3],
							'direccion' => $row[4],
							'telefono2' => $row[5],
							'correo' => $row[6],
							'tipo_precio' => $row[7],
							'subtotal' => $row[8],
							'tarifa' => $row[9],
							'tarifa0' => $row[10],
							'iva' => $row[11],
							'descuento' => $row[12],
							'total_pagar' => $row[13]);
		}

		print_r(json_encode($data));
	}
	//fin

	// llenar detalle proforma
	if (isset($_POST['llenar_detalle_proforma'])) {
		$resultado = $class->consulta("SELECT D.id_producto, U.codigo, U.descripcion, D.cantidad, D.precio, D.descuento, D.total, P.nombre_tarifa_impuesto, U.incluye_iva FROM detalle_proforma D, proforma N, productos U, tarifa_impuesto P WHERE D.id_producto = U.id AND D.id_proforma = N.id AND U.id_porcentaje = P.id AND N.id = '".$_POST['id']."' ORDER BY D.id asc");
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
		}

		echo json_encode($arr_data);
	}
	//fin

	// reenviarCorreo
	if (isset($_POST['reenviarCorreo']) == "reenviarCorreo") {
		$resultado = $class->consulta("SELECT C.correo, C.nombre_comercial, F.total_venta FROM factura_venta F, clientes C WHERE F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {						
			$email = $row[0];
			$nombre = $row[1];
			$total = $row[2];
		} 
		
		if($email != "") {
			include 'generarPDF.php';

			$data = correo($fecha,$total,$_POST['aut'].'.xml',$_POST['aut'].'.pdf', $nombre, $email,'comprobantes/'.$_POST['aut'].'.xml',generarPDF($_POST['id']),1);

			if($data == 1) {
				$resultado = $class->consulta("UPDATE factura_venta set estado = '1' where id = '".$_POST['id']."'");

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
		$resultado = $class->consulta("SELECT F.emision, E.token, E.clave, F.clave_acceso, C.correo, C.nombre_comercial, F.total_venta FROM factura_venta F, empresa E, clientes C WHERE F.id_empresa = E.id AND F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
			$total = $row[6];
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
	            $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

	            $dataFile = generarXMLCDATA($respuesta);		                
	            $doc = new DOMDocument('1.0', 'UTF-8');
				$doc->loadXML($dataFile); // xml	 
				if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
	    			$data = 3; // comprobante autorizado
					$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	    			
				} else {
					$data = 2; // error al generar los documentos
			        $class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");			                	
				} 
			} else {
				$data = 7; // Error en el service web rechazado
				$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");		
			}     
		} else {
			$data = 7; // Error en el service web rechazado
			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
		}

		echo $data;
	}
	// fin

	// errorWebService
	if (isset($_POST['errorWebService']) == "errorWebService") {
		$resultado = $class->consulta("SELECT F.emision, E.token, E.clave, F.clave_acceso, C.correo, C.nombre_comercial, F.total_venta FROM factura_venta F, empresa E, clientes C WHERE F.id_empresa = E.id AND F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
			$total = $row[6];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'factura', $pass, $token, $ambiente,'1'); // firmar xml

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
				                $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

				                $dataFile = generarXMLCDATA($respuesta);		                
				                $doc = new DOMDocument('1.0', 'UTF-8');
			        			$doc->loadXML($dataFile); // xml	 
			        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
									$data = 3; // comprobante autorizado
					    			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
			        			} else {
			        				$data = 2; // error al generar los documentos
			        				$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");                	
			        			}
		        			} else {
		        				$data = 7; // Error en el service web rechazado
								$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
		        			}      
						} else {
							$data = 7; // Error en el service web rechazado
							$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
						}
					} else {
						$data = 8; // Error en el service web
						$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				} else {
					if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {
						
	            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
	        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
	    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;
		                $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

		                $dataFile = generarXMLCDATA($respuesta);		                
		                $doc = new DOMDocument('1.0', 'UTF-8');
	        			$doc->loadXML($dataFile); // xml	 
	        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
			    			$data = 3; // comprobante autorizado
					    	$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
	        			} else {
	        				$data = 2; // error al generar los documentos
			        		$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
	        			}      
					} else {
						$data = 7; // Error en el service web rechazado
						$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				}				
			}
		}

		echo $data;
	}
	// fin

	// generarFirma
	if (isset($_POST['generarFirma']) == "generarFirma") {
		$resultado = $class->consulta("SELECT F.emision, E.token, E.clave, F.clave_acceso, C.correo, C.nombre_comercial, F.total_venta FROM factura_venta F, empresa E, clientes C WHERE F.id_empresa = E.id AND F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
			$total = $row[6];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'factura', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado, $ambiente, $clave,'','factura',$pass,$token,'0'); // Envio Archivo XML Validar 
				
				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
								$data = 3; // comprobante autorizado
					    		$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
		        			} else {
		        				// error al gererar archivos
		        				$data = 2;
			        			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
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
		$resultado = $class->consulta("SELECT F.emision, E.token, E.clave, F.clave_acceso, C.correo, C.nombre_comercial, F.total_venta FROM factura_venta F, empresa E, clientes C WHERE F.id_empresa = E.id AND F.id_cliente = C.id AND F.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$emision = $row[0];
			$token = $row[1];
			$pass = $row[2];
			$clave = $row[3];
			$email = $row[4];
			$nombre = $row[5];
			$total = $row[6];
		}

		// parametro ambiente
		$resultado = $class->consulta("SELECT codigo FROM tipo_ambiente WHERE principal = 'Si' AND estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$ambiente = $row[0];
		}
		// fin

		$xml = generarXML($_POST['id'], $codDoc, $ambiente, $emision); // generar xml
			
		$firmado = generarFirma($xml, $clave, 'factura', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // archivo token
		} else {
			if($firmado == 6) {
				$data = 6; // contrasenia incorrecta
			} else {
				$respWeb = webService($firmado,$ambiente,$clave,'','factura',$pass,$token,'0'); // Envio Archivo XML Validar 

				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE factura_venta SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
								$data = 3; // comprobante autorizado
					    		$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
		        			} else {
		        				// error al gererar archivos
		        				$data = 2;
			        			$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE factura_venta SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
						}
					}
				}
			}
		}

		echo $data;
	}
	// fin

	// consultar factura
	if(isset($_POST['cargar_tabla'])) {
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