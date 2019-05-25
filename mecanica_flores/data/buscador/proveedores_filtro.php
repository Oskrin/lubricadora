<?php  
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	$texto = $_GET['term'];
	$tipo_busqueda = $_GET['tipo_busqueda'];

	// buscar proveedores
	if ($tipo_busqueda == 'identificacion') {
		
		$resultado = $class->consulta("SELECT id, identificacion, razon_social, telefono2, direccion, correo FROM proveedores WHERE estado = '1' AND id_tipo_documento = '1' AND identificacion like '%$texto%' limit 20");
		while ($row = $class->fetch_array($resultado)) {
			$data[] = array(
	            'id' => $row[0],
	            'value' => $row[1],
	            'razon_social' => $row[2],
	            'telefono' => $row[3],
	            'direccion' => $row[4],
	            'correo' => $row[5]
	        );	
		}
	} else {
		if ($tipo_busqueda == 'razon_social') {
			$resultado = $class->consulta("SELECT id, identificacion, razon_social, telefono2, direccion, correo FROM proveedores WHERE estado = '1' AND id_tipo_documento = '1' AND razon_social like '%$texto%' limit 20");
			while ($row = $class->fetch_array($resultado)) {
				$data[] = array(
		            'id' => $row[0],
		            'ruc' => $row[1],
		            'value' => $row[2],
		            'telefono' => $row[3],
		            'direccion' => $row[4],
		            'correo' => $row[5]
		        );
			}	
		}
	}
	// fin

	echo $data = json_encode($data);
?>