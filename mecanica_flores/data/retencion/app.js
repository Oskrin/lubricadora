app.controller('retencionController', function($scope, $interval) {

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
	 	new:function(num, simbol) {
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

	// intevalos
	$interval(function() {
		show();
	}, 1000);
	// fin

	// digitos
	function toFixedDown(value, digits) {
	    if( isNaN(value) )
	        return 0;
	    var n = value - Math.pow(10, -digits)/2;
	    n += n / Math.pow(2, 53);
	    if(n<0)
	        n=0.000;
	    return n.toFixed(digits);
	}
	// fin

	jQuery(function($) {
		// tooltip
		$('[data-toggle="tooltip"]').tooltip();
		// fin

		// tabs retenciones
		$( "#tabRetencion" ).click(function(event) {
			event.preventDefault();  
		});

		$("#tabRetencion").on('shown.bs.tab', function(e) {
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
				url: 'data/retencion/app.php',
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

		// llenar establecimiento / emision
		function llenar_info() {
			$.ajax({
				url: 'data/factura_venta/app.php',
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

		// limpiar select2
		$("#select_forma_pago,#select_mes,#select_anio,#select_tipo_comprobante,#select_tipo_retencion,#select_tarifa_retencion,#select_estado").select2({
		  // allowClear: true
		});
		// fin

		// llenar combo tipo comprobante
		function llenar_select_tipo_comprobante() {
			$.ajax({
				url: 'data/retencion/app.php',
				type: 'post',
				data: {llenar_tipo_comprobante:'llenar_tipo_comprobante'},
				success: function(data) {
					$('#select_tipo_comprobante').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo forma pago
		function llenar_select_forma_pago() {
			$.ajax({
				url: 'data/retencion/app.php',
				type: 'post',
				data: {llenar_forma_pago:'llenar_forma_pago'},
				success: function(data) {
					$('#select_forma_pago').html(data).trigger("change");
				}
			});
		}
		// fin


		// llenar combo tipo retención
		function llenar_select_tipo_retencion() {
			$.ajax({
				url: 'data/retencion/app.php',
				type: 'post',
				data: {llenar_tipo_retencion:'llenar_tipo_retencion'},
				success: function(data) {
					$('#select_tipo_retencion').html(data).trigger("change");
				}
			});
		}
		// fin

		//selectores anidados tipo retención
		$("#select_tipo_retencion").change(function() {
			$("#select_tipo_retencion option:selected").each(function() {
	            id = $(this).val();

	            $.ajax({
					url: 'data/retencion/app.php',
					type: 'post',
					data: {llenar_tarifa_retencion:'llenar_tarifa_retencion',id: id},
					success: function(data) {
						$('#select_tarifa_retencion').html(data).trigger("change");
					}
				});
			});
		});
		// fin

		//selectores anidados para valor porcentaje
		$("#select_tarifa_retencion").change(function () {
			$("#select_tarifa_retencion option:selected").each(function() {
	            id = $(this).val();

	            $.ajax({
					url: 'data/retencion/app.php',
					type: 'post',
					data: {llenar_porcentaje:'llenar_porcentaje',id: id},
					success: function(data) {
						$('#porcentaje').val(data);
						//$('#base_imponible').focus();
					}
				});
			});
		});
		// fin

		function limpiar_input() {
			llenar_select_tipo_retencion();
			$('#select_tarifa_retencion').html("");
			$('#porcentaje').val("");
			$('#base_imponible').val("");	
		}


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
	     		source: "data/buscador/proveedores_filtro.php?tipo_busqueda=" + tipo,
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

		            $("#comprobante").focus();

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
	     		source: "data/buscador/proveedores_filtro.php?tipo_busqueda=" + tipo,
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

		            $("#comprobante").focus();
	                
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
	  	$("#base_imponible").on("keypress",function(e) {
	  		if(e.keyCode == 13) {//tecla del alt para el entrer poner 13 
			    var total_retenido = 0;

		        if ($("#select_tipo_retencion").val() == "") {
		            $.gritter.add({
						title: 'Error... Seleccione Tipo Retención',
						class_name: 'gritter-error gritter-center',
						time: 1000,
					});
		        } else {
		            if ($("#select_tarifa_retencion").val() == "") {
		                $.gritter.add({
							title: 'Error... Seleccione Tarifa Retención',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});
		            } else {
		                if ($("#porcentaje").val() == "") {
		                    $("#porcentaje").focus();
		                    $.gritter.add({
								title: 'Error... Ingrese una Porcentaje',
								class_name: 'gritter-error gritter-center',
								time: 1000,
							});
		                } else {
		                    if ($("#base_imponible").val() == "") {
		                        $("#base_imponible").focus();
		                        $.gritter.add({
									title: 'Error... Ingrese Base Imponible',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
			               	} else {
			                    var filas = jQuery("#table").jqGrid("getRowData");
			                    var retenido = 0;
			                    var repe = 0;
			                  
			                    if (filas.length == 0) {
			                    	retenido = (parseFloat($("#base_imponible").val()) * parseFloat($("#porcentaje").val())) / 100;
			                        retenido = parseFloat(Math.round(retenido * 100) /100).toFixed(2);

			                        var datarow = {
			                            id: $("#select_tipo_comprobante").val(), 
			                            ejercicio_fiscal: $("#select_mes option:selected").text() + ' / ' +$("#select_anio option:selected").text(), 
			                            id_tipo_impuesto: $("#select_tarifa_retencion").val(), 
			                            codigo_impuesto: $("#select_tarifa_retencion option:selected").text(), 
			                            base_imponible: parseFloat($("#base_imponible").val()).toFixed(2), 
			                            id_retencion: $("#select_tipo_retencion").val(),
			                            impuesto: $("#select_tipo_retencion option:selected").text(),
			                            porcentaje: $("#porcentaje").val(),
			                            valor_retenido: retenido
			                        };

			                        jQuery("#table").jqGrid('addRowData', $("#select_tarifa_retencion").val(), datarow);
			                        limpiar_input();
			                    } else {
			                    	for (var i = 0; i < filas.length; i++) {
			                            var id = filas[i];

			                            if (id['id'] == $("#select_tarifa_retencion").val()) {
			                            	repe = 1;
			                            }
			                        }

			                        if (repe == 1) {
			                        	$.gritter.add({
											title: 'Error... Retención ya Ingresada',
											class_name: 'gritter-error gritter-center',
											time: 1000,
										});	
			                        } else {
			                        	retenido = (parseFloat($("#base_imponible").val()) * parseFloat($("#porcentaje").val())) / 100;
				                        retenido = parseFloat(Math.round(retenido * 100) /100).toFixed(2);
		                            
		                                var datarow = {
				                            id: $("#select_tipo_comprobante").val(), 
				                            ejercicio_fiscal: $("#select_mes option:selected").text() + ' / ' +$("#select_anio option:selected").text(), 
				                            id_tipo_impuesto: $("#select_tarifa_retencion").val(), 
				                            codigo_impuesto: $("#select_tarifa_retencion option:selected").text(), 
				                            base_imponible: parseFloat($("#base_imponible").val()).toFixed(2), 
				                            id_retencion: $("#select_tipo_retencion").val(),
				                            impuesto: $("#select_tipo_retencion option:selected").text(),
				                            porcentaje: $("#porcentaje").val(),
				                            valor_retenido: retenido
				                        };

		                                jQuery("#table").jqGrid('addRowData', $("#select_tarifa_retencion").val(), datarow);
		                                limpiar_input();
			                        }			                       
			                    }
			                    
			                    // proceso 
			                    var total_retenido = 0;
			                    var total_total = 0;
			                    // fin                                                     
			                    
			                    var fil = jQuery("#table").jqGrid("getRowData");
			                    for (var t = 0; t < fil.length; t++) {
			                        var dd = fil[t];
			                        total_retenido = parseFloat(total_retenido) + parseFloat(dd['valor_retenido']);
			                    } 

			                    total_total = parseFloat(total_retenido).toFixed(2);
			                    $("#total_retenido").val(total_total);
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
			var myWindow = window.open('data/retencion/generarPDF.php?id='+id,'popup','width=900,height=650');
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
		llenar_info();
		consultar_secuencial()
		llenar_select_tipo_comprobante();
		llenar_select_forma_pago();
		llenar_select_tipo_retencion();
		$("#base_imponible").keypress(ValidPun);
		$("#serie").keypress(ValidNum);
		$("#serie").attr("maxlength", "9");
		$("#ruc").keypress(ValidNum);
		$("#ruc").attr("maxlength", "13");
		$("#ruc").focus();
		$("#comprobante").keypress(ValidNum);
		$("#comprobante").attr("maxlength", "15");

		//$("#btn_1").attr("disabled", true);
		$("#btn_3").attr("disabled", true);

		// recargar
		function reload() {
    		setTimeout(function() {
        	location.reload()
    		}, 100);
		}
		// fin

		// guardar retencion
		$('#btn_0').click(function() {
			var formulario = $("#form_retencion").serialize();
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
				if ($('#serie').val().length != 9) {
					$('#serie').focus();
					$.gritter.add({
						title: 'Serie Mínimo 9 Caracteres',
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
						if($('#comprobante').val() == '') {
							$('#comprobante').focus();
							$.gritter.add({
								title: 'Ingrese N° Comprobante',
								class_name: 'gritter-error gritter-center',
								time: 1000,
							});	
						} else {
							if ($('#comprobante').val().length != 15) {
								$('#comprobante').focus();
								$.gritter.add({
									title: 'Comprobante Mínimo 15 Caracteres',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
							} else {
								if($('#select_forma_pago').val() == '') {
									$.gritter.add({
										title: 'Seleccione un Forma Pago',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});	
								} else {
									if(filas.length == 0) {
										$('#base_imponible').focus();
						                $.gritter.add({
											title: 'Ingrese datos a la Retención',
											class_name: 'gritter-error gritter-center',
											time: 1000,
										});	
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
							                v1[i] = datos['id_tipo_impuesto'];
							                v2[i] = datos['base_imponible'];
							                v3[i] = datos['id_retencion'];
							                v4[i] = datos['porcentaje'];
							                v5[i] = datos['valor_retenido'];
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
							                url: "data/retencion/app.php",
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
							                success: function(data) {
							                	$.unblockUI();
							                	
												col_1 = $($("#result").children().children().children().next()[0]).children().children()[0];
												col_2 = $($("#result").children().children().children().next()[0]).children().children()[1];				    	
												$(col_1).html('');
												$(col_2).html('');
										    	if(data.estado == 1) {					
													$(col_1).append("<b>La información se encuentra Guardada</b></br>");
													$(col_1).append("<b>El Documento esta Generado</b></br>");
													$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
													$(col_1).append("<b>Documentos enviados al correo del cliente</b></br>");
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
													$('#result').modal('show');
													$("#pdf").on("click", function() {
														window.open('data/retencion/generarPDF.php?id='+data.id, '_blank');
														reload();
													});
													$("#cerrar").on("click", function() {
														reload();
													});
												} else {
													if(data.estado == 2) {														
														$(col_1).append("<b>La información se encuentra Guardada</b></br>");
														$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
														$(col_1).append("<b>El Documento esta Generado</b></br>");							
														$(col_1).append("<b>Documentos enviados al correo del cliente</b></br>");
														$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
														$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
														$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
														$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
														$('#result').modal('show');
														$("#pdf").on("click", function() {								
															reload();
														});
														$("#cerrar").on("click", function() {
															reload();
														});
													} else {
														if(data.estado == 3) {																
															$(col_1).append("<b>La información se encuentra Guardada</b></br>");
															$(col_1).append("<b>El Documento esta Generado</b></br>");
															$(col_1).append("<b>El Documento se encuentra Validado</b></br>");
															$(col_1).append("<b>Documentos enviados al email del cliente</b></br>");
															$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
															$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
															$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
															$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
															$('#result').modal('show');
															$("#pdf").on("click", function() {
																window.open('data/retencion/generarPDF.php?id='+data.id, '_blank');
																reload();
															});
															$("#cerrar").on("click", function() {
																reload();
															});
														} else {
															if(data.estado == 4) {																		
																$(col_1).append("<b>Error en la base de datos</b></br>");								
																$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
																$('#result').modal('show');
																$("#pdf").on("click", function() {										
																	reload();
																});
																$("#cerrar").on("click", function() {
																	reload();
																});
															} else {
																if(data.estado == 5) {
																	$(col_1).append("<b>Error el Archivo para la firma no existe</b></br>");									
																	$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');									
																	$('#result').modal('show');
																	$("#pdf").on("click", function() {										
																		reload();
																	});
																	$("#cerrar").on("click", function() {
																		reload();
																	});
																} else {
																	if(data.estado == 6) {
																		$(col_1).append("<b>Error! La clave del certificado es incorrecta</b></br>");									
																		$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');									
																		$('#result').modal('show');
																		$("#pdf").on("click", function() {										
																			reload();
																		});
																		$("#cerrar").on("click", function() {
																			reload();
																		});
																	} else {
																		if(data.estado == 7) {																				
																			$(col_1).append("<b>La información se encuentra Guardada</b></br>");
																			$(col_1).append("<b>Error en el web service del SRI vuelva a intentarlo </b></br>");
																			$(col_1).append("<b>El Documento se encuentra Rechazadp</b></br>");
																			$(col_1).append("<b>Documentos enviados al email del cliente</b></br>");
																			$(col_2).append('<i class="fa fa-check" style="color: green;"></i></br>');
																			$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																			$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																			$(col_2).append('<i class="fa fa-times" style="color: red;"></i></br>');
																			$('#result').modal('show');
																			$("#pdf").on("click", function() {										
																				reload();
																			});
																			$("#cerrar").on("click", function() {
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
																			$("#pdf").on("click", function() {										
																				reload();
																			});
																			$("#cerrar").on("click", function() {
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
			}	
		});
		// fin

		// reimprimir facturas
		$('#btn_3').click(function() {
			if($('#id_retencion').val() == '') {
				$.gritter.add({
					title: 'Seleccione Retención a Reimprimir',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {
				var id = $('#id_retencion').val();
				var myWindow = window.open('data/retencion/generarPDF.php?id='+id,'popup','width=900,height=650');
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

	    // tabla local retencion
	    jQuery(grid_selector).jqGrid({
	        datatype: "local",
	        colNames: ['','ID','EJERCICIO FISCAL','ID_TIPO_IMPUESTO','RETIENE','BASE IMPONIBLE','ID_RETENCION','TIPO RETENCIÓN','% RETENCIÓN','VALOR RETENIDO'],
	        colModel:[  
	        	{name:'myac', width: 50, fixed: true, sortable: false, resize: false, formatter: 'actions',
			        formatoptions: {keys: false, delbutton: true, editbutton: false}
			    }, 
			    {name: 'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name: 'ejercicio_fiscal', index: 'ejercicio_fiscal', editable: false, search: false, hidden: false, editrules: {edithidden: false}, align: 'center', frozen: true, width: 100},
	            {name: 'id_tipo_impuesto', index: 'id_tipo_impuesto', editable: false, frozen: true, hidden: true, editrules: {required: true}, align: 'center', width: 50},
	            {name: 'codigo_impuesto', index: 'codigo_impuesto', editable: true, frozen: true, editrules: {required: true}, align: 'center', width: 200, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return numeros(e)})}}}, 
	            {name: 'base_imponible', index: 'base_imponible', editable: true, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 90, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return punto(e)})}}}, 
	            {name: 'id_retencion', index: 'id_retencion', editable: false, frozen: true, hidden: true, editrules: {required: true}, align: 'center', width: 70},
	            {name: 'impuesto', index: 'impuesto', editable: false, hidden: false, frozen: true, editrules: {required: true}, align: 'center', width: 90},
	            {name: 'porcentaje', index: 'porcentaje', editable: false, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 90},
	            {name: 'valor_retenido', index: 'valor_retenido', align: 'center', width: 100, hidden: false},
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

	                // proceso 
                    var total_retenido = 0;
                    var total_total = 0;
                    // fin                                                     
                    
	                var filas = jQuery(grid_selector).jqGrid("getRowData"); 

	                for (var t = 0; t < filas.length; t++) {
	                	total_retenido = parseFloat($("#total_retenido").val()) - parseFloat(ret.valor_retenido);
	                }

	                total_total = parseFloat(total_retenido).toFixed(2);
			        $("#total_retenido").val(total_total);

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

	/*jqgrid table 2 buscador retenciones*/    
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
					$(grid_selector2).jqGrid('setGridWidth', parent_column.width());
				}, 0);
			}
	    })

	    // buscador facturas
	    jQuery(grid_selector2).jqGrid({	 
	    	datatype: "xml",
		    url: "data/retencion/xml_retencion.php",         
	        colNames: ['ID','IDENTIFICACIÓN','PROVEEDOR','SERIE','FECHA EMISIÓN','TOTAL','ACCIÓN'],
	        colModel:[ 
			    {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name:'identificacion',index:'identificacion', frozen:true, align:'left', search:true, hidden: false},
	            {name:'razon_social',index:'razon_social',frozen : true,align:'left', search:true, width: '250px'},
	            {name:'serie',index:'serie',frozen : true, hidden: false, align:'left', search:true,width: ''},
	            {name:'fecha_emision',index:'fecha_emision',frozen : true, align:'left', search:false,width: '120px'},
	            {name:'total_comprobante',index:'total_comprobante',frozen : true, align:'left', search:false,width: '100px'},
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
					var id_retencion = ids[i];
					pdf = "<a onclick=\"angular.element(this).scope().methodspdf('"+id_retencion+"')\" title='Reporte Retención'><i class='fa fa-file-pdf-o red2' style='cursor:pointer; cursor: hand'> PDF</i></a>"; 
					jQuery(grid_selector2).jqGrid('setRowData',ids[i],{accion:pdf});
				}	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector2).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector2).jqGrid('getRowData',gsr);
            	//$("#table").jqGrid("clearGridData", true);	

            	//$.ajax({
				//	url: 'data/retencion/app.php',
				//	type: 'post',
				//	data: {llenar_cabezera_retencion:'llenar_cabezera_retencion',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {					
				//		$('#id_retencion').val(data.id_retencion);
				//		$('#fecha_emision').val(data.fecha_emision);
				//		$('#serie').val(data.secuencial);
				//		$('#id_proveedor').val(data.id_proveedor);
				//		$('#ruc').val(data.identificacion);
				//		$('#razon_social').val(data.razon_social);
				//		$('#telefono').val(data.telefono2);
				//		$('#direccion').val(data.direccion);
				//		$('#correo').val(data.correo);
				//		$("#select_tipo_comprobante").select2('val', data.id_tipo_comprobante).trigger("change");
				//		$("#select_mes").select2('val', data.mes).trigger("change");
				//		$("#select_anio").select2('val', data.anio).trigger("change");
				//		$('#comprobante').val(data.numero_comprobante);
				//		$("#select_forma_pago").select2('val', data.id_forma_pago).trigger("change");

				//		$("#total_retenido").val(data.total_retenido);
				//	}
				//});

				//$.ajax({
				//	url: 'data/retencion/app.php',
				//	type: 'post',
				//	data: {llenar_detalle_retencion:'llenar_detalle_retencion',id: ret.id},
				//	dataType: 'json',
				//	success: function(data) {
				//		var tama = data.length;
				//		var retenido = 0;

				//		for (var i = 0; i < tama; i = i + 8) {
				//			retenido = (parseFloat(data[i + 4]) * parseFloat(data[i + 7])) / 100;
			    //            retenido = parseFloat(Math.round(retenido * 100) /100).toFixed(2);

                //            var datarow = {
	            //                id: data[i], 
	            //                ejercicio_fiscal: data[i + 1], 
	            //                id_tipo_impuesto: data[i + 2], 
	            //                codigo_impuesto: data[i + 3], 
	            //                base_imponible: data[i + 4], 
	            //                id_retencion: data[i + 5],
	            //                impuesto: data[i + 6],
	            //                porcentaje: data[i + 7],
	            //                valor_retenido: retenido
	            //            };

                //            jQuery("#table").jqGrid('addRowData', data[i + 2], datarow);
				//		}
				//	}
				//});  

				//$('#myModal').modal('hide'); 
		        $('#btn_0').attr('disabled', true);
		        $('#btn_1').attr('disabled', false);
		        $('#btn_3').attr('disabled', false);           
	        },
	        caption: "LISTA RETENCIONES"
	    });

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
	            $(cell).find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector2).jqGrid('navGrid', pager_selector2, { // navbar options
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
	        $(grid_selector2).jqGrid('GridUnload');
	        $('.ui-jqdialog').remove();
	    });
	});
	// fin

	/*jqgrid table 3 buscador comprobantes*/ 
	jQuery(function($) {
		// eventos estados
		function fn_click() {	    	
	    	$("button.boton").click(function() {
	    		id = $(this)['context'].id;	    			    		
	    		ids = $(this)['context']['dataset'].ids;
	    		idxml = $(this)['context']['dataset'].xml;

	    		if(id == "btn_1") {	    			
	    			window.open('data/retencion/generarPDF.php?id='+ids, '_blank');
	    		} else {
	    			if(id == "btn_2") {	
	    				window.open('data/retencion/comprobantes/'+idxml+'.xml','_blank');
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
						        url: 'data/retencion/app.php',
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
							        url: 'data/retencion/app.php',
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
								        url: 'data/retencion/app.php',
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
									        url: 'data/retencion/app.php',
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
										        url: 'data/retencion/app.php',
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
											        url: 'data/retencion/app.php',
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
	    }
	    // fin

	    // buscar comprobantes
	    $("#btn_buscar").on("click",function(){
			jQuery("#table3").jqGrid('setGridParam',{url:"data/retencion/xml_validar.php?estado="+$("#select_estado").val(),page:1}).trigger("reloadGrid");
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
	        url: "data/retencion/xml_validar.php?estado="+$("#select_estado").val(),      
	        colNames: ['ID','N° AUTORIZACIÓN','ACCIONES','ESTADO','FECHA EMISIÓN','NOMBRE COMERCIAL','FECHA AUTORIZACIÓN','CLAVE DE ACCESO','TOTAL RETENIDO'],
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
	        loadComplete: function() {
	        	var table = this;
	            setTimeout(function() {
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	                fn_click();		                
	            }, 0);
	        },
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector3).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector3).jqGrid('getRowData',gsr);
            	var id = ret.id;
	        },
	        
	        caption: "LISTA RETENCIONES ELECTRÓNICAS"
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
});