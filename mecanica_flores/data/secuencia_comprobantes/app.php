<?php 
	if(!isset($_SESSION)){
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	error_reporting(0);
	$fecha = $class->fecha_hora();

	// contador secuencia
	$id_secuencia = 0;
	$resultado = $class->consulta("SELECT max(id) FROM secuencia_comprobantes");
	while ($row = $class->fetch_array($resultado)) {
		$id_secuencia = $row[0];
	}
	$id_secuencia++;
	// fin

	if ($_POST['oper'] == "add") {
		$class->consulta("INSERT INTO secuencia_comprobantes VALUES ('$id_secuencia','$_POST[factura]','$_POST[nota_credito]','$_POST[nota_debito]','$_POST[guia_remision]','$_POST[retencion]','1','$fecha')");

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Secuencia Comprobantes','INSERT','Configuración Secuencial','','','$id_secuencia','$fecha')");

		$data = "1";
	} else {
	    if ($_POST['oper'] == "edit") {
			$class->consulta("UPDATE secuencia_comprobantes SET secuencia_factura = '$_POST[factura]', secuencia_nota_credito = '$_POST[nota_credito]', secuencia_nota_debito = '$_POST[nota_debito]', secuencia_guia_remision = '$_POST[guia_remision]', secuencia_retencion = '$_POST[retencion]',fecha_creacion = '$fecha' WHERE id = '".$_POST['id']."'");

			// auditoria update
			$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Secuencia Comprobantes','UPDATE','Configuración Secuencial','','','".$_POST['id']."','$fecha')");

		    $data = "2";
	    } else {
	    	if ($_POST['oper'] == "del") {
	    		$class->consulta("UPDATE secuencia_comprobantes SET estado = '0' WHERE id = '".$_POST['id']."'");

	    		// auditoria delete
				$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Secuencia Comprobantes','DELETE','Configuración Secuencial','','','".$_POST['id']."','$fecha')");

	    		$data = "4";	
	    	}
	    }
	}
	    
	echo $data;
?>