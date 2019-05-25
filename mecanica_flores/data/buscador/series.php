<?php  
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	$texto = $_GET['term'];

	// buscar series
	$resultado = $class->consulta("SELECT id, secuencial FROM factura_venta WHERE secuencial like '%$texto%' AND estado = '1' limit 20");
	while ($row = $class->fetch_array($resultado)) {
		$data[] = array(
            'id' => $row[0],
	        'value' => $row[1]
        );	
	}		

	echo $data = json_encode($data);
?>