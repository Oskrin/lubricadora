<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }

	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	// contador categorias
	$id_categoria = 0;
	$resultado = $class->consulta("SELECT max(id) FROM categorias");
	while ($row = $class->fetch_array($resultado)) {
		$id_categoria = $row[0];
	}
	$id_categoria++;
	// fin

	if ($_POST['oper'] == "add") {
		$resultado = $class->consulta("SELECT count(*) FROM categorias WHERE nombre_categoria = '$_POST[nombre_categoria]'");
		while ($row = $class->fetch_array($resultado)) {
			$data = $row[0];
		}

		if ($data != 0) {
			$data = "3";
		} else {
			$class->consulta("INSERT INTO categorias VALUES ('$id_categoria','$_POST[nombre_categoria]','$_POST[principal]','$_POST[observaciones]','1','$fecha');");

			// auditoria insert
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Categorias','INSERT','".$_POST['nombre_categoria']."','','','$id_categoria','$fecha')");

			$data = "1";
		}
	} else {
	    if ($_POST['oper'] == "edit") {
	    	$resultado = $class->consulta("SELECT count(*) FROM categorias WHERE nombre_categoria = '$_POST[nombre_categoria]' AND id NOT IN ('".$_POST['id']."')");
			while ($row = $class->fetch_array($resultado)) {
				$data = $row[0];
			}

			if ($data != 0) {
			 	$data = "3";
			} else {
				$class->consulta("UPDATE categorias SET nombre_categoria = '$_POST[nombre_categoria]',principal = '$_POST[principal]',observaciones = '$_POST[observaciones]',estado = '$_POST[estado]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

				// auditoria update
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Categorias','UPDATE','".$_POST['nombre_categoria']."','','','".$_POST['id']."','$fecha')");

	    		$data = "2";
			}
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE categorias SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Categorias','DELETE','".$_POST['nombre_categoria']."','','','".$_POST['id']."','$fecha')");

	    		$data = "4";	
	    	}
	    }
	}
	    
	echo $data;
?>