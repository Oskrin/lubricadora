app.controller('reporte_ventasController', function ($scope, $route) {
	$scope.$route = $route;

	jQuery(function($) {
		$('[data-toggle="tooltip"]').tooltip(); 
		//para la fecha del calendario
		$(".datepicker").datepicker({ 
			format: "dd/mm/yyyy",
	        autoclose: true
		}).datepicker("setDate","today");
		// fin

		// estilo select2 
		$(".select2").css({
		    'width': '100%',
		    allow_single_deselect: true,
		    no_results_text: "No se encontraron resultados",
		    allowClear: true,
		    }).select2().on("change", function (e) {
			$(this).closest('form').validate().element($(this));
	    });

	    $("#select_cliente1").select2({
		  	// allowClear: true
		});
		// fin

		$('#form_registro1').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			ignore: "",
			rules: {
				select_cliente1: {
					required: true			
				},
			},
			messages: {
				select_cliente1: {
					required: "Campo Requerido",
				},
			},
			//para prender y apagar los errores
			highlight: function (e) {
				$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
			},
			success: function (e) {
				$(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
				$(e).remove();
			},
			submitHandler: function (form) {
				
			}
		});
		// Fin

		// llenar combo directores
		function llenar_select_clientes() {
			$.ajax({
				url: 'data/reporte_ventas/app.php',
				type: 'post',
				data: {llenar_clientes:'llenar_clientes'},
				success: function (data) {
					$('#select_cliente1').html(data);
				}
			});
		}
		// fin


		// inicio
		llenar_select_clientes();

		// fin

		// // generar btn 0 
		// $('#btn_0').click(function() {
		// 	var respuesta = $('#form_registro1').valid();
		// 	if (respuesta == true) {
		// 		var myWindow = window.open('data/reportes/reporte_director.php?inicio=' + $('#fecha_inicio').val() + '&fin=' +$('#fecha_fin').val() + '&id=' +$('#select_cliente1').val());	
		// 	}			
		// })
		// // fin

		// generar btn 1 
		$('#btn_1').click(function() {
			var myWindow = window.open('data/reportes/facturas_ventas.php?inicio=' + $('#fecha_inicio1').val() + '&fin=' +$('#fecha_fin1').val());	
		})
		// fin

		// generar btn 2 
		$('#btn_2').click(function() {
			var myWindow = window.open('data/reportes/utilidad_productos.php?inicio=' + $('#fecha_inicio2').val() + '&fin=' +$('#fecha_fin2').val());	
		})
		// fin

		// generar btn 3 
		$('#btn_3').click(function() {
			var myWindow = window.open('data/reportes/utilidad_factura_general.php?inicio=' + $('#fecha_inicio3').val() + '&fin=' +$('#fecha_fin3').val());	
		})
		// fin

		// generar btn 4
		$('#btn_4').click(function() {
			var myWindow = window.open('data/reportes/facturas_ventas_excel.php?inicio=' + $('#fecha_inicio4').val() + '&fin=' +$('#fecha_fin4').val());	
		})
		// fin

		// generar btn 5
		$('#btn_5').click(function() {
			var myWindow = window.open('data/reportes/resumen_ventas_excel.php?inicio=' + $('#fecha_inicio5').val() + '&fin=' +$('#fecha_fin5').val());	
		})
		// fin

		// generar btn 6
		$('#btn_6').click(function() {
			var myWindow = window.open('data/reportes/notas_ventas.php?inicio=' + $('#fecha_inicio6').val() + '&fin=' +$('#fecha_fin6').val());	
		})
		// fin
	});
});