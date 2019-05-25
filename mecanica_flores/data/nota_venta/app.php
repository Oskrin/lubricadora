<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	ini_set('max_execution_time', 240); //240 segundos = 4 minutos
	$fecha = $class->fecha_hora();
	$fecha_corta = $class->fecha();
	error_reporting(0);

	//guardar factura venta
	if (isset($_POST['btn_guardar']) == "Guardar") {
		// contador nota
		$id = 0;
		$resultado = $class->consulta("SELECT max(id) FROM nota_venta");
		while ($row = $class->fetch_array($resultado)) {
			$id = $row[0];
		}
		$id++;
		// fin

		// max secuencial
		$resultado = $class->consulta("SELECT MAX(secuencial) FROM nota_venta GROUP BY id ORDER BY id asc");
		while ($row = $class->fetch_array($resultado)) {
			$secuencial = $row[0];
		}
		$secuencial = $secuencial + 1;

		$ceros = 9;
		$temp = '';
		$tam = $ceros - strlen($secuencial);
      	for ($i = 0; $i < $tam; $i++) {                 
        	$temp = $temp .'0';        
      	}
      	$secuencial = $temp .''. $secuencial;

      	// valor cambio
      	$valor = number_format($_POST['efectivo'], 2, '.', '');

		if ($_POST['id_cliente'] != "") {
			// guardar nota normal
			$class->consulta("INSERT INTO nota_venta VALUES  (	'".$id."',
																'".$_SESSION['empresa']['id']."',
																'".$_SESSION['user']['id']."',
																'".$_POST['id_cliente']."',
																'".$_POST['fecha_emision']."',
																'',
																'".$secuencial."',
																'".$_POST['select_tipo_precio']."',
																'".$_POST['subtotal']."',
																'".$_POST['tarifa']."',
																'".$_POST['tarifa_0']."',
																'".$_POST['iva']."',
																'".$_POST['otros']."',
																'".$_POST['total_pagar']."',
																'".$valor."',
																'".$_POST['cambio']."',
																'1', 
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

			// guardar nota nuevo cliente
			$class->consulta("INSERT INTO nota_venta VALUES  (	'".$id."',
																'".$_SESSION['empresa']['id']."',
																'".$_SESSION['user']['id']."',
																'".$id_cliente."',
																'".$_POST['fecha_emision']."',
																'',
																'".$secuencial."',
																'".$_POST['select_tipo_precio']."',
																'".$_POST['subtotal']."',
																'".$_POST['tarifa']."',
																'".$_POST['tarifa_0']."',
																'".$_POST['iva']."',
																'".$_POST['otros']."',
																'".$_POST['total_pagar']."',
																'".$valor."',
																'".$_POST['cambio']."',
																'1', 
																'".$fecha."')");
			
			// fin	
		}

		
		// modificar proformas
        if ($_POST['id_proforma'] != "") {
        	$class->consulta("UPDATE proforma SET estado = '0' WHERE id = '".$_POST['id_proforma']."'");
        }
        // fin

        // auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Nota Venta','INSERT','".'Creación Nota Venta:'.$secuencial."','','','$id','$fecha')");

		// datos detalle nota
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    $campo4 = $_POST['campo4'];
	    $campo5 = $_POST['campo5'];
	    $campo6 = $_POST['campo6'];
	    // Fin

	    // descomponer detalle nota
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $arreglo4 = explode('|', $campo4);
	    $arreglo5 = explode('|', $campo5);
	    $arreglo6 = explode('|', $campo6);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle nota
			$id_detalle_nota = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_nota_venta");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_nota = $row[0];
			}
			$id_detalle_nota++;
			// fin

			$class->consulta("INSERT INTO detalle_nota_venta VALUES (	'".$id_detalle_nota."',
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

			            if ($res == 0) {
			            	$class->consulta("UPDATE productos SET stock = '0' WHERE id = '".$id_pro."'");
			            }
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
			// $id_kardex = 0;
			// $resultado = $class->consulta("SELECT max(id) FROM kardex");
			// while ($row = $class->fetch_array($resultado)) {
			//	$id_kardex = $row[0];
			// }
			// $id_kardex++;
			// fin

			// guardar kardex
			// $class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
			//												'".$arreglo1[$i]."',
			//												'".$fecha_corta."',
			//												'".'N.V:'.$secuencial."',
			//												'".$arreglo2[$i]."',
			//												'".$arreglo3[$i]."',
			//												'".$arreglo5[$i]."',
			//												'".$cal."',
			//												'',
			//												'',
			//												'8', 
			//												'".$fecha."')");
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
			$resultado = $class->consulta("SELECT max(id) FROM formas_pagos_notas");
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

			$class->consulta("INSERT INTO formas_pagos_notas VALUES (	'".$id_detalle_pagos."',
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
																				'".'NOTA Nº '.$secuencial.' - Cuota Diferida'."',
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
																						'".'NOTA Nº '.$secuencial.' - Cuota '.$k."',
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
																							'".'NOTA Nº '.$secuencial.' - Cuota '.$k."',
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

		echo $id;
	}

	// anular notas
	if (isset($_POST['btn_anular']) == "Anular") {
		$fecha_emision = $_POST['fecha_emision'];

		$class->consulta("UPDATE nota_venta SET fecha_anulacion = '$fecha_emision', estado = '0'  WHERE id = '".$_POST['id_nota']."'");

		// datos detalle notas
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    // Fin

	    // descomponer detalle notas
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

	//cargar ultima serie nota venta
	if (isset($_POST['cargar_secuencial'])) {
		$resultado = $class->consulta("SELECT MAX(secuencial) FROM nota_venta GROUP BY id ORDER BY id asc");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('serie' => $row[0]);
		}

		print_r(json_encode($data));
	}
	//fin

	// LLenar info
	if (isset($_POST['llenar_infomacion'])) {
		$resultado = $class->consulta("SELECT establecimiento, punto_emision FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('establecimiento' => $row[0], 'emision' => $row[1]);
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

	// llenar cabezera nota venta
	if (isset($_POST['llenar_cabezera_nota'])) {
		$resultado = $class->consulta("SELECT N.id, N.fecha_emision, N.secuencial,  N.id_cliente, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, N.id_forma_pago, N.tipo_precio, N.subtotal, N.tarifa, N.tarifa0, N.iva, N.total_descuento, N.total_nota, N.efectivo, N.cambio, N.estado FROM nota_venta N, clientes C WHERE N.id_cliente = C.id AND N.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id_nota' => $row[0],
							'fecha_emision' => $row[1],
							'secuencial' => $row[2],
							'id_cliente' => $row[3],
							'identificacion' => $row[4],
							'razon_social' => $row[5],
							'direccion' => $row[6],
							'telefono2' => $row[7],
							'correo' => $row[8],
							'id_forma_pago' => $row[9],
							'tipo_precio' => $row[10],
							'subtotal' => $row[11],
							'tarifa' => $row[12],
							'tarifa0' => $row[13],
							'iva' => $row[14],
							'descuento' => $row[15],
							'total_pagar' => $row[16],
							'efectivo' => $row[17],
							'cambio' => $row[18],
							'estado' => $row[19]);
		}
		
		print_r(json_encode($data));
	}
	//fin

	// llenar detalle nota venta
	if (isset($_POST['llenar_detalle_nota'])) {
		$resultado = $class->consulta("SELECT D.id_producto, U.codigo, U.descripcion, D.cantidad, D.precio, D.descuento, D.total, P.nombre_tarifa_impuesto, U.incluye_iva, D.pendientes FROM detalle_nota_venta D, factura_venta F, productos U, tarifa_impuesto P  WHERE D.id_producto = U.id AND D.id_nota_venta = F.id AND U.id_porcentaje = P.id AND F.id = '".$_POST['id']."' ORDER BY D.id ASC");
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

	// consultar nota
	if(isset($_POST['cargar_tabla'])) {
		$resultado = $class->consulta("SELECT N.id, U.nombres_completos, C.razon_social, N.secuencial, N.total_nota, N.fecha_creacion FROM nota_venta  N, clientes C, usuarios U WHERE N.id_usuario = U.id AND N.id_cliente = C.id AND N.fecha_emision BETWEEN '$_POST[fecha_inicio]' AND '$_POST[fecha_fin]' ORDER BY N.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'id' => $row[0],
								'vendedor' => $row[1],
								'cliente' => $row[2],
								'secuencial' => $row[3],
								'total_nota' => $row[4],
								'fecha_creacion' => $row[5]
								);
		}

		echo $lista = json_encode($lista);
	}
	// fin

	// consultar detalles
	if(isset($_POST['cargar_tabla_detalle'])) {
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.precio, D.cantidad, D.total FROM detalle_nota_venta D, nota_venta N, productos P WHERE D.id_nota_venta = N.id AND D.id_producto = P.id AND D.id_nota_venta = '$_POST[id]' ORDER BY D.id ASC");
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