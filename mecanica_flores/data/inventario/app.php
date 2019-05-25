<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	// guardar inventario
	if ($_POST['btn_guardar'] == "Guardar") {
		$fecha = $class->fecha_hora();
		$fecha_corta = $class->fecha();

		// contador inventario
		$id_inventario = 0;
		$resultado = $class->consulta("SELECT max(id) FROM inventario");
		while ($row = $class->fetch_array($resultado)) {
			$id_inventario = $row[0];
		}
		$id_inventario++;
		// fin

		$class->consulta("INSERT INTO inventario VALUES  (	'".$id_inventario."',
															'".$_SESSION['empresa']['id']."',
															'".$_SESSION['user']['id']."',													
															'".$_POST['fecha_actual']."',
															'".$_POST['hora_actual']."',
															'1', 
															'".$fecha."')");

		// auditoria insert
		$class->consulta("INSERT INTO auditoria VALUES ('".$_SESSION['user']['id']."','".gethostname()."','Inventario','INSERT','Creación Inventario','','','$id_inventario','$fecha')");

		// datos detalle inventario
		$campo1 = $_POST['campo1'];
	    $campo2 = $_POST['campo2'];
	    $campo3 = $_POST['campo3'];
	    $campo4 = $_POST['campo4'];
	    $campo5 = $_POST['campo5'];
	    $campo6 = $_POST['campo6'];
	    // Fin

	    // descomponer detalle inventario
		$arreglo1 = explode('|', $campo1);
	    $arreglo2 = explode('|', $campo2);
	    $arreglo3 = explode('|', $campo3);
	    $arreglo4 = explode('|', $campo4);
	    $arreglo5 = explode('|', $campo5);
	    $arreglo6 = explode('|', $campo6);
	    $nelem = count($arreglo1);
	    // fin

	    for ($i = 1; $i < $nelem; $i++) {
	    	// contador detalle inventario
			$id_detalle_inventario = 0;
			$resultado = $class->consulta("SELECT max(id) FROM detalle_inventario");
			while ($row = $class->fetch_array($resultado)) {
				$id_detalle_inventario = $row[0];
			}
			$id_detalle_inventario++;
			// fin

			$class->consulta("INSERT INTO detalle_inventario VALUES(	'".$id_detalle_inventario."',
																		'".$id_inventario."',
																		'".$arreglo1[$i]."',
																		'".$arreglo2[$i]."',
																		'".$arreglo3[$i]."',
																		'".$arreglo4[$i]."',
																		'".$arreglo5[$i]."',
																		'".$arreglo6[$i]."',
																		'1', 
																		'".$fecha."')");

			// modificar productos
           	$resultado = $class->consulta("SELECT P.stock, P.disponibles, U.cantidad, P.id_tipo_producto FROM productos P, unidades_medida U WHERE P.id_unidad_medida = U.id AND P.id = '".$arreglo1[$i]."'");
           	while ($row = $class->fetch_array($resultado)) {
                $stock = $row[0];
                $disponibles = $row[1];
                $canti = $row[2];
                $tipo = $row[3];
            }

            if ($tipo == 4) {
		    	//$mul = $arreglo4[$i] * $canti;
		    	//$res = $disponibles + $mul;
		    	$cal = $stock + $arreglo4[$i];
		        $mul = $arreglo4[$i] * $canti;
		        $res = $disponibles + $mul;

		    	// modificar productos
	            $class->consulta("UPDATE productos SET stock = '$cal', disponibles = '$res' WHERE id = '".$arreglo1[$i]."'");
	            // fin
		    } else {
		    	// modificar productos
            	$class->consulta("UPDATE productos SET stock = '".$arreglo4[$i]."' WHERE id = '".$arreglo1[$i]."'");
            	// fin	
		    }

            // modificar movimientos
            $class->consulta("UPDATE movimientos SET saldo_inicial = '".$arreglo4[$i]."', saldo = '".$arreglo4[$i]."' WHERE id_producto = '".$arreglo1[$i]."'");
            // fin

			// contador kardex
			//$id_kardex = 0;
			//$resultado = $class->consulta("SELECT max(id) FROM kardex");
			//while ($row = $class->fetch_array($resultado)) {
			//	$id_kardex = $row[0];
			//}
			//$id_kardex++;
			// fin

			// guardar kardex
			//$valor1 = number_format($arreglo2[$i], 3, '.', '');
			//$total = number_format($valor1 * $arreglo4[$i], 3, '.', '');

			//$class->consulta("INSERT INTO kardex VALUES (	'".$id_kardex."',
			//												'".$arreglo1[$i]."',
			//												'".$fecha_corta."',
			//												'INVENTARIO',
			//												'".$arreglo4[$i]."',
			//												'".$valor1."',
			//												'".$total."',
			//												'".$arreglo4[$i]."',
			//												'',
			//												'',
			//												'6', 
			//												'".$fecha."')");
			// fin
	    }

		echo $id_inventario;
	}
?>