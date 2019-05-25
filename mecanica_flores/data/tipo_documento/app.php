<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador tipo documento
	$id_tipo_documento = 0;
	$resultado = $class->consulta("SELECT max(id) FROM tipo_documento");
	while ($row = $class->fetch_array($resultado)) {
		$id_tipo_documento = $row[0];
	}
	$id_tipo_documento++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM tipo_documento WHERE nombre_tipo_documento = '$_POST[nombre_tipo_documento]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO tipo_documento VALUES ('$id_tipo_documento','$_POST[codigo]','$_POST[nombre_tipo_documento]','$_POST[principal]','$_POST[observaciones]','1','$fecha')");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Documento','INSERT','".$_POST['nombre_tipo_documento']."','','','$id_tipo_documento','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM tipo_documento WHERE nombre_tipo_documento = '$_POST[nombre_tipo_documento]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE tipo_documento SET codigo = '$_POST[codigo]',nombre_tipo_documento = '$_POST[nombre_tipo_documento]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Documento','UPDATE','".$_POST['nombre_tipo_documento']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE tipo_documento SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Tipo Documento','DELETE','".$_POST['nombre_tipo_documento']."','','','".$_POST['id']."','$fecha')");
				
	    		$data = "4";	
	    	}
	    }
	} 

	echo $data;
?>