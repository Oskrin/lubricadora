<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador tipo ambiente
	$id_tipo_ambiente = 0;
	$resultado = $class->consulta("SELECT max(id) FROM tipo_ambiente");
	while ($row = $class->fetch_array($resultado)) {
		$id_tipo_ambiente = $row[0];
	}
	$id_tipo_ambiente++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM tipo_ambiente WHERE nombre_tipo_ambiente = '$_POST[nombre_tipo_ambiente]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO tipo_ambiente VALUES ('$id_tipo_ambiente','$_POST[codigo]','$_POST[nombre_tipo_ambiente]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Ambiente','INSERT','".$_POST['nombre_tipo_ambiente']."','','','$id_tipo_ambiente','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM tipo_ambiente WHERE nombre_tipo_ambiente = '$_POST[nombre_tipo_ambiente]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE tipo_ambiente SET codigo = '$_POST[codigo]',nombre_tipo_ambiente = '$_POST[nombre_tipo_ambiente]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Ambiente','UPDATE','".$_POST['nombre_tipo_ambiente']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE tipo_ambiente SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Ambiente','DELETE','".$_POST['nombre_tipo_ambiente']."','','','".$_POST['id']."','$fecha')");
				
	    		$data = "4";	
	    	}
	    }
	}
	   
	echo $data;
?>