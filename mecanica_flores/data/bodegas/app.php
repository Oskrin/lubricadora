<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
    
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador bodegas
	$id_bodega = 0;
	$resultado = $class->consulta("SELECT max(id) FROM bodegas");
	while ($row = $class->fetch_array($resultado)) {
		$id_bodega = $row[0];
	}
	$id_bodega++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM bodegas WHERE nombre_bodega = '$_POST[nombre_bodega]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO bodegas VALUES ('$id_bodega','$_POST[nombre_bodega]','$_POST[ubicacion_bodega]','$_POST[telefono_bodega]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Bodegas','INSERT','".$_POST['nombre_bodega']."','','','$id_bodega','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM bodegas WHERE nombre_bodega = '$_POST[nombre_bodega]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE bodegas SET nombre_bodega = '$_POST[nombre_bodega]',ubicacion_bodega = '$_POST[ubicacion_bodega]',telefono_bodega = '$_POST[telefono_bodega]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Bodegas','UPDATE','".$_POST['nombre_bodega']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE bodegas SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Bodegas','DELETE','".$_POST['nombre_bodega']."','','','".$_POST['id']."','$fecha')");

	    		$data = "4";	
	    	}
	    }
	} 

	echo $data;
?>