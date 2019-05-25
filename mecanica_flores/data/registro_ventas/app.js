app.controller('registro_ventasController', function ($scope, $interval) {
    // formato totales
    var formatNumber = {
		separador: ".", // separador para los miles
	 	sepDecimal: '.', // separador para los decimales
	 	formatear:function (num) {
	  	num +='';
	  	var splitStr = num.split('.');
	  	var splitLeft = splitStr[0];
	  	var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
	  	var regx = /(\d+)(\d{3})/;
	  	while (regx.test(splitLeft)) {
	  		splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
	  		}
	  	return this.simbol + splitLeft  +splitRight;
	 	},
	 	new:function(num, simbol){
	  		this.simbol = simbol ||'';
	  	return this.formatear(num);
	 	}
	}
	// fin

    // para horas 
    function show() {
	    var Digital = new Date();
	    var hours = Digital.getHours();
	    var minutes = Digital.getMinutes();
	    var seconds = Digital.getSeconds();
	    var dn = "AM";    
	    if (hours > 12) {
	        dn = "PM";
	        hours = hours - 12;
	    }
	    if (hours == 0)
	        hours = 12;
	    if (minutes <= 9)
	        minutes = "0" + minutes;
	    if (seconds <= 9)
	        seconds = "0" + seconds;
	    $("#hora_actual").val(hours + ":" + minutes + ":" + seconds + " " + dn);
	}

	$interval(function() {
		show();
	}, 1000);
	// fin

	jQuery(function($) {
		// tooltip
		$('[data-toggle="tooltip"]').tooltip();
		// fin

		var oTable1 = $('#dynamic-table')
		.dataTable({					
			bAutoWidth: false,
			"aoColumns": [
			  { "bSortable": false },null, null,null, null, null, null
			],
			"aaSorting": [],			
			language: {
			    "sProcessing":     "Procesando...",
			    "sLengthMenu":     "Mostrar _MENU_ registros",
			    "sZeroRecords":    "No se encontraron resultados",
			    "sEmptyTable":     "Ningún dato disponible en esta tabla",
			    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
			    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
			    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			    "sInfoPostFix":    "",
			    "sSearch":         "Buscar: ",
			    "sUrl":            "",
			    "sInfoThousands":  ",",					    
			    "sLoadingRecords": "Cargando...",
			    "oPaginate": {
			        "sFirst":    "Primero",
			        "sLast":     "Último",
			        "sNext":     "Siguiente",
			        "sPrevious": "Anterior"
			    },
			    "oAria": {
			        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
			    }
			},
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": true,	
	                "bVisible":true,			                
	            },			            
	            {
	                "targets": [ 1 ],
	                "visible": true,			                
	            },			            
	            {
	                "targets": [ 2 ],
	                "visible": true,			                
	            },			            
	            {
	                "targets": [ 3 ],
	                "visible": true, 		                
	            },			            
	            {
	                "targets": [ 4 ],
	                "visible": true,			                
	            },			            
	            {
	                "targets": [ 5 ],
	                "visible": true,			                
	            },			                    
	        ],
	    });

		//TableTools settings
		TableTools.classes.container = "btn-group btn-overlap";
		TableTools.classes.print = {
			"body": "DTTT_Print",
			"info": "tableTools-alert gritter-item-wrapper gritter-info gritter-center white",
			"message": "tableTools-print-navbar"
		}
	
		//initiate TableTools extension
		var tableTools_obj = new $.fn.dataTable.TableTools( oTable1, {					 
			"sSwfPath": "dist/js/dataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf", //in Ace demo dist will be replaced by correct assets path					
			"sRowSelector": "td:not(:last-child)",
			"sRowSelect": "multi",					
			"fnRowSelected": function(row) {
				//check checkbox when row is selected
				try { $(row).find('input[type=checkbox]').get(0).checked = true }
				catch(e) {}
			},
			"fnRowDeselected": function(row) {
				//uncheck checkbox
				try { $(row).find('input[type=checkbox]').get(0).checked = false }
				catch(e) {}
			},					
			"sSelectedClass": "success",
	        "aButtons": [
				{
					"sExtends": "copy",
					"sToolTip": "Copiar al portapapeles",
					"sButtonClass": "btn btn-white btn-primary btn-bold",
					"sButtonText": "<i class='fa fa-copy bigger-110 pink'></i>",
					"fnComplete": function() {
						this.fnInfo( '<h3 class="no-margin-top smaller">Copiado Tabla</h3>\
							<p>Copiado '+(oTable1.fnSettings().fnRecordsTotal())+' Fila(s) en el Portapapeles.</p>',
							1000
						);
					}
				},
				
				{
					"sExtends": "pdf",
					"sToolTip": "Exportar a PDF",
					"sButtonClass": "btn btn-white btn-primary  btn-bold",
					"sButtonText": "<i class='fa fa-file-pdf-o bigger-110 red'></i>"
				},
				
				{
					"sExtends": "print",
					"sToolTip": "Vista de Impresión",
					"sButtonClass": "btn btn-white btn-primary  btn-bold",
					"sButtonText": "<i class='fa fa-print bigger-110 grey'></i>",
					
					"sMessage": "<div class='navbar navbar-default'><div class='navbar-header pull-left'></div></div>",
					
					"sInfo": "<h3 class='no-margin-top'>Vista Impresión</h3>\
							  <p>Por favor, utilice la función de impresión de su navegador para \
								imprimir esta tabla.\
							  <br />Presione <b>ESCAPE</b> cuando haya terminado.</p>",
				}
	        ]
	    } );
		//we put a container before our table and append TableTools element to it
	    $(tableTools_obj.fnContainer()).appendTo($('.tableTools-container'));
		setTimeout(function() {
			$(tableTools_obj.fnContainer()).find('a.DTTT_button').each(function() {
				var div = $(this).find('> div');
				if(div.length > 0) div.tooltip({container: 'body'});
				else $(this).tooltip({container: 'body'});
			});
		}, 200);
		
		//ColVis extension
		var colvis = new $.fn.dataTable.ColVis( oTable1, {
			"buttonText": "<i class='fa fa-search'></i>",
			"aiExclude": [0, 3, 6],
			"bShowAll": true,
			//"bRestore": true,
			"sAlign": "right",
			"fnLabel": function(i, title, th) {
				return $(th).text();//remove icons, etc
			}
		}); 
		
		//style it
		$(colvis.button()).addClass('btn-group').find('button').addClass('btn btn-white btn-info btn-bold')
		
		//and append it to our table tools btn-group, also add tooltip
		$(colvis.button())
		.prependTo('.tableTools-container .btn-group')
		.attr('title', 'Mostrar / ocultar las columnas').tooltip({container: 'body'});
		
		//and make the list, buttons and checkboxed Ace-like
		$(colvis.dom.collection)
		.addClass('dropdown-menu dropdown-light dropdown-caret dropdown-caret-right')
		.find('li').wrapInner('<a href="javascript:void(0)" />') //'A' tag is required for better styling
		.find('input[type=checkbox]').addClass('ace').next().addClass('lbl padding-8');
		
		/////////////////////////////////
		//table checkboxes
		$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
		
		//select/deselect all rows according to table header checkbox
		$('#dynamic-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function() {					
			var th_checked = this.checked;//checkbox inside "TH" table header
			
			$(this).closest('table').find('tbody > tr').each(function(){
				var row = this;
				if(th_checked) tableTools_obj.fnSelect(row);
				else tableTools_obj.fnDeselect(row);
			});
		});
		
		//select/deselect a row when the checkbox is checked/unchecked
		$('#dynamic-table').on('click', 'td input[type=checkbox]' , function() {
			var row = $(this).closest('tr').get(0);					
			if(!this.checked) tableTools_obj.fnSelect(row);
			else tableTools_obj.fnDeselect($(this).closest('tr').get(0));
		});
		
			$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {						
			e.stopImmediatePropagation();
			e.stopPropagation();
			e.preventDefault();
		});
		
		//And for the first simple table, which doesn't have TableTools or dataTables
		//select/deselect all rows according to table header checkbox
		var active_class = 'active';
		$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function() {					
			var th_checked = this.checked;//checkbox inside "TH" table header
			
			$(this).closest('table').find('tbody > tr').each(function(){
				var row = this;
				if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
				else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
			});
		});
		
		//select/deselect a row when the checkbox is checked/unchecked
		$('#simple-table').on('click', 'td input[type=checkbox]' , function() {
			var $row = $(this).closest('tr');
			if(this.checked) $row.addClass(active_class);
			else $row.removeClass(active_class);
		});
	
		/********************************/
		//add tooltip for small view action buttons in dropdown menu
		$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
		
		//tooltip placement on right or left
		function tooltip_placement(context, source) {
			var $source = $(source);
			var $parent = $source.closest('table')
			var off1 = $parent.offset();
			var w1 = $parent.width();
	
			var off2 = $source.offset();
			//var w2 = $source.width();
			if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
			return 'left';
		}
		// Fin tablas

		//para la fecha del calendario
		$(".datepicker").datepicker({ 
			format: "dd/mm/yyyy",
	        autoclose: true
		}).datepicker("setDate","today");
		// fin

		// abrir en una nueva ventana detalle ventas
		$scope.methodoview = function(id) { 
			$('#myModal').modal('show');
			$("#detalle-table tbody tr").remove(); 

			$.ajax({
				url: 'data/factura_venta/app.php',
				type: 'post',
				data: {llenar_cabezera_factura:'llenar_cabezera_factura', id: id},
				dataType: 'json',
				success: function(data) {
					$scope.id = data.id_factura;
					$scope.cliente = data.razon_social;	
					$scope.fecha = data.fecha_emision;
					
					$scope.subtotal = data.subtotal;
					$scope.iva = data.iva;
					$scope.total = data.total_pagar;
				}
			});

			$.ajax({
				url: 'data/registro_ventas/app.php',
				type: 'post',
				data: {cargar_tabla_detalle:'cargar_tabla_detalle', id: id},
				dataType: 'json',
				success: function(response) { 
					for (var i = 0; i < response.length; i++) {
						var html_fila = '<tr>'
									+'<td>'+response[i]['codigo']+'</td>'
									+'<td>'+response[i]['descripcion']+'</td>'
									+'<td>'+response[i]['precio']+'</td>'
									+'<td>'+response[i]['cantidad']+'</td>'
									+'<td>'+response[i]['total']+'</td>'
								+'</tr>'
						$('#detalle-table tbody').append(html_fila);                          
			        }
				}
			});
		} 
		// fin

		// llenar tablas facturas
		function llenar_tabla() {
			$('#dynamic-table').dataTable().fnClearTable();

			$.ajax({
				url: 'data/registro_ventas/app.php',
				type: 'post',
				data: {cargar_tabla:'cargar_tabla', fecha_inicio: $("#fecha_inicio").val(), fecha_fin: $("#fecha_fin").val()},
				dataType: 'json',
				success: function(response) { 
					if(response == null) {
						swal({
			                title: "Lo sentimos sin resultados",
			                type: "warning",
			            });
					} else {
						var tabla = $('#dynamic-table').DataTable();
						for (var i = 0; i < response.length; i++) {
							var vizualizar = "<button type='button' class='btn btn-info btn-bold btn-sm' onclick=\"angular.element(this).scope().methodoview('"+response[i].id+"')\" data-toggle='tooltip' title = 'Visualizar Detalle'><span class='glyphicon glyphicon-eye-open'> Ver Detalle</button>";
							var acciones =  vizualizar;

							tabla.row.add([
					            response[i]['id'],
					            response[i]['vendedor'],
					            response[i]['cliente'],
					            response[i]['secuencial'],
					            response[i]['total_venta'],
					            response[i]['fecha_creacion'],
					            acciones
			                ]).draw(false);                            
				        }
			        }
				}
			});
		}
		// fin

		// filtrar 
		$('#btn_filtrar').click(function() {
			llenar_tabla()	
		});
		// fin
	});
	// fin
});