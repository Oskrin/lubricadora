<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
    
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();
	
	// contador cargos
	$id_cargo = 0;
	$resultado = $class->consulta("SELECT max(id) FROM cargos");
	while ($row = $class->fetch_array($resultado)) {
		$id_cargo = $row[0];
	}
	$id_cargo++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM cargos WHERE nombre_cargo = '$_POST[nombre_cargo]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO cargos VALUES ('$id_cargo','$_POST[nombre_cargo]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Cargos','INSERT','".$_POST['nombre_cargo']."','','','$id_cargo','$fecha')");

			// contador privilegios
			$id_privilegios = 0;
			$resultado = $class->consulta("SELECT max(id) FROM privilegios");
			while ($row = $class->fetch_array($resultado)) {
				$id_privilegios = $row[0];
			}
			$id_privilegios++;
			// fin

			$arreglo = array(	'require',
								'empresa',
								'auditoria',
								'tipo_documento',
								'tipo_producto',
								'formas_pago',
								'secuencia_comprobantes',
								'categorias',
								'marcas',
								'bodegas',
								'medida',
								'clientes',
								'proveedores',
								'productos',
								'inventario',
								'movimientos',
								'factura_compra',
								'factura_venta',
								'cuentas_cobrar',
								'cuenta',
								'cargos',
								'usuarios',
								'privilegios',
								'reporte_varios',
								'reporte_ventas');

			$array = json_encode($arreglo);

			$class->consulta("INSERT INTO privilegios VALUES (	'$id_privilegios',
																'$id_cargo',
																'$array',
																'1', 
																'$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM cargos WHERE nombre_cargo = '$_POST[nombre_cargo]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE cargos SET nombre_cargo = '$_POST[nombre_cargo]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Cargos','UPDATE','".$_POST['nombre_cargo']."','','','".$_POST['id']."','$fecha')");
	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE bodegas SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Cargos','DELETE','".$_POST['nombre_cargo']."','','','".$_POST['id']."','$fecha')");
				
	    		$data = "4";	
	    	}
	    }
	}

	echo $data;
?>