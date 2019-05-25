app.controller('nota_creditoController', function ($scope, $interval, loaddatosSRI) {

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

		// mask
		$('#telefono').mask('(999) 999-9999');
		// fin

		// tabs nota credito
		$( "#tabNota" ).click(function(event) {
			event.preventDefault();  
		});

		$("#tabNota").on('shown.bs.tab', function(e) {
			$('.chosen-select').each(function() {
				var $this = $(this);
				$this.next().css({'width': $this.parent().width()});
			})	
		});
		// fin

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
		$("#select_tipo_precio,#select_estado").select2({
		  	// allowClear: true
		});
		// fin

		// event ctrl+b abrir
		Mousetrap.bind(['ctrl+b'], function(e) {
			$('#myModal').modal('show'); 	    
		});
		// fin

		// funcion autocompletar la serie
		function autocompletar() {
		    var temp = "";
		    var serie = $("#serie").val();
		    for (var i = serie.length; i < 9; i++) {
		        temp = temp + "0";
		    }
		    return temp;
		}
		// fin

		// consultar secuencial
		function consultar_secuencial() {
			$.ajax({
				url: 'data/nota_credito/app.php',
				type: 'post',
				data: {cargar_secuencial:'cargar_secuencial'},
				dataType: 'json',
				success: function(data) {
					if(data.serie == null) {
						swal({
			                title: "Lo sentimos secuencial no creado",
			                type: "warning",
			            });
			            $("#btn_0").attr("disabled", true);	
					} else {
						var res = parseInt(data.serie);
						res = res + 1;

						$("#serie").val(res);
						var a = autocompletar(res);
						var validado = a + "" + res;
						$("#serie").val(validado);
					}
				}
			});
		}
		// fin

		// llenar establecimiento / emision / token
		function llenar_info() {
			$.ajax({
				url: 'data/nota_credito/app.php',
				type: 'post',
				data: {llenar_infomacion:'llenar_infomacion'},
				dataType: 'json',
				success: function(data) {
					if (data.token == null) {
						$.gritter.add({
							title: 	'<span>¡Información! </span>',
							text: 	'<span class=""></span>'
									+' <span> Le recordamos que para emitir documentos electrónicos debe registrar su Firma Electrónica desde Empresa.</span>',
							image: 	'dist/images/file_ok.png', 
							sticky: false,
							time: 10000,												
						});
						$("#btn_0").attr("disabled", true);
					} else {
						$("#btn_0").attr("disabled", false);
						$scope.establecimiento = data.establecimiento;
						$scope.emision = data.emision;	
					}
				}
			});
		}
		// fin

		// select tipo precio 
	    $("#select_tipo_precio").change(function () { 
	    	var valor = $(this).val();

	    	if (valor == "MINORISTA") {
	    		limpiar_input();
	    		$('#codigo_barras').focus();
	    	} else {
	    		if (valor == "MAYORISTA") {
	    			limpiar_input();
	    			$('#codigo_barras').focus();
	    		}
	    	}
	    });	
	    // fin

	    // limpiar ruc
	    $("#ruc").keyup(function(e) {
		    if($('#ruc').val() == '') {
		    	$('#id_cliente').val('');
		    	$('#razon_social').val('');
		    	$('#direccion').val('');
		    	$('#telefono').val('');
		    	$('#correo').val('');
		    }
		});
	    // fin

	    // limpiar cliente
	    $("#razon_social").keyup(function(e) {
		    if($('#razon_social').val() == '') {
		    	$('#id_cliente').val('');
		    	$('#ruc').val('');
		    	$('#direccion').val('');
		    	$('#telefono').val('');
		    	$('#correo').val('');
		    }
		});
	    // fin

	    // busqueda cliente ruc
		$("#ruc").keyup(function(e) {
			var tipo = 'identificacion';

	     	$("#ruc").autocomplete({
	     		source: "data/buscador/clientes.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_cliente").val(ui.item.id); 
		            $("#ruc").val(ui.item.value); 
		            $("#razon_social").val(ui.item.razon_social);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_cliente").val(ui.item.id); 
		            $("#ruc").val(ui.item.value); 
		            $("#razon_social").val(ui.item.razon_social);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
		});
		// fin

		// busqueda razon_social cliente 
		$("#razon_social").keyup(function(e) {
			var tipo = 'razon_social';

	     	$("#razon_social").autocomplete({
	     		source: "data/buscador/clientes.php?tipo_busqueda=" + tipo,
                minLength: 1,
                focus: function(event, ui) {
	                $("#id_cliente").val(ui.item.id); 
		            $("#razon_social").val(ui.item.value); 
		            $("#ruc").val(ui.item.ruc);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);

                	return false;
                },
                select: function(event, ui) {
                	$("#id_cliente").val(ui.item.id); 
		            $("#razon_social").val(ui.item.value); 
		            $("#ruc").val(ui.item.ruc);
		            $("#telefono").val(ui.item.telefono);
		            $("#direccion").val(ui.item.direccion);
		            $("#correo").val(ui.item.correo);
	                
	                return false;
                }

                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<a>" + item.value + "</a>")
                .appendTo(ul);
            };
		});
		// fin

		// limpiar serie nota credito
	    $("#serie_factura").keyup(function(e) {
		    if($('#serie_factura').val() == '') {
		    	$('#id_factura').val('');
		    }
		});
	    // fin

		// buscar serie nota credito
     	$("#serie_factura").keyup(function(e) {

	     	$("#serie_factura").autocomplete({
	            source: "data/buscador/series.php",
	            minLength: 1,
	            focus: function(event, ui) {
	                $("#id_factura").val(ui.item.id); 
		            $("#serie_factura").val(ui.item.value);

	            	return false;
	            },
	            select: function(event, ui) {
	            	$("#id_factura").val(ui.item.id); 
		            $("#serie_factura").val(ui.item.value);

	                return false;
	            }

	            }).data("ui-autocomplete")._renderItem = function(ul, item) {
	            return $("<li>")
	            .append("<a>" + item.value + "</a>")
	            .appendTo(ul);
	        };
		});
		// fin

		// comparar series nota credito
        $("#serie").keyup(function() {
	        $.ajax({
	            type: "POST",
	            url: "data/nota_credito/app.php",
	            data: {comparar_serie:'comparar_serie',serie: $("#serie").val()},
	            success: function(data) {
	                var val = data;
	                if (val == 1) {
	                    $("#serie").val('');
	                    $("#serie").focus();
	                    $.gritter.add({
							title: 'Error... La Serie ya fue Registrada',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});
					}
				}
			});
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
	        var precio = $("#select_tipo_precio").val(); 
	        
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
	     	var precio = $("#select_tipo_precio").val();

	     	$("#codigo").autocomplete({
	     		source: "data/buscador/productos.php?tipo_precio=" + precio + "&tipo_busqueda=" + tipo,
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
	     	var precio = $("#select_tipo_precio").val();

	     	$("#producto").autocomplete({
	     		source: "data/buscador/productos.php?tipo_precio=" + precio + "&tipo_busqueda=" + tipo,
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

	    /*---agregar a la tabla---*/
	  	$("#cantidad").on("keypress", function(e) {
		    if (e.keyCode == 13) { // tecla del alt para el entrer poner 13 
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
		                    if ($("#precio_venta").val() == "") {
		                        $("#precio_venta").focus();
		                        $.gritter.add({
		                            title: 'Error... Ingrese un Precio',
		                            class_name: 'gritter-error gritter-center',
		                            time: 1000,
		                        });
		                    } else {
		                        var filas = jQuery("#table").jqGrid("getRowData");
		                        var descuento = 0;
		                        var desc = 0;
		                        var precio = 0;
		                        var multi = 0;
		                        var flotante = 0;
		                        var resultado = 0;
		                        var total = 0;
		                        var repe = 0;

		                        if (filas.length == 0) {
                                    if ($("#descuento").val() != "") {
                                        desc = $("#descuento").val();
                                        precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                        multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                        descuento = ((multi * parseFloat(desc)) / 100);
                                        flotante = parseFloat(descuento);
                                        resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                        total = (multi - resultado).toFixed(2);
                                    } else {
                                        desc = 0;
                                        precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                        multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                        descuento = ((multi * parseFloat(desc)) / 100);
                                        flotante = parseFloat(descuento);
                                        resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                        total = (parseFloat(multi)).toFixed(2);
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
                                            precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                            multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                            descuento = ((multi * parseFloat(desc)) / 100);
                                            flotante = parseFloat(descuento);
                                            resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                            total = (multi - resultado).toFixed(2);
                                        } else {
                                            desc = 0;
                                            precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                            multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                            descuento = ((multi * parseFloat(desc)) / 100);
                                            flotante = parseFloat(descuento);
                                            resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                            total = (parseFloat(multi)).toFixed(2);
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
                                        if (filas.length < 26) {
                                            if ($("#descuento").val() != "") {
                                                desc = $("#descuento").val();
                                                precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                                multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                                descuento = ((multi * parseFloat(desc)) / 100);
                                                flotante = parseFloat(descuento);
                                                resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                                total = (multi - resultado).toFixed(2);
                                            } else {
                                                desc = 0;
                                                precio = (parseFloat($("#precio_venta").val())).toFixed(2);
                                                multi = (parseFloat($("#cantidad").val()) * parseFloat(precio)).toFixed(3);
                                                descuento = ((multi * parseFloat(desc)) / 100);
                                                flotante = parseFloat(descuento);
                                                resultado = (Math.round(flotante * Math.pow(10, 2)) / Math.pow(10, 2)).toFixed(3);
                                                total = (parseFloat(multi)).toFixed(2);
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
                                        } else {
                                            $.gritter.add({
                                                title: 'Error... Alcanzo el limite Máximo de Items',
                                                class_name: 'gritter-error gritter-center',
                                                time: 1000,
                                            });
                                        }
                                    }
                                }

                                // proceso iva
                                var fil = jQuery("#table").jqGrid("getRowData");
                                var subtotal = 0;
                                var sub = 0;
                                var sub1 = 0;
                                var sub2 = 0;
                                var iva = 0;
                                var iva1 = 0;
                                var iva2 = 0;
                                var suma_total = 0;

                                for (var t = 0; t < fil.length; t++) {
                                    var dd = fil[t];
                                    if (dd['iva'] != 0) {
                                        if (dd['incluye'] == "NO") {
                                            subtotal = parseFloat(dd['total']);
                                            sub1 = parseFloat(subtotal);
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
                                            suma_total = suma_total + parseFloat(dd['cantidad']);
                                        } else {
                                            if (dd['incluye'] == "SI") {
                                                subtotal = parseFloat(dd['total']);
                                                sub2 = parseFloat(subtotal / ((dd['iva'] / 100) + 1)).toFixed(3);
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
                                                suma_total = suma_total + parseFloat(dd['cantidad']);
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
                                            suma_total = suma_total + parseFloat(dd['cantidad']);
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
                                $("#items").val(fil.length);
                                $("#num").val(suma_total);
                                $("#codigo_barras").focus();
                                // fin	
		                    }
		                }
		            }
		        }
		    }
		});
		// fin

		// validacion punto
		function ValidPun() {
		    var key;
		    if (window.event) {
		        key = event.keyCode;
		    } else if (event.which) {
		        key = event.which;
		    }

		    if (key < 48 || key > 57) {
		        if (key == 46 || key == 8) {
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

		// abrir en una nueva ventana reporte Nota Crédito
		$scope.methodspdf = function(id) { 
			//var myWindow = window.open('data/reportes/nota_credito.php?id='+id,'popup','width=900,height=650');
			var myWindow = window.open('data/nota_credito/generarPDF.php?id='+id,'popup','width=900,height=650');
		} 
		// fin

		// recargar formulario
		function redireccionar() {
			setTimeout(function() {
			    location.reload(true);
			}, 1000);
		}
		// fin

		// reload
		function reload() {
    		setTimeout(function() {
        	location.reload()
    		}, 100);
		}
		// fin

		// inicio llamado funciones
		llenar_info();
		consultar_secuencial();
		$("#serie").keypress(ValidNum);
		$("#serie").attr("maxlength", "9");
		$("#ruc").keypress(ValidNum);
		$("#ruc").attr("maxlength", "13");
		$("#ruc").focus();
		$("#serie_factura").keypress(ValidNum);
		$("#cantidad").keypress(ValidNum);
		$("#precio_venta").keypress(ValidPun);

		//$("#btn_1").attr("disabled", true);
		$("#btn_2").attr("disabled", true);
		$("#btn_3").attr("disabled", true);
		// fin

		// guardar Nota Crédito
		$('#btn_0').click(function() {
			var formulario = $("#form_nota").serialize();
			var submit = "Guardar";
			var filas = jQuery("#table").jqGrid("getRowData");

			if($('#serie').val() == '') {
				$('#serie').focus();
				$.gritter.add({
					title: 'Ingrese una Serie',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {	
				if($('#id_cliente').val() == '') {
					$('#ruc').focus();
					$.gritter.add({
						title: 'Seleccione un Cliente',
						class_name: 'gritter-error gritter-center',
						time: 1000,
					});	
				} else {
					if($('#serie_factura').val() == '') {
						$('#serie_factura').focus();
						$.gritter.add({
							title: 'Seleccione una Factura',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});	
					} else {
						if($('#motivo').val() == '') {
							$.gritter.add({
								title: 'Ingrese un Motivo',
								class_name: 'gritter-error gritter-center',
								time: 1000,
							});
							$('#motivo').focus();	
						} else {
							if($('#select_tipo_precio').val() == '') {
								$.gritter.add({
									title: 'Seleccione un Tipo Precio',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});	
							} else {
								if(filas.length == 0) {
					                $.gritter.add({
										title: 'Ingrese productos a la Nota Crédito',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});
					                $('#codigo_barras').focus();	
					            } else {
					            	$("#btn_0").attr("disabled", true);
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

					                $.ajax({
					                    type: "POST",
					                    url: "data/nota_credito/app.php",
					                    data: formulario +"&btn_guardar=" + submit + "&campo1=" + string_v1 + "&campo2=" + string_v2 + "&campo3=" + string_v3 + "&campo4=" + string_v4 + "&campo5=" + string_v5,
					                    dataType: 'json',
						                beforeSend: function(response) {
						                	$.blockUI({ css: { 
						                        border: 'none', 
						                        padding: '10px',
						                        backgroundColor: '#000', 
						                        '-webkit-border-radius': '10px', 
						                        '-moz-border-radius': '10px', 
						                        opacity: 0.5, 
						                        color: '#fff' 
						                        },
						                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
						                    });
						                },
						                success: function(response) {
									    	$.unblockUI();

											col_1 = $($("#result").children().children().children().next()[0]).children().children()[0];
											col_2 = $($("#result").children().children().children().next()[0]).children().children()[1];				    	
											$(col_1).html('');
											$(col_2).html('');
									    	if(response.estado == 1) {					
												$(col_1).append("<b>La información se encuentra Guardada</b></br>");
												$(col_1).append("<b>El Documento esta Generado</b></br>");
												$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
												$(col_1).append("<b>Documentos enviados al correo del cliente</b></br>");
												$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
												$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
												$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
												$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
												$('#result').modal('show');
												$("#pdf").on("click",function() {
													window.open('data/nota_credito/generarPDF.php?id='+response.id, '_blank');
													reload();
												});
												$("#cerrar").on("click",function() {
													reload();
												});
											}else{
												if(response.estado == 2) {														
													$(col_1).append("<b>La información se encuentra Guardada</b></br>");
													$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
													$(col_1).append("<b>El Documento esta Generado</b></br>");							
													$(col_1).append("<b>Documentos enviados al correo del cliente</b></br>");
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
													$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
													$('#result').modal('show');
													$("#pdf").on("click",function() {								
														reload();
													});
													$("#cerrar").on("click",function() {
														reload();
													});
												} else {
													if(response.estado == 3) {																
														$(col_1).append("<b>La información se encuentra Guardada</b></br>");
														$(col_1).append("<b>El Documento esta Generado</b></br>");
														$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
														$(col_1).append("<b>Documentos enviados al email del cliente</b></br>");
														$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
														$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
														$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
														$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
														$('#result').modal('show');
														$("#pdf").on("click",function() {
															window.open('data/nota_credito/generarPDF.php?id='+response.id, '_blank');
															reload();
														});
														$("#cerrar").on("click",function() {
															reload();
														});
													} else {
														if(response.estado == 4) {																		
															$(col_1).append("<b>Error en la base de datos</b></br>");								
															$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
															$('#result').modal('show');
															$("#pdf").on("click",function() {										
																reload();
															});
															$("#cerrar").on("click",function() {
																reload();
															});
														} else {
															if(response.estado == 5) {
																$(col_1).append("<b>Error el Archivo para la firma no existe</b></br>");									
																$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');									
																$('#result').modal('show');
																$("#pdf").on("click",function() {										
																	reload();
																});
																$("#cerrar").on("click",function() {
																	reload();
																});
															} else {
																if(response.estado == 6) {
																	$(col_1).append("<b>Error! La clave del certificado es incorrecta</b></br>");									
																	$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');									
																	$('#result').modal('show');
																	$("#pdf").on("click",function() {										
																		reload();
																	});
																	$("#cerrar").on("click",function() {
																		reload();
																	});
																} else {
																	if(response.estado == 7) {																				
																		$(col_1).append("<b>La información se encuentra Guardada</b></br>");
																		$(col_1).append("<b>Error en el web service del SRI vuelva a intentarlo </b></br>");
																		$(col_1).append("<b>El Documento se encuentra Rechazadp</b></br>");
																		$(col_1).append("<b>Documentos enviados al email del cliente</b></br>");
																		$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$('#result').modal('show');
																		$("#pdf").on("click",function() {										
																			reload();
																		});
																		$("#cerrar").on("click",function() {
																			reload();
																		});
																	} else {
																		$(col_1).append("<b>La información se encuentra Guardada</b></br>");
																		$(col_1).append("<b>Error en el web service del SRI vuelva a intentarlo </b></br>");
																		$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
																		$(col_1).append("<b>Documentos enviados al email del cliente</b></br>");
																		$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																		$('#result').modal('show');
																		$("#pdf").on("click",function() {										
																			reload();
																		});
																		$("#cerrar").on("click",function() {
																			reload();
																		});
																	}	
																}
															}
																
														}		
													}			
												}		
											}	
						                }
					                });
								}
							}
						}
					}				       
				}
			}	
		});
		// fin

		// anular nota credito
		$('#btn_2').click(function() {
			$("#btn_2").attr("disabled", true);
			var formulario = $("#form_nota").serialize();
			var submit = "Anular";
			var filas = jQuery("#table").jqGrid("getRowData");

            var v1 = new Array();
            var v2 = new Array();

            var string_v1 = "";
            var string_v2 = "";

            for (var i = 0; i < filas.length; i++) {
                var datos = filas[i];
                v1[i] = datos['id'];
                v2[i] = datos['cantidad'];
            }

            for (i = 0; i < filas.length; i++) {
                string_v1 = string_v1 + "|" + v1[i];
                string_v2 = string_v2 + "|" + v2[i];
            }

            $.ajax({
                type: "POST",
                url: "data/nota_credito/app.php",
                data: formulario +"&btn_anular=" + submit + "&campo1=" + string_v1 + "&campo2=" + string_v2,
                success: function(data) {
                	var val = data;
                	if(val == 1) {
	            		bootbox.alert("Gracias! Por su Información Nota Crédito Anulada Correctamente!", function() {
							location.reload();
						});
					}		
                }
            });	
		});
		// fin

		// reimprimir nota credito
		$('#btn_3').click(function() {
			if($('#id_nota').val() == '') {
				$.gritter.add({
					title: 'Seleccione Nota Crédito a Reimprimir',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {
				var id = $('#id_nota').val();
				var myWindow = window.open('data/reportes/nota_credito.php?hoja=A5&id='+id,'popup','width=900,height=650');
			}
		});
		// fin

		// actualizar formulario
		$('#btn_4').click(function() {
			location.reload();
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
	        colNames: ['','ID','CÓDIGO','DESCRIPCIÓN','CANT.','PVP','DESC.','CALCULADO','TOTAL','IVA','INCLUYE'],
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
	            {name: 'incluye', index: 'incluye', editable: false, hidden: true, frozen: true, editrules: {required: true}, align: 'center', width: 90},
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
	                var suma_total = 0;

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
		                        suma_total = parseFloat($("#num").val()) - parseFloat(ret.cantidad);
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
		                            suma_total = parseFloat($("#num").val()) - parseFloat(ret.cantidad);
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
	                            suma_total = parseFloat($("#num").val()) - parseFloat(ret.cantidad);
	                        }
	                    }
	                }

	                total_total = parseFloat(total_total) + (parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(iva12));
	                total_total = parseFloat(total_total).toFixed(2);

	                var item = filas.length - 1;
	                $("#subtotal").val(subtotal_total);
	                $("#tarifa_0").val(subtotal0);
	                $("#tarifa").val(subtotal12);
	                $("#iva").val(iva12);
	                $("#otros").val(descu_total);
	                $("#total_pagar").val(total_total);
	                $("#items").val(item);
                	$("#num").val(suma_total);
                
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

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector).jqGrid('navGrid', pager_selector, { // navbar options
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
	    var grid_selector1 = "#table1";
	    var pager_selector1 = "#pager1";
	    
	    $(window).on('resize.jqGrid', function() {
			$(grid_selector1).jqGrid('setGridWidth', $("#myModal .modal-dialog").width()-30);
	    }).trigger('resize');  

	    var parent_column = $(grid_selector1).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selector1).jqGrid( 'setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // buscador notas credito 
	    jQuery(grid_selector1).jqGrid({	 
	    	datatype: "xml",
		    url: 'data/nota_credito/xml_nota_credito.php',         
	        colNames: ['ID','IDENTIFICACIÓN','RAZÓN SOCIAL','SERIE','FECHA EMISIÓN','TOTAL','ACCIÓN'],
	        colModel:[ 
			    {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name:'identificacion',index:'identificacion', frozen:true, align:'left', search:true, hidden: false},
	            {name:'razon_social',index:'razon_social',frozen : true,align:'left', search:true, width: '250px'},
	            {name:'secuencial',index:'secuencial',frozen : true, hidden: false, align:'left', search:true,width: ''},
	            {name:'fecha_emision',index:'fecha_emision',frozen : true, align:'left', search:false,width: '120px'},
	            {name:'total_pagar',index:'total_pagar',frozen : true, search:false, align:'left',width: '80px'},
	            {name:'accion', index:'accion', editable: false, hidden: false, search:false, frozen: true, editrules: {required: true}, align: 'center', width: '80px'},
	        ],          
	        rowNum: 10,       
	        width: 600,
	        shrinkToFit: false,
	        height: 330,
	        rowList: [10,20,30],
	        pager: pager_selector1,        
	        sortname: 'id',
	        sortorder: 'desc',
	        autoencode: false,
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
				var ids = jQuery(grid_selector1).jqGrid('getDataIDs');
				for(var i = 0;i < ids.length;i++) {
					var id_nota = ids[i];
					pdf = "<a onclick=\"angular.element(this).scope().methodspdf('"+id_nota+"')\" title='Reporte Nota Crédito' ><i class='fa fa-file-pdf-o red2' style='cursor:pointer; cursor: hand'> PDF</i></a>"; 
					jQuery(grid_selector1).jqGrid('setRowData',ids[i],{accion:pdf});
				}	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector1).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector1).jqGrid('getRowData',gsr);
            	//$("#table").jqGrid("clearGridData", true);

            	//$.ajax({
				//	url: 'data/nota_credito/app.php',
				//	type: 'post',
				//	data: {llenar_cabezera_nota:'llenar_cabezera_nota',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {
				//		if (data != null) {
				//			$('#id_nota').val(data.id_nota);
				//			$('#fecha_emision').val(data.fecha_emision);
				//			$('#serie').val(data.secuencial);
				//			$('#id_cliente').val(data.id_cliente);
				//			$('#ruc').val(data.identificacion);
				//			$('#razon_social').val(data.razon_social);
				//			$('#direccion').val(data.direccion);
				//			$('#telefono').val(data.telefono2);
				//			$('#correo').val(data.correo);
				//			$("#select_tipo_precio").select2('val', data.tipo_precio).trigger("change");
				//			$('#serie_factura').val(data.secuencial_factura);
				//			$('#motivo').val(data.motivo);
							
				//			$('#subtotal').val(data.subtotal);
				//			$('#tarifa').val(data.tarifa);
				//			$('#tarifa_0').val(data.tarifa0);
				//			$('#iva').val(data.iva);
				//			$('#otros').val(data.descuento);
				//			$('#total_pagar').val(data.total_pagar);

				//			if(data.estado == "10") {
				//				$scope.estado = "Anulada";
		        //               $("#btn_2").attr("disabled", true);
		        //            } else {
		        //            	$scope.estado = "";
		        //                $("#btn_2").attr("disabled", false);
		        //            }
		        //        }
				//	}
				//});

				//$.ajax({
				//	url: 'data/nota_credito/app.php',
				//	type: 'post',
				//	data: {llenar_detalle_nota:'llenar_detalle_nota',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {
				//		if (data != null) {
				//			var tama = data.length;
				//			var descuento = 0;
		        //            var desc = 0;
		        //            var precio = 0;
		        //            var multi = 0;
		        //            var flotante = 0;
		        //            var resultado = 0;
		        //            var total = 0;
		        //            var suma_total = 0;

				//			for (var i = 0; i < tama; i = i + 9) {
				//				desc = data[i + 5];
	            //                precio = (parseFloat(data[i + 4])).toFixed(3);
	            //                multi = (parseFloat(data[i + 3]) * parseFloat(precio)).toFixed(3);
	            //                descuento = ((multi * parseFloat(desc)) / 100);
	            //                flotante = parseFloat(descuento);
	            //                resultado = (Math.round(flotante * Math.pow(10,2)) / Math.pow(10,2)).toFixed(3);
	            //                total = (multi - resultado).toFixed(3);

				//				var datarow = {
	            //                    id: data[i], 
	            //                    codigo: data[i + 1], 
	            //                    detalle: data[i + 2], 
	            //                    cantidad: data[i + 3], 
	            //                    precio_u: precio, 
	            //                    descuento: desc,
	            //                    cal_des: resultado, 
	            //                    total: total,
	            //                    iva: data[i + 7],
	            //                    incluye: data[i + 8]
	            //                };

	            //                jQuery("#table").jqGrid('addRowData',data[i],datarow);
	            //                suma_total = suma_total + parseFloat(data[i + 3]);
				//			}
				//			var filas = jQuery("#table").jqGrid("getRowData");
		        //            $("#items").val(filas.length);
		        //            $("#num").val(suma_total);
		        //        }
				//	}
				//});  

				//$('#myModal').modal('hide'); 
		        $('#btn_0').attr('disabled', true);
		        $('#btn_3').attr('disabled', false);           
	        },
	        caption: "LISTA NOTAS CRÉDITO"
	    });

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector1).jqGrid('navGrid', pager_selector1, { // navbar options
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
	        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
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
	        $(grid_selector1).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin

	/*jqgrid table 3 buscador comprobantes*/ 
	jQuery(function($) {
	    // buscar comprobantes
	    $("#btn_buscar").on("click",function(){
			jQuery("#table3").jqGrid('setGridParam',{url:"data/nota_credito/xml_validar.php?estado="+$("#select_estado").val(),page:1}).trigger("reloadGrid");
		});
		// fin

		var grid_selector3 = "#table3";
	    var pager_selector3 = "#pager3";
	    
	    //cambiar el tamaño para ajustarse al tamaño de la página
	    $(window).on('resize.jqGrid', function () {        
	        $(grid_selector3).jqGrid('setGridWidth', $(".tab-pane").width());
	    });
	    //cambiar el tamaño de la barra lateral collapse/expand
	    var parent_column = $(grid_selector3).closest('[class*="col-"]');
	    $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
	        if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
	            //para dar tiempo a los cambios de DOM y luego volver a dibujar!!!
	            setTimeout(function() {
	                $(grid_selector3).jqGrid('setGridWidth', parent_column.width());
	            }, 0);
	        }
	    });

	    jQuery(grid_selector3).jqGrid({	        
	        datatype: "xml",
	        url: "data/nota_credito/xml_validar.php?estado="+$("#select_estado").val(),        
	        colNames: ['ID','N° AUTORIZACIÓN','ACCIONES','ESTADO','FECHA EMISIÓN','NOMBRE COMERCIAL','FECHA AUTORIZACIÓN','CLAVE DE ACCESO','TOTAL FACTURA'],
	        colModel:[      
	            {name:'id',index:'id', align:'left',search:false,editable: true, hidden: true, editoptions: {readonly: 'readonly'}},
				{name:'autorizacion',index:'autorizacion',width:10, hidden:true},
				{name:'acciones',index:'acciones',align:'center',width:140,frozen:true,},
				{name:'estado',index:'estado',width:280,frozen:true,},					
				{name:'fechaCreacion',index:'fechaCreacion',width:140,align:'center'},
				{name:'nombreComercial',index:'nombreComercial',width:200},				
				{name:'fechaAutorizacion',index:'fechaAutorizacion', align:'center',width:170,hidden: true},
				{name:'claveAcceso',index:'claveAcceso', align:'center',hidden: true,editrules:{edithidden:true},editable:true, width:10},
				{name:'totalFactura',index:'totalFactura',width:100},
	        ],
	        rowNum: 10,
	        height: 400,          
	        rowList: [10,20,30],
	        pager: pager_selector3,
	        sortname: 'id',
	        sortorder: 'desc',
	        rownumbers: true,
	        shrinkToFit: true, 
	        scrollerbar: true,
	        altRows: true,		       
	        viewrecords: true,
	        loadComplete: function(data) {
	            $("button.boton").click(function(e) {
	            	e.preventDefault();
	
		    		var id = $(this)['context'].id;	    			    		
		    		var ids = $(this)['context']['dataset'].ids;
		    		var idxml = $(this)['context']['dataset'].xml;

		    		if(id == "btn_1") {	    			
		    			window.open('data/nota_credito/generarPDF.php?id='+ids, '_blank');
		    		} else {
		    			if(id == "btn_2") {	
		    				window.open('data/nota_credito/comprobantes/'+idxml+'.xml','_blank');
		    			} else {
		    				if(id == "btn_3") {
		    					$.blockUI({ css: { 
			                        border: 'none', 
			                        padding: '10px',
			                        backgroundColor: '#000', 
			                        '-webkit-border-radius': '10px', 
			                        '-moz-border-radius': '10px', 
			                        opacity: 0.5, 
			                        color: '#fff' 
			                        },
			                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
			                    });

		    					$.ajax({
							        type: "POST",
							        url: 'data/nota_credito/app.php',
							        data: {
										reenviarCorreo:'reenviarCorreo',
										id : ids,
										aut: idxml
									},
							        dataType: 'json',	
							        success: function(response) {
							        	$.unblockUI();
							        	$('#table3').trigger('reloadGrid');
							        			
							        	if(response == 1) {
							        		$.gritter.add({
												title: 	'<span>Mensaje de Información </span>',
												text: 	'<span class=""></span>'
														+' <span> Correo Enviado</span>',
												image: 	'dist/images/email.png', 
												sticky: false,
												time: 2000,												
											});	
							        	} else {
							        		if(response == 2) {
								        		$.gritter.add({
						                            title: 	'<span>Mensaje de Error </span>',
						                            text: 	'<span class=""></span>'
						                                	+' <span>El Destinatario no cuenta con un correo</span>',
						                            image: 	'dist/images/mail_warning.png',
						                            sticky: false,
						                            time: 2000
						                        });
						                    }
							        	}		
							        }  					        
							    });  	
		    				} else {
		    					if(id == "btn_4") {	
		    						$.blockUI({ css: { 
				                        border: 'none', 
				                        padding: '10px',
				                        backgroundColor: '#000', 
				                        '-webkit-border-radius': '10px', 
				                        '-moz-border-radius': '10px', 
				                        opacity: 0.5, 
				                        color: '#fff' 
				                        },
				                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
				                    });

			    					$.ajax({
								        type: "POST",
								        url: 'data/nota_credito/app.php',
								        data: {
											generarArchivos:'generarArchivos',
											id : ids,
											aut: idxml
										},
								        dataType: 'json',
								        success: function(response) {
								        	$.unblockUI();
								        	$('#table3').trigger( 'reloadGrid');

								        	if(response == 1) {										        		
								        		$.gritter.add({
													title: 	'<span>Mensaje de Información </span>',
													text: 	'<span class=""></span>'
															+' <span> Comprobante Autorizado</span>',
													image: 	'dist/images/file_ok-1.png', 
													sticky: false,
													time: 3000,												
												});	
								        	} else {
								        		$.gritter.add({
													title: 	'<span>Mensaje de Error </span>',
													text: 	'<span class=""></span>'
															+' <span> Documentos no Generados</span>',
													image: 	'dist/images/file_error.png', 
													sticky: false,
													time: 3000,												
												});		
								        	}		
								        }        
								    });  	
			    				} else {
			    					if(id == "btn_5") {	
			    						$.blockUI({ css: { 
					                        border: 'none', 
					                        padding: '10px',
					                        backgroundColor: '#000', 
					                        '-webkit-border-radius': '10px', 
					                        '-moz-border-radius': '10px', 
					                        opacity: 0.5, 
					                        color: '#fff' 
					                        },
					                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
					                    });

				    					$.ajax({
									        type: "POST",
									        url: 'data/nota_credito/app.php',
									        data: {
												reenviarCorreo:'reenviarCorreo',
												id : ids,
												aut: idxml
											},
									        dataType: 'json',
									        success: function(response) {
									        	$.unblockUI();
								        		$('#table3').trigger( 'reloadGrid');

									        	if(response == 1) {
									        		$.gritter.add({
														title: 	'<span>Mensaje de Información </span>',
														text: 	'<span class=""></span>'
																+' <span> Correo Enviado</span>',
														image: 	'dist/images/email.png', 
														sticky: false,
														time: 3000,												
													});	
									        	} else {
									        		if(response == 2) {
										        		$.gritter.add({
								                            title: 	'<span>Mensaje de Error </span>',
								                            text: 	'<span class=""></span>'
								                                	+' <span>El Destinatario no cuenta con un correo</span>',
								                            image: 	'dist/images/mail_warning.png',
								                            sticky: false,
								                            time: 3000
								                        });
								                    }
									        	}		
									        }        
									    });  	
				    				} else {
				    					if(id == "btn_6") {
					    					$.blockUI({ css: { 
						                        border: 'none', 
						                        padding: '10px',
						                        backgroundColor: '#000', 
						                        '-webkit-border-radius': '10px', 
						                        '-moz-border-radius': '10px', 
						                        opacity: 0.5, 
						                        color: '#fff' 
						                        },
						                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
						                    });

					    					$.ajax({
										        type: "POST",
										        url: 'data/nota_credito/app.php',
										        data: {
													volverValidar:'volverValidar',
													id : ids,
													aut: idxml
												},
										        dataType: 'json',
										        success: function(response) {
										        	$.unblockUI();
										        	$('#table3').trigger( 'reloadGrid');

										        	if(response == 1) {							        		
										        		$.gritter.add({
															title: 	'<span>Mensaje de Información </span>',
															text: 	'<span class=""></span>'
																	+' <span> Información Enviada</span>',
															image: 	'dist/images/email.png', 
															sticky: false,
															time: 3000,												
														});	
										        	} else {
										        		if(response == 2) {							        		
											        		$.gritter.add({
																title: 	'<span>Mensaje de Error </span>',
																text: 	'<span class=""></span>'
																		+' <span> Documentos no Generados</span>',
																image: 	'dist/images/file_error.png', 
																sticky: false,
																time: 3000,												
															});	
											        	} else {
											        		if(response == 3) {							        		
												        		$.gritter.add({
																	title: 	'<span>Mensaje de Información </span>',
																	text: 	'<span class=""></span>'
																			+' <span> Comprobante Autorizado</span>',
																	image: 	'dist/images/file_ok-1.png', 
																	sticky: false,
																	time: 3000,												
																});	
												        	} else {
												        		if(response == 4) {							        		
													        		$.gritter.add({
																		title: 	'<span>Mensaje de Error </span>',
																		text: 	'<span class=""></span>'
																				+' <span> Archivo Rechazado Vuelva a firmar</span>',
																		image: 	'dist/images/error_file.png', 
																		sticky: false,
																		time: 3000,												
																	});	
													        	} else {										        								        		
													        		$.gritter.add({
																		title: 	'<span>Mensaje de Error </span>',
																		text: 	'<span class=""></span>'
																				+' <span> Error... WebService Verifique XML</span>',
																		image: 	'dist/images/error_file.png', 
																		sticky: false,
																		time: 3000,												
																	});												        	
													        	}
												        	}
											        	}	
										        	}	
										        }         
										    }); 	
				    					} else {
				    						if(id == "btn_7") {
				    							$.blockUI({ css: { 
							                        border: 'none', 
							                        padding: '10px',
							                        backgroundColor: '#000', 
							                        '-webkit-border-radius': '10px', 
							                        '-moz-border-radius': '10px', 
							                        opacity: 0.5, 
							                        color: '#fff' 
							                        },
							                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
							                    });

						    					$.ajax({
											        type: "POST",
											        url: 'data/nota_credito/app.php',
											        data: {
														errorWebService:'errorWebService',
														id : ids,
														aut: idxml
													},
											        dataType: 'json',
											        success: function(response) {   
											        	$.unblockUI();
											        	$('#table3').trigger( 'reloadGrid'); 

											        	if(response == 1) {							        		
											        		$.gritter.add({
																title: 	'<span>Mensaje de Información </span>',
																text: 	'<span class=""></span>'
																		+' <span> Información Enviada</span>',
																image: 	'dist/images/email.png', 
																sticky: false,
																time: 3000,												
															});	
											        	} else {
											        		if(response == 2) {							        		
												        		$.gritter.add({
																	title: '<span>Mensaje de Error </span>',
																	text: 	'<span class=""></span>'
																			+' <span> Documentos no Generados</span>',
																	image: 	'dist/images/file_error.png', 
																	sticky: false,
																	time: 3000,												
																});	
												        	} else {
												        		if(response == 3) {							        		
													        		$.gritter.add({
																		title: '<span>Mensaje de Información </span>',
																		text: 	'<span class=""></span>'
																				+' <span> Comprobante Autorizado</span>',
																		image: 	'dist/images/file_ok-1.png', 
																		sticky: false,
																		time: 3000,												
																	});	
													        	} else {
													        		if(response == 4) {							        		
														        		$.gritter.add({
																			title: 	'<span>Mensaje de Error </span>',
																			text: 	'<span class=""></span>'
																					+' <span> Archivo Rechazado Vuelva a firmar</span>',
																			image: 	'dist/images/error_file.png', 
																			sticky: false,
																			time: 3000,												
																		});	
														        	} else {											        		
														        		$.gritter.add({
																			title:  '<span>Mensaje de Error </span>',
																			text: 	'<span class=""></span>'
																					+' <span> Error... WebService Verifique XML</span>',
																			image:  'dist/images/error_file.png', 
																			sticky: false,
																			time: 3000,												
																		});													        	
														        	}
													        	}
												        	}	
											        	}	
											        }        
											    }); 
				    						} else {
				    							if(id ="btn_9") {
				    								$.blockUI({ css: { 
								                        border: 'none', 
								                        padding: '10px',
								                        backgroundColor: '#000', 
								                        '-webkit-border-radius': '10px', 
								                        '-moz-border-radius': '10px', 
								                        opacity: 0.5, 
								                        color: '#fff' 
								                        },
								                        message: '<h4><i class="ace-icon fa fa-spinner fa-spin bigger-125"></i> Enviando...</h4>',
								                    });

							    					$.ajax({
												        type: "POST",
												        url: 'data/nota_credito/app.php',
												        data: {
															generarFirma:'generarFirma',
															id : ids,
															aut: idxml
														},
												        dataType: 'json',
												        success: function(response) {         
												        	$.unblockUI();
												        	$('#table3').trigger( 'reloadGrid');

												        	if(response == 1) {							        		
												        		$.gritter.add({
																	title: 	'<span>Mensaje de Información </span>',
																	text: 	'<span class=""></span>'
																			+' <span> Información Enviada</span>',
																	image: 	'dist/images/email.png', 
																	sticky: false,
																	time: 3000,												
																});	
												        	} else {
												        		if(response == 2) {							        		
													        		$.gritter.add({
																		title: 	'<span>Mensaje de Error </span>',
																		text: 	'<span class=""></span>'
																				+' <span> Documentos no Generados</span>',
																		image: 	'dist/images/file_error.png', 
																		sticky: false,
																		time: 3000,												
																	});	
													        	} else {
													        		if(response == 3) {							        		
														        		$.gritter.add({
																			title: 	'<span>Mensaje de Información </span>',
																			text: 	'<span class=""></span>'
																					+' <span> Comprobante Autorizado</span>',
																			image: 	'dist/images/file_ok-1.png', 
																			sticky: false,
																			time: 3000,												
																		});	
														        	} else {
														        		if(response == 4) {							        		
															        		$.gritter.add({
																				title: 	'<span>Mensaje de Error </span>',
																				text: 	'<span class=""></span>'
																						+' <span> Archivo Rechazado Vuelva a firmar</span>',
																				image: 	'dist/images/error_file.png', 
																				sticky: false,
																				time: 3000,												
																			});	
															        	} else {										        								        		
															        		$.gritter.add({
																				title: 	'<span>Mensaje de Error </span>',
																				text: 	'<span class=""></span>'
																						+' <span> Error... WebService Verifique XML</span>',
																				image: 	'dist/images/error_file.png', 
																				sticky: false,
																				time: 3000,												
																			});												        	
															        	}
														        	}
													        	}	
												        	}	
												        }         
												    }); 
						    					}			    							
				    						}	
				    					}
				    				}
			    				}	
		    				}	
		    			}	
		    		}
		    	});
	        },
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector3).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector3).jqGrid('getRowData',gsr);
            	var id = ret.id;
	        },
	        
	        caption: "LISTA NOTAS CRÉDITO ELECTRÓNICAS"
	    });

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function(){
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector3).jqGrid('navGrid',pager_selector3, {   
	        edit: false,
	        editicon: 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon: 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon: 'ace-icon fa fa-trash-o red',
	        search: false,
	        searchicon: 'ace-icon fa fa-search orange',
	        refresh: true,
	        refreshicon: 'ace-icon fa fa-refresh green',
	        view: true,
	        viewicon: 'ace-icon fa fa-search-plus grey'
	    },
	    {	        
	        recreateForm: true,
	        beforeShowForm : function(e) {
	            var form = $(e[0]);
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_edit_form(form);
	        }
	    },
	    {
	        closeAfterAdd: true,
	        recreateForm: true,
	        viewPagerButtons: false,
	        beforeShowForm : function(e) {
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
	        afterRedraw: function(){
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

	        //update buttons classes
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
	        $(grid_selector3).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin

	/*jqgrid table 4 buscador facturas*/    
	jQuery(function($) {
	    var grid_selector4 = "#table4";
	    var pager_selector4 = "#pager4";
	    
	    $(window).on('resize.jqGrid', function() {
			$(grid_selector4).jqGrid('setGridWidth', $("#myModal .modal-dialog").width()-30);
	    }).trigger('resize');  

	    var parent_column = $(grid_selector4).closest('[class*="col-"]');
		$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
				//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				setTimeout(function() {
					$(grid_selector4).jqGrid( 'setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // buscador facturas
	    jQuery(grid_selector4).jqGrid({	 
	    	datatype: "xml",
		    url: 'data/factura_venta/xml_factura_venta.php',         
	        colNames: ['ID','IDENTIFICACIÓN','CLIENTE','SERIE','FECHA EMISIÓN','TOTAL','ACCIÓN'],
	        colModel:[ 
			    {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name:'identificacion',index:'identificacion', frozen:true, align:'left', search:true, hidden: false},
	            {name:'razon_social',index:'razon_social',frozen : true,align:'left', search:true, width: '250px'},
	            {name:'secuencial',index:'secuencial',frozen : true, hidden: false, align:'left', search:true,width: ''},
	            {name:'fecha_emision',index:'fecha_emision',frozen : true, align:'left', search:false,width: '120px'},
	            {name:'total_pagar',index:'total_pagar',frozen : true, search:false, align:'left',width: '80px'},
	            {name:'accion', index:'accion', editable: false, hidden: true, search:false, frozen: true, editrules: {required: true}, align: 'center', width: '80px'},
	        ],          
	        rowNum: 10,       
	        width: 600,
	        shrinkToFit: false,
	        height: 350,
	        rowList: [10,20,30],
	        pager: pager_selector4,        
	        sortname: 'id',
	        sortorder: 'desc',
	        autoencode: false,
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
				var ids = jQuery(grid_selector4).jqGrid('getDataIDs');
				for(var i = 0;i < ids.length;i++) {
					var id_factura = ids[i];
					pdf = "<a onclick=\"angular.element(this).scope().methodspdf('"+id_factura+"')\" title='Reporte Factura Venta' ><i class='fa fa-file-pdf-o red2' style='cursor:pointer; cursor: hand'> PDF</i></a>"; 
					jQuery(grid_selector4).jqGrid('setRowData',ids[i],{accion:pdf});
				}	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector4).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector4).jqGrid('getRowData',gsr);
            	

            	$.ajax({
					url: 'data/factura_venta/app.php',
					type: 'post',
					data: {llenar_cabezera_factura:'llenar_cabezera_factura',id: ret.id},
					dataType: 'json',
					success: function(data) {
						if (data != null) {
							$('#id_cliente').val(data.id_cliente);
							$('#ruc').val(data.identificacion);
							$('#razon_social').val(data.razon_social);
							$('#direccion').val(data.direccion);
							$('#telefono').val(data.telefono2);
							$('#correo').val(data.correo);
							
							$('#id_factura').val(data.id_factura);
							$('#serie_factura').val(data.secuencial);

							$('#motivo').focus();
		                }
					}
				});
				$('#myModal2').modal('hide');          
	        },
	        caption: "LISTA FACTURAS VENTA"
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
	        $(grid_selector4).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin
});