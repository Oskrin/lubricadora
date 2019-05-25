<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }

	include_once('../../admin/datos_sri.php');
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();
	error_reporting(0);

	if (isset($_POST["Guardar"]) == "Guardar") {
		// contador empresa
		$id_empresa = 0;
		$resultado = $class->consulta("SELECT max(id) FROM empresa");
		while ($row = $class->fetch_array($resultado)) {
			$id_empresa = $row[0];
		}
		$id_empresa++;

		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $id_empresa.".".$extension;
            $destino = "/token/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirToken = $nombre;
            } else {
            	$dirToken = "";	
            }          
		}

		if(isset($_FILES["file_2"])) {
			$temporal = $_FILES['file_2']['tmp_name'];
            $extension = explode(".",  $_FILES['file_2']['name']); 
            $extension = end($extension);                    			            
            $nombre = $id_empresa.".".$extension;
            $destino = "/logo/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirLogo = $nombre;
            } else {
            	$dirFoto = "defaul.jpg";	
            }      	
		}
		// fin
		$obligacion = "NO";
		if(isset($_POST["obligacion"]))
			$obligacion = "SI";

		$class->consulta("INSERT INTO empresa VALUES (	'$id_empresa',
														'$_POST[ruc]',
														'$_POST[razon_social]',
														'$_POST[nombre_comercial]',
														'$_POST[actividad_economica]',
														'$_POST[representante_legal]',
														'$_POST[identificacion_representante]',
														'$_POST[telefono1]',
														'$_POST[telefono2]',
														'$_POST[ciudad]',
														'$_POST[direccion_matriz]',
														'$_POST[direccion_establecimiento]',
														'$_POST[establecimiento]',
														'$_POST[punto_emision]',
														'$obligacion',
														'$_POST[contribuyente]',
														'$_POST[autorizacion]',
														'',
														'',
														'$dirToken',
														'$_POST[clave_token]',
														'$_POST[correo]',
														'$_POST[sitio_web]',
														'$_POST[slogan]',
														'$dirLogo',
														'',
														'1', 
														'$fecha')");

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Empresa','INSERT','".$_POST['nombre_comercial']."','','','$id_empresa','$fecha')");

		$data = 1;
		echo $data;
	}

	if (isset($_POST['Modificar']) == "Modificar") {

		$obligacion = "NO";
		if(isset($_POST["obligacion"]))
			$obligacion = "SI";

		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $_POST["id"].".".$extension;
            $destino = "/token/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)){
            	$dirToken = $nombre;
            } else {
            	$dirToken = "";	
            }           
		}

		if(isset($_FILES["file_2"])) {
			$temporal = $_FILES['file_2']['tmp_name'];
            $extension = explode(".",  $_FILES['file_2']['name']); 
            $extension = end($extension);                    			            
            $nombre = $_POST["id"].".".$extension;
            $destino = "/logo/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirLogo = $nombre;
            } else {
            	$dirLogo = "";	
            }     	
		}

		if($dirLogo == "" && $dirToken == "") {
			$class->consulta("UPDATE empresa SET	ruc = '$_POST[ruc]',
													razon_social = '$_POST[razon_social]',
													nombre_comercial = '$_POST[nombre_comercial]',
													actividad_economica = '$_POST[actividad_economica]',
													representante_legal = '$_POST[representante_legal]',
													identificacion_representante = '$_POST[identificacion_representante]',
													telefono1 = '$_POST[telefono1]',
													telefono2 = '$_POST[telefono2]',
													ciudad = '$_POST[ciudad]',
													direccion_matriz = '$_POST[direccion_matriz]',
													direccion_establecimiento = '$_POST[direccion_establecimiento]',
													establecimiento = '$_POST[establecimiento]',
													punto_emision = '$_POST[punto_emision]',
													obligacion = '$obligacion',
													contribuyente = '$_POST[contribuyente]',
													autorizacion = '$_POST[autorizacion]',
													clave = '$_POST[clave_token]',
													correo = '$_POST[correo]',
													sitio_web = '$_POST[sitio_web]'
													WHERE id = '".$_POST['id']."'");
		} else {
			if($dirToken == "") {
				$class->consulta("UPDATE empresa SET	ruc = '$_POST[ruc]',
														razon_social = '$_POST[razon_social]',
														nombre_comercial = '$_POST[nombre_comercial]',
														actividad_economica = '$_POST[actividad_economica]',
														representante_legal = '$_POST[representante_legal]',
														identificacion_representante = '$_POST[identificacion_representante]',
														telefono1 = '$_POST[telefono1]',
														telefono2 = '$_POST[telefono2]',
														ciudad = '$_POST[ciudad]',
														direccion_matriz = '$_POST[direccion_matriz]',
														direccion_establecimiento = '$_POST[direccion_establecimiento]',
														establecimiento = '$_POST[establecimiento]',
														punto_emision = '$_POST[punto_emision]',
														obligacion = '$obligacion',
														contribuyente = '$_POST[contribuyente]',
														autorizacion = '$_POST[autorizacion]',
														clave = '$_POST[clave_token]',
														correo = '$_POST[correo]',
														sitio_web = '$_POST[sitio_web]',
														imagen = '$dirLogo'
														WHERE id = '".$_POST['id']."'");	
			} else {
				if($dirLogo == "") {
					$class->consulta("UPDATE empresa SET	ruc = '$_POST[ruc]',
															razon_social = '$_POST[razon_social]',
															nombre_comercial = '$_POST[nombre_comercial]',
															actividad_economica = '$_POST[actividad_economica]',
															representante_legal = '$_POST[representante_legal]',
															identificacion_representante = '$_POST[identificacion_representante]',
															telefono1 = '$_POST[telefono1]',
															telefono2 = '$_POST[telefono2]',
															ciudad = '$_POST[ciudad]',
															direccion_matriz = '$_POST[direccion_matriz]',
															direccion_establecimiento = '$_POST[direccion_establecimiento]',
															establecimiento = '$_POST[establecimiento]',
															punto_emision = '$_POST[punto_emision]',
															obligacion = '$obligacion',
															contribuyente = '$_POST[contribuyente]',
															autorizacion = '$_POST[autorizacion]',
															token = '$dirToken',
															clave = '$_POST[clave_token]',
															correo = '$_POST[correo]',
															sitio_web = '$_POST[sitio_web]'
															WHERE id = '".$_POST['id']."'");
				} else {
					$class->consulta("UPDATE empresa SET	ruc = '$_POST[ruc]',
															razon_social = '$_POST[razon_social]',
															nombre_comercial = '$_POST[nombre_comercial]',
															actividad_economica = '$_POST[actividad_economica]',
															representante_legal = '$_POST[representante_legal]',
															identificacion_representante = '$_POST[identificacion_representante]',
															telefono1 = '$_POST[telefono1]',
															telefono2 = '$_POST[telefono2]',
															ciudad = '$_POST[ciudad]',
															direccion_matriz = '$_POST[direccion_matriz]',
															direccion_establecimiento = '$_POST[direccion_establecimiento]',
															establecimiento = '$_POST[establecimiento]',
															punto_emision = '$_POST[punto_emision]',
															obligacion = '$obligacion',
															contribuyente = '$_POST[contribuyente]',
															autorizacion = '$_POST[autorizacion]',
															token = '$dirToken',
															clave = '$_POST[clave_token]',
															correo = '$_POST[correo]',
															sitio_web = '$_POST[sitio_web]',
															imagen = '$dirLogo'
															WHERE id = '".$_POST['id']."'");
				}
			}	
		}

		// auditoria update
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Empresa','UPDATE','".$_POST['nombre_comercial']."','','','".$_POST['id']."','$fecha')");

		$data = 2;
		echo $data;
	}

	// consultar ruc
	if (isset($_POST['consulta_ruc'])) {
		$ruc = $_POST['txt_ruc'];
		$servicio = new ServicioSRI();///creamos nuevo objeto de servicios SRI
		$datosEmpresa = $servicio->consultar_ruc($ruc); ////accedemos a la funcion datosSRI
		$establecimientos = $servicio->establecimientoSRI($ruc);

		print_r(json_encode(['datosEmpresa'=>$datosEmpresa,'establecimientos'=>$establecimientos]));		
	}
	// fin

	// llenar datos empresa
	if (isset($_POST['consulta_empresa'])) {
		$resultado = $class->consulta("SELECT * FROM empresa WHERE estado = '1'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array(  'id' => $row[0],
							'ruc' => $row[1],
							'razon_social' => $row[2],
							'nombre_comercial' => $row[3],
							'actividad_economica' => $row[4],
							'representante_legal' => $row[5],
							'identificacion_representante' => $row[6],
							'telefono1' => $row[7],
							'telefono2' => $row[8],
							'ciudad' => $row[9],
							'direccion_matriz' => $row[10],
							'direccion_establecimiento' => $row[11],
							'establecimiento' => $row[12],
							'punto_emision' => $row[13],
							'obligacion' => $row[14],
							'contribuyente' => $row[15],
							'autorizacion' => $row[16],
							'token' => $row[19],
							'clave' => $row[20],
							'correo' => $row[21],
							'sitio_web' => $row[22],
							'slogan' => $row[23],
							'logo' => $row[24]);
		}
		print_r(json_encode($data));
	}
	//fin
?>