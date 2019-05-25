<?php 
	if(!isset($_SESSION)){
        session_start();        
    }
    include_once('../../admin/datos_cedula.php');
	include_once('../../admin/class.php');
	$class = new constante();
	error_reporting(0);
	
	$fecha = $class->fecha_hora();

	// Guardar usuarios
	if (isset($_POST['Guardar']) == "Guardar") {
		// contador usuarios
		$id_usuarios = 0;
		$resultado = $class->consulta("SELECT max(id) FROM usuarios");
		while ($row = $class->fetch_array($resultado)) {
			$id_usuarios = $row[0];
		}
		$id_usuarios++;
		// fin

		// contador acceso
		$id_aceeso = 0;
		$resultado = $class->consulta("SELECT max(id) FROM accesos");
		while ($row = $class->fetch_array($resultado)) {
			$id_aceeso = $row[0];
		}
		$id_aceeso++;
		// fin

		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $id_usuarios.".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "defaul.jpg";	
            }      	
		}

		$contrasenia = md5($_POST['clave2']);


		$class->consulta("INSERT INTO usuarios VALUES (	'$id_usuarios',
														'$_POST[identificacion]',
														'$_POST[nombres_completos]',
														'$_POST[telefono1]',
														'$_POST[telefono2]',
														'$_POST[ciudad]',
														'$_POST[direccion]',
														'$_POST[correo]',
														'$_POST[usuario]',
														'$contrasenia',
														'$_POST[select_cargo]',
														'$dirFoto',
														'$_POST[observaciones]',
														'1', 
														'$fecha')");

		$class->consulta("INSERT INTO accesos VALUES (	'$id_aceeso',
														'$id_usuarios',
														'".gethostname()."',
														'1', 
														'$fecha')");

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Usuarios','INSERT','".$_POST['nombres_completos']."','','','$id_usuarios','$fecha')");
		
		$data = 1;
		echo $data;
	}
	// fin

	// Modificar usuarios
	if (isset($_POST['Modificar']) == "Modificar") {
		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $_POST["id_usuario"].".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "";	
            }     	
		}

		if($dirFoto == "") {
			$class->consulta("UPDATE usuarios SET	identificacion = '$_POST[identificacion]',
													nombres_completos = '$_POST[nombres_completos]',
													telefono1 = '$_POST[telefono1]',
													telefono2 = '$_POST[telefono2]',
													ciudad = '$_POST[ciudad]',
													direccion = '$_POST[direccion]',
													correo = '$_POST[correo]',
													usuario = '$_POST[usuario]',
													id_cargo = '$_POST[select_cargo]',
													observaciones = '$_POST[observaciones]',
													estado = '$_POST[select_estado]',
													fecha_creacion = '$fecha' WHERE id = '".$_POST['id_usuario']."'");	
		} else {
			$class->consulta("UPDATE usuarios SET	identificacion = '$_POST[identificacion]',
													nombres_completos = '$_POST[nombres_completos]',
													telefono1 = '$_POST[telefono1]',
													telefono2 = '$_POST[telefono2]',
													ciudad = '$_POST[ciudad]',
													direccion = '$_POST[direccion]',
													correo = '$_POST[correo]',
													usuario = '$_POST[usuario]',
													id_cargo = '$_POST[select_cargo]',
													imagen = '$dirFoto',
													observaciones = '$_POST[observaciones]',
													estado = '$_POST[select_estado]', 
													fecha_creacion = '$fecha' WHERE id = '".$_POST['id_usuario']."'");	
		}

		// auditoria update
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Usuarios','UPDATE','".$_POST['nombres_completos']."','','','".$_POST['id_usuario']."','$fecha')");	
		$data = 2;
		echo $data;
	}
	// fin

	// cambiar contraseña
	if (isset($_POST['modificar_clave']) == "modificar_clave") {
		$contrasenia = md5($_POST['clave4']);

		$class->consulta("UPDATE usuarios SET	clave = '$contrasenia' WHERE id = '".$_POST['id_usuario']."'");

		$data = 3;
		echo $data;
	}
	// fin

	// comparar identificacion usuarios
	if (isset($_POST['comparar_identificacion'])) {
		$resultado = $class->consulta("SELECT * FROM usuarios U WHERE U.identificacion = '$_POST[identificacion]' AND estado = '1'");
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

	//LLena combo cargos
	if (isset($_POST['llenar_cargo'])) {
		$resultado = $class->consulta("SELECT id, nombre_cargo, principal FROM cargos WHERE estado = '1' order by id asc");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_cargo'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_cargo'].'</option>';
			}
		}
	}
	// fin

	// consultar cedula
	if (isset($_POST['consulta_cedula'])) {
		$ruc = $_POST['txt_ruc'];
		$servicio = new DatosCedula();///creamos nuevo objeto de antecedentes
		$datosCedula = $servicio->consultar_cedula($ruc); // accedemos a la funcion datosSRI

		print_r(json_encode(['datosPersona'=>$datosCedula]));		
	}
	// fin
?>