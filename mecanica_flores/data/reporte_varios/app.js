app.controller('reporte_variosController', function ($scope, $route) {
	$scope.$route = $route;

	jQuery(function($) {
		$('[data-toggle="tooltip"]').tooltip(); 

	
		// generar btn 1 
		$('#btn_abrir0').click(function() {
			var myWindow = window.open('data/reportes/reporte_productos.php');	
		})
		// fin

		// generar btn 1 
		$('#btn_abrir1').click(function() {
			var myWindow = window.open('data/reportes/reporte_existencia_minima.php');	
		})
		// fin

		// generar btn 2 
		$('#btn_abrir2').click(function() {
			var myWindow = window.open('data/reportes/reporte_clientes.php');	
		})
		// fin

		// generar btn 3 
		$('#btn_abrir3').click(function() {
			var myWindow = window.open('data/reportes/reporte_proveedores.php');	
		})
		// fin

		// generar btn 4 
		$('#btn_abrir4').click(function() {
			var myWindow = window.open('data/reportes/reporte_clientes_excel.php');	
		})
		// fin

		// generar btn 5 
		$('#btn_abrir5').click(function() {
			var myWindow = window.open('data/reportes/reporte_proveedores_excel.php');	
		})
		// fin

		// generar btn 6 
		$('#btn_abrir6').click(function() {
			var myWindow = window.open('data/reportes/reporte_productos_excel.php');	
		})
		// fin
	});
});