<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	ini_set('max_execution_time', 240); //240 segundos = 4 minutos
	error_reporting(0);

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