app.controller('kardexController', function ($scope, $interval) {

	// procesos tab
	$scope.tab = 1;

    $scope.setTab = function(newTab) {
      $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum) {
      return $scope.tab == tabNum;
    };
    // fin

	jQuery(function($) {
		$('[data-toggle="tooltip"]').tooltip();

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
	                "bVisible":true,  		                
	            },			            
	            {
	                "targets": [ 4 ],
	                "visible": true,			                
	            },			            
	            {
	                "targets": [ 5 ],
	                "visible": true,			                
	            },
	            {
	                "targets": [ 6 ],
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
			format: "yyyy-mm-dd",
	        autoclose: true
		}).datepicker("setDate","today");
		// fin

		// stilo select2
		$(".select2").css({
		    'width': '100%',
		    allow_single_deselect: true,
		    no_results_text: "No se encontraron resultados",
		    allowClear: true,
		});
		// fin

		// limpiar imputs
	    function limpiar_input() {
	    	$('#id_producto').val('');
	    	$('#codigo_barras').val('');
	    	$('#codigo').val('');
	    	$('#producto').val('');
	    	$('#cantidad').val('');
	    	$('#precio_venta').val('');
	    	$('#precio_costo').val('');
	    	$('#descuento').val('');
	    	$('#stock').val('');
		    $('#iva_producto').val('');
		    $('#incluye').val('');
		    $('#inventariable').val('');
	    }
	    // fin

	    // limpiar codigo
	    $("#codigo").keyup(function(e) {
		    if($('#codigo').val() == '') {
		    	$('#id_producto').val('');
		    	$('#codigo_barras').val('');
		    	$('#producto').val('');
		    	$('#cantidad').val('');
		    	$('#precio_venta').val('');
	    		$('#precio_costo').val('');
		    	$('#descuento').val('');
		    	$('#stock').val('');
		    	$('#iva_producto').val('');
		    	$('#incluye').val('');
		    	$('#inventariable').val('');
		    }
		});
	    // fin

	    // limpiar descripcion
	    $("#producto").keyup(function(e) {
		    if($('#producto').val() == '') {
		    	$('#id_producto').val('');
		    	$('#codigo_barras').val('');
		    	$('#codigo').val('');
		    	$('#cantidad').val('');
		    	$('#precio_venta').val('');
	    		$('#precio_costo').val('');
		    	$('#descuento').val('');
		    	$('#stock').val('');
		    	$('#iva_producto').val('');
		    	$('#incluye').val('');
		    	$('#inventariable').val('');
		    }
		});
	    // fin

	    // buscar productos codigo barras
	    $("#codigo_barras").change(function(e) {
	        barras();
	    });

	    function barras() {
	    	var codigo_barras = $("#codigo_barras").val();
	        var precio = 'MINORISTA'; 
	        
            $.getJSON('data/buscador/barras.php?codigo_barras=' + codigo_barras + '&tipo_precio=' + precio, function(data) {
            	if(data == null) {
					swal({
		                title: "Lo sentimos Articulo no Creado",
		                type: "warning",
		            });
		            limpiar_input();
				} else {
	            	$("#id_producto").val(data.id);
	                $("#codigo_barras").val(data.codigo_barras);
	                $("#codigo").val(data.codigo);
	                $("#producto").val(data.producto);
	                $("#precio_costo").val(data.precio_costo);
	        		$("#precio_venta").val(data.precio_venta);
	                $("#descuento").val(data.descuento);
	                $("#stock").val(data.stock);
	                $("#iva_producto").val(data.iva_producto);
	                $("#incluye").val(data.incluye);
	                $("#inventariable").val(data.inventariable);

	                $('#cantidad').focus();
	            }
            });   
	    } 
	    // fin

	    // buscar productos codigo
	    $("#codigo").keyup(function(e) {
	    	var tipo = 'codigo';

	     	$("#codigo").autocomplete({
	     		source: "data/buscador/productos_general.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_producto").val(ui.item.id);	
	                $("#codigo_barras").val(ui.item.codigo_barras);
	                $("#codigo").val(ui.item.value);
	                $("#producto").val(ui.item.producto);
	                $("#precio_costo").val(ui.item.precio_costo);
	            	$("#precio_venta").val(ui.item.precio_venta);
	                $("#descuento").val(ui.item.descuento);
	                $("#stock").val(ui.item.stock);
	                $("#iva_producto").val(ui.item.iva_producto);
	                $("#incluye").val(ui.item.incluye);
	                $("#inventariable").val(ui.item.inventariable);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_producto").val(ui.item.id);
	                $("#codigo_barras").val(ui.item.codigo_barras);
	                $("#codigo").val(ui.item.value);
	                $("#producto").val(ui.item.producto);
	                $("#precio_costo").val(ui.item.precio_costo);
	            	$("#precio_venta").val(ui.item.precio_venta);
	                $("#descuento").attr(ui.item.descuento);
	                $("#stock").val(ui.item.stock);
	                $("#iva_producto").val(ui.item.iva_producto);
	                $("#incluye").val(ui.item.incluye);
	                $("#inventariable").val(ui.item.inventariable);

	                $('#cantidad').focus();

	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
	    });
	    // fin

	    // buscar productos descripcion
	    $("#producto").keyup(function(e) {
	    	var tipo = 'descripcion';

	     	$("#producto").autocomplete({
	     		source: "data/buscador/productos_general.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_producto").val(ui.item.id);	
	                $("#codigo_barras").val(ui.item.codigo_barras);
	                $("#codigo").val(ui.item.codigo);
	                $("#producto").val(ui.item.value);
	                $("#precio_costo").val(ui.item.precio_costo);
	            	$("#precio_venta").val(ui.item.precio_venta);
	                $("#descuento").val(ui.item.descuento);
	                $("#stock").val(ui.item.stock);
	                $("#iva_producto").val(ui.item.iva_producto);
	                $("#incluye").val(ui.item.incluye);
	                $("#inventariable").val(ui.item.inventariable);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_producto").val(ui.item.id);
	                $("#codigo_barras").val(ui.item.codigo_barras);
	                $("#codigo").val(ui.item.codigo);
	                $("#producto").val(ui.item.value);
	                $("#precio_costo").val(ui.item.precio_costo);
	            	$("#precio_venta").val(ui.item.precio_venta);
	                $("#descuento").attr(ui.item.descuento);
	                $("#stock").val(ui.item.stock);
	                $("#iva_producto").val(ui.item.iva_producto);
	                $("#incluye").val(ui.item.incluye);
	                $("#inventariable").val(ui.item.inventariable);

	                $('#cantidad').focus();
	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
	    });
	    // fin

		// recargar formulario
		function redireccionar() {
			setTimeout(function() {
			    location.reload(true);
			}, 1000);
		}
		// fin

		// consultar kardex
		$('#btn_consultar').click(function() {
			var formulario = $("#form_kardex").serialize();
			$('#dynamic-table').dataTable().fnClearTable();

			$.ajax({
				url: 'data/kardex/app.php',
				type: 'post',
				data: formulario + "&cargar_tabla=" + 'cargar_tabla',
				dataType: 'json',
				success: function(response) {
					if (response != null) {
						var tabla = $('#dynamic-table').DataTable();
						for (var i = 0; i < response.length; i++) {
							tabla.row.add([
						            response[i]['descripcion'],
						            response[i]['fecha_kardex'],
						            response[i]['detalle'],
						            response[i]['cantidad'],
						            response[i]['valor_unitario'],
						            response[i]['total'],
						            response[i]['saldo']
			                ]).draw(false);                                 
				        }	 	
					} else {
						swal({
			                title: "Sin Resultados",
			                type: "warning",
			            });		
					} 
				}
			});
		});
		// fin
	});
});