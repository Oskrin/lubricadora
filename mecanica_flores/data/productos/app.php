<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }

	include_once('../../admin/class.php');
	include_once('../../phpexcel/PHPExcel-1.7.7/Classes/PHPExcel/IOFactory.php');
	$class = new constante();
	$fecha = $class->fecha_hora();
	$fecha_corta = $class->fecha();
	error_reporting(0);

	// Guardar productos
	if (isset($_POST['Guardar']) == "Guardar") {
		// contador productos
		$id_producto = 0;
		$resultado = $class->consulta("SELECT max(id) FROM productos");
		while ($row = $class->fetch_array($resultado)) {
			$id_producto = $row[0];
		}
		$id_producto++;
		// fin

		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $id_producto.".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "defaul.jpg";	
            }      	
		}

		$incluye_iva = "NO";
		$expiracion = "NO";
		$facturar_existencia = "NO";
		$series = "NO";
		$valor1 = number_format($_POST['precio_costo'], 2, '.', '');
	    $valor2 = number_format($_POST['utilidad_minorista'], 2, '.', '');
	    $valor3 = number_format($_POST['utilidad_mayorista'], 2, '.', '');
	    $valor4 = number_format($_POST['precio_minorista'], 2, '.', '');
	    if ($_POST['precio_mayorista'] == "") {
	    	$valor5 = number_format('0.00', 2, '.', '');
	    } else {
	    	$valor5 = number_format($_POST['precio_mayorista'], 2, '.', '');
	    }

	    if ($_POST['select_tipo'] == 4) {
	    	$disponibles = $_POST['cantidad'] * $_POST['stock'];
	    } else {
	    	$disponibles = 0;	
	    }

		if(isset($_POST["incluye_iva"]))
		$incluye_iva = "SI";
		if(isset($_POST["expiracion"]))
			$expiracion = "SI";
		if(isset($_POST["facturar_existencia"]))
			$facturar_existencia = "SI";
		if(isset($_POST["series"]))
			$series = "SI";

		$class->consulta("INSERT INTO productos VALUES (	'$id_producto',
															'$_POST[codigo_barras]',
															'$_POST[codigo]',
															'$_POST[descripcion]',
															'$valor1',
															'$valor2',
															'$valor3',
															'$valor4',
															'$valor5',
															'$_POST[select_tipo]',
															'$_POST[select_categoria]',
															'$_POST[select_marca]',
															'$_POST[select_medida]',
															'$_POST[select_bodega]',
															'$_POST[select_iva]',
															'$incluye_iva',
															'$_POST[stock]',
															'$_POST[stock_minimo]',
															'$_POST[stock_maximo]',
															'$_POST[descuento]',
															'$expiracion',
															'$facturar_existencia',
															'$_POST[ubicacion]',
															'$series',
															'$disponibles',
															'$dirFoto',
															'$_POST[observaciones]',
															'1', 
															'$fecha')");

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Productos','INSERT','".$_POST['descripcion']."','','','$id_producto','$fecha')");

		// contador kardex
		$id_kardex = 0;
		$resultado = $class->consulta("SELECT max(id) FROM kardex");
		while ($row = $class->fetch_array($resultado)) {
			$id_kardex = $row[0];
		}
		$id_kardex++;
		// fin

		$total = number_format($valor1 * $_POST['stock'], 3, '.', '');

		$class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
														'".$id_producto."',
														'".$fecha_corta."',
														'C.P',
														'".$_POST['stock']."',
														'".$valor1."',
														'".$total."',
														'".$_POST['stock']."',
														'',
														'',
														'5', 
														'".$fecha."')");

		// contador movimientos
		$id_movimientos = 0;
		$resultado = $class->consulta("SELECT max(id) FROM movimientos");
		while ($row = $class->fetch_array($resultado)) {
			$id_movimientos = $row[0];
		}
		$id_movimientos++;
		// fin

		$class->consulta("INSERT INTO movimientos VALUES (	'".$id_movimientos."',
															'".$id_producto."',
															'".$fecha_corta."',
															'".$_POST['stock']."',
															'0',
															'0',
															'".$_POST['stock']."',
															'1',
															'".$fecha."')");																
		
		$data = 1;
		echo $data;
	}
	// fin

	// Modificar productos
	if (isset($_POST['Modificar']) == "Modificar") {
		if(isset($_FILES["file_1"])) {
			$temporal = $_FILES['file_1']['tmp_name'];
            $extension = explode(".",  $_FILES['file_1']['name']); 
            $extension = end($extension);                    			            
            $nombre = $_POST["id_producto"].".".$extension;
            $destino = "/fotos/".$nombre;			            
            $root = getcwd();	
            if(move_uploaded_file($temporal, $root.$destino)) {
            	$dirFoto = $nombre;
            } else {
            	$dirFoto = "";	
            }     	
		}

		$incluye_iva = "NO";
		$expiracion = "NO";
		$facturar_existencia = "NO";
		$series = "NO";
		$valor1 = number_format($_POST['precio_costo'], 2, '.', '');
	    $valor2 = number_format($_POST['utilidad_minorista'], 2, '.', '');
	    $valor3 = number_format($_POST['utilidad_mayorista'], 2, '.', '');
	    $valor4 = number_format($_POST['precio_minorista'], 2, '.', '');
	    if ($_POST['precio_mayorista'] == "") {
	    	$valor5 = number_format('0.00', 2, '.', '');
	    } else {
	    	$valor5 = number_format($_POST['precio_mayorista'], 2, '.', '');
	    }

	    if ($_POST['select_tipo'] == 4) {
	    	$disponibles = $_POST['cantidad'] * $_POST['stock'];
	    } else {
	    	$disponibles = 0;	
	    }

		if(isset($_POST["incluye_iva"]))
		$incluye_iva = "SI";
		if(isset($_POST["expiracion"]))
			$expiracion = "SI";
		if(isset($_POST["facturar_existencia"]))
			$facturar_existencia = "SI";
		if(isset($_POST["series"]))
			$series = "SI";

		if($dirFoto == "") {
			$resp = $class->consulta("UPDATE productos SET	codigo_barras = '$_POST[codigo_barras]',
															codigo = '$_POST[codigo]',
															descripcion = '$_POST[descripcion]',
															precio_costo = '$valor1',
															utilidad_minorista = '$valor2',
															utilidad_mayorista = '$valor3',
															precio_minorista = '$valor4',
															precio_mayorista = '$valor5',
															id_tipo_producto = '$_POST[select_tipo]',
															id_categoria = '$_POST[select_categoria]',
															id_marca = '$_POST[select_marca]',
															id_unidad_medida = '$_POST[select_medida]',
															id_bodega = '$_POST[select_bodega]',
															id_porcentaje = '$_POST[select_iva]',
															incluye_iva = '$incluye_iva',
															stock = '$_POST[stock]',
															stock_minimo = '$_POST[stock_minimo]',
															stock_maximo = '$_POST[stock_maximo]',
															descuento = '$_POST[descuento]',
															expiracion = '$expiracion',
															facturar_existencia = '$facturar_existencia',
															ubicacion = '$_POST[ubicacion]',
															series = '$series',
															disponibles = '$disponibles',
															observaciones = '$_POST[observaciones]',
															estado = '$_POST[select_estado]',
															fecha_creacion = '$fecha' WHERE id = '".$_POST['id_producto']."'");
		} else {
			$resp = $class->consulta("UPDATE productos SET	codigo_barras = '$_POST[codigo_barras]',
															codigo = '$_POST[codigo]',
															descripcion = '$_POST[descripcion]',
															precio_costo = '$valor1',
															utilidad_minorista = '$valor2',
															utilidad_mayorista = '$valor3',
															precio_minorista = '$valor4',
															precio_mayorista = '$valor5',
															id_tipo_producto = '$_POST[select_tipo]',
															id_categoria = '$_POST[select_categoria]',
															id_marca = '$_POST[select_marca]',
															id_unidad_medida = '$_POST[select_medida]',
															id_bodega = '$_POST[select_bodega]',
															id_porcentaje = '$_POST[select_iva]',
															incluye_iva = '$incluye_iva',
															stock = '$_POST[stock]',
															stock_minimo = '$_POST[stock_minimo]',
															stock_maximo = '$_POST[stock_maximo]',
															descuento = '$_POST[descuento]',
															expiracion = '$expiracion',
															facturar_existencia = '$facturar_existencia',
															ubicacion = '$_POST[ubicacion]',
															series = '$series',
															disponibles = '$disponibles',
															imagen = '$dirFoto',
															observaciones = '$_POST[observaciones]',
															estado = '$_POST[select_estado]',
															fecha_creacion = '$fecha' WHERE id = '".$_POST['id_producto']."'");
		}

		// auditoria update
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Productos','UPDATE','".$_POST['descripcion']."','','','".$_POST['id_producto']."','$fecha')");	

		$data = 2;
		echo $data;
	}
	// fin

	// Comparar codigo barras productos 
	if (isset($_POST['comparar_codigo_barras'])) {
		$cont = 0; 

		$resultado = $class->consulta("SELECT * FROM productos P WHERE P.codigo_barras = '$_POST[codigo_barras]'");
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

	// Comparar codigos productos
	if (isset($_POST['comparar_codigos'])) {
		$cont = 0; 

		$resultado = $class->consulta("SELECT * FROM productos P WHERE P.codigo = '$_POST[codigo]'");
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

	// LLenar tipo productos
	if (isset($_POST['llenar_tipo_producto'])) {
		$resultado = $class->consulta("SELECT id, nombre_tipo_producto, principal FROM tipo_producto WHERE estado = '1' order by id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_tipo_producto'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_tipo_producto'].'</option>';
			}
		}
	}
	// fin

	// LLenar categoria
	if (isset($_POST['llenar_categoria'])) {
		$resultado = $class->consulta("SELECT id, nombre_categoria, principal FROM categorias WHERE estado = '1' order by id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_categoria'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_categoria'].'</option>';
			}
		}
	}
	// fin

	// LLenar marca
	if (isset($_POST['llenar_marca'])) {
		$resultado = $class->consulta("SELECT id, nombre_marca, principal FROM marcas WHERE estado = '1' order by id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_marca'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_marca'].'</option>';
			}
		}
	}
	// fin

	// LLenar unidades medida
	if (isset($_POST['llenar_unidades_medida'])) {
		$resultado = $class->consulta("SELECT id, nombre_unidad FROM unidades_medida WHERE estado = '1' order by id asc");
		while ($row = $class->fetch_array($resultado)) {
			print '<option value="'.$row['id'].'">'.$row['nombre_unidad'].'</option>';
		}
	}
	// fin

	// LLenar unidad
	if (isset($_POST['llenar_unidad'])) {
		$resultado = $class->consulta("SELECT cantidad FROM unidades_medida WHERE id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$cantidad = $row[0];
			//print '<option value="'.$row['id'].'">'.$row['nombre_unidad'].'</option>';
		}

		echo $cantidad;
	}
	// fin

	// LLenar bodega
	if (isset($_POST['llenar_bodega'])) {
		$resultado = $class->consulta("SELECT id, nombre_bodega, principal FROM bodegas WHERE estado = '1' order by id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			if($row['principal'] == 'Si') {
				print '<option value="'.$row['id'].'" selected>'.$row['nombre_bodega'].'</option>';
			} else {
				print '<option value="'.$row['id'].'">'.$row['nombre_bodega'].'</option>';
			}
		}
	}
	// fin

	// LLenar porcentaje
	if (isset($_POST['llenar_iva'])) {
		$resultado = $class->consulta("SELECT id, codigo, nombre_tarifa_impuesto, descripcion FROM tarifa_impuesto WHERE id_tipo_impuesto = '1' AND estado = '1' order by id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			print '<option value="'.$row['id'].'">'.$row['nombre_tarifa_impuesto'].' - '.$row['descripcion'].'</option>';	
		}
	}
	// fin

	// LLenar combo proveedores
	if (isset($_POST['llenar_proveedores'])) {
		$resultado = $class->consulta("SELECT id, nombres_completos FROM proveedores WHERE estado = '1' ORDER BY id asc");
		print'<option value=""></option>';
		while ($row = $class->fetch_array($resultado)) {
			print '<option value="'.$row['id'].'">'.$row['nombres_completos'].'</option>';
		}
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

					// contador productos
					$id_producto = 0;
					$resultado = $class->consulta("SELECT max(id) FROM productos");
					while ($row = $class->fetch_array($resultado)) {
						$id_producto = $row[0];
					}
					$id_producto++;
					// fin

					$dirFoto = "./fotos/defaul.jpg";

					$incluye_iva = "NO";
					$facturar_existencia = "SI";
					$expiracion = "NO";
					$series = "NO";

					$valor1 = number_format($objCelda['D'], 2, '.', '');
				    $valor2 = number_format($objCelda['E'], 2, '.', '');
				    $valor3 = number_format($objCelda['F'], 2, '.', '');
	    
					$class->consulta("INSERT INTO productos VALUES (	'$id_producto',
																		'".$objCelda['A']."',
																		'".$objCelda['B']."',
																		'".strtoupper($objCelda['C'])."',
																		'$valor1',
																		'0',
																		'0',
																		'$valor2',
																		'$valor3',
																		'1',
																		'1',
																		'1',
																		'1',
																		'2',
																		'$incluye_iva',
																		'".$objCelda['G']."',
																		'".$objCelda['H']."',
																		'0',
																		'".$objCelda['I']."',
																		'$expiracion',
																		'$facturar_existencia',
																		'',
																		'$series',
																		'$dirFoto',
																		'',
																		'1', 
																		'$fecha')");

					// contador kardex
					$id_kardex = 0;
					$resultado = $class->consulta("SELECT max(id) FROM kardex");
					while ($row = $class->fetch_array($resultado)) {
						$id_kardex = $row[0];
					}
					$id_kardex++;
					// fin

					$total = number_format($valor1 * $objCelda['G'], 3, '.', '');

					$class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
																	'".$id_producto."',
																	'".$fecha_corta."',
																	'C.P',
																	'".$objCelda['G']."',
																	'".$valor1."',
																	'".$total."',
																	'".$objCelda['G']."',
																	'',
																	'',
																	'5', 
																	'".$fecha."')");

					// contador movimientos
					$id_movimientos = 0;
					$resultado = $class->consulta("SELECT max(id) FROM movimientos");
					while ($row = $class->fetch_array($resultado)) {
						$id_movimientos = $row[0];
					}
					$id_movimientos++;
					// fin

					$class->consulta("INSERT INTO movimientos VALUES (	'".$id_movimientos."',
																		'".$id_producto."',
																		'".$fecha_corta."',
																		'".$objCelda['G']."',
																		'0',
																		'0',
																		'".$objCelda['G']."',
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