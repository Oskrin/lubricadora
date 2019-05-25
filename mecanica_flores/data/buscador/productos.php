<?php  
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	$texto = $_GET['term'];
	$tipo_precio = $_GET['tipo_precio'];
	$tipo_busqueda = $_GET['tipo_busqueda'];

	// buscar productos
	if ($tipo_busqueda == 'codigo') {
		$resultado = $class->consulta("SELECT P.id, P.codigo_barras, P.codigo, P.descripcion, P.precio_costo, P.precio_minorista, P.precio_mayorista, P.descuento, P.stock, T.nombre_tarifa_impuesto, P.incluye_iva, P.facturar_existencia, TP.nombre_tipo_producto FROM productos P, tarifa_impuesto T, tipo_producto TP WHERE P.id_porcentaje = T.id AND P.id_tipo_producto = TP.id AND P.codigo like '$texto%' AND P.estado = '1' limit 20");
		while ($row = $class->fetch_array($resultado)) {
			if ($tipo_precio == "MINORISTA") {
		        $data[] = array(
		        	'codigo_barras' => $row[1],
		        	'id' => $row[0],
		            'value' => $row[2],
		            'producto' => $row[3],
		            'precio_costo' => $row[4],
		            'precio_venta' => $row[5],
		            'descuento' => $row[7],
		            'stock' => $row[8],
		            'iva_producto' => $row[9],
		            'incluye' => $row[10],
		            'inventariable' => $row[11],
		            'tipo' => $row[12]
		        );
		    } else {
		        if ($tipo_precio == "MAYORISTA") {
		            $data[] = array(
		            	'id' => $row[0],
		            	'codigo_barras' => $row[1],
		                'value' => $row[2],
		                'producto' => $row[3],
		                'precio_costo' => $row[4],
		                'precio_venta' => $row[6],
		                'descuento' => $row[7],
		                'stock' => $row[8],
		                'iva_producto' => $row[9],
		                'incluye' => $row[10],
		                'inventariable' => $row[11],
		                'tipo' => $row[12]
		            );
		        }
	    	}	
		}	
	} else {
		if ($tipo_busqueda == 'descripcion') {
			$resultado = $class->consulta("SELECT P.id, P.codigo_barras, P.codigo, P.descripcion, P.precio_costo, P.precio_minorista, P.precio_mayorista, P.descuento, P.stock, T.nombre_tarifa_impuesto, P.incluye_iva, P.facturar_existencia, TP.nombre_tipo_producto  FROM productos P, tarifa_impuesto T, tipo_producto TP WHERE P.id_porcentaje = T.id AND P.id_tipo_producto = TP.id AND P.descripcion like '$texto%' AND P.estado = '1' limit 20");
			while ($row = $class->fetch_array($resultado)) {
				if ($tipo_precio == "MINORISTA") {
			        $data[] = array(
			        	'id' => $row[0],
			        	'codigo_barras' => $row[1],
			        	'codigo' => $row[2],
			            'value' => $row[3],
			            'precio_costo' => $row[4],
			            'precio_venta' => $row[5],
			            'descuento' => $row[7],
			            'stock' => $row[8],
			            'iva_producto' => $row[9],
			            'incluye' => $row[10],
			            'inventariable' => $row[11],
			            'tipo' => $row[12]
			        );
			    } else {
			        if ($tipo_precio == "MAYORISTA") {
			            $data[] = array(
			            	'id' => $row[0],
			            	'codigo_barras' => $row[1],
			                'codigo' => $row[2],
			                'value' => $row[3],
			                'precio_costo' => $row[4],
			                'precio_venta' => $row[6],
			                'descuento' => $row[7],
			                'stock' => $row[8],
			                'iva_producto' => $row[9],
			                'incluye' => $row[10],
			                'inventariable' => $row[11],
			                'tipo' => $row[12]
			            );
			        }
			    }
			}	
		}
	}
	// fin

	echo $data = json_encode($data);
?>