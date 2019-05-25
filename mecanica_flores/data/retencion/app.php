<?php 
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	ini_set('max_execution_time', 240); //240 segundos = 4 minutos
	include 'generarXML.php';	
	include 'firma/firma.php';	
	include 'firma/xades.php';
	//include_once('../../admin/correolocal.php');
    include_once('../../admin/correoweb.php');
    $fecha = $class->fecha_hora();
    $defaultMail = mailDefecto;
	$codDoc = '07'; // tipo documento
	error_reporting(0);

	// guardar retención
	if (isset($_POST['btn_guardar']) == "Guardar") {
		// contador retención
		$id = 0;
		$resultado = $class->consulta("SELECT max(id) FROM retencion");
		while ($row = $class->fetch_array($resultado)) {
			$id = $row[0];
		}
		$id++;
		// fin

		// modificar secuencia_comprobantes
       	$resultado = $class->consulta("SELECT MAX(secuencia_retencion) FROM secuencia_comprobantes GROUP BY id");
       	while ($row = $class->fetch_array($resultado)) {
            $secuencial = $row[0];
        }

        $secuencial = $secuencial + 1;
        $class->consulta("UPDATE secuencia_comprobantes SET secuencia_retencion = '".$secuencial."'");
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

		// guardar retencion
		$class->consulta("INSERT INTO retencion VALUES  (	'".$id."',
															'".$_SESSION['empresa']['id']."',
															'".$_POST['id_proveedor']."',
															'".$_SESSION['user']['id']."',
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
															'".$_POST['select_mes']."',
															'".$_POST['select_anio']."',
															'".$_POST['select_tipo_comprobante']."',
															'".$_POST['comprobante']."',
															'".$_POST['select_forma_pago']."',
															'".$_POST['total_retenido']."',
															'9', 
															'".$fecha."')");
		// fin

        // auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Retención','INSERT','".'Creación Retención:'.$secuencial."','','','$id','$fecha')");

		// datos detalle retencion
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    $campo4 = $_POST['campo4'];
	    $campo5 = $_POST['campo5'];
	    // Fin

	    // descomponer detalle retencion
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $arreglo4 = explode('|', $campo4);
	    $arreglo5 = explode('|', $campo5);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle retencion
			$id_detalle_retencion = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_retencion");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_retencion = $row[0];
			}
			$id_detalle_retencion++;
			// fin

			$resp = $class->consulta("INSERT INTO detalle_retencion VALUES(	'".$id_detalle_retencion."',
																			'".$id."',
																			'".$arreglo1[$i]."',
																			'".$arreglo2[$i]."',
																			'".$arreglo3[$i]."',
																			'".$arreglo4[$i]."',
																			'".$arreglo5[$i]."',
																			'1', 
																			'".$fecha."')");
		}

		$xml = generarXML($id,$codDoc,$ambiente,$emision); // generar xml

		$firmado = generarFirma($xml,$clave,'comprobanteRetencion',$pass,$token,$ambiente);//devuelvo archivo firmado en formato

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado,$ambiente,$clave,'','comprobanteRetencion',$pass,$token,'0');// Envio Archivo XML Validar

				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado'];

					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$id."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml	 
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
		        				$email = $_POST['correo'];
		        				$total = $_POST['total_retenido'];
		        				$nombre = $_POST['razon_social'];

		        				// include 'generarPDF.php';
			        			// $data = correo($fecha,$total,$numeroAutorizacion.'.xml',$numeroAutorizacion.'.pdf',$nombre,$email,'comprobantes/'.$numeroAutorizacion.'.xml',generarPDF($id),1);

			        			if(trim($email) == '' && $email != '') {
			        				$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$id."'");			
									if($resultado) {
										$data = 1; // datos actualizados
									} else {
										$data = 4; // error al momento de guadar
									}
								} else {
									$data = 3; // error al momento de enviar el correo
					    			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$id."'"); 
				    			}	
		        			} else {
		        				$data = 2; // error al generar los documentos
			        			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$id."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$id."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$id."'");
						}
					}
				}
			}
		}

		print_r(json_encode(array('estado' => $data, 'id' => $id)));
	}
	// fin

	//cargar ultimo secuencial
	if (isset($_POST['cargar_secuencial'])) {
		$resultado = $class->consulta("SELECT MAX(secuencia_retencion) FROM secuencia_comprobantes GROUP BY id ORDER BY id asc");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('serie' => $row[0]);
		}

		print_r(json_encode($data));
	}
	//fin

	// LLenar info
	if (isset($_POST['llenar_infomacion'])) {
		$resultado = $class->consulta("SELECT establecimiento, punto_emision, token FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('establecimiento' => $row[0], 'emision' => $row[1], 'token' => $row[2]);
		}

		print_r(json_encode($data));
	}
	// fin

	// LLenar tipo comprobante
	if (isset($_POST['llenar_tipo_comprobante'])) {
		$resultado = $class->consulta("SELECT id, codigo ,nombre_tipo_comprobante, principal FROM tipo_comprobante WHERE estado = '1' ORDER BY id ASC");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['codigo'].' - '.$row['nombre_tipo_comprobante'].'</option>';	
			} else {
				print '<option value="'.$row['id'].'">'.$row['codigo'].' - '.$row['nombre_tipo_comprobante'].'</option>';	
			}
		}
	}
	// fin

	// LLenar forma pago
	if (isset($_POST['llenar_forma_pago'])) {
		$resultado = $class->consulta("SELECT id, codigo ,nombre_forma, principal FROM formas_pago WHERE estado = '1' ORDER BY id ASC");
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

	// LLenar tipo comprobante
	if (isset($_POST['llenar_tipo_retencion'])) {
		$resultado = $class->consulta("SELECT id, codigo ,nombre_tipo_retencion, principal FROM tipo_retencion WHERE estado = '1' ORDER BY id ASC");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_tipo_retencion'].'</option>';	
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_tipo_retencion'].'</option>';	
			}
		}
	}
	// fin

	// LLenar retencion fuente
	if (isset($_POST['llenar_tarifa_retencion'])) {
		$resultado = $class->consulta("SELECT id, codigo, descripcion FROM tarifa_retencion WHERE id_tipo_retencion = '".$_POST['id']."' ORDER BY id ASC");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			print '<option value="'.$row['id'].'">'.$row['descripcion'].'</option>';	
		}
	}
	// fin

	// buscar porcentaje
	if (isset($_POST['llenar_porcentaje'])) {
		$resultado = $class->consulta("SELECT nombre_tarifa_retencion FROM tarifa_retencion WHERE id = '".$_POST['id']."' ORDER BY id ASC");
		while ($row = $class->fetch_array($resultado)) {
		    $valor = $row['nombre_tarifa_retencion'];
		}
		$data = $valor;

		echo $data;
	}
	// fin

	// llenar cabezera retencion
	if (isset($_POST['llenar_cabezera_retencion'])) {
		$resultado = $class->consulta("SELECT R.id, R.fecha_emision, R.secuencial, P.id, P.identificacion, P.razon_social, P.telefono2, P.direccion, P.correo, R.id_tipo_comprobante, R.mes, R.anio, R.numero_comprobante, R.id_forma_pago, R.total_retenido FROM retencion R, proveedores P WHERE R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id_retencion' => $row[0],
							'fecha_emision' => $row[1],
							'secuencial' => $row[2],
							'id_proveedor' => $row[3],
							'identificacion' => $row[4],
							'razon_social' => $row[5],
							'telefono2' => $row[6],
							'direccion' => $row[7],
							'correo' => $row[8],
							'id_tipo_comprobante' => $row[9],
							'mes' => $row[10],
							'anio' => $row[11],
							'numero_comprobante' => $row[12],
							'id_forma_pago' => $row[13],
							'total_retenido' => $row[14]);
		}

		print_r(json_encode($data));
	}
	//fin

	// llenar detalle reetncion
	if (isset($_POST['llenar_detalle_retencion'])) {
		$resultado = $class->consulta("SELECT R.id_tipo_comprobante, R.mes, R.anio, D.id_tarifa_retencion, T.nombre_tarifa_retencion, D.base_imponible, D.id_tipo_retencion, TR.nombre_tipo_retencion, D.porcentaje, D.valor_retenido FROM retencion R, detalle_retencion D, tipo_retencion TR, tarifa_retencion T WHERE D.id_retencion = R.id AND D.id_tarifa_retencion = T.id AND D.id_tipo_retencion = TR.id AND R.id = '".$_POST['id']."' ORDER BY D.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$arr_data[] = $row['0'];
		    $arr_data[] = $row['1'].'/'.$row['2'];
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
		$resultado = $class->consulta("SELECT P.correo, P.razon_social, R.total_comprobante FROM retencion R, proveedores P WHERE R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {						
			$email = $row[0];
			$nombre = $row[1];
			$total = $row[2];
		} 
		
		if(trim($email) == '') {
			$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");

			if($resultado) {
				$data = 1; // datos actualizados
			} else {
				$data = 4; // error al momento de guadar
			}
		} else {
			include 'generarPDF.php';			        				      
			$data = correo($fecha,$total,$_POST['aut'].'.xml',$_POST['aut'].'.pdf', $nombre, $email,'comprobantes/'.$_POST['aut'].'.xml',generarPDF($_POST['id']),1);

			if($data == 1) {
				$resultado = $class->consulta("UPDATE retencion set estado = '1' where id = '".$_POST['id']."'");

				if($resultado) {
					$data = 1; // datos actualizados
				} else {
					$data = 4; // error al momento de guadar
				}
			}	
		}			
		
		echo $data;
	}
	// fin

	// generarArchivos
	if (isset($_POST['generarArchivos']) == "generarArchivos") {
		$resultado = $class->consulta("SELECT R.emision, E.token, E.clave, R.clave_acceso, P.correo, P.nombre_comercial, R.total_comprobante FROM retencion R, empresa E, proveedores P WHERE R.id_empresa = E.id AND R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
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
	            $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

	            $dataFile = generarXMLCDATA($respuesta);		                
	            $doc = new DOMDocument('1.0', 'UTF-8');
				$doc->loadXML($dataFile);//xml	 
				if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {

	    			if(trim($email) == '') {		
						$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");

						if($resultado) {
							//datos actualizados
							$data = 1; 
						} else {
							//error al momento de guadar
							$data = 4;
						}
					} else {
	    				$data = 3; // error al momento de enviar el correo
					    $class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
	    			}	    			
				} else {
					$data = 2; // error al generar los documentos
			        $class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");			                	
				} 
			} else {
				$data = 7; // Error en el service web rechazado
				$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");		
			}     
		} else {
			$data = 7; // Error en el service web rechazado
			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
		}

		echo $data;
	}
	// fin

	// errorWebService
	if (isset($_POST['errorWebService']) == "errorWebService") {
		$resultado = $class->consulta("SELECT R.emision, E.token, E.clave, R.clave_acceso, P.correo, P.nombre_comercial, R.total_comprobante FROM retencion R, empresa E, proveedores P WHERE R.id_empresa = E.id AND R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
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
			
		$firmado = generarFirma($xml, $clave, 'comprobanteRetencion', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5;//ARCHIVO NO EXISTE
		}else{
			if($firmado == 6) {
				$data = 6;////CONTRASEÑA DE TOKEN INCORRECTA
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
				                $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

				                $dataFile = generarXMLCDATA($respuesta);		                
				                $doc = new DOMDocument('1.0', 'UTF-8');
			        			$doc->loadXML($dataFile);//xml	 
			        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
			        				/*include '../generarPDF.php';				        				
				        			$data = correo($fecha,$totalComprobante,'./comprobantes/'.$numeroAutorizacion.'.xml','../comprobantes/'.$numeroAutorizacion.'.pdf',$_POST['nombreComercial'],$mail,$doc->saveXML(),generarPDF($id),0);*/
				        			if(trim($email) == '') {
				        				$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");

										if($resultado) {
											//datos actualizados
											$data = 1; 
										} else {
											//error al momento de guadar
											$data = 4;
										}
									} else {
										$data = 3; // error al momento de enviar el correo
					    				$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					    			}	
			        			} else {
			        				$data = 2; // error al generar los documentos
			        				$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");                	
			        			}
		        			} else {
		        				$data = 7; // Error en el service web rechazado
								$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
		        			}      
						} else {
							$data = 7; // Error en el service web rechazado
							$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
						}
					} else {
						$data = 8; // Error en el service web
						$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				} else {
					if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {
	            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
	        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
	    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;
		                $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

		                $dataFile = generarXMLCDATA($respuesta);		                
		                $doc = new DOMDocument('1.0', 'UTF-8');
	        			$doc->loadXML($dataFile);//xml	 
	        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {
	        				/*include '../generarPDF.php';		        				
		        			$data = correo($fecha,$totalComprobante,'./comprobantes/'.$numeroAutorizacion.'.xml','../comprobantes/'.$numeroAutorizacion.'.pdf',$_POST['nombreComercial'],$mail,$doc->saveXML(),generarPDF($id),0);*/
		        			if(trim($email) == '') {
		        				$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");

								if($resultado) {
									//datos actualizados
									$data = 1; 
								} else {
									//error al momento de guadar
									$data = 4;
								}
							} else {
			    				$data = 3; // error al momento de enviar el correo
					    		$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
			    			}
	        			} else {
	        				$data = 2; // error al generar los documentos
			        		$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
	        			}      
					} else {
						$data = 7; // Error en el service web rechazado
						$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
					}
				}				
			}
		}

		echo $data;
	}
	// fin

	// generarFirma
	if (isset($_POST['generarFirma']) == "generarFirma") {
		$resultado = $class->consulta("SELECT R.emision, E.token, E.clave, R.clave_acceso, P.correo, P.nombre_comercial, R.total_comprobante FROM retencion R, empresa E, proveedores P WHERE R.id_empresa = E.id AND R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
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
			
		$firmado = generarFirma($xml, $clave, 'comprobanteRetencion', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado, $ambiente, $clave,'','comprobanteRetencion',$pass,$token,'0'); // Envio Archivo XML Validar 
				
				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {

			        			if(trim($email) == '') {
			        				$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");			
									if($resultado) {
										//datos actualizados
										$data = 1; 
									} else {
										//error al momento de guadar
										$data = 4;
									}
								} else {
									// error al momento de enviar el correo
									$data = 3; 
					    			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'"); 
				    			}	
		        			} else {
		        				// ERROR AL GENERAR LOS DOCUMENTOS
		        				$data = 2;
			        			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
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
		$resultado = $class->consulta("SELECT R.emision, E.token, E.clave, R.clave_acceso, P.correo, P.nombre_comercial, R.total_comprobante FROM retencion R, empresa E, proveedores P WHERE R.id_empresa = E.id AND R.id_proveedor = P.id AND R.id = '".$_POST['id']."'");
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
			
		$firmado = generarFirma($xml, $clave, 'comprobanteRetencion', $pass, $token, $ambiente,'1'); // firmar xml

		if($firmado == 5) {
			$data = 5; // ARCHIVO NO EXISTE
		} else {
			if($firmado == 6) {
				$data = 6; // CONTRASEÑA DE TOKEN INCORRECTA
			} else {
				$respWeb = webService($firmado,$ambiente,$clave,'','comprobanteRetencion',$pass,$token,'0'); // Envio Archivo XML Validar 

				if($respWeb) {
					$estado = $respWeb['RespuestaRecepcionComprobante']['estado']; 
					if($estado == 'RECIBIDA') {
						$respuesta = consultarComprobante($ambiente, $clave);														
						if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'AUTORIZADO') {

		            	    $numeroAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
		        	        $fechaAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
		    	            $ambienteAutorizacion = $respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

			                $class->consulta("UPDATE retencion SET fecha_autorizacion = '".$fechaAutorizacion."', estado = '2', numero_autorizacion = '".$numeroAutorizacion."', ambiente = '".$ambienteAutorizacion."' WHERE id = '".$_POST['id']."'");

			                $dataFile = generarXMLCDATA($respuesta);		                
			                $doc = new DOMDocument('1.0', 'UTF-8');
		        			$doc->loadXML($dataFile); // xml
		        			if($doc->save('comprobantes/'.$numeroAutorizacion.'.xml')) {

			        			if(trim($email) == '') {
			        				$resultado = $class->consulta("UPDATE retencion SET estado = '1' WHERE id = '".$_POST['id']."'");			
									if($resultado) {
										//datos actualizados
										$data = 1; 
									} else {
										//error al momento de guadar
										$data = 4;
									}
								} else {
									// error al momento de enviar el correo
									$data = 3; 
					    			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'"); 
				    			}	
		        			} else {
		        				// ERROR AL GENERAR LOS DOCUMENTOS
		        				$data = 2;
			        			$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");               	
		        			}      
						} else {
							if(isset($respuesta->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) == 'NO AUTORIZADO') {
								$data = 7; // Error en el service web
								$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");
							}
						}
					} else {
						if($estado == 'DEVUELTA') {
							$data = 8; // Error en el service web
							$class->consulta("UPDATE retencion SET estado = '".$data."' WHERE id = '".$_POST['id']."'");	
						}
					}
				}
			}
		}

		echo $data;
	}
	// fin 
?>