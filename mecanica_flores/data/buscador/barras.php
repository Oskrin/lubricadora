<?php 
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	$resultado = $class->consulta("SELECT P.id, P.codigo_barras, P.codigo, P.descripcion, P.precio_costo, P.precio_minorista, P.precio_mayorista, P.descuento, P.stock, T.nombre_tarifa_impuesto, P.incluye_iva, P.facturar_existencia  FROM productos P, tarifa_impuesto T WHERE P.id_porcentaje = T.id AND P.codigo_barras = '".$_GET['codigo_barras']."'");
	while ($row = $class->fetch_array($resultado)) {
		if ($_GET['tipo_precio'] == "MINORISTA") {
	        $data = array(
	        	'id' => $row[0],
	            'codigo_barras' => $row[1],
	            'codigo' => $row[2],
	            'producto' => $row[3],
	            'precio_costo' => $row[4],
	            'precio_venta' => $row[5],
	            'descuento' => $row[7],
	            'stock' => $row[8],
	            'iva_producto' => $row[9],
	            'incluye' => $row[10],
	            'inventariable' => $row[11]
	        );
	    } else {
	        if ($_GET['tipo_precio'] == "MAYORISTA") {
	            $data = array(
	            	'id' => $row[0],
	                'codigo_barras' => $row[1],
	                'codigo' => $row[2],
	                'producto' => $row[3],
	                'precio_costo' => $row[4],
	                'precio_venta' => $row[6],
	                'descuento' => $row[7],
	                'stock' => $row[8],
	                'iva_producto' => $row[9],
	                'incluye' => $row[10],
	                'inventariable' => $row[11]
	            );
	        }
		}
	}

	echo $data = json_encode($data);
?>