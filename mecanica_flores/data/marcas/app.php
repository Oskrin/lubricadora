<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
    
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador marcas
	$id_marca = 0;
	$resultado = $class->consulta("SELECT max(id) FROM marcas");
	while ($row = $class->fetch_array($resultado)) {
		$id_marca = $row[0];
	}
	$id_marca++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM marcas WHERE nombre_marca = '$_POST[nombre_marca]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO marcas VALUES ('$id_marca','$_POST[nombre_marca]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Marcas','INSERT','".$_POST['nombre_marca']."','','','$id_marca','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM marcas WHERE nombre_marca = '$_POST[nombre_marca]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE marcas SET nombre_marca = '$_POST[nombre_marca]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Marcas','UPDATE','".$_POST['nombre_marca']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE marcas SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Marcas','DELETE','".$_POST['nombre_marca']."','','','".$_POST['id']."','$fecha')");
				
	    		$data = "4";	
	    	}
	    }
	}
	    
	echo $data;
?>