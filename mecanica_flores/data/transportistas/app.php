<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
    
    include_once('../../admin/datos_sri.php');
	include_once('../../admin/datos_cedula.php');
	include_once('../../admin/class.php');
	include_once('../../phpexcel/PHPExcel-1.7.7/Classes/PHPExcel/IOFactory.php');
	$class = new constante();
	$fecha = $class->fecha_hora();
	error_reporting(0);

	// Guardar transportistas
	if (isset($_POST['Guardar']) == "Guardar") {
		// contador transportistas
		$id_transportista = 0;
		$resultado = $class->consulta("SELECT max(id) FROM transportistas");
		while ($row = $class->fetch_array($resultado)) {
			$id_transportista = $row[0];
		}
		$id_transportista++;
		// fin

		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $id_transportista.".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "defaul.jpg";	
            }      	
		}

		$class->consulta("INSERT INTO transportistas VALUES  (	'$id_transportista',
																'$_POST[select_documento]',
																'$_POST[identificacion]',
																'$_POST[razon_social]',
																'$_POST[nombre_comercial]',
																'$_POST[telefono1]',
																'$_POST[telefono2]',
																'$_POST[ciudad]',
																'$_POST[direccion]',
																'$_POST[correo]',
																'$dirFoto',
																'$_POST[observaciones]',
																'1', 
																'$fecha')");	
		
		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Transportistas','INSERT','".$_POST['razon_social']."','','','$id_transportista','$fecha')");

		$data = 1;
		echo $data;
	}
	// fin

	// Modificar transportistas
	if (isset($_POST['Modificar']) == "Modificar") {
		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $_POST["id_transportista"].".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "";
            }    	
		}

		if($dirFoto == "") {
			$class->consulta("UPDATE transportistas SET	id_tipo_documento = '$_POST[select_documento]',
														identificacion = '$_POST[identificacion]',
														razon_social = '$_POST[razon_social]',
														nombre_comercial = '$_POST[nombre_comercial]',
														telefono1 = '$_POST[telefono1]',
														telefono2 = '$_POST[telefono2]',
														ciudad = '$_POST[ciudad]',
														direccion = '$_POST[direccion]',
														correo = '$_POST[correo]',
														observaciones = '$_POST[observaciones]',
														estado = '$_POST[select_estado]',
														fecha_creacion = '$fecha' WHERE id = '".$_POST['id_transportista']."'");	
		} else {
			$class->consulta("UPDATE transportistas SET	id_tipo_documento = '$_POST[select_documento]',
														identificacion = '$_POST[identificacion]',
														razon_social = '$_POST[razon_social]',
														nombre_comercial = '$_POST[nombre_comercial]',
														telefono1 = '$_POST[telefono1]',
														telefono2 = '$_POST[telefono2]',
														ciudad = '$_POST[ciudad]',
														direccion = '$_POST[direccion]',
														correo = '$_POST[correo]',
														imagen = '$dirFoto',
														observaciones = '$_POST[observaciones]',
														estado = '$_POST[select_estado]',
														fecha_creacion = '$fecha' WHERE id = '".$_POST['id_transportista']."'");	
		}

		// auditoria update
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Transportistas','UPDATE','".$_POST['razon_social']."','','','".$_POST['id_transportista']."','$fecha')");

		$data = 2;
		echo $data;
	}
	// fin

	//comprarar identificaciones transportistas
	if (isset($_POST['comparar_identificacion'])) {
		$cont = 0;

		$resultado = $class->consulta("SELECT * FROM transportistas C WHERE C.id_tipo_documento = '".$_POST['tipo_documento']."' AND C.identificacion = '".$_POST['identificacion']."'");
		while ($row = $class->fetch_array($resultado)) {
			$cont++;
		}

		if ($cont == 0) {
		    $data = 0;
		} else {
		    $data = 1;
		}
		echo $data;
	}
	// fin

	//LLena combo tipo documentos
	if (isset($_POST['llenar_tipo_documento'])) {
		$id = $class->idz();
		$resultado = $class->consulta("SELECT id, nombre_tipo_documento, principal FROM tipo_documento WHERE estado = '1' order by id asc");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_tipo_documento'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_tipo_documento'].'</option>';
			}
		}
	}
	// fin

	// consultar ruc
	if (isset($_POST['consulta_ruc'])) {
		$ruc = $_POST['txt_ruc'];
		$servicio = new ServicioSRI();///creamos nuevo objeto de servicios SRI
		$datosEmpresa = $servicio->consultar_ruc($ruc); ////accedemos a la funcion datosSRI
		$establecimientos = $servicio->establecimientoSRI($ruc);

		print_r(json_encode(['datosEmpresa'=>$datosEmpresa,'establecimientos'=>$establecimientos]));		
	}
	// fin

	// consultar cedula
	if (isset($_POST['consulta_cedula'])) {
		$ruc = $_POST['txt_ruc'];
		$servicio = new DatosCedula();///creamos nuevo objeto de antecedentes
		$datosCedula = $servicio->consultar_cedula($ruc); ////accedemos a la funcion datosSRI

		print_r(json_encode(['datosPersona'=>$datosCedula]));		
	}
	// fin

	// cargar excel
	if (isset($_POST['Cargar_excel']) == "Cargar_excel") {
		$extension = explode(".", $_FILES["archivo_excel"]["name"]);

		$extension = end($extension);
		$type = $_FILES["archivo_excel"]["type"];
		$tmp_name = $_FILES["archivo_excel"]["tmp_name"];
		$size = $_FILES["archivo_excel"]["size"];
		$nombre = basename($_FILES["archivo_excel"]["name"], "." . $extension);

		$nombreTemp = $nombre . '.' . $extension;
		 if(move_uploaded_file($_FILES["archivo_excel"]["tmp_name"], "temp/" . $nombreTemp)) {
		 	$data = 1;
		 } else {
		 	$data = 0;
		 }

		 if($data == 1) {	
			//cargamos el archivo_excel que deseamos leer
			$objPHPExcel = PHPExcel_IOFactory::load('temp/'.$nombreTemp);
			$objHoja = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$cont = 0;
			foreach ($objHoja as $iIndice=>$objCelda) {
				if($cont >= 2) {
					$lista[] = $objCelda['A'];
					$lista[] = $objCelda['B'];
					$lista[] = $objCelda['C'];
					$lista[] = $objCelda['D'];
					$lista[] = $objCelda['E'];
					$lista[] = $objCelda['F'];
					$lista[] = $objCelda['G'];
					$lista[] = $objCelda['H'];
					$lista[] = $objCelda['I'];

					// contador transportistas
					$id_transportista = 0;
					$resultado = $class->consulta("SELECT max(id) FROM transportistas");
					while ($row = $class->fetch_array($resultado)) {
						$id_transportista = $row[0];
					}
					$id_transportista++;
					// fin

					$dirFoto = "./fotos/defaul.jpg";

					if ($objCelda['A'] == "RUC") {
						$id_tipo_documento = 1; 		
					} else {
						if ($objCelda['A'] == "CEDULA") {
							$id_tipo_documento = 2;
						}	
					}

					$class->consulta("INSERT INTO transportistas VALUES  (	'".$id_transportista."',
																		'".$id_tipo_documento."',
																		'".$objCelda['B']."',
																		'".strtoupper($objCelda['C'])."',
																		'".strtoupper($objCelda['D'])."',
																		'".$objCelda['E']."',
																		'".$objCelda['F']."',
																		'".$objCelda['G']."',
																		'".$objCelda['H']."',
																		'".$objCelda['I']."',
																		'".$dirFoto."',
																		'',
																		'1', 
																		'".$fecha."')");
				}
				$cont++;
			}	
		 }
		echo $lista = json_encode($lista);
	}
	// fin
?>