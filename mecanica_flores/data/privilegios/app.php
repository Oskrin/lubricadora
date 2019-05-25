<?php 
	if(!isset($_SESSION)){
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	error_reporting(0);
	$fecha = $class->fecha_hora();

	// modificar privilegios
	if (isset($_POST['updateprivilegios'])) {
		$vector = json_encode($_POST['data']);
		$data = 0;

		$resp = $class->consulta("UPDATE privilegios SET data = '$vector' WHERE id_cargo = '$_POST[user]'");
		if ($resp) {
			$data = 1;
		} 

		echo $data;
	}
	// fin

	//LLena combo cargos
	if (isset($_POST['llenar_cargos'])) {
		$id = $class->idz();
		$resultado = $class->consulta("SELECT * FROM cargos WHERE estado = '1' order by id asc");
		print'<option value="">&nbsp;</option>';
		while ($row = $class->fetch_array($resultado)) {
			print '<option value="'.$row['id'].'">'.$row['nombre_cargo'].'</option>';
		}
	}
	// fin

	// estado privilegios
	function buscarstatus($array, $valor) {
		$retorno = array_search($valor, $array);
		if ($retorno) {
			return true;
		} else {
			return false;
		}	
	}
	// fin

	// Inicios methodo recursos data
	if (isset($_POST['retornar'])) {
		$sum;
		$result = $class->consulta("SELECT * FROM privilegios WHERE id_cargo = '".$_POST['id']."'");
		while ($row = $class->fetch_array($result)) {
			$sum = json_decode($row['data']);
		}

		$acumulador = 
		array(
			'Menú' => 
				array(
				'text' => 'Menú',
				'type' => 'folder',
				'additionalParameters' => 
					array(
						'id' => 1,
						'children' => 
						array(
							'Empresa'=> 
							array(
								'text' => 'Empresa', 
								'type' => 'item',
								'id' => 'empresa',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'empresa')
								)
							),
							'Auditoria'=> 
							array(
								'text' => 'Auditoria', 
								'type' => 'item',
								'id' => 'auditoria',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'auditoria')
								)
							),
							//'TipoAmbiente'=> 
							//array(
							//	'text' => 'Tipo Ambiente', 
							//	'type' => 'item',
							//	'id' => 'tipo_ambiente',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'tipo_ambiente')
							//	)
							//),
							//'TipoEmision'=> 
							//array(
							//	'text' => 'Tipo Emisión', 
							//	'type' => 'item',
							//	'id' => 'tipo_emision',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'tipo_emision')
							//	)
							//),
							//'TipoComprobante'=> 
							//array(
							//	'text' => 'Tipo Comprobante', 
							//	'type' => 'item',
							//	'id' => 'tipo_comprobante',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'tipo_comprobante')
							//	)
							//),
							'TipoDocumento'=> 
							array(
								'text' => 'Tipo Documento', 
								'type' => 'item',
								'id' => 'tipo_documento',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'tipo_documento')
								)
							),
							'TipoProducto'=> 
							array(
								'text' => 'Tipo Producto', 
								'type' => 'item',
								'id' => 'tipo_producto',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'tipo_producto')
								)
							),
							//'TipoImpuesto'=> 
							//array(
							//	'text' => 'Tipo Impuesto', 
							//	'type' => 'item',
							//	'id' => 'tipo_impuesto',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'tipo_impuesto')
							//	)
							//),
							//'TarifaImpuesto'=> 
							//array(
							//	'text' => 'Tarifa Impuesto', 
							//	'type' => 'item',
							//	'id' => 'tarifa_impuesto',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'tarifa_impuesto')
							//	)
							//),
							'FormasPago'=> 
							array(
								'text' => 'Formas Pago', 
								'type' => 'item',
								'id' => 'formas_pago',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'formas_pago')
								)
							),
							'SecuenciaComprobantes'=> 
							array(
								'text' => 'Secuencia Comprobantes', 
								'type' => 'item',
								'id' => 'secuencia_comprobantes',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'secuencia_comprobantes')
								)
							),
							'Categorias'=> 
							array(
								'text' => 'Categorias', 
								'type' => 'item',
								'id' => 'categorias',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'categorias')
								)
							),
							'Marcas'=> 
							array(
								'text' => 'Marcas', 
								'type' => 'item',
								'id' => 'marcas',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'marcas')
								)
							),
							'Bodegas'=> 
							array(
								'text' => 'Bodegas', 
								'type' => 'item',
								'id' => 'bodegas',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'bodegas')
								)
							),
							'UnidadMedida'=> 
							array(
								'text' => 'Unidad Medida', 
								'type' => 'item',
								'id' => 'medida',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'medida')
								)
							),
							'Clientes'=> 
							array(
								'text' => 'Clientes', 
								'type' => 'item',
								'id' => 'clientes',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'clientes')
								)
							),
							'Proveedores'=> 
							array(
								'text' => 'Proveedores', 
								'type' => 'item',
								'id' => 'proveedores',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'proveedores')
								)
							),
							//'Transportistas'=> 
							//array(
							//	'text' => 'Transportistas', 
							//	'type' => 'item',
							//	'id' => 'transportistas',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'transportistas')
							//	)
							//),
							'Productos'=> 
							array(
								'text' => 'Productos', 
								'type' => 'item',
								'id' => 'productos',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'productos')
								)
							),
							'Inventario'=> 
							array(
								'text' => 'Inventario', 
								'type' => 'item',
								'id' => 'inventario',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'inventario')
								)
							),
							'Movimientos'=> 
							array(
								'text' => 'Movimientos', 
								'type' => 'item',
								'id' => 'movimientos',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'movimientos')
								)
							),
							//'Profomas'=> 
							//array(
							//	'text' => 'Proformas', 
							//	'type' => 'item',
							//	'id' => 'proformas',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'proformas')
							//	)
							//),
							'FacturaCompra'=> 
							array(
								'text' => 'Factura Compra', 
								'type' => 'item',
								'id' => 'factura_compra',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'factura_compra')
								)
							),
							'FacturaVenta'=> 
							array(
								'text' => 'Factura Venta', 
								'type' => 'item',
								'id' => 'factura_venta',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'factura_venta')
								)
							),
							'CuentasCobrar'=> 
							array(
								'text' => 'Cuentas Cobrar', 
								'type' => 'item',
								'id' => 'cuentas_cobrar',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'cuentas_cobrar')
								)
							),
							'NotaVenta'=> 
							array(
								'text' => 'Nota Venta', 
								'type' => 'item',
								'id' => 'nota_venta',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'nota_venta')
								)
							),
							//'Retencion'=> 
							//array(
							//	'text' => 'Retención', 
							//	'type' => 'item',
							//	'id' => 'retencion',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'retencion')
							//	)
							//),
							//'NotaCredito'=> 
							//array(
							//	'text' => 'Nota Crédito', 
							//	'type' => 'item',
							//	'id' => 'nota_credito',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'nota_credito')
							//	)
							//),
							//'GuiaREmision'=> 
							//array(
							//	'text' => 'Guía Remisión', 
							//	'type' => 'item',
							//	'id' => 'guia_remision',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'guia_remision')
							//	)
							//),
							//'Ingreso'=> 
							//array(
							//	'text' => 'Ingreso', 
							//	'type' => 'item',
							//	'id' => 'ingresos',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'ingresos')
							//	)
							//),
							//'Egreso'=> 
							//array(
							//	'text' => 'Egreso', 
							//	'type' => 'item',
							//	'id' => 'egresos',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'egresos')
							//	)
							//),
							//'Kardex'=> 
							//array(
							//	'text' => 'Kardex', 
							//	'type' => 'item',
							//	'id' => 'kardex',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'kardex')
							//	)
							//),
							//'Formulario104a'=> 
							//array(
							//	'text' => 'Formulario 104a', 
							//	'type' => 'item',
							//	'id' => 'formulario104a',
							//	'additionalParameters' => 
							//	array(
							//		'item-selected' => buscarstatus($sum,'formulario104a')
							//	)
							//),
							'Cuenta'=> 
							array(
								'text' => 'Cuenta', 
								'type' => 'item',
								'id' => 'cuenta',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'cuenta')
								)
							),
							'Cargos'=> 
							array(
								'text' => 'Cargos', 
								'type' => 'item',
								'id' => 'cargos',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'cargos')
								)
							),
							'Usuario'=> 
							array(
								'text' => 'Usuario', 
								'type' => 'item',
								'id' => 'usuarios',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'usuarios')
								)
							),
							'Privilegios'=> 
							array(
								'text' => 'Privilegios', 
								'type' => 'item',
								'id' => 'privilegios',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'privilegios')
								)
							),
							'ReporteVarios'=> 
							array(
								'text' => 'Reportes Varios', 
								'type' => 'item',
								'id' => 'reporte_varios',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'reporte_varios')
								)
							),
							'ReporteVentas'=> 
							array(
								'text' => 'Reporte Ventas', 
								'type' => 'item',
								'id' => 'reporte_ventas',
								'additionalParameters' => 
								array(
									'item-selected' => buscarstatus($sum,'reporte_ventas')
								)
							)
						)
					)
				)
			);

		$acu2;
		for ($i = 0; $i < count($acu); $i++) { 
			$acu2[$i] = array(
							'text' => $acu[$i], 
							'type' => 'folder',
							'additionalParameters' => 
												array(
													'id' => '1',
													'children'=> 
													array('opcion2' => 
														array(
															'text' => 'opcion2', 
															'type' => 'item',
															'id'=>'moji',
															'additionalParameters' => 
															array(
																'item-selected' => true
															)
														)
													)
												)
											);
		}

		print(json_encode($acumulador));
	}
	// fin
?>

