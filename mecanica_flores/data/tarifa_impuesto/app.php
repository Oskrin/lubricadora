<?php 
	if(!isset($_SESSION)){
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador tarifa impuesto
	$id_tarifa_impuesto = 0;
	$resultado = $class->consulta("SELECT max(id) FROM tarifa_impuesto");
	while ($row = $class->fetch_array($resultado)) {
		$id_tarifa_impuesto = $row[0];
	}
	$id_tarifa_impuesto++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM tarifa_impuesto WHERE codigo = '$_POST[codigo]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO tarifa_impuesto VALUES ('$id_tarifa_impuesto','$_POST[tipo_impuesto]','$_POST[codigo]','$_POST[nombre_tarifa_impuesto]','$_POST[descripcion]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tarifa Impuesto','INSERT','".$_POST['descripcion']."','','','$id_tarifa_impuesto','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM tarifa_impuesto WHERE codigo = '$_POST[codigo]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE tarifa_impuesto SET id_tipo_impuesto = '$_POST[tipo_impuesto]',codigo = '$_POST[codigo]',nombre_tarifa_impuesto = '$_POST[nombre_tarifa_impuesto]',descripcion = '$_POST[descripcion]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tarifa Impuesto','UPDATE','".$_POST['descripcion']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE tarifa_impuesto SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tarifa Impuesto','DELETE','".$_POST['descripcion']."','','','".$_POST['id']."','$fecha')");
				
	    		$data = "4";	
	    	}
	    }
	}

	echo $data;
?>