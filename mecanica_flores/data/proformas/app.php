<?php        
	include_once('../../admin/class.php');
	include_once('../../admin/proformalocal.php');
	//include_once('../../admin/proformaweb.php');
	$class = new constante();
	$fecha = $class->fecha_hora();
	session_start(); 
	error_reporting(0);

	// guardar proformas
	if (isset($_POST['btn_guardar']) == "Guardar") {
		// contador proforma
		$id_proforma = 0;
		$resultado = $class->consulta("SELECT max(id) FROM proforma");
		while ($row = $class->fetch_array($resultado)) {
			$id_proforma = $row[0];
		}
		$id_proforma++;
		// fin

		// max secuencial
		$resultado = $class->consulta("SELECT MAX(secuencial) FROM proforma GROUP BY id ORDER BY id asc");
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

		$class->consulta("INSERT INTO proforma VALUES  (	'".$id_proforma."',
															'".$_SESSION['empresa']['id']."',
															'".$_SESSION['user']['id']."',
															'".$_POST['id_cliente']."',
															'".$_POST['fecha_emision']."',
															'$secuencial',
															'".$_POST['select_tipo_precio']."',
															'".$_POST['subtotal']."',
															'".$_POST['tarifa']."',
															'".$_POST['tarifa_0']."',
															'".$_POST['iva']."',
															'".$_POST['otros']."',
															'".$_POST['total_pagar']."',
															'',
															'',
															'1', 
															'".$fecha."')");

		// enviar proforma
		$total = $_POST['total_pagar'];
		$nombre = $_POST['razon_social'];
		$email = $_POST['correo'];
		
		include 'proforma.php';
		$data = correo($fecha,$total,$secuencial.'.pdf',$nombre,$email,generarPDF($id_proforma),1);

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Proforma','INSERT','".'Creaci贸n Proforma:'.$_POST['serie']."','','','$id_proforma','$fecha')");

		// datos detalle proforma
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    $campo4 = $_POST['campo4'];
	    $campo5 = $_POST['campo5'];
	    // Fin

	    // descomponer detalle proforma
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $arreglo4 = explode('|', $campo4);
	    $arreglo5 = explode('|', $campo5);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle proforma
			$id_detalle_proforma = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_proforma");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_proforma = $row[0];
			}
			$id_detalle_proforma++;
			// fin

			$class->consulta("INSERT INTO detalle_proforma VALUES 	(	'".$id_detalle_proforma."',
																		'".$id_proforma."',
																		'".$arreglo1[$i]."',
																		'".$arreglo2[$i]."',
																		'".$arreglo3[$i]."',
																		'".$arreglo4[$i]."',
																		'".$arreglo5[$i]."',
																		'1', 
																		'".$fecha."')");
	    }
		echo $id_proforma;
	}
	// fin

	// anular proforma
	if (isset($_POST['btn_anular']) == "Anular") {
		$class->consulta("UPDATE proforma SET estado = '0' WHERE id = '".$_POST['id_proforma']."'");

		$data = 1;
		echo $data;
	}
	// fin

	//cargar ultima serie proforma
	if (isset($_POST['cargar_secuencial'])) {
		$resultado = $class->consulta("SELECT MAX(secuencial) FROM proforma GROUP BY id ORDER BY id asc");
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

	// consultar proforma
	if(isset($_POST['cargar_tabla'])) {
		$resultado = $class->consulta("SELECT P.id, U.nombres_completos, C.razon_social, P.secuencial, P.total_proforma, P.fecha_creacion FROM proforma P, clientes C, usuarios U WHERE P.id_usuario = U.id AND P.id_cliente = C.id AND P.fecha_emision BETWEEN '$_POST[fecha_inicio]' AND '$_POST[fecha_fin]' ORDER BY P.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'id' => $row[0],
								'vendedor' => $row[1],
								'cliente' => $row[2],
								'secuencial' => $row[3],
								'total_proforma' => $row[4],
								'fecha_creacion' => $row[5]
								);
		}

		echo $lista = json_encode($lista);
	}

	//llenar cabezera proforma
	if (isset($_POST['llenar_cabezera_proforma'])) {
		$resultado = $class->consulta("SELECT P.id, P.fecha_emision, P.secuencial, P.id_cliente, C.identificacion, C.razon_social, C.direccion, C.telefono2, C.correo, P.tipo_precio, P.subtotal, P.tarifa, P.tarifa0, P.iva, P.total_descuento, P.total_proforma, P.estado FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id_proforma' => $row[0],
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
							'estado' => $row[16]);
		}
		print_r(json_encode($data));
	}
	//fin

	//llenar detalle proforma
	if (isset($_POST['llenar_detalle_proforma'])) {
		$resultado = $class->consulta("SELECT D.id_producto, U.codigo, U.descripcion, D.cantidad, D.precio, D.descuento, D.total, P.nombre_tarifa_impuesto, U.incluye_iva FROM detalle_proforma D, proforma N, productos U, tarifa_impuesto P  WHERE D.id_producto = U.id AND D.id_proforma = N.id AND U.id_porcentaje = P.id AND D.id = '".$_POST['id']."' ORDER BY D.id ASC");
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

	// consultar detalles
	if(isset($_POST['cargar_tabla_detalle'])) {
		$resultado = $class->consulta("SELECT P.codigo, P.descripcion, D.precio, D.cantidad, D.total FROM detalle_proforma D, proforma F, productos P WHERE D.id_proforma = F.id AND D.id_producto = P.id AND D.id_proforma = '$_POST[id]' ORDER BY D.id ASC");
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

	// consultar correo
	if (isset($_POST['cargar_correo'])) {
		$resultado = $class->consulta("SELECT C.correo FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$correo = $row[0];
		}

		echo $correo;
	}

	// consultar correo
	if (isset($_POST['reenviarCorreo'])) {
		$resultado = $class->consulta("SELECT C.correo, C.nombre_comercial, P.secuencial, P.total_proforma FROM proforma P, clientes C WHERE P.id_cliente = C.id AND P.id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {						
			$email = $row[0];
			$nombre = $row[1];
			$secuencial = $row[2];
			$total = $row[3];
		}

		if($email != "") {
			include 'proforma.php';
			$data = correo($fecha,$total,$secuencial.'.pdf',$nombre,$email,generarPDF($_POST['id']),1);
			$data = 1; // proforma enviada
		} else {
			$data = 2; // error
		}

		echo $data;
	} 
?>