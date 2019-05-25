<?php 
	session_start();
	if(!$_SESSION) {
		header('Location: login/');
	}
?> 
<!DOCTYPE html>
<html ng-app="scotchApp" lang="es">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>.:FACTURACIÓN.:</title>
		<meta name="description" content="3 styles with inline editable feature" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="dist/css/bootstrap.min.css" />
		<link rel="stylesheet" href="dist/css/font-awesome.min.css" />
		<link rel="stylesheet" href="dist/css/style.css" />
		<link rel="stylesheet" href="dist/css/style_content.css" />
		<link rel="shortcut icon" href="dist/images/logoCoatlFac.ico">

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="dist/css/animate.min.css" />
		<link rel="stylesheet" href="dist/css/jquery.gritter.min.css" />
		<link rel="stylesheet" href="dist/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="dist/css/chosen.min.css" />
		<link rel="stylesheet" href="dist/css/select2.min.css" />
		<link rel="stylesheet" href="dist/css/ui.jqgrid.min.css" />
		<link rel="stylesheet" href="dist/css/bootstrap-timepicker.min.css" />
		<link rel="stylesheet" href="dist/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="dist/css/bootstrap-datetimepicker.min.css" />
		<link rel="stylesheet" href="dist/css/bootstrap-datetimepicker-standalone.css" />
		<link rel="stylesheet" href="dist/css/bootstrap-editable.min.css" />
		<link rel="stylesheet" href="dist/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="dist/css/sweetalert.css" />

		<link rel="stylesheet" href="dist/css/jquery-ui.custom.min.css" />
		<link href="dist/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
		
		<!-- text fonts -->
		<link rel="stylesheet" href="dist/css/fontdc.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="dist/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<script src="dist/js/ace-extra.min.js"></script>
		<script src="dist/js/mousetrap.min.js"></script>

		<!-- Angular js -->
		<script src="dist/angular-1.5.0/angular.js"></script>
		<script src="dist/angular-1.5.0/angular-route.js"></script>
		<script src="dist/angular-1.5.0/angular-animate.js"></script>
		<script src="dist/angular-1.5.0/ui-bootstrap-tpls-1.1.2.min.js"></script>
		<script src="dist/angular-1.5.0/angular-resource.js"></script>
		<script src="dist/js/ngStorage.min.js"></script>

		<!-- controlador procesos angular -->
  		<script src="data/app.js"></script>
  		<script src="data/inicio/app.js"></script>
  		<script src="data/empresa/app.js"></script>
  		<script src="data/auditoria/app.js"></script>
  		<script src="data/tipo_ambiente/app.js"></script>
  		<script src="data/tipo_emision/app.js"></script>
  		<script src="data/tipo_comprobante/app.js"></script>
  		<script src="data/tipo_documento/app.js"></script>
  		<script src="data/tipo_producto/app.js"></script>
  		<script src="data/tipo_impuesto/app.js"></script>
  		<script src="data/tarifa_impuesto/app.js"></script>
  		<script src="data/formas_pago/app.js"></script>
  		<script src="data/secuencia_comprobantes/app.js"></script>
  		<script src="data/categorias/app.js"></script>
  		<script src="data/marcas/app.js"></script>
  		<script src="data/bodegas/app.js"></script>
  		<script src="data/medida/app.js"></script>
  		<script src="data/clientes/app.js"></script>
  		<script src="data/proveedores/app.js"></script>
  		<script src="data/transportistas/app.js"></script>
  		<script src="data/productos/app.js"></script>
  		<script src="data/inventario/app.js"></script>
  		<script src="data/movimientos/app.js"></script>
  		<script src="data/proformas/app.js"></script>
  		<script src="data/factura_compra/app.js"></script>
  		<script src="data/retencion/app.js"></script>
  		<script src="data/factura_venta/app.js"></script>
  		<script src="data/cuentas_cobrar/app.js"></script>
  		<script src="data/nota_venta/app.js"></script>
  		<script src="data/registro_ventas/app.js"></script>
  		<script src="data/nota_credito/app.js"></script>
  		<script src="data/guia_remision/app.js"></script>
  		<script src="data/ingresos/app.js"></script>
  		<script src="data/egresos/app.js"></script>
  		<script src="data/kardex/app.js"></script>
  		<script src="data/formulario104a/app.js"></script>
  		<script src="data/cargos/app.js"></script>
  		<script src="data/usuarios/app.js"></script>
  		<script src="data/privilegios/app.js"></script>
  		<script src="data/cuenta/app.js"></script>
  		<script src="data/reporte_varios/app.js"></script>
  		<script src="data/reporte_ventas/app.js"></script>

  		<style type="text/css">
			hc-chart {
			  width: 100%;
			  display: block;
			}

			hc-pie-chart {
			  width: 100%;
			  display: block;
			}

			hc-column-chart {
			  width: 100%;
			  display: block;
			}
		</style>
	</head>

	<body ng-controller="mainController" class="no-skin">
		<div id="navbar" class="navbar navbar-default navbar-fixed-top">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>
			<div class="navbar-container" id="navbar-container">
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
					<a href="#" class="navbar-brand">
						<small>
							FACT SERVICE
						</small>
					</a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="" class="dropdown-toggle">
								<img class="nav-user-photo" src=<?php  print_r('data/usuarios/fotos/'. $_SESSION['user']['imagen']); ?> alt="" />
								<span class="user-info">
									<small>Bienvenido,</small>
									<?php print_r($_SESSION['user']['name']); ?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="#/cuenta">
										<i class="ace-icon fa fa-user"></i>
										Configuración
									</a>
								</li>

								<li>
									<a href="#/privilegios">
										<i class="ace-icon fa fa-unlock"></i>
										Privilegios
									</a>
								</li>

								<li class="divider"></li>

								<li>
									<a href="login/exit.php">
										<i class="ace-icon fa fa-power-off"></i>
										Salir
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div id="sidebar" class="sidebar responsive">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>

				<ul class="nav nav-list">
					<li ng-class="{active: $route.current.activetab == 'inicio'}">
						<a href="#/">
							<i class="menu-icon fa fa-home"></i>
							<span class="menu-text"> Inicio </span>
						</a>

						<b class="arrow"></b>
					</li>

					<li ng-class="{active: $route.current.activetab == 'empresa'}">
						<a href="#/empresa">
							<i class="menu-icon fa fa-building"></i>
							<span class="menu-text">
								Empresa
							</span>
						</a>
						<b class="arrow"></b>
					</li>

					<li ng-class = "{'active open':
												$route.current.activetab == 'auditoria' ||
												$route.current.activetab == 'tipo_ambiente' ||
												$route.current.activetab == 'tipo_emision' ||	
												$route.current.activetab == 'tipo_comprobante' ||
												$route.current.activetab == 'tipo_documento' ||
												$route.current.activetab == 'tipo_producto' ||
												$route.current.activetab == 'tipo_impuesto' ||
												$route.current.activetab == 'tarifa_impuesto' ||
												$route.current.activetab == 'formas_pago' ||
												$route.current.activetab == 'secuencia_comprobantes' 
												
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-cog"></i>
							Parametros
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
						<li ng-class="{active: $route.current.activetab == 'auditoria'}">
								<a href="#/auditoria">
									<i class="menu-icon fa fa-caret-right"></i>
									Auditoria
								</a>

								<b class="arrow"></b>
							</li>

							<!--<li ng-class="{active: $route.current.activetab == 'tipo_ambiente'}">
								<a href="#/tipo_ambiente">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Ambiente
								</a>

								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'tipo_emision'}">
								<a href="#/tipo_emision">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Emisión
								</a>

								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'tipo_comprobante'}">
								<a href="#/tipo_comprobante">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Comprobante
								</a>

								<b class="arrow"></b>
							</li-->
							<li ng-class="{active: $route.current.activetab == 'tipo_documento'}">
								<a href="#/tipo_documento">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Documento
								</a>

								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'tipo_producto'}">
								<a href="#/tipo_producto">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Producto
								</a>

								<b class="arrow"></b>
							</li>

							<!--<li ng-class="{active: $route.current.activetab == 'tipo_impuesto'}">
								<a href="#/tipo_impuesto">
									<i class="menu-icon fa fa-caret-right"></i>
									Tipo Impuesto
								</a>

								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'tarifa_impuesto'}">
								<a href="#/tarifa_impuesto">
									<i class="menu-icon fa fa-caret-right"></i>
									Tarifa Impuesto
								</a>

								<b class="arrow"></b>
							</li>-->

							<li ng-class="{active: $route.current.activetab == 'formas_pago'}">
								<a href="#/formas_pago">
									<i class="menu-icon fa fa-caret-right"></i>
									Formas Pago
								</a>

								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'secuencia_comprobantes'}">
								<a href="#/secuencia_comprobantes">
									<i class="menu-icon fa fa-caret-right"></i>
									Secuencia Comprobantes
								</a>

								<b class="arrow"></b>
							</li>
						</ul>
					</li>

					<li ng-class = "{'active open': 
												$route.current.activetab == 'categorias' ||
												$route.current.activetab == 'marcas' ||
												$route.current.activetab == 'bodegas' ||
												$route.current.activetab == 'medida'
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-cogs"></i>
							Generales
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'categorias'}">
								<a href="#/categorias">
									<i class="menu-icon fa fa-caret-right"></i>
									Categorias
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'marcas'}">
								<a href="#/marcas">
									<i class="menu-icon fa fa-caret-right"></i>
									Marcas
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'bodegas'}">
								<a href="#/bodegas">
									<i class="menu-icon fa fa-caret-right"></i>
									Bodegas
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'medida'}">
								<a href="#/medida">
									<i class="menu-icon fa fa-caret-right"></i>
									Unidades Medida
								</a>
								<b class="arrow"></b>
							</li>
						</ul>
					</li>

					<li ng-class = "{'active open': 
												$route.current.activetab == 'clientes' ||
												$route.current.activetab == 'proveedores' ||
												$route.current.activetab == 'transportistas' ||
												$route.current.activetab == 'productos' 
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-desktop"></i>
							<span class="menu-text">
								Ingresos
							</span>
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'clientes'}">
								<a href="#/clientes">
									<i class="menu-icon fa fa-caret-right"></i>
									Clientes
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'proveedores'}">
								<a href="#/proveedores">
									<i class="menu-icon fa fa-caret-right"></i>
									Proveedores
								</a>
								<b class="arrow"></b>
							</li>

							<!--<li ng-class="{active: $route.current.activetab == 'transportistas'}">
								<a href="#/transportistas">
									<i class="menu-icon fa fa-caret-right"></i>
									Transportistas
								</a>
								<b class="arrow"></b>
							</li>-->

							<li ng-class="{active: $route.current.activetab == 'productos'}">
								<a href="#/productos">
									<i class="menu-icon fa fa-caret-right"></i>
									Productos
								</a>
								<b class="arrow"></b>
							</li>
						</ul>
					</li>

					<li ng-class = "{'active open': 
												$route.current.activetab == 'inventario' ||
												$route.current.activetab == 'movimientos' ||
												$route.current.activetab == 'kardex'
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-files-o"></i>
							Inventario
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'inventario'}">
								<a href="#/inventario">
									<i class="menu-icon fa fa-caret-right"></i>
									Registro Inventario
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'movimientos'}">
								<a href="#/movimientos">
									<i class="menu-icon fa fa-caret-right"></i>
									Movimientos
								</a>
								<b class="arrow"></b>
							</li>

							<!--<li ng-class="{active: $route.current.activetab == 'kardex'}">
								<a href="#/kardex">
									<i class="menu-icon fa fa-caret-right"></i>
									Kardex
								</a>
								<b class="arrow"></b>
							</li>-->
						</ul>
					</li>

					<!--<li ng-class="{active: $route.current.activetab == 'proformas'}">
						<a href="#/proformas">
							<i class="menu-icon fa fa-list"></i>
							<span class="menu-text">
								Proformas
							</span>
						</a>
						<b class="arrow"></b>
					</li>

					<li ng-class="{active: $route.current.activetab == 'factura_compra'}">
						<a href="#/factura_compra">
							<i class="menu-icon fa fa-shopping-cart"></i>
							<span class="menu-text">
								Factura Compra
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<li ng-class="{active: $route.current.activetab == 'factura_venta'}">
						<a href="#/factura_venta">
							<i class="menu-icon fa fa-shopping-cart"></i>
							<span class="menu-text">
								Factura Venta
							</span>
						</a>
						<b class="arrow"></b>
					</li>

					<li ng-class="{active: $route.current.activetab == 'nota_venta'}">
						<a href="#/nota_venta">
							<i class="menu-icon fa fa-shopping-cart"></i>
							<span class="menu-text">
								Nota Venta
							</span>
						</a>
						<b class="arrow"></b>
					</li>

					<li ng-class="{active: $route.current.activetab == 'cuentas_cobrar'}">
						<a href="#/cuentas_cobrar">
							<i class="menu-icon fa fa-pencil-square"></i>
							<span class="menu-text">
								Cuentas Cobrar
							</span>
						</a>
						<b class="arrow"></b>
					</li>

					<!--<li ng-class="{active: $route.current.activetab == 'retencion'}">
						<a href="#/retencion">
							<i class="menu-icon fa fa-tag"></i>
							<span class="menu-text">
								Retención
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<!--<li ng-class="{active: $route.current.activetab == 'nota_credito'}">
						<a href="#/nota_credito">
							<i class="menu-icon fa fa-book"></i>
							<span class="menu-text">
								Nota Crédito
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<!--<li ng-class="{active: $route.current.activetab == 'guia_remision'}">
						<a href="#/guia_remision">
							<i class="menu-icon glyphicon glyphicon-list-alt"></i>
							<span class="menu-text">
								Guía Remisión
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<!--<li ng-class = "{'active open': 
												$route.current.activetab == 'ingresos' ||
												$route.current.activetab == 'egresos'
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-exchange"></i>
							<span class="menu-text">
								Transacciones
							</span>
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'ingresos'}">
								<a href="#/ingresos">
									<i class="menu-icon fa fa-caret-right"></i>
									Ingresos
								</a>
							</li>

							<li ng-class="{active: $route.current.activetab == 'egresos'}">
								<a href="#/egresos">
									<i class="menu-icon fa fa-caret-right"></i>
									Egresos
								</a>

								<b class="arrow"></b>
							</li>
						</ul>
					</li>-->

					<!--<li ng-class="{active: $route.current.activetab == 'kardex'}">
						<a href="#/kardex">
							<i class="menu-icon fa fa-folder-o"></i>
							<span class="menu-text">
								Kardex
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<!--<li ng-class="{active: $route.current.activetab == 'formulario104a'}">
						<a href="#/formulario104a">
							<i class="menu-icon fa fa-folder-open"></i>
							<span class="menu-text">
								Formulario 104A
							</span>
						</a>
						<b class="arrow"></b>
					</li>-->

					<li ng-class = "{'active open': 
												$route.current.activetab == 'cargos' ||
												$route.current.activetab == 'usuarios'
												
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-users"></i>
							Usuarios
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'cargos'}">
								<a href="#/cargos">
									<i class="menu-icon fa fa-caret-right"></i>
									Cargos
								</a>
								<b class="arrow"></b>
							</li>

							<li ng-class="{active: $route.current.activetab == 'usuarios'}">
								<a href="#/usuarios">
									<i class="menu-icon fa fa-caret-right"></i>
									Registro Usuario
								</a>
								<b class="arrow"></b>
							</li>
						</ul>
					</li>

					<li ng-class = "{'active open':
												$route.current.activetab == 'reporte_varios' ||
												$route.current.activetab == 'reporte_ventas'
									}">
						<a href="" class="dropdown-toggle">
							<i class="menu-icon fa fa-archive"></i>
							<span class="menu-text">
								Reportes
							</span>
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li ng-class="{active: $route.current.activetab == 'reporte_varios'}">
								<a href="#/reporte_varios">
									<i class="menu-icon fa fa-caret-right"></i>
									Reportes Varios
								</a>
							</li>

							<li ng-class="{active: $route.current.activetab == 'reporte_ventas'}">
								<a href="#/reporte_ventas">
									<i class="menu-icon fa fa-caret-right"></i>
									Reportes Ventas
								</a>
							</li>
						</ul>
					</li>
				</ul>

				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<div class="main-content ng-view" id="main-container"></div>

			<div class="footer">
				<div class="footer-inner">
					<div class="footer-content">
						<span class="bigger-120">
							Applicación &copy; 2017-2018
						</span>
					</div>
				</div>
			</div>

			<a href="" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div>

		<script type="text/javascript">
			window.jQuery || document.write("<script src='dist/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='dist/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		
		<script src="dist/js/jquery-ui.min.js"></script>
		<script src="dist/js/jquery.ui.touch-punch.min.js"></script>
		<script src="dist/js/jquery.easypiechart.min.js"></script>
		<script src="dist/js/jquery.sparkline.min.js"></script>
    	<script src="dist/js/highcharts.js"></script>
		<script src="dist/js/exporting.js"></script>
		<script src="dist/js/export-data.js"></script>

		<script src="dist/js/fileinput.js" type="text/javascript"></script>
		<script src="dist/js/bootstrap.min.js"></script>
		<script src="dist/js/jquery.form.js"></script>
		<script src="dist/js/chosen.jquery.min.js"></script>

		<script src="dist/js/jquery.validate.min.js"></script>
		<script src="dist/js/jquery.gritter.min.js"></script>
		<script src="dist/js/bootbox.min.js"></script>
		<script src="dist/js/fuelux/fuelux.wizard.min.js"></script>
		<script src="dist/js/additional-methods.min.js"></script>
		
		<script src="dist/js/jquery.hotkeys.min.js"></script>
		<script src="dist/js/bootstrap-wysiwyg.min.js"></script>
		<script src="dist/js/select2.min.js"></script>
		<script src="dist/js/fuelux/fuelux.spinner.min.js"></script>
		<script src="dist/js/fuelux/fuelux.tree.min.js"></script>
		<script src="dist/js/x-editable/bootstrap-editable.min.js"></script>
		<script src="dist/js/x-editable/ace-editable.min.js"></script>
		<script src="dist/js/jquery.maskedinput.min.js"></script>
		<script src="dist/js/bootbox.min.js"></script>
		<script src="dist/js/date-time/bootstrap-datepicker.min.js"></script>
		<script src="dist/js/date-time/bootstrap-timepicker.min.js"></script>
		<script src="dist/js/date-time/moment.min.js"></script>
		<script src="dist/js/date-time/daterangepicker.min.js"></script>
		<script src="dist/js/date-time/bootstrap-datetimepicker.min.js"></script>
		
		<!-- script de las tablas -->
		<script src="dist/js/jqGrid/jquery.jqGrid.min.js"></script>
		<script src="dist/js/jqGrid/i18n/grid.locale-en.js"></script>
		<script src="dist/js/dataTables/jquery.dataTables.min.js"></script>
		<script src="dist/js/dataTables/jquery.dataTables.bootstrap.min.js"></script>
		<script src="dist/js/dataTables/dataTables.tableTools.min.js"></script>
		<script src="dist/js/dataTables/dataTables.colVis.min.js"></script>

		<!-- ace scripts -->
		<script src="dist/js/jquery.highlight-4.js"></script>
		<script src="dist/js/ace-elements.min.js"></script>
		<script src="dist/js/ace.min.js"></script>
		<script src="dist/js/lockr.min.js"></script>
		<script src="dist/js/sweetalert.min.js"></script>
		<script src="dist/js/jquery.blockUI.js"></script>	
	</body>
</html>
