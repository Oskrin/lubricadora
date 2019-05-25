app.controller('clientesController', function ($scope, $route, loaddatosSRI) {

	$scope.$route = $route;
	
	jQuery(function($) {
		// tooltip
		$('[data-toggle="tooltip"]').tooltip();
		// fin

		// mascaras input
		$('#telefono1').mask('(999) 999-999');
		$('#telefono2').mask('(999) 999-9999');
		// fin

		// estilos file
		$("#file_1").ace_file_input('reset_input');
		// fin

		// estilos select2
		$(".select2").css({
			width: '100%',
		    allow_single_deselect: true,
		    no_results_text: "No se encontraron resultados",
		}).select2().on("change", function(e) {
			$(this).closest('form').validate().element($(this));
	    });

		$("#select_documento").select2({
		  	//allowClear: true 
		});
		// fin

		// event ctrl+b abrir
		Mousetrap.bind(['ctrl+b'], function(e) {
			$('#myModal').modal('show'); 	    
		});
		// fin

		// tabs
		$( "#tabCliente" ).click(function(event) {
			event.preventDefault();  
		});	

		$("#tabCliente").on('shown.bs.tab', function(e) {
			$('.chosen-select').each(function() {
				var $this = $(this);
				$this.next().css({'width': $this.parent().width()});
			})	
		});
		// fin

		if(!ace.vars['touch']) {			
			$('.chosen-select').chosen({allow_single_deselect:true}); 
			//resize the chosen on window resize		
			$(window).off('resize.chosen').on('resize.chosen', function() {
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

	    // select tipo documento 
	    $("#select_documento").change(function () { 
	    	var valor = $(this).val();

	    	if (valor == "2") {
	    		$('#btn_verificar').attr('disabled', false);
	    		$("#identificacion").val('');
	    		$("#identificacion").keypress(ValidNum);
	    		$("#identificacion").attr("maxlength", "10");
	    		$('#identificacion').focus();
	    	} else {
	    		if (valor == "1") {
	    			$('#btn_verificar').attr('disabled', false);
	    			$("#identificacion").val('');
	    			$("#identificacion").keypress(ValidNum);
	    			$("#identificacion").removeAttr("maxlength");
	    			$("#identificacion").attr("maxlength", "13");
	    			$('#identificacion').focus();
	    		} else {
	    			if (valor == "3") {
	    				$('#btn_verificar').attr('disabled', true);
	    				$("#identificacion").val('');
	    				$("#identificacion").unbind("keypress");
	    				$("#identificacion").attr("maxlength", "30");
	    				$('#identificacion').focus();
	    			}
	    		}
	    	}
	    });	
	    // fin

		// consultar cliente
		$("#identificacion").on("keypress",function(e) {
			if(e.keyCode == 13) { // tecla del alt para el entrer poner 13
				if($('#identificacion').val() == '') {
				$.gritter.add({
					title: 'Error... Ingrese una Identificación',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});
				$('#identificacion').focus();
				} else {
					if($('#select_documento').val() == '2') {
						$.ajax({
			                type: "POST",
			                url: "data/clientes/app.php",          
			                data:{consulta_cedula:'consulta_cedula',txt_ruc:$("#identificacion").val()},
			                dataType: 'json',
			                beforeSend: function() {
			                	$.blockUI({ css: { 
						            border: 'none', 
						            padding: '15px', 
						            backgroundColor: '#000', 
						            '-webkit-border-radius': '10px', 
						            '-moz-border-radius': '10px', 
						            opacity: .5, 
						            color: '#fff' 
						        	},
						            message: '<h3>Consultando, Por favor espere un momento    ' + '<i class="fa fa-spinner fa-spin"></i>' + '</h3>'
						    	});
			                },
		                    success: function(data) {
		                    	$.unblockUI();
	                    		if(data.datosPersona.valid == false) {
				            		$.gritter.add({
										title: 'Lo sentimos, Cédula Erronea',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});
									$('#identificacion').focus();
									$('#identificacion').val("");	
				            	} else {
				            		if(data.datosPersona.valid == true) {
					            		$('#razon_social').val(data.datosPersona.name);
					            		$('#nombre_comercial').val(data.datosPersona.name);
					            		$('#ciudad').val(data.datosPersona.residence);
					            		$('#direccion').val(data.datosPersona.streets);

					            		$('#telefono1').focus();
					            	}	 		
				            	}
			                }
			            });
					} else {
						if($('#select_documento').val() == '1') {
							$.ajax({
				                type: "POST",
				                url: "data/clientes/app.php",          
				                data:{consulta_ruc:'consulta_ruc',txt_ruc:$("#identificacion").val()},
				                dataType: 'json',
				                beforeSend: function() {
				                	$.blockUI({ css: { 
							            border: 'none', 
							            padding: '15px', 
							            backgroundColor: '#000', 
							            '-webkit-border-radius': '10px', 
							            '-moz-border-radius': '10px', 
							            opacity: .5, 
							            color: '#fff' 
							        	},
							            message: '<h3>Consultando, Por favor espere un momento    ' + '<i class="fa fa-spinner fa-spin"></i>' + '</h3>'
							    	});
				                },
			                    success: function(data) {
			                    	$.unblockUI();
			                    	if(data.datosEmpresa.valid == 'false') {
					            		$.gritter.add({
											title: 'Lo sentimos", "Usted no dispone de un RUC registrado en el SRI, o el número ingresado es Incorrecto."',
											class_name: 'gritter-error gritter-center',
											time: 1000,
										});

										$('#identificacion').focus();
										$('#identificacion').val("");
					            	} else {
					            		if(data.datosEmpresa.valid == 'true') {
							            	$('#razon_social').val(data.datosEmpresa.razon_social);
							            	if (data.datosEmpresa.nombre_comercial == "no disponible") {
							            		$('#nombre_comercial').val(data.datosEmpresa.razon_social);
							            	} else {
							            		$('#nombre_comercial').val(data.datosEmpresa.nombre_comercial);
							            	}
							            	$('#telefono1').focus();			            		
							            }
					            	}
				                }
				            });
						}	
					}
				}
			}
		});
		// fin

		// consultar identificacion
		$scope.cargadatos = function(estado) {
			if($('#identificacion').val() == '') {
				$.gritter.add({
					title: 'Error... Ingrese una Identificación',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});
				$('#identificacion').focus();
			} else {
				if($('#select_documento').val() == '2') {
					$.ajax({
		                type: "POST",
		                url: "data/clientes/app.php",          
		                data:{consulta_cedula:'consulta_cedula',txt_ruc:$("#identificacion").val()},
		                dataType: 'json',
		                beforeSend: function() {
		                	$.blockUI({ css: { 
					            border: 'none', 
					            padding: '15px', 
					            backgroundColor: '#000', 
					            '-webkit-border-radius': '10px', 
					            '-moz-border-radius': '10px', 
					            opacity: .5, 
					            color: '#fff' 
					        	},
					            message: '<h3>Consultando, Por favor espere un momento    ' + '<i class="fa fa-spinner fa-spin"></i>' + '</h3>'
					    	});
		                },
	                    success: function(data) {
	                    	$.unblockUI();
                    		if(data.datosPersona.valid == false) {
			            		$.gritter.add({
									title: 'Lo sentimos, Cédula Erronea',
									class_name: 'gritter-error gritter-center',
									time: 1000,
								});
								$('#identificacion').focus();
								$('#identificacion').val("");	
			            	} else {
			            		if(data.datosPersona.valid == true) {
				            		$('#razon_social').val(data.datosPersona.name);
				            		$('#nombre_comercial').val(data.datosPersona.name);
				            		$('#ciudad').val(data.datosPersona.residence);
				            		$('#direccion').val(data.datosPersona.streets);
				            	}	 		
			            	}
		                }
		            });
				} else {
					if($('#select_documento').val() == '1') {
						$.ajax({
			                type: "POST",
			                url: "data/clientes/app.php",          
			                data:{consulta_ruc:'consulta_ruc',txt_ruc:$("#identificacion").val()},
			                dataType: 'json',
			                beforeSend: function() {
			                	$.blockUI({ css: { 
						            border: 'none', 
						            padding: '15px', 
						            backgroundColor: '#000', 
						            '-webkit-border-radius': '10px', 
						            '-moz-border-radius': '10px', 
						            opacity: .5, 
						            color: '#fff' 
						        	},
						            message: '<h3>Consultando, Por favor espere un momento    ' + '<i class="fa fa-spinner fa-spin"></i>' + '</h3>'
						    	});
			                },
		                    success: function(data) {
		                    	$.unblockUI();
		                    	if(data.datosEmpresa.valid == 'false') {
				            		$.gritter.add({
										title: 'Lo sentimos", "Usted no dispone de un RUC registrado en el SRI, o el número ingresado es Incorrecto."',
										class_name: 'gritter-error gritter-center',
										time: 1000,
									});

									$('#identificacion').focus();
									$('#identificacion').val("");
				            	} else {
				            		if(data.datosEmpresa.valid == 'true') {
						            	$('#razon_social').val(data.datosEmpresa.razon_social);
						            	if (data.datosEmpresa.nombre_comercial == "no disponible") {
						            		$('#nombre_comercial').val(data.datosEmpresa.razon_social);
						            	} else {
						            		$('#nombre_comercial').val(data.datosEmpresa.nombre_comercial);
						            	}			            		
						            }
				            	}
			                }
			            });
					}	
				}
	    	} 
	    }
	    // fin

	    // comparar identificaciones
        $("#identificacion").keyup(function() {
	        $.ajax({
	            type: "POST",
	            url: "data/clientes/app.php",
	            data: {comparar_identificacion:'comparar_identificacion',identificacion: $("#identificacion").val(),tipo_documento: $("#select_documento").val()},
	            success: function(data) {
	                var val = data;
	                if (val == 1) {
	                    $("#identificacion").val('');
	                    $("#identificacion").focus();
	                    $.gritter.add({
							title: 'Error... El Cliente ya se encuentra Registrado',
							class_name: 'gritter-error gritter-center',
							time: 1000,
						});
					}
				}
			});
		});
		// fin

		//var response;
    	//jQuery.validator.addMethod("unique",  function(value, element) {
        //    $.ajax({
        //        type: "POST",
        //        url: "data/clientes/app.php",
        //        data: {comparar_identificacion:'comparar_identificacion',identificacion: $("#identificacion").val(),tipo_documento: $("#select_documento").val()},
        //        success: function(msg) {
        //            response = ( msg != 1 ) ? true : false;
        //        }
        //     });
        //    return response;
        //});

		//validacion formulario clientes
		$('#form_clientes').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			ignore: "",
			rules: {
				select_documento: {
					required: true				
				},
				identificacion: {
					required: true,
					//unique: true				
				},
				razon_social: {
					required: true				
				},
				nombre_comercial: {
					required: true				
				},
				telefono2: {
					required: true,
					minlength: 10				
				},
				ciudad: {
					required: true				
				},
				direccion: {
					required: true				
				},
				cupo_credito: {
					required: true				
				},	
			},
			messages: {
				select_documento: {
					required: "Por favor, Especifique Tipo Documento",
				},
				identificacion: {
					required: "Por favor, Indique una Identificación",
					//unique: "El Cliente ya se encuentra Registrado"
				},
				razon_social: { 	
					required: "Por favor, Indique una Razón Social",			
				},
				nombre_comercial: { 	
					required: "Por favor, Indique un Nombre Comercial",			
				},
				telefono2: {
					required: "Por favor, Indique número celular",
					minlength: "Por favor, Especifique mínimo 10 digitos"
				},
				ciudad: {
					required: "Por favor, Indique una Ciudad",
				},
				direccion: {
					required: "Por favor, Indique una Dirección",
				},
				cupo_credito: {
					required: "Por favor, Indique cupo Crédito",
				},
			},
			//para prender y apagar los errores
			highlight: function(e) {
				$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
			},
			success: function(e) {
				$(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
				$(e).remove();
			},
			submitHandler: function(form) {}
		});
		// Fin 

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

		// llenar combo tipo documento
		function llenar_select_tipo_documento() {
			$.ajax({
				url: 'data/clientes/app.php',
				type: 'post',
				data: {llenar_tipo_documento:'llenar_tipo_documento'},
				success: function(data) {
					$('#select_documento').html(data).trigger("change");
				}
			});
		}
		// fin

		// inicio
		$("#logo").attr("src", "data/clientes/fotos/defaul.jpg");
		llenar_select_tipo_documento();
		$('#btn_1').attr('disabled', true);
		$('#identificacion').focus();
		$("#identificacion").attr("maxlength", "10");
    	$("#identificacion").keypress(ValidNum);
    	$("#cupo_credito").keypress(ValidPun);
    	// fin

		// guardar formulario
		$('#btn_0').click(function() {
			var respuesta = $('#form_clientes').valid();
			
			if (respuesta == true) {
				$('#btn_0').attr('disabled', true);
				var formData = new FormData(document.getElementById("form_clientes"));
				formData.append('Guardar', "Guardar");

				$.ajax({
			        url: "data/clientes/app.php",
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
			        error: function (xhr, status, errorThrown) {
				        alert("Hubo un problema!");
				        console.log("Error: " + errorThrown);
				        console.log("Status: " + status);
				        console.dir(xhr);
			        }
			    });
			}		 
		});
		// fin

		// modificar formulario
		$('#btn_1').click(function() {
			var respuesta = $('#form_clientes').valid();

			if (respuesta == true) {
				$('#btn_1').attr('disabled', true);
				var formData = new FormData(document.getElementById("form_clientes"));
				formData.append('Modificar', "Modificar");

				$.ajax({
			        url: "data/clientes/app.php",
			        data: formData,
			        type: "POST",
			        contentType: false,
			        processData: false,
	  				cache: false,
			        success: function (data) {
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
			var archivo = "data/clientes/plantilla/clientes.xlsx";
			window.open(archivo);
		});
		// fin

		// cargar archivo 
		$('#btn_excel').click(function() {
			$('#btn_excel').attr('disabled', true);
			var formData = new FormData(document.getElementById("form_cargar"));
			formData.append('Cargar_excel', "Cargar_excel");;

			$.ajax({
                url: "data/clientes/app.php",
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
		        $(grid_selector).jqGrid('setGridWidth', $("#myModal .modal-dialog").width()-30);
		    });
		    //cambiar el tamaño de la barra lateral collapse/expand
		    var parent_column = $(grid_selector).closest('[class*="col-"]');
		    $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
		        if(event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
		            //para dar tiempo a los cambios de DOM y luego volver a dibujar!!!
		            setTimeout(function() {
		                $(grid_selector).jqGrid('setGridWidth', parent_column.width());
		            }, 0);
		        }
		    });

		    // buscador clientes
		    jQuery(grid_selector).jqGrid({	        
		        datatype: "xml",
		        url: 'data/clientes/xml_clientes.php',        
		        colNames: ['ID','ID_TIPO_DOCUMENTO','TIPO DOCUMENTO','IDENTIFICACIÓN','RAZÓN SOCIAL','NOMBRE COMERCIAL','TELÉFONO','CELULAR','CIUDAD','DIRECCIÓN','CORREO','CUPO','FOTO','OBSERVACIONES','','ESTADO'],
		        colModel:[      
		            {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},
		            {name:'id_tipo_documento',index:'id_tipo_documento', frozen:true, align:'left', search:false, hidden: true},
		            {name:'tipo_documento',index:'tipo_documento',frozen : true, hidden: false, align:'left',search:false,width:''},
		            {name:'identificacion',index:'identificacion',frozen : true, hidden: false, align:'left',search:true,width:'120'},
		            {name:'razon_social',index:'razon_social',frozen : true, hidden: false, align:'left',search:true,width:''},
		            {name:'nombre_comercial',index:'nombre_comercial',frozen : true, hidden: false, align:'left',search:true,width:''},
		            {name:'telefono1',index:'telefono1',frozen : true, hidden: false, align:'left',search:false,width:'110'},
		            {name:'telefono2',index:'telefono2',frozen : true, hidden: false, align:'left',search:false,width:'110'},
		            {name:'ciudad',index:'ciudad',frozen : true, hidden: false, align:'left',search:false,width:''},
		            {name:'direccion',index:'direccion',frozen : true, hidden: false, align:'left',search:false,width:''},
		            {name:'correo',index:'correo',frozen : true, hidden: false, align:'left',search:false,width:''},
		            {name:'cupo',index:'cupo',frozen : true, hidden: true, align:'left',search:false,width:''},
		            {name:'foto',index:'foto',frozen : true, hidden: true, align:'left',search:false,width:''},
		            {name:'observaciones',index:'observaciones',frozen : true, hidden: false, align:'left',search:false,width:''},
		            {name:'estado',index:'estado',frozen : true, hidden: true, align:'left',search:false,width:''},
		            {name:'accion',index:'accion',frozen : true, hidden: false, align:'center',search:false,width:'150'},
		        ],          
		        rowNum: 10,
		        height: 350,
		        rowList: [10,20,30],
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

	            	$('#id_cliente').val(ret.id);
	            	$("#logo").attr("src", "data/clientes/fotos/"+ ret.foto);
	            	$("#select_documento").select2('val', ret.id_tipo_documento).trigger("change");
	            	$('#identificacion').val(ret.identificacion);
	            	$('#razon_social').val(ret.razon_social);
	            	$('#nombre_comercial').val(ret.nombre_comercial);
	            	$('#telefono1').val(ret.telefono1);
	            	$('#telefono2').val(ret.telefono2);
	            	$('#ciudad').val(ret.ciudad);
	            	$('#direccion').val(ret.direccion);
	            	$('#correo').val(ret.correo);
	            	$('#cupo_credito').val(ret.cupo);
	            	$('#observaciones').val(ret.observaciones);
	            	$("#select_estado").select2('val', ret.estado).trigger("change");

		            $('#myModal').modal('hide'); 
		            $('#btn_0').attr('disabled', true); 
		            $('#btn_1').attr('disabled', false); 	            
		        },
		        editurl: "data/clientes/app.php",
		        caption: "LISTA CLIENTES"
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
		        delicon: 'ace-icon fa fa-trash-o red',
		        deltext: 'Borrar',
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