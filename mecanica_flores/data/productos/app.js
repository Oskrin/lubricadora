app.controller('productosController', function ($scope, $route) {

	$scope.$route = $route;

	jQuery(function($) {
		// tooltip
		$('[data-toggle="tooltip"]').tooltip();
		// fin

		$( "#tabProducto" ).click(function(event) {
			event.preventDefault();  
		});	
		$("#tabProducto").on('shown.bs.tab', function(e) {
			$('.chosen-select').each(function() {
				var $this = $(this);
				$this.next().css({'width': $this.parent().width()});
			})	
		});		
			
		if(!ace.vars['touch']) {			
			$('.chosen-select').chosen({allow_single_deselect:true}); 
			//resize the chosen on window resize		
			$(window)
			.off('resize.chosen')
			.on('resize.chosen', function() {
				$('.chosen-select').each(function() {
					var $this = $(this);
					$this.next().css({'width': $this.parent().width()});
				})
			}).trigger('resize.chosen');
			//resize chosen on sidebar collapse/expand
			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {					
				if(event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					var $this = $(this);
					$this.next().css({'width': $this.parent().width()});
				});
			});
		}

		// formato archivo
		$('#file_1').ace_file_input({
			no_file:'Selecione un archivo ...',
			btn_choose:'Selecionar',
			btn_change:'Cambiar',
			droppable:false,
			onchange:null,
			thumbnail:false
		});
		// fin

		// formato archivo excel
		$('#archivo_excel').fileinput({
	        uploadUrl: '#',
	        uploadAsync: false,
	        minFileCount: 1,
	        maxFileCount: 20,
	        showUpload: true,
	        slugCallback: function(filename) {
	            return filename.replace('(', '_').replace(']', '_');
	        }
	    });
	    // fin

		// mascaras input
		$('#stock').ace_spinner({value:0,min:0,max:100,step:1, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
		$('#stock_minimo').ace_spinner({value:1,min:1,step:1, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
		$('#stock_maximo').ace_spinner({value:1,min:1,step:1, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
		$('#descuento').ace_spinner({value:0,min:0,step:1, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
		// fin

		// estilos select2
		$(".select2").css({
			width: '100%',
		    allow_single_deselect: true,
		    no_results_text: "No se encontraron resultados",
		}).select2().on("change", function(e) {
			$(this).closest('form').validate().element($(this));
	    });

		$("#select_iva").select2({
		  	allowClear: true 
		});
		// fin

		// event ctrl+b abrir
		Mousetrap.bind(['ctrl+b'], function(e) {
			$('#myModal').modal('show'); 	    
		});
		// fin

		// estilos file
		$("#file_1").ace_file_input('reset_input');
		// fin

		// Visualizar imagen
		$(function() {
		    Test = {
		        UpdatePreview: function(obj) {
		            if(!window.FileReader){
		            // don't know how to proceed to assign src to image tag
		            } else {
		                var reader = new FileReader();
		                var target = null;
		                reader.onload = function(e) {
		                    target =  e.target || e.srcElement;
		                    $("#logo").prop("src", target.result);
		                };
		                reader.readAsDataURL(obj.files[0]);
		            }
		        }
		    };
		});
		// fin

		// validacion punto
		function ValidPun(e) {
		    var key;
		    if (window.event) {
		        key = e.keyCode;
		    }
		    else if (e.which) {
		        key = e.which;
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

		// validacion solo numeros
		function ValidNum() {
		    if (event.keyCode < 48 || event.keyCode > 57) {
		        event.returnValue = false;
		    }
		    return true;
		}
		// fin

		// recargar formulario
		function redireccionar() {
			setTimeout(function() {
			    location.reload(true);
			}, 1000);
		}
		// fin

		// llenar combo tipo producto
		function llenar_select_tipo_productos() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_tipo_producto:'llenar_tipo_producto'},
				success: function(data) {
					$('#select_tipo').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo categoria
		function llenar_select_categoria() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_categoria:'llenar_categoria'},
				success: function(data) {
					$('#select_categoria').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo marca
		function llenar_select_marca() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_marca:'llenar_marca'},
				success: function(data) {
					$('#select_marca').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo presentacion
		function llenar_select_unidades_medida() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_unidades_medida:'llenar_unidades_medida'},
				success: function(data) {
					$('#select_medida').html(data).trigger("change");
				}
			});
		}
		// fin

		//selectores anidados tipo retención
		$("#select_medida").change(function() {
			$("#select_medida option:selected").each(function() {
	            id = $(this).val();

	            $.ajax({
					url: 'data/productos/app.php',
					type: 'post',
					data: {llenar_unidad:'llenar_unidad',id: id},
					success: function(data) {
						$("#cantidad").val(data);
					}
				});
			});
		});
		// fin

		// llenar combo almacenes
		function llenar_select_bodega() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_bodega:'llenar_bodega'},
				success: function(data) {
					$('#select_bodega').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo porcentaje
		function llenar_select_porcentaje() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_iva:'llenar_iva'},
				success: function(data) {
					$('#select_iva').html(data).trigger("change");
				}
			});
		}
		// fin

		// llenar combo proveedores
		function llenar_select_proveedores() {
			$.ajax({
				url: 'data/productos/app.php',
				type: 'post',
				data: {llenar_proveedores:'llenar_proveedores'},
				success: function(data) {
					$('#select_proveedor').html(data);
				}
			});
		}
		// fin

		// validaciones al iniciar
		$("#logo").attr("src", "data/productos/fotos/defaul.jpg");
		llenar_select_tipo_productos();
		llenar_select_categoria();
		llenar_select_marca();
		llenar_select_unidades_medida()
		llenar_select_bodega();
		llenar_select_porcentaje();
		llenar_select_proveedores();
		$('#btn_1').attr('disabled', true);
		$('#codigo_barras').focus();
    	$("#precio_costo").keypress(ValidPun);
    	$("#utilidad_minorista").keypress(ValidPun);
    	$("#utilidad_mayorista").keypress(ValidPun);
    	$("#precio_minorista").keypress(ValidPun);
    	$("#precio_mayorista").keypress(ValidPun);
    	$("#stock").keypress(ValidNum);
    	$("#stock_minimo").keypress(ValidNum);
    	$("#descuento").keypress(ValidNum);
    	$("#stock_maximo").keypress(ValidNum);
    	$("#expiracion").prop("checked",false);
    	$("#series").prop("checked",false);
    	$("#facturar_existencia").prop("checked",false);
    	// fin

    	// ver codigos repetidos
    	function comparar_codigo_barras() {
		    var remote;

		    $.ajax({
		        type: "POST",
			    url: "data/productos/app.php",
			    data: {comparar_codigo_barras:'comparar_codigo_barras',codigo_barras: $("#codigo_barras").val()},
		        async: false,
		        success : function(data) {
		            remote = data;
		        }
		    });

		    return remote;
		}
		// fin

    	// ver codigos repetidos
    	function comparar_codigo() {
		    var remote;

		    $.ajax({
		        type: "POST",
			    url: "data/productos/app.php",
			    data: {comparar_codigos:'comparar_codigos',codigo: $("#codigo").val()},
		        async: false,
		        success : function(data) {
		            remote = data;
		        }
		    });

		    return remote;
		}
		// fin

    	// funcion guardar
    	function guardar() {
			if($('#codigo_barras').val() != '' && comparar_codigo_barras() == 1) {
				$('#codigo_barras').focus();
				$.gritter.add({
					title: 'Error... Código Barras ya Ingresado',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});		
			} else {
				if($('#codigo').val() != '' && comparar_codigo() == 1) {
					$('#codigo').focus();
					$.gritter.add({
						title: 'Error... Código ya Ingresado',
						class_name: 'gritter-error gritter-center',
						time: 1000,
					});	
				} else {
					if($('#descripcion').val() == '') {
						$('#descripcion').focus();
						$.gritter.add({
							title: 'Ingrese una Descripción',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});	
					} else {
						if($('#precio_costo').val() == '') {
							$('#precio_costo').focus();
							$.gritter.add({
								title: 'Ingrese Precio Costo',
								class_name: 'gritter-error gritter-center',
								time: 1000,
							});	
						} else {
							if($('#precio_minorista').val() == '') {
								$('#precio_minorista').focus();
								$.gritter.add({
									title: 'Ingrese Precio Minorista',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});	
							} else {
								//if($('#precio_mayorista').val() == '') {
								//	$('#precio_mayorista').focus();
								//	$.gritter.add({
								//		title: 'Ingrese Precio Mayorista',
								//		class_name: 'gritter-error gritter-center',
								//		time: 1000,
								//	});	
								//} else {
									if($('#select_categoria').val() == '') {
										$('#select_categoria').focus();
										$.gritter.add({
											title: 'Seleccione una Categoria',
											class_name: 'gritter-error gritter-center',
											time: 1000,
										});	
									} else {
										if($('#select_marca').val() == '') {
											$('#select_marca').focus();
											$.gritter.add({
												title: 'Seleccione una Marca',
												class_name: 'gritter-error gritter-center',
												time: 1000,
											});	
										} else {
											if($('#select_bodega').val() == '') {
												$('#select_bodega').focus();
												$.gritter.add({
													title: 'Seleccione una Bodega',
													class_name: 'gritter-error gritter-center',
													time: 1000,
												});	
											} else {
												if($('#select_tipo').val() == '') {
													$('#select_tipo').focus();
													$.gritter.add({
														title: 'Seleccione Tipo Producto',
														class_name: 'gritter-error gritter-center',
														time: 1000,
													});	
												} else {
													if($('#stock').val() == '') {
														$('#stock').focus();
														$.gritter.add({
															title: 'Ingrese un Stock',
															class_name: 'gritter-error gritter-center',
															time: 1000,
														});	
													} else {
														if($('#select_iva').val() == '') {
															$('#select_iva').focus();
															$.gritter.add({
																title: 'Seleccione IVA',
																class_name: 'gritter-error gritter-center',
																time: 1000,
															});	
														} else {
															$('#btn_0').attr('disabled', true);
															var formData = new FormData(document.getElementById("form_productos"));
															formData.append('Guardar', "Guardar");

															$.ajax({
														        url: "data/productos/app.php",
														        data: formData,
														        type: "POST",
														        contentType: false,
														        processData: false,
												  				cache: false,
														        success: function(data) {
														        	if(data == '1') {
														        		$.gritter.add({
																			title: 	'<span>Mensaje de Información </span>',
																			text: 	'<span class=""></span>'
																					+' <span> Registro Agregado Correctamente</span>',
																			image: 	'dist/images/file_ok-1.png', 
																			sticky: false,											
																		});
																		redireccionar();
															    	}              
														        },
														        error: function(xhr, status, errorThrown) {
															        alert("Hubo un problema!");
															        console.log("Error: " + errorThrown);
															        console.log("Status: " + status);
															        console.dir(xhr);
														        }
														    });
														}
													}
												}
											}
										}
									}
								//}
							}
						}
					}	
				}
			}
		}
		// fin

		// guardar formulario
		$('#btn_0').click(function() {
			guardar();	 
		});
		// fin

		// funcion modificar
		function modificar() {
			if($('#descripcion').val() == '') {
				$('#descripcion').focus();
				$.gritter.add({
					title: 'Ingrese una Descripción',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});	
			} else {
				if($('#precio_costo').val() == '') {
					$('#precio_costo').focus();
					$.gritter.add({
						title: 'Ingrese Precio Costo',
						class_name: 'gritter-error gritter-center',
						time: 1000,
					});	
				} else {
					if($('#precio_minorista').val() == '') {
						$('#precio_minorista').focus();
						$.gritter.add({
							title: 'Ingrese Precio Minorista',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});	
					} else {
						//if($('#precio_mayorista').val() == '') {
						//	$('#precio_mayorista').focus();
						//	$.gritter.add({
						//		title: 'Ingrese Precio Mayorista',
						//		class_name: 'gritter-error gritter-center',
						//		time: 1000,
						//	});	
						//} else {
							if($('#select_categoria').val() == '') {
								$('#select_categoria').focus();
								$.gritter.add({
									title: 'Seleccione una Categoria',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});	
							} else {
								if($('#select_marca').val() == '') {
									$('#select_marca').focus();
									$.gritter.add({
										title: 'Seleccione una Marca',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});	
								} else {
									if($('#select_bodega').val() == '') {
										$('#select_bodega').focus();
										$.gritter.add({
											title: 'Seleccione una Bodega',
											class_name: 'gritter-error gritter-center',
											time: 1000,
										});	
									} else {
										if($('#select_tipo').val() == '') {
											$('#select_tipo').focus();
											$.gritter.add({
												title: 'Seleccione Tipo Producto',
												class_name: 'gritter-error gritter-center',
												time: 1000,
											});	
										} else {
											if($('#stock').val() == '') {
												$('#stock').focus();
												$.gritter.add({
													title: 'Ingrese un Stock',
													class_name: 'gritter-error gritter-center',
													time: 1000,
												});	
											} else {
												if($('#select_iva').val() == '') {
													$('#select_iva').focus();
													$.gritter.add({
														title: 'Seleccione IVA',
														class_name: 'gritter-error gritter-center',
														time: 1000,
													});	
												} else {
													$('#btn_1').attr('disabled', true);
													var formData = new FormData(document.getElementById("form_productos"));
													formData.append('Modificar', "Modificar");

													$.ajax({
												        url: "data/productos/app.php",
												        data: formData,
												        type: "POST",
												        contentType: false,
												        processData: false,
										  				cache: false,
												        success: function(data) {
												        	if(data == '2') {
												        		$.gritter.add({
																	title: 	'<span>Mensaje de Información </span>',
																	text: 	'<span class=""></span>'
																			+' <span> Registro Modificado Correctamente</span>',
																	image: 	'dist/images/file_ok-1.png', 
																	sticky: false,											
																});
																redireccionar();
													  		}              
												        },
												        error: function(xhr, status, errorThrown) {
													        alert("Hubo un problema!");
													        console.log("Error: " + errorThrown);
													        console.log("Status: " + status);
													        console.dir(xhr);
												        }
												    });
												}
											}
										}
									}
								}
							}
						//}
					}	
				}
			}
		}
		// fin

		// modificar formulario
		$('#btn_1').click(function() {
			modificar();
		});
		// fin

		// abrir modal
		$('#btn_2').click(function() {
			$('#myModal').modal('show');
		});
		// fin

		// refrescar formulario
		$('#btn_3').click(function() {
			location.reload(true);
		});
		// fin

		// descargar archivo 
		$('#btn_descargar').click(function() {
			var archivo = "data/productos/plantilla/productos.xlsx";
			window.open(archivo);
		});
		// fin

		// cargar archivo 
		$('#btn_excel').click(function() {
			$('#btn_excel').attr('disabled', true);
			var formData = new FormData(document.getElementById("form_productos"));
			formData.append('Cargar_excel', "Cargar_excel");;

			$.ajax({
                url: "data/productos/app.php",
                type: "POST",
                data:  formData,
                mimeType:"multipart/form-data",
                dataType: 'json',
                contentType: false,
                cache: false, 
                processData:false,
                beforeSend: function() {
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
                success: function(data, textStatus, jqXHR) {
                    if(data != null) {
                    	$.unblockUI();

						$.gritter.add({
							title: 	'<span>Mensaje de Información </span>',
							text: 	'<span class=""></span>'
									+' <span> Registros Agregado Correctamente</span>',
							image: 	'dist/images/file_ok-1.png', 
							sticky: false,											
						}); 
						redireccionar();
                    } else {
                    	$.unblockUI();
                    	$('#btn_excel').attr('disabled', false);

                    	swal({
			                title: "Lo sentimos Seleccione un Archivo",
			                type: "warning",
			            });
                    }
		        }	        
		    });
		});
		// fin

		/*jqgrid*/    
		jQuery(function($) {
		    var grid_selector = "#table";
		    var pager_selector = "#pager";
		    
		    //cambiar el tamaño para ajustarse al tamaño de la página
		    $(window).on('resize.jqGrid', function() {        
		        $(grid_selector).jqGrid( 'setGridWidth', $("#myModal .modal-dialog").width()-30);
		    });
		    //cambiar el tamaño de la barra lateral collapse/expand
		    var parent_column = $(grid_selector).closest('[class*="col-"]');
		    $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
		        if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
		            //para dar tiempo a los cambios de DOM y luego volver a dibujar!!!
		            setTimeout(function() {
		                $(grid_selector).jqGrid( 'setGridWidth', parent_column.width());
		            }, 0);
		        }
		    });

		    // buscador productos
		    jQuery(grid_selector).jqGrid({	        
		        datatype: "xml",
		        url: 'data/productos/xml_productos.php',        
		        colNames: ['ID','CÓDIGO BARRAS','CÓDIGO','DESCRIPCIÓN','PRECIO COSTO','UTILIDAD MINORISTA','UTILIDAD MINORISTA','PRECIO MINORISTA','PRECIO MAYORISTA','ID_TIPO_PRODUCTO','ID_CATEGORIA','ID_MARCA','ID_MEDIDA','ID_BODEGA','ID_PORCENTAJE','INCLUYE IVA','STOCK','STOCK MÍNIMO','STOCK MÁXIMO','DESCUENTO','EXPIRACIÓN','FACTURAR EXISTENCIA','UBICACION','SERIES','FOTO','OBSERVACIONES','','ESTADO'],
		        colModel:[      
		            {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},
		            {name:'codigo_barras',index:'codigo_barras',frozen : true, hidden: false, align:'left',search:true,width: ''},
		            {name:'codigo',index:'codigo',frozen : true, hidden: false, align:'left',search:true,width: ''},
		            {name:'descripcion',index:'descripcion',frozen : true, hidden: false, align:'left',search:true,width: ''},
		            {name:'precio_costo',index:'precio_costo',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'utilidad_minorista',index:'utilidad_minorista',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'utilidad_mayorista',index:'utilidad_mayorista',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'precio_minorista',index:'precio_minorista',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'precio_mayorista',index:'precio_mayorista',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'id_tipo_producto',index:'id_tipo_producto',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'id_categoria',index:'id_categoria',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'id_marca',index:'id_marca',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'id_medida',index:'id_medida',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'id_bodega',index:'id_bodega',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'id_porcentaje',index:'id_porcentaje',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'incluye_iva',index:'incluye_iva',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'stock',index:'stock',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'stock_minimo',index:'stock_minimo',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'stock_maximo',index:'stock_maximo',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'descuento',index:'descuento',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'expiracion',index:'expiracion',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'facturar_existencia',index:'facturar_existencia',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'ubicacion',index:'ubicacion',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'series',index:'series',frozen : true, hidden: true, align:'left',search:false,width: ''},
		            {name:'foto',index:'foto',frozen : true, hidden: true, align:'left',search:false,width:''},
		            {name:'observaciones',index:'observaciones',frozen : true, hidden: false, align:'left',search:false,width: ''},
		            {name:'estado',index:'estado',frozen : true, hidden: true, align:'left',search:false,width:''},
		            {name:'accion',index:'accion',frozen : true, hidden: false, align:'center',search:false,width:'150'},
		        ],          
		        rowNum: 10,
		        rowList: [10,20,30],
		        width: 600,
		        height: 350,
		        pager: pager_selector,        
		        sortname: 'id',
		        sortorder: 'asc',
		        shrinkToFit: false,
		        altRows: true,
		        multiselect: false,
		        multiboxonly: true,
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
		        ondblClickRow: function(rowid) {     	            	            
		            var gsr = jQuery(grid_selector).jqGrid('getGridParam','selrow');                                              
	            	var ret = jQuery(grid_selector).jqGrid('getRowData',gsr);

	            	$('#id_producto').val(ret.id);
	            	$("#logo").attr("src", "data/productos/fotos/"+ ret.foto);
	            	$('#codigo_barras').val(ret.codigo_barras);
	            	$('#codigo').val(ret.codigo);
	            	$('#descripcion').val(ret.descripcion);
	            	$('#precio_costo').val(ret.precio_costo);
	            	$('#utilidad_minorista').val(ret.utilidad_minorista);
	            	$('#utilidad_mayorista').val(ret.utilidad_mayorista);
	            	$('#precio_minorista').val(ret.precio_minorista);
	            	$('#precio_mayorista').val(ret.precio_mayorista);
	            	$("#select_tipo").select2('val', ret.id_tipo_producto).trigger("change");
	            	$("#select_categoria").select2('val', ret.id_categoria).trigger("change");
	            	$("#select_marca").select2('val', ret.id_marca).trigger("change");
	            	$("#select_medida").select2('val', ret.id_medida).trigger("change");
	            	$("#select_bodega").select2('val', ret.id_bodega).trigger("change");
	            	$("#select_iva").select2('val', ret.id_porcentaje).trigger("change");
	            	if(ret.incluye_iva == "SI") {
				    	$("#incluye_iva").prop("checked",true);
				    } else {
				    	$("#incluye_iva").prop("checked",false);
				    }
	            	$('#stock').val(ret.stock);
	            	$('#stock_minimo').val(ret.stock_minimo);
	            	$('#stock_maximo').val(ret.stock_maximo);
	            	if(ret.expiracion == "SI") {
				    	$("#expiracion").prop("checked",true);
				    } else {
				    	$("#expiracion").prop("checked",false);
				    }
				    if(ret.facturar_existencia == "SI") {
				    	$("#facturar_existencia").prop("checked",true);
				    } else {
				    	$("#facturar_existencia").prop("checked",false);
				    }
				    $("#select_proveedor").select2('val', ret.id_proveedor).trigger("change");
				    $('#ubicacion').val(ret.ubicacion);
				    if(ret.series == "SI") {
				    	$("#series").prop("checked",true);
				    } else {
				    	$("#series").prop("checked",false);
				    }
	            	$('#observaciones').val(ret.observaciones);
	            	$("#select_estado").select2('val', ret.estado).trigger("change");

		            $('#myModal').modal('hide'); 
		            $('#btn_0').attr('disabled', true); 
		            $('#btn_1').attr('disabled', false); 	            
		        },
		        editurl: "data/productos/app.php",
		        caption: "LISTA PRODUCTOS"
		    });
	
		    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

		    function aceSwitch(cellvalue, options, cell) {
		        setTimeout(function() {
		            $(cell).find('input[type=checkbox]')
		            .addClass('ace ace-switch ace-switch-5')
		            .after('<span class="lbl"></span>');
		        }, 0);
		    }	    	   

		    jQuery(grid_selector).jqGrid('navGrid', pager_selector, {   
		        edit: false,
		        editicon: 'ace-icon fa fa-pencil blue',
		        add: false,
		        addicon: 'ace-icon fa fa-plus-circle purple',
		        del: false,
		        deltext: 'Borrar',
		        delicon: 'ace-icon fa fa-trash-o red',
		        search: true,
		        searchicon: 'ace-icon fa fa-search orange',
		        //searchtext: 'Buscar',
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
		        sopt: ['cn','eq'],
	            defaultSearch: 'cn',            	       
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
	});
});