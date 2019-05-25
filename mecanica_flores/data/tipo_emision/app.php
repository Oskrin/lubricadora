<?php 
	if(!isset($_SESSION)){
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador tipo emision
	$id_tipo_emision = 0;
	$resultado = $class->consulta("SELECT max(id) FROM tipo_emision");
	while ($row = $class->fetch_array($resultado)) {
		$id_tipo_emision = $row[0];
	}
	$id_tipo_emision++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM tipo_emision WHERE nombre_tipo_emision = '$_POST[nombre_tipo_emision]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO tipo_emision VALUES ('$id_tipo_emision','$_POST[codigo]','$_POST[nombre_tipo_emision]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Emision','INSERT','".$_POST['nombre_tipo_emision']."','','','$id_tipo_emision','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM tipo_emision WHERE nombre_tipo_emision = '$_POST[nombre_tipo_emision]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE tipo_emision SET codigo = '$_POST[codigo]',nombre_tipo_emision = '$_POST[nombre_tipo_emision]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Emision','UPDATE','".$_POST['nombre_tipo_emision']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE tipo_emision SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Emision','DELETE','".$_POST['nombre_tipo_emision']."','','','".$_POST['id']."','$fecha')");

	    		$data = "4";	
	    	}
	    }
	}

	echo $data;
?>