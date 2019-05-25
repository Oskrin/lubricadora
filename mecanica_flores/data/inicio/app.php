<?php 
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	$fecha = $class->fecha_hora();
	$fecha2 = $class->fecha2();
	$fecha_corta = $class->fecha();

	if (isset($_POST['cargar_ventas_mensual'])) {
		setlocale(LC_TIME, 'spanish');
		$resultado = $class->consulta("SELECT TO_CHAR(fecha_creacion::date,'MM') AS mes, SUM(CAST(total_nota as float)) total_venta, COUNT(*)
		FROM nota_venta
		GROUP BY TO_CHAR(fecha_creacion::date,'MM')");
		while ($row = $class->fetch_array($resultado)) {
			$mes = strftime("%B",mktime(0, 0, 0, $row[0], 1, 2000));
			$data[] = array('name' => $mes, 'y' => floatval($row[1]));
		}

		echo $data = json_encode($data);
	}

	// cargar_productos_vendidos
	if (isset($_POST['cargar_productos_vendidos'])) {
		$resultado = $class->consulta("SELECT D.id_producto, P.descripcion, SUM(CAST(D.cantidad AS INT)) total
										FROM detalle_nota_venta D, productos P WHERE D.id_producto = P.id
										GROUP BY D.id_producto, P.descripcion 
										ORDER BY total DESC
										LIMIT 10");
		while ($row = $class->fetch_array($resultado)) {
			$data[] = array('name' => $row[1], 'y' => intval($row[2]));
		}

		echo $data = json_encode($data);
	}
	// fin

	// proformas diaria
	if (isset($_POST['cargar_proformas'])) {
		$resultado = $class->consulta("SELECT SUM(CAST(total_proforma as float)) total_proforma FROM proforma WHERE fecha_emision = '$fecha2' ORDER BY total_proforma DESC");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('total_proforma' => $row[0]);
		}

		echo $data = json_encode($data);
	}
	// fin

	// factura ventas diaria
	if (isset($_POST['cargar_facturas_venta'])) {
		$resultado = $class->consulta("SELECT SUM(CAST(total_venta as float)) total_venta FROM factura_venta WHERE fecha_emision = '$fecha2' ORDER BY total_venta DESC");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('total_venta' => $row[0]);
		}

		echo $data = json_encode($data);
	}
	// fin

	// notas ventas diaria
	if (isset($_POST['cargar_notas_venta'])) {
		$resultado = $class->consulta("SELECT SUM(CAST(total_nota as float)) total_nota FROM nota_venta WHERE fecha_emision = '$fecha2' ORDER BY total_nota DESC");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('total_nota' => $row[0]);
		}

		echo $data = json_encode($data);
	}
	// fin

	// informacion ingresos usuarios
	if (isset($_POST['cargar_informacion'])) {
		$resultado = $class->consulta("SELECT usuario, fecha_creacion FROM usuarios WHERE id = '".$_SESSION['user']['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('usuario' => $row[0], 'fecha_creacion' => substr($row[1], 0, -6));
		}

		echo $data = json_encode($data);
	}
	// fin

	// informacion cargar chat
	if (isset($_POST['cargar_chat'])) {
		$resultado = $class->consulta("SELECT U.nombres_completos, U.imagen, C.mensaje, C.fecha_creacion FROM chat C, usuarios U WHERE C.id_usuario = U.id ORDER BY C.id ASC");
		while ($row = $class->fetch_array($resultado)) {
			$data[] = array('nombres_completos' => $row[0], 'imagen' => $row[1], 'mensaje' => $row[2], 'fecha' => substr($row[3], 0, -6));
		}

		echo $data = json_encode($data);
	}
	// fin

	// consultar tabla
	if(isset($_POST['cargar_tabla'])){
		$resultado = $class->consulta("SELECT codigo, descripcion, stock FROM productos WHERE stock < stock_minimo ORDER BY id LIMIT 10");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'codigo' => $row[0],
								'descripcion' => $row[1],
								'stock' => $row[2]
							);
		}

		echo $lista = json_encode($lista);
	}
	// fin

	// consultar tabla
	if(isset($_POST['cargar_tabla2'])){
		$resultado = $class->consulta("SELECT P.id, P.codigo, P.descripcion, C.nombre_categoria, M.nombre_marca, P.stock, P.disponibles, P.observaciones FROM productos P, categorias C, marcas M WHERE P.id_categoria = C.id AND P.id_marca = M.id AND P.estado = '1' ORDER BY P.id");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'id' => $row[0],
								'codigo' => $row[1],
								'descripcion' => $row[2],
								'categoria' => $row[3], 
								'marca' => $row[4],
								'stock' => $row[5],
								'disponibles' => $row[6],
								'detalles' => $row[7]
							);
		}

		echo $lista = json_encode($lista);
	}
	// fin

	// cargar usuarios conectados
	if (isset($_POST['guardar_chat'])) {
		// contador chat
		$id_chat = 0;
		$resultado = $class->consulta("SELECT max(id) FROM chat");
		while ($row = $class->fetch_array($resultado)) {
			$id_chat = $row[0];
		}
		$id_chat++;
		// fin
		$fecha = $class->fecha_hora();

		$class->consulta("INSERT INTO chat VALUES  (	'".$id_chat."',
														'".$_SESSION['user']['id']."',
														'".$_POST['mensaje']."',
														'1',
														'".$fecha."')");
		
		$data = 1;
		echo $data;
	}
	// fin
?>