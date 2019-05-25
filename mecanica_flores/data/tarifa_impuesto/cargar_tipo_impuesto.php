<?php 
	if(!isset($_SESSION)){
        session_start();        
    }

	include_once('../../admin/class.php');
	$class = new constante();

	$resultado = $class->consulta("SELECT id, nombre_tipo_impuesto FROM tipo_impuesto WHERE estado = '1' ORDER BY id ASC");
	$response = '<select>';
	while ($row = $class->fetch_array($resultado)) {
		$response .= '<option value="'.$row['id'].'">'.$row['nombre_tipo_impuesto'].'</option>';
	}

	$response .= "</select>";
	print $response;	
?>