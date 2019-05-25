<?php        
	include_once('../../admin/class.php');
	$class = new constante();
	session_start(); 
	error_reporting(0);

	// guardar detalle cuentas cobrar
	if ($_POST['btn_guardar'] == "Guardar") {
		$fecha = $class->fecha_hora();
		$pago = $_POST['select_forma_pago'];
		$monto = $_POST['monto'];

		$resultado = $class->consulta("SELECT saldo FROM detalle_cuentas_cobrar WHERE id = '".$_POST['id_detalle']."'");
		while ($row = $class->fetch_array($resultado)) {
			$saldo = $row[0];
			$saldo = $saldo - $monto;
			$valor = number_format($saldo, 2, '.', '');

			// modificar detalle
			$class->consulta("UPDATE detalle_cuentas_cobrar SET saldo = '$valor', abono = '$monto', pago = '$pago', estado = '0' WHERE id = '".$_POST['id_detalle']."'");
		}
		
		// consultar cuenta
		$resultado = $class->consulta("SELECT id_cuentas_cobrar FROM detalle_cuentas_cobrar WHERE id  = '".$_POST['id_detalle']."'");
		while ($row = $class->fetch_array($resultado)) {
			$id_cuenta = $row[0];
		}

		$resultado = $class->consulta("SELECT saldo_factura FROM cuentas_cobrar WHERE id = '".$id_cuenta."'");
		while ($row = $class->fetch_array($resultado)) {
			$saldo_factura = $row[0];
			$saldo_factura = $saldo_factura - $monto;
			$saldo_nuevo = number_format($saldo_factura, 2, '.', '');

			if($saldo_nuevo == "0.00") {
				// modificar cuenta
				$class->consulta("UPDATE cuentas_cobrar SET saldo_factura = '$saldo_nuevo' , estado = '0' WHERE id = '".$id_cuenta."'");
			} else {
				// modificar cuenta
				$class->consulta("UPDATE cuentas_cobrar SET saldo_factura = '$saldo_nuevo' WHERE id = '".$id_cuenta."'");	
			}	
		}

		echo "1";
	}
	// fin

	//cargar ultima detalla cuentas
	if (isset($_POST['cargar_detalle'])) {
		$resultado = $class->consulta("SELECT cuota FROM detalle_cuentas_cobrar WHERE id = '".$_POST['id']."'");
		while ($row = $class->fetch_array($resultado)) {
			$data = array('cuota' => $row[0]);
		}

		print_r(json_encode($data));
	}
	//fin
?>