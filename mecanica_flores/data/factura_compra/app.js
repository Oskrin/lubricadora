app.controller('factura_compraController', function ($scope, $interval) {

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

	function toFixedDown(value, digits) {
	    if( isNaN(value) )
	        return 0;
	    var n = value - Math.pow(10, -digits)/2;
	    n += n / Math.pow(2, 53);
	    if(n<0)
	        n=0.000;
	    return n.toFixed(digits);
	}

	jQuery(function($) {
		// tooltip
		$('[data-toggle="tooltip"]').tooltip();
		// fin

		$('#serie').mask('999-999-999999999');
		
		//para la fecha del calendario
		$(".datepicker").datepicker({ 
			format: "dd/mm/yyyy",
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

		// limpiar select2
		$("#select_forma_pago,#select_tiempo").select2({
		  // allowClear: true
		});
		// fin

		// event ctrl+b abrir
		Mousetrap.bind(['ctrl+b'], function(e) {
			$('#myModal').modal('show'); 	    
		});
		// fin

		// llenar combo forma pago
		function llenar_select_forma_pago() {
			$.ajax({
				url: 'data/factura_compra/app.php',
				type: 'post',
				data: {llenar_forma_pago:'llenar_forma_pago'},
				success: function (data) {
					$('#select_forma_pago').html(data).trigger("change");
				}
			});
		}
		// fin

		// limpiar ruc
	    $("#ruc").keyup(function(e) {
		    if($('#ruc').val() == '') {
		    	$('#id_proveedor').val('');
		    	$('#razon_social').val('');
		    	$('#telefono').val('');
		    	$('#direccion').val('');
		    	$('#correo').val('');
		    }
		});
	    // fin

	    // limpiar razon_social
	    $("#razon_social").keyup(function(e) {
		    if($('#razon_social').val() == '') {
		    	$('#id_proveedor').val('');
		    	$('#ruc').val('');
		    	$('#telefono').val('');
		    	$('#direccion').val('');
		    	$('#correo').val('');
		    }
		});
	    // fin

	    // busqueda ruc proveedor
		$("#ruc").keyup(function(e) {
			var tipo = 'identificacion';

	     	$("#ruc").autocomplete({
	     		source: "data/buscador/proveedores.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_proveedor").val(ui.item.id); 
		            $("#ruc").val(ui.item.value); 
		            $("#razon_social").val(ui.item.razon_social);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_proveedor").val(ui.item.id); 
		            $("#ruc").val(ui.item.value); 
		            $("#razon_social").val(ui.item.razon_social);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

		            $("#autorizacion").focus();

	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
		});
		// fin

		// busqueda razon_social proveedor 
		$("#razon_social").keyup(function(e) {
			var tipo = 'razon_social';

	     	$("#razon_social").autocomplete({
	     		source: "data/buscador/proveedores.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_proveedor").val(ui.item.id); 
		            $("#razon_social").val(ui.item.value); 
		            $("#ruc").val(ui.item.ruc);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_proveedor").val(ui.item.id); 
		            $("#razon_social").val(ui.item.value); 
		            $("#ruc").val(ui.item.ruc);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

		            $("#autorizacion").focus();
	                
	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
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
	                //$("#descuento").val(ui.item.descuento);
	                //$("#stock").val(ui.item.stock);
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
	                //$("#descuento").attr(ui.item.descuento);
	                //$("#stock").val(ui.item.stock);
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
	                //$("#descuento").val(ui.item.descuento);
	                //$("#stock").val(ui.item.stock);
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
	                //$("#descuento").attr(ui.item.descuento);
	               // $("#stock").val(ui.item.stock);
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

	    /*---agregar a la tabla---*/
	  	$("#cantidad").on("keypress",function (e) {
	  		if(e.keyCode == 13) { // tecla del alt para el entrer poner 13 
			    var subtotal0 = 0;
			    var subtotal12 = 0;
			    var subtotal_total = 0;
			    var iva12 = 0;
			    var total_total = 0;
			    var descu_total = 0;

		        if ($("#id_producto").val() == "") {
		            $("#codigo_barras").focus();
		            $.gritter.add({
		                title: 'Error... Ingrese un Producto',
		                class_name: 'gritter-error gritter-center',
		                time: 1000,
		            });
		        } else {
		            if ($("#producto").val() == "") {
		                $("#producto").focus();
		                $.gritter.add({
							title: 'Error... Ingrese un Producto',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});
		            } else {
		                if ($("#cantidad").val() == "") {
		                    $("#cantidad").focus();
		                    $.gritter.add({
								title: 'Error... Ingrese una Cantidad',
								class_name: 'gritter-error gritter-center',
								time: 1000,
							});
		                } else {
		                    if ($("#precio_costo").val() == "") {
		                        $("#precio_costo").focus();
		                        $.gritter.add({
									title: 'Error... Ingrese un Precio',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
			               	} else {
			                    var filas = jQuery("#table").jqGrid("getRowData");
			                    var descuento = 0;
			                    var total = 0;
			                    var desc = 0;
			                    var precio = 0;
			                    var multi = 0;
			                    var flotante = 0;
			                    var resultado = 0;
			                    var repe = 0;
			                  
			                    if (filas.length == 0) {
			                        if ($("#descuento").val() != "") {
			                            desc = $("#descuento").val();
			                            precio = (parseFloat($("#precio_costo").val())).toFixed(3);
			                            multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
			                            descuento = ((multi * parseFloat(desc)) / 100);
			                            flotante = parseFloat(descuento);
			                            resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
			                            total = (multi - resultado).toFixed(3);
			                        } else {
			                            desc = 0;
			                            precio = (parseFloat($("#precio_costo").val())).toFixed(3);
			                            multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
			                            descuento = ((multi * parseFloat(desc)) / 100);
			                            flotante = parseFloat(descuento);
			                            resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
			                            total = (parseFloat(multi)).toFixed(3);
			                        }

			                        var datarow = {
			                            id: $("#id_producto").val(), 
			                            codigo: $("#codigo").val(), 
			                            detalle: $("#producto").val(), 
			                            cantidad: $("#cantidad").val(), 
			                            precio_u: precio, 
			                            descuento: desc,
			                            cal_des: resultado, 
			                            total: total, 
			                            iva: $("#iva_producto").val(),
			                            incluye: $("#incluye").val()
			                        };

			                        jQuery("#table").jqGrid('addRowData', $("#id_producto").val(), datarow);
			                        limpiar_input();
			                    } else {
			                        for (var i = 0; i < filas.length; i++) {
			                            var id = filas[i];

			                            if (id['id'] == $("#id_producto").val()) {
			                                repe = 1;
			                            }
			                        }

			                        if (repe == 1) {
			                            if ($("#descuento").val() != "") {
			                                desc = $("#descuento").val();
			                                precio = (parseFloat($("#precio_costo").val())).toFixed(3);
			                                multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
			                                descuento = ((multi * parseFloat(desc)) / 100);
			                                flotante = parseFloat(descuento);
			                                resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
			                                total = (multi - resultado).toFixed(3);
			                            } else {
			                                desc = 0;
			                                precio = (parseFloat($("#precio_costo").val())).toFixed(3);
			                                multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
			                                descuento = ((multi * parseFloat(desc)) / 100);
			                                flotante = parseFloat(descuento);
			                                resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
			                                total = (parseFloat(multi)).toFixed(3);
			                            }

			                            datarow = {
			                                id: $("#id_producto").val(), 
			                                codigo: $("#codigo").val(), 
			                                detalle: $("#producto").val(), 
			                                cantidad: $("#cantidad").val(), 
			                                precio_u: precio, 
			                                descuento: desc,
			                                cal_des: resultado,  
			                                total: total, 
			                                iva: $("#iva_producto").val(),
			                                incluye: $("#incluye").val()
			                            };

			                            jQuery("#table").jqGrid('setRowData', $("#id_producto").val(), datarow);
			                            limpiar_input();
			                        } else {
		                                if ($("#descuento").val() != "") {
		                                    desc = $("#descuento").val();
		                                    precio = (parseFloat($("#precio_costo").val())).toFixed(3);
		                                    multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
		                                    descuento = ((multi * parseFloat(desc)) / 100);
		                                    flotante = parseFloat(descuento) ;
		                                    resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
		                                    total = (multi - resultado).toFixed(3);
		                                } else {
		                                    desc = 0;
		                                    precio = (parseFloat($("#precio_costo").val())).toFixed(3);
		                                    multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
		                                    descuento = ((multi * parseFloat(desc)) / 100);
		                                    flotante = parseFloat(descuento);
		                                    resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
		                                    total = (parseFloat(multi)).toFixed(3);
		                                }
		                            
		                                datarow = {
		                                    id: $("#id_producto").val(), 
		                                    codigo: $("#codigo").val(), 
		                                    detalle: $("#producto").val(), 
		                                    cantidad: $("#cantidad").val(), 
		                                    precio_u: precio, 
		                                    descuento: desc, 
		                                    cal_des: resultado,
		                                    total: total, 
		                                    iva: $("#iva_producto").val(), 
		                                    incluye: $("#incluye").val()
		                                };

		                                jQuery("#table").jqGrid('addRowData', $("#id_producto").val(), datarow);
		                                limpiar_input();
			                        }
			                    }
			                    
			                    // proceso impuestos
			                    var subtotal = 0;
			                    var sub = 0;
			                    var sub1 = 0;
			                    var sub2 = 0;
			                    var iva = 0;
			                    var iva1 = 0;
			                    var iva2 = 0;
			                    // fin                                                     
			                    
			                    var fil = jQuery("#table").jqGrid("getRowData");
			                    for (var t = 0; t < fil.length; t++) {
			                        var dd = fil[t];
			                        if (dd['iva'] != 0) {
			                            if(dd['incluye'] == "NO") {
			                                subtotal = dd['total'];
			                                sub1 = subtotal;
			                                iva1 = parseFloat(sub1 * dd['iva'] / 100).toFixed(3);                                          

			                                subtotal0 = parseFloat(subtotal0) + 0;
			                                subtotal12 = parseFloat(subtotal12) + parseFloat(sub1);
			                                subtotal_total = parseFloat(subtotal0) + parseFloat(subtotal12);
			                                descu_total = parseFloat(descu_total) + parseFloat(dd['cal_des']);
			                                iva12 = parseFloat(iva12) + parseFloat(iva1);
			                            
			                                subtotal0 = parseFloat(subtotal0).toFixed(3);
			                                subtotal12 = parseFloat(subtotal12).toFixed(3);
			                                subtotal_total = parseFloat(subtotal_total).toFixed(3);
			                                iva12 = parseFloat(iva12).toFixed(3);
			                                descu_total = parseFloat(descu_total).toFixed(3);
			                            } else {
			                                if(dd['incluye'] == "SI") {
			                                    subtotal = dd['total'];
			                                    sub2 = parseFloat(subtotal / ((dd['iva']/100)+1)).toFixed(3);
			                                    iva2 = parseFloat(sub2 * dd['iva'] / 100).toFixed(3);

			                                    subtotal0 = parseFloat(subtotal0) + 0;
			                                    subtotal12 = parseFloat(subtotal12) + parseFloat(sub2);
			                                    subtotal_total = parseFloat(subtotal0) + parseFloat(subtotal12);
			                                    iva12 = parseFloat(iva12) + parseFloat(iva2);
			                                    descu_total = parseFloat(descu_total) + parseFloat(dd['cal_des']);

			                                    subtotal0 = parseFloat(subtotal0).toFixed(3);
			                                    subtotal12 = parseFloat(subtotal12).toFixed(3);
			                                    subtotal_total = parseFloat(subtotal_total).toFixed(3);
			                                    iva12 = parseFloat(iva12).toFixed(3);
			                                    descu_total = parseFloat(descu_total).toFixed(3);
			                                }
			                            }
			                        } else {
			                            if (dd['iva'] == 0) {                                               
			                                subtotal = dd['total'];
			                                sub = subtotal;

			                                subtotal0 = parseFloat(subtotal0) + parseFloat(sub);
			                                subtotal12 = parseFloat(subtotal12) + 0;
			                                subtotal_total = parseFloat(subtotal0) + parseFloat(subtotal12);
			                                iva12 = parseFloat(iva12) + 0;
			                                descu_total = parseFloat(descu_total) + parseFloat(dd['cal_des']);
			                                
			                                subtotal0 = parseFloat(subtotal0).toFixed(3);
			                                subtotal12 = parseFloat(subtotal12).toFixed(3);
			                                subtotal_total = parseFloat(subtotal_total).toFixed(3);
			                                iva12 = parseFloat(iva12).toFixed(3);
			                                descu_total = parseFloat(descu_total).toFixed(3);                                  
			                            }       
			                        }
			                    } 

			                    total_total = parseFloat(total_total) + (parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(iva12));
			                    total_total = parseFloat(total_total).toFixed(2);

			                    $("#subtotal").val(subtotal_total);
			                    $("#tarifa").val(subtotal12);
			                    $("#tarifa_0").val(subtotal0);
			                    $("#iva").val(iva12);
			                    $("#otros").val(descu_total);
			                    $("#total_pagar").val(total_total);
			                    $("#codigo_barras").focus();
		                    }
		                }
		            }
		        }
		    }
	  	});

		// validacion punto
		function ValidPun() {
		    var key;
		    if (window.event) {
		        key = event.keyCode;
		    } else if (event.which) {
		        key = event.which;
		    }

		    if (key < 48 || key > 57) {
		        if (key === 46 || key === 8) {
		            return true;
		        } else {
		            return false;
		        }
		    }
		    return true;
		}
		// fin

		// funcion validar solo numeros
		function ValidNum() {
		    if (event.keyCode < 48 || event.keyCode > 57) {
		        event.returnValue = false;
		    }
		    return true;
		}
		// fin

		// abrir en una nueva ventana reporte facturas
		$scope.methodspdf = function(id) { 
			var myWindow = window.open('data/reportes/factura_compra.php?id='+id,'popup','width=900,height=650');
		} 
		// fin

		// recargar formulario
		function redireccionar() {
			setTimeout(function() {
			    location.reload(true);
			}, 1000);
		}
		// fin

		// inicio llamado funciones
		llenar_select_forma_pago();

		$("#ruc").keypress(ValidNum);
		$("#ruc").attr("maxlength", "13");
		$("#serie").focus();
		$("#autorizacion").keypress(ValidNum);
		$("#autorizacion").attr("maxlength", "49");
		$("#cantidad").keypress(ValidNum);
		$("#precio_costo").keypress(ValidPun);

		//$("#btn_1").attr("disabled", true);
		$("#btn_3").attr("disabled", true);

		// guardar factura compra
		$('#btn_0').click(function() {
			var filas = jQuery("#table").jqGrid("getRowData");
			var filas2 = jQuery("#table4").jqGrid("getRowData");

			if($('#serie').val() == '') {
				$('#serie').focus();
				$.gritter.add({
					title: 'Ingrese una Serie',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {
				if($('#id_proveedor').val() == '') {
					$('#ruc').focus();
					$.gritter.add({
						title: 'Seleccione un Proveedor',
						class_name: 'gritter-error gritter-center',
						time: 1000,
					});	
				} else {
					if($('#autorizacion').val() == '') {
						$('#autorizacion').focus();
						$.gritter.add({
							title: 'Ingrese una Autorización',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});	
					} else {
							if(filas.length == 0) {
				                $.gritter.add({
									title: 'Ingrese productos a la Factura',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
				                $('#codigo_barras').focus();	
				            } else {
				            	if(filas2.length == 0) {
					                $.gritter.add({
										title: 'Seleccione una Forma de Pago',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});
									$("#valor").val($("#total_pagar").val());
					                $("#myModalformas").modal('show');	
					            } else {
				            		var formulario = $("#form_factura").serialize();
									var submit = "Guardar";
			            			//$("#btn_0").attr("disabled", true);
						            var v1 = new Array();
						            var v2 = new Array();
						            var v3 = new Array();
						            var v4 = new Array();
						            var v5 = new Array();

						            var string_v1 = "";
						            var string_v2 = "";
						            var string_v3 = "";
						            var string_v4 = "";
						            var string_v5 = "";

						            for (var i = 0; i < filas.length; i++) {
						                var datos = filas[i];
						                v1[i] = datos['id'];
						                v2[i] = datos['cantidad'];
						                v3[i] = datos['precio_u'];
						                v4[i] = datos['descuento'];
						                v5[i] = datos['total'];
						            }

						            for (i = 0; i < filas.length; i++) {
						                string_v1 = string_v1 + "|" + v1[i];
						                string_v2 = string_v2 + "|" + v2[i];
						                string_v3 = string_v3 + "|" + v3[i];
						                string_v4 = string_v4 + "|" + v4[i];
						                string_v5 = string_v5 + "|" + v5[i];
						            }

						            // forma pago
						            var v7 = new Array();
						            var v8 = new Array();
						            var v9 = new Array();
						            var v10 = new Array();

						            var string_v7 = "";
						            var string_v8 = "";
						            var string_v9 = "";
						            var string_v10 = "";

						            for (var j = 0; j < filas2.length; j++) {
						                var datos2 = filas2[j];
						                v7[j] = datos2['id'];
						                v8[j] = datos2['valor'];
						                v9[j] = datos2['plazo'];
						                v10[j] = datos2['unidad'];
						            }

						            for (j = 0; j < filas2.length; j++) {
						                string_v7 = string_v7 + "|" + v7[j];
						                string_v8 = string_v8 + "|" + v8[j];
						                string_v9 = string_v9 + "|" + v9[j];
						                string_v10 = string_v10 + "|" + v10[j];
						            }

						            $.ajax({
						                type: "POST",
						                url: "data/factura_compra/app.php",
						                data: formulario +"&btn_guardar=" + submit + "&campo1=" + string_v1 + "&campo2=" + string_v2 + "&campo3=" + string_v3 + "&campo4=" + string_v4 + "&campo5=" + string_v5 + "&campo7=" + string_v7 + "&campo8=" + string_v8+ "&campo9=" + string_v9+ "&campo10=" + string_v10,
						                success: function(data) {
						                	var id = data;

						                	bootbox.alert("Gracias! Por su Información Factura Compra Agregada Correctamente!", function() {
											  	//var myWindow = window.open('data/reportes/factura_compra.php?id='+id,'popup','width=900,height=650'); 
											  	location.reload();
											});
						                }
						            });
					            //}       
							}
						}
					}
				}
			}	
		});
		// fin

		// reimprimir facturas
		$('#btn_3').click(function() {
			if($('#id_factura').val() == '') {
				$.gritter.add({
					title: 'Seleccione Factura a Reimprimir',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {
				var id = $('#id_factura').val();
				var myWindow = window.open('data/reportes/factura_compra.php?id='+id,'popup','width=900,height=650');
			}
		});
		// fin

		// actualizar formulario
		$('#btn_4').click(function() {
			location.reload();
		});
		// fin

		// abrir formas pago
		$('#btn_brir').click(function() {
			var filas = jQuery("#table").jqGrid("getRowData");

			if(filas.length == 0) {
                $.gritter.add({
					title: 'Ingrese productos a la Factura',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});
                $('#codigo_barras').focus();
            } else {
            	$("#valor").val($("#total_pagar").val());
				$('#myModalformas').modal('show');
			}	
		});
		// fin

		// agregar formas pago
		$('#btn_agregar').click(function() {
			var codigo_pago = "";
			if ($("#valor").val() == "") {
				$('#valor').focus();
				$.gritter.add({
					title: 'Ingrese un valor válido',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});		
			} else {
				var filas = jQuery("#table4").jqGrid("getRowData");
				var id_forma = $("#select_forma_pago").val();
				var repe = 0;
				
				$.ajax({
					url: 'data/factura_venta/app.php',
					type: 'post',
					data: {llenar_campos_pago:'llenar_campos_pago', id: id_forma},
					dataType: 'json',
					success: function(data) {
						if (filas.length == 0) {
							if ($("#select_tiempo").val() == "Ninguno") {
								var datarow = {
			                        id: data.id, 
			                        codigo: data.codigo, 
			                        descripcion: data.descripcion, 
			                        valor: $("#valor").val(), 
			                        plazo: '', 
			                        unidad: $("#plazo").val()
			                    };
							} else {
								var datarow = {
			                        id: data.id, 
			                        codigo: data.codigo, 
			                        descripcion: data.descripcion, 
			                        valor: $("#valor").val(), 
			                        plazo: $("#select_tiempo").val(), 
			                        unidad: $("#plazo").val()
			                    };	
							}

		                   	jQuery("#table4").jqGrid('addRowData', $("#select_forma_pago").val(), datarow);
		                   	$("#plazo").val("");
		                   	$('#myModalformas').modal('hide');
		                } else {
		                    for (var i = 0; i < filas.length; i++) {
		                        var id = filas[i];

		                        if (id['id'] == data.id) {
		                            repe = 1;
		                        }
		                    }

		                    if (repe == 1) {
		                        $.gritter.add({
									title: 'Forma de Pago ya Agregada',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
		                    } else {
		                    	if ($("#select_tiempo").val() == "Ninguno") {
									var datarow = {
				                        id: data.id, 
				                        codigo: data.codigo, 
				                        descripcion: data.descripcion, 
				                        valor: $("#valor").val(), 
				                        plazo: '', 
				                        unidad: $("#plazo").val()
				                    };
								} else {
									var datarow = {
				                        id: data.id, 
				                        codigo: data.codigo, 
				                        descripcion: data.descripcion, 
				                        valor: $("#valor").val(), 
				                        plazo: $("#select_tiempo").val(), 
				                        unidad: $("#plazo").val()
				                    };	
								}

			                   	jQuery("#table4").jqGrid('addRowData', $("#select_forma_pago").val(), datarow);
			                   	$("#plazo").val("");
			                   	$('#myModalformas').modal('hide');	
		                    }
		                }
					}
				}); 	
			}
		});
		// fin
	});
	// fin
	
	/*jqgrid table 1 local*/    
	jQuery(function($) {
	    var grid_selector = "#table";
	    var pager_selector = "#pager";
	    
	    $(window).on('resize.jqGrid', function() {
			$(grid_selector).jqGrid('setGridWidth', $("#grid_container").width(), true);
	    }).trigger('resize');  

	    var parent_column = $(grid_selector).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				setTimeout(function() {
					$(grid_selector).jqGrid( 'setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // tabla local facturas
	    jQuery(grid_selector).jqGrid({
	        datatype: "local",
	        colNames: ['','ID','CÓDIGO','DESCRIPCIÓN','CANT.','PVP','DESC.','CALCULADO','VALOR TOTAL','IVA','INCLUYE'],
	        colModel:[  
	        	{name:'myac', width: 50, fixed: true, sortable: false, resize: false, formatter: 'actions',
			        formatoptions: {keys: false, delbutton: true, editbutton: false}
			    }, 
			    {name: 'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name: 'codigo', index: 'codigo', editable: false, search: false, hidden: false, editrules: {edithidden: false}, align: 'center', frozen: true, width: 100},
	            {name: 'detalle', index: 'detalle', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 290},
	            {name: 'cantidad', index: 'cantidad', editable: true, frozen: true, editrules: {required: true}, align: 'center', width: 70, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return numeros(e)})}}}, 
	            {name: 'precio_u', index: 'precio_u', editable: true, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 110, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return punto(e)})}}}, 
	            {name: 'descuento', index: 'descuento', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 70},
	            {name: 'cal_des', index: 'cal_des', editable: false, hidden: true, frozen: true, editrules: {required: true}, align: 'center', width: 90},
	            {name: 'total', index: 'total', editable: false, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 110},
	            {name: 'iva', index: 'iva', align: 'center', width: 100, hidden: true},
	            {name: 'incluye', index: 'incluye', editable: false, hidden: true, frozen: true, editrules: {required: true}, align: 'center', width: 90}
	        ],          
	        rowNum: 10,
	        rowList: [10,20,30],
	        width: 600,
	        height: 330,
	        pager: pager_selector,        
	        sortname: 'id',
	        sortorder: 'asc',
	        autoencode: false,
	        shrinkToFit: false,
	        altRows: true,
	        multiselect: false,
	        viewrecords: true,
	        shrinkToFit: true,
	        delOptions: {
            	modal: true,
	            jqModal: true,
	            onclickSubmit: function(rp_ge, rowid) {
	                var id = jQuery(grid_selector).jqGrid('getGridParam', 'selrow');
	                jQuery(grid_selector).jqGrid('restoreRow', id);
	                var ret = jQuery(grid_selector).jqGrid('getRowData', id);

	                var subtotal0 = 0;
	                var subtotal12 = 0;
	                var subtotal_total = 0;
	                var iva12 = 0;
	                var total_total = 0;
	                var descu_total = 0;

	                var subtotal = 0;
	                var sub = 0;
	                var sub1 = 0;
	                var sub2 = 0;
	                var iva = 0;
	                var iva1 = 0;
	                var iva2 = 0;

	                var filas = jQuery(grid_selector).jqGrid("getRowData"); 

	                for (var t = 0; t < filas.length; t++) {
	                    if(ret.iva != 0) {
	                      	if(ret.incluye == "NO") {
		                        subtotal = ret.total;
		                        sub1 = subtotal;
		                        iva1 = parseFloat(sub1 * ret.iva / 100).toFixed(3);                                          

		                        subtotal0 = parseFloat($("#tarifa_0").val()) + 0;
		                        subtotal12 = parseFloat($("#tarifa").val()) - parseFloat(sub1);
		                        subtotal_total = parseFloat($("#subtotal").val()) - parseFloat(sub1);
		                        iva12 = parseFloat($("#iva").val()) - parseFloat(iva1);
		                        descu_total = parseFloat($("#otros").val()) - parseFloat(ret.cal_des);

		                        subtotal0 = parseFloat(subtotal0).toFixed(3);
		                        subtotal12 = parseFloat(subtotal12).toFixed(3);
		                        subtotal_total = parseFloat(subtotal_total).toFixed(3);
		                        iva12 = parseFloat(iva12).toFixed(3);
		                        descu_total = parseFloat(descu_total).toFixed(3);
		                    } else {
		                        if(ret.incluye == "SI") {
		                            subtotal = ret.total;
		                            sub2 = parseFloat(subtotal / ((ret.iva/100)+1)).toFixed(3);
		                            iva2 = parseFloat(sub2 * ret.iva / 100).toFixed(3);

		                            subtotal0 = parseFloat($("#tarifa_0").val()) + 0;
		                            subtotal12 = parseFloat($("#tarifa").val()) - parseFloat(sub2);
		                            subtotal_total = parseFloat($("#subtotal").val()) - parseFloat(sub2);
		                            iva12 = parseFloat($("#iva").val()) - parseFloat(iva2);
		                            descu_total = parseFloat($("#otros").val()) - parseFloat(ret.cal_des);

		                            subtotal0 = parseFloat(subtotal0).toFixed(3);
		                            subtotal12 = parseFloat(subtotal12).toFixed(3);
		                            subtotal_total = parseFloat(subtotal_total).toFixed(3);
		                            iva12 = parseFloat(iva12).toFixed(3);
		                            descu_total = parseFloat(descu_total).toFixed(3);
		                        }
		                    }
	                    } else {
	                        if (ret.iva == 0) {
	                            subtotal = ret.total;
	                            sub = subtotal;

	                            subtotal0 = parseFloat($("#tarifa_0").val()) - parseFloat(sub);
	                            subtotal12 = parseFloat($("#tarifa").val()) + 0;
	                            subtotal_total = parseFloat($("#subtotal").val()) - parseFloat(sub);
	                            iva12 = parseFloat($("#iva").val()) + 0;
	                            descu_total = parseFloat($("#otros").val()) - parseFloat(ret.cal_des);
	                          
	                            subtotal0 = parseFloat(subtotal0).toFixed(3);
	                            subtotal12 = parseFloat(subtotal12).toFixed(3);
	                            subtotal_total = parseFloat(subtotal_total).toFixed(3);
	                            iva12 = parseFloat(iva12).toFixed(3);
	                            descu_total = parseFloat(descu_total).toFixed(3);
	                        }
	                    }
	                }

	                total_total = parseFloat(total_total) + (parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(iva12));
	                total_total = parseFloat(total_total).toFixed(3);

	                $("#subtotal").val(subtotal_total);
	                $("#tarifa_0").val(subtotal0);
	                $("#tarifa").val(subtotal12);
	                $("#iva").val(iva12);
	                $("#otros").val(descu_total);
	                $("#total_pagar").val(total_total);
                
	                var su = jQuery(grid_selector).jqGrid('delRowData', rowid);
                   	if (su == true) {
                       rp_ge.processing = true;
                       $(".ui-icon-closethick").trigger('click'); 
                    }
	                return true;
	            },
	            processing: true
	        },
	        loadComplete: function() {
	            var table = this;
	            setTimeout(function() {
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	            }, 0);
	        },
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector).jqGrid('getRowData',gsr);	            
	        },
	    });

	    $(window).triggerHandler('resize.jqGrid');//cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector).jqGrid('navGrid', pager_selector, {   //navbar options
	        edit: false,
	        editicon: 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon: 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon: 'ace-icon fa fa-trash-o red',
	        search: false,
	        searchicon: 'ace-icon fa fa-search orange',
	        refresh: false,
	        refreshicon: 'ace-icon fa fa-refresh green',
	        view: false,
	        viewicon: 'ace-icon fa fa-search-plus grey'
	    },
	    {	        
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        closeAfterAdd: true,
	        recreateForm: true,
	        viewPagerButtons: false,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
	            .wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            if(form.data('styled')) return false;      
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_delete_form(form); 
	            form.data('styled', true);
	        },
	        onClick: function(e) {}
	    },
	    {
	        recreateForm: true,
	        afterShowSearch: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	            style_search_form(form);
	        },
	        afterRedraw: function() {
	            style_search_filters($(this));
	        },

	        //multipleSearch: true
	        overlay: false,
	        sopt: ['eq', 'cn'],
            defaultSearch: 'eq',            	       
	    },
	    {
	        //view record form
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	        }
	    })	

	    function style_edit_form(form) {
	        form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
	        form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');

	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
	        buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')
	        
	        buttons = form.next().find('.navButton a');
	        buttons.find('.ui-icon').hide();
	        buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
	        buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');       
	    }

	    function style_delete_form(form) {
	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
	        buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
	    }
	    
	    function style_search_filters(form) {
	        form.find('.delete-rule').val('X');
	        form.find('.add-rule').addClass('btn btn-xs btn-primary');
	        form.find('.add-group').addClass('btn btn-xs btn-success');
	        form.find('.delete-group').addClass('btn btn-xs btn-danger');
	    }

	    function style_search_form(form) {
	        var dialog = form.closest('.ui-jqdialog');
	        var buttons = dialog.find('.EditTable')
	        buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
	        buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
	        buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
	    }
	    
	    function beforeDeleteCallback(e) {
	        var form = $(e[0]);
	        if(form.data('styled')) return false; 
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_delete_form(form);
	        form.data('styled', true);
	    }
	    
	    function beforeEditCallback(e) {
	        var form = $(e[0]);
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_edit_form(form);
	    }

	    function styleCheckbox(table) {}
	    
	    function updateActionIcons(table) {}
	    
	    function updatePagerIcons(table) {
	        var replacement = {
	            'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
	            'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
	            'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
	            'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
	        };
	        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
	            var icon = $(this);
	            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
	            if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
	        })
	    }

	    function enableTooltips(table) {
	        $('.navtable .ui-pg-button').tooltip({container:'body'});
	        $(table).find('.ui-pg-div').tooltip({container:'body'});
	    }

	    $(document).one('ajaxloadstart.page', function(e) {
	        $(grid_selector).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin

	/*jqgrid table 2 buscador*/    
	jQuery(function($) {
	    var grid_selector2 = "#table2";
	    var pager_selector2 = "#pager2";
	    
	    $(window).on('resize.jqGrid', function() {
			$(grid_selector2).jqGrid( 'setGridWidth', $("#myModal .modal-dialog").width()-30);
	    }).trigger('resize');  

	    var parent_column = $(grid_selector2).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selector2).jqGrid( 'setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // buscador facturas
	    jQuery(grid_selector2).jqGrid({	 
	    	datatype: "xml",
		    url: 'data/factura_compra/xml_factura_compra.php',         
	        colNames: ['ID','IDENTIFICACIÓN','PROVEEDOR','SERIE','FECHA EMISIÓN','TOTAL','ACCIÓN'],
	        colModel:[ 
			    {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name:'P.identificacion',index:'P.identificacion', frozen:true, align:'left', search:true, hidden: false},
	            {name:'P.empresa',index:'P.empresa',frozen : true,align:'left', search:true, width: '250px'},
	            {name:'F.serie',index:'F.serie',frozen : true, hidden: false, align:'left', search:true,width: ''},
	            {name:'fecha_emision',index:'fecha_emision',frozen : true, align:'left', search:false,width: '120px'},
	            {name:'total_pagar',index:'total_pagar',frozen : true, align:'left', search:false,width: '100px'},
	            {name:'accion', index:'accion', editable: false, search:false, hidden: false, frozen: true, editrules: {required: true}, align: 'center', width: '80px'},
	        ],          
	        rowNum: 10,
	        rowList: [10,20,30],
	        width: 600,
	        height: 330,
	        pager: pager_selector2,        
	        sortname: 'id',
	        sortorder: 'asc',
	        autoencode: false,
	        shrinkToFit: false,
	        altRows: true,
	        multiselect: false,
	        viewrecords: true,
	        loadComplete: function() {
	            var table = this;
	            setTimeout(function() {
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	            }, 0);
	        },
	        gridComplete: function() {
				var ids = jQuery(grid_selector2).jqGrid('getDataIDs');
				for(var i = 0;i < ids.length;i++) {
					var id_factura = ids[i];
					pdf = "<a onclick=\"angular.element(this).scope().methodspdf('"+id_factura+"')\" title='Reporte Factura Compra' ><i class='fa fa-file-pdf-o red2' style='cursor:pointer; cursor: hand'> PDF</i></a>"; 
					jQuery(grid_selector2).jqGrid('setRowData',ids[i],{accion:pdf});
				}	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector2).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector2).jqGrid('getRowData',gsr);
            	$("#table").jqGrid("clearGridData", true);	

            	//$.ajax({
				//	url: 'data/factura_compra/app.php',
				//	type: 'post',
				//	data: {llenar_cabezera_factura:'llenar_cabezera_factura',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {					
				//		$('#id_factura').val(data.id_factura);
				//		$('#fecha_emision').val(data.fecha_emision);
				//		$('#serie').val(data.secuencial);
				//		$('#id_proveedor').val(data.id_proveedor);
				//		$('#ruc').val(data.identificacion);
				//		$('#razon_social').val(data.razon_social);
				//		$('#telefono').val(data.telefono);
				//		$('#direccion').val(data.direccion);
				//		$('#correo').val(data.correo);
				//		$('#fecha_registro').val(data.fecha_registro);
				//		$('#fecha_caducidad').val(data.fecha_caducidad);
				//		$('#fecha_cancelacion').val(data.fecha_cancelacion);
				//		$('#autorizacion').val(data.autorizacion);
				//		$("#select_forma_pago").select2('val', data.id_forma_pago).trigger("change");

				//		$('#subtotal').val(data.subtotal);
				//		$('#tarifa').val(data.tarifa);
				//		$('#tarifa_0').val(data.tarifa0);
				//		$('#iva').val(data.iva);
				//		$('#otros').val(data.descuento);
				//		$('#total_pagar').val(data.total_pagar);
				//	}
				//});

				//$.ajax({
				//	url: 'data/factura_compra/app.php',
				//	type: 'post',
				//	data: {llenar_detalle_factura:'llenar_detalle_factura',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {
				//		var tama = data.length;
				//		var descuento = 0;
	            //      var desc = 0;
	            //      var precio = 0;
	            //      var multi = 0;
	            //      var flotante = 0;
	            //      var resultado = 0;
	            //        var total = 0;
	            //        var suma_total = 0;

				//		for (var i = 0; i < tama; i = i + 9) {
				//			desc = data[i + 5];
                //            precio = (parseFloat(data[i + 4])).toFixed(3);
                //            multi = (parseFloat(data[i + 3]) * parseFloat(precio)).toFixed(3);
                //            descuento = ((multi * parseFloat(desc)) / 100);
                //            flotante = parseFloat(descuento);
                //            resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
                //            total = (multi - resultado).toFixed(3);

				//			var datarow = {
                //                id: data[i], 
                //                codigo: data[i + 1], 
                //                detalle: data[i + 2], 
                //                cantidad: data[i + 3], 
                //                precio_u: precio, 
                //                descuento: desc,
                //                cal_des: resultado, 
                //                total: total,
                //                iva: data[i + 7],
                //                incluye: data[i + 8]
                //            };

                //            jQuery("#table").jqGrid('addRowData',data[i],datarow);
				//		}
				//	}
				//});  

				//$('#myModal').modal('hide'); 
		        $('#btn_0').attr('disabled', true);
		        $('#btn_3').attr('disabled', false);           
	        },
	        caption: "LISTA FACTURAS COMPRAS"
	    });

	    $(window).triggerHandler('resize.jqGrid');//cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector2).jqGrid('navGrid', pager_selector2, {   //navbar options
	        edit: false,
	        editicon: 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon: 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon: 'ace-icon fa fa-trash-o red',
	        search: true,
	        searchicon: 'ace-icon fa fa-search orange',
	        refresh: true,
	        refreshicon: 'ace-icon fa fa-refresh green',
	        view: false,
	        viewicon: 'ace-icon fa fa-search-plus grey'
	    },
	    {	        
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        closeAfterAdd: true,
	        recreateForm: true,
	        viewPagerButtons: false,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
	            .wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            if(form.data('styled')) return false;      
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_delete_form(form); 
	            form.data('styled', true);
	        },
	        onClick: function(e) {}
	    },
	    {
	        recreateForm: true,
	        afterShowSearch: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	            style_search_form(form);
	        },
	        afterRedraw: function() {
	            style_search_filters($(this));
	        },

	        //multipleSearch: true
	        overlay: false,
	        sopt: ['eq', 'cn'],
            defaultSearch: 'eq',            	       
	    },
	    {
	        //view record form
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	        }
	    })

	    function style_edit_form(form) {
	        form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
	        form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');

	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
	        buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')
	        
	        buttons = form.next().find('.navButton a');
	        buttons.find('.ui-icon').hide();
	        buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
	        buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');       
	    }

	    function style_delete_form(form) {
	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
	        buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
	    }
	    
	    function style_search_filters(form) {
	        form.find('.delete-rule').val('X');
	        form.find('.add-rule').addClass('btn btn-xs btn-primary');
	        form.find('.add-group').addClass('btn btn-xs btn-success');
	        form.find('.delete-group').addClass('btn btn-xs btn-danger');
	    }

	    function style_search_form(form) {
	        var dialog = form.closest('.ui-jqdialog');
	        var buttons = dialog.find('.EditTable')
	        buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
	        buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
	        buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
	    }
	    
	    function beforeDeleteCallback(e) {
	        var form = $(e[0]);
	        if(form.data('styled')) return false; 
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_delete_form(form);
	        form.data('styled', true);
	    }
	    
	    function beforeEditCallback(e) {
	        var form = $(e[0]);
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_edit_form(form);
	    }

	    function styleCheckbox(table) {}
	    
	    function updateActionIcons(table) {}
	    
	    function updatePagerIcons(table) {
	        var replacement = {
	            'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
	            'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
	            'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
	            'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
	        };
	        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
	            var icon = $(this);
	            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
	            if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
	        })
	    }

	    function enableTooltips(table) {
	        $('.navtable .ui-pg-button').tooltip({container:'body'});
	        $(table).find('.ui-pg-div').tooltip({container:'body'});
	    }

	    $(document).one('ajaxloadstart.page', function(e) {
	        $(grid_selector2).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin

	/*jqgrid table 4 local*/    
	jQuery(function($) {
	    var grid_selector4 = "#table4";
	    var pager_selector4 = "#pager4";
	    
	    $(window).on('resize.jqGrid', function() {
			$(grid_selector4).jqGrid('setGridWidth', $("#grid_container2").width(), true);
	    }).trigger('resize');  

	    var parent_column = $(grid_selector4).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				setTimeout(function() {
					$(grid_selector4).jqGrid( 'setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // tabla local formas pago
	    jQuery(grid_selector4).jqGrid({
	        datatype: "local",
	        colNames: ['','ID','CÓDIGO','DESCRIPCIÓN','TOTAL','PLAZO','TIEMPO'],
	        colModel:[  
	        	{name:'myac', width: 50, fixed: true, sortable: false, resize: false, formatter: 'actions',
			        formatoptions: {keys: false, delbutton: true, editbutton: false}
			    }, 
			    {name: 'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name: 'codigo', index: 'codigo', editable: false, search: false, hidden: false, editrules: {edithidden: false}, align: 'center', frozen: true, width: 100},
	            {name: 'descripcion', index: 'descripcion', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 290},
	            {name: 'valor', index: 'valor', editable: true, frozen: true, editrules: {required: true}, align: 'center', width: 70, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return numeros(e)})}}}, 
	            {name: 'plazo', index: 'plazo', editable: true, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 110, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return punto(e)})}}}, 
	            {name: 'unidad', index: 'unidad', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 80},
	        ],          
	        rowNum: 10, 
	        rowList: [10,20,30],
	        width: 600,
	        height: 100,
	        pager: pager_selector4,        
	        sortname: 'id',
	        sortorder: 'asc',
	        autoencode: false,
	        shrinkToFit: false,
	        altRows: true,
	        multiselect: false,
	        viewrecords: true,
	        shrinkToFit: true,
	        delOptions: {
            	modal: true,
	            jqModal: true,
	            onclickSubmit: function(rp_ge, rowid) {
	                var id = jQuery(grid_selector4).jqGrid('getGridParam', 'selrow');
	                jQuery(grid_selector4).jqGrid('restoreRow', id);
	                var ret = jQuery(grid_selector4).jqGrid('getRowData', id);
                
	                var su = jQuery(grid_selector4).jqGrid('delRowData', rowid);
                   	if (su == true) {
                       rp_ge.processing = true;
                       $(".ui-icon-closethick").trigger('click'); 
                    }
	                return true;
	            },
	            processing: true
	        },
	        loadComplete: function() {
	            var table = this;
	            setTimeout(function() {
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	            }, 0);
	        },
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector4).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector4).jqGrid('getRowData',gsr);	            
	        },
	    });

	    $(window).triggerHandler('resize.jqGrid');//cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector4).jqGrid('navGrid', pager_selector4, {   //navbar options
	        edit: false,
	        editicon: 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon: 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon: 'ace-icon fa fa-trash-o red',
	        search: false,
	        searchicon: 'ace-icon fa fa-search orange',
	        refresh: false,
	        refreshicon: 'ace-icon fa fa-refresh green',
	        view: false,
	        viewicon: 'ace-icon fa fa-search-plus grey'
	    },
	    {	        
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        closeAfterAdd: true,
	        recreateForm: true,
	        viewPagerButtons: false,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
	            .wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            if(form.data('styled')) return false;      
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_delete_form(form); 
	            form.data('styled', true);
	        },
	        onClick: function(e) {}
	    },
	    {
	        recreateForm: true,
	        afterShowSearch: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	            style_search_form(form);
	        },
	        afterRedraw: function() {
	            style_search_filters($(this));
	        },

	        //multipleSearch: true
	        overlay: false,
	        sopt: ['eq', 'cn'],
            defaultSearch: 'eq',            	       
	    },
	    {
	        //view record form
	        recreateForm: true,
	        beforeShowForm: function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
	        }
	    })

	    function style_edit_form(form) {
	        form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
	        form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');

	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
	        buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')
	        
	        buttons = form.next().find('.navButton a');
	        buttons.find('.ui-icon').hide();
	        buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
	        buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');       
	    }

	    function style_delete_form(form) {
	        var buttons = form.next().find('.EditButton .fm-button');
	        buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
	        buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
	        buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
	    }
	    
	    function style_search_filters(form) {
	        form.find('.delete-rule').val('X');
	        form.find('.add-rule').addClass('btn btn-xs btn-primary');
	        form.find('.add-group').addClass('btn btn-xs btn-success');
	        form.find('.delete-group').addClass('btn btn-xs btn-danger');
	    }

	    function style_search_form(form) {
	        var dialog = form.closest('.ui-jqdialog');
	        var buttons = dialog.find('.EditTable')
	        buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
	        buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
	        buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
	    }
	    
	    function beforeDeleteCallback(e) {
	        var form = $(e[0]);
	        if(form.data('styled')) return false; 
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_delete_form(form);
	        form.data('styled', true);
	    }
	    
	    function beforeEditCallback(e) {
	        var form = $(e[0]);
	        form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	        style_edit_form(form);
	    }

	    function styleCheckbox(table) {}
	    
	    function updateActionIcons(table) {}
	    
	    function updatePagerIcons(table) {
	        var replacement = {
	            'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
	            'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
	            'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
	            'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
	        };
	        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
	            var icon = $(this);
	            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
	            if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
	        })
	    }

	    function enableTooltips(table) {
	        $('.navtable .ui-pg-button').tooltip({container:'body'});
	        $(table).find('.ui-pg-div').tooltip({container:'body'});
	    }

	    $(document).one('ajaxloadstart.page', function(e) {
	        $(grid_selector4).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin
});