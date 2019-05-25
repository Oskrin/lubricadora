<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	// consultar auditoria
	if(isset($_POST['cargar_tabla'])) {
		$resultado = $class->consulta("SELECT U.nombres_completos, A.host, A.entidad, A.proceso, A.actual_value, A.fecha FROM auditoria A, usuarios U WHERE A.usuario_id = U.id AND SUBSTRING(CAST(A.fecha AS TEXT),0, 11) BETWEEN '$_POST[fecha_inicio]' AND '$_POST[fecha_fin]' ORDER BY A.fecha ASC");
		while ($row = $class->fetch_array($resultado)) {
			$lista[] = array(	'responsable' => $row[0],
									'host' => $row[1],
									'entidad' => $row[2],
									'proceso' => $row[3],
									'valor' => $row[4],
									'fecha' => $row[5]
								); 
		}
		
		echo $lista = json_encode($lista);
	}
	// fin
?>