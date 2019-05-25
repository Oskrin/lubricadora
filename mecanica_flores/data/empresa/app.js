app.controller('empresaController', function ($scope, $route, loaddatosSRI) {

	$scope.$route = $route;

	jQuery(function($) {
		$('[data-toggle="tooltip"]').tooltip();

		$( "#tabEmpresa" ).click(function(event) {
			event.preventDefault();  
		});	
		$("#tabEmpresa").on('shown.bs.tab', function(e) {
			$('.chosen-select').each(function() {
				var $this = $(this);
				$this.next().css({'width': $this.parent().width()});
			})	
		});		

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

			$('#file_1 , #file_2').ace_file_input({
				no_file:'Selecione un archivo ...',
				btn_choose:'Selecionar',
				btn_change:'Cambiar',
				droppable:false,
				onchange:null,
				thumbnail:false
			});
		} 

		// mascaras input
		$('#telefono1').mask('(999) 999-999');
		$('#telefono2').mask('(999) 999-9999');
		// fin

		// estilos file
		$("#file_1, #file_2").ace_file_input('reset_input');
		// fin

	 	// consultar ruc
		$scope.cargadatos = function(estado) {
			if($('#ruc').val() == '') {
				$.gritter.add({
					title: 'Error... Ingrese una Identificación',
					class_name: 'gritter-error gritter-center',
					time: 1000,
				});
				$('#ruc').focus();
			} else {
				$.ajax({
	                type: "POST",
	                url: "data/empresa/app.php",          
	                data:{consulta_ruc:'consulta_ruc',txt_ruc:$("#ruc").val()},
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

							$('#ruc').focus();
							$('#ruc').val("");
		            	} else {
		            		if(data.datosEmpresa.valid == 'true') {
		            			$('#razon_social').val(data.datosEmpresa.razon_social);
					            $('#nombre_comercial').val(data.datosEmpresa.nombre_comercial);
				            	$('#actividad_economica').val(data.datosEmpresa.actividad_economica);
				            	$('#representante_legal').val(data.establecimientos.adicional.representante_legal);
				            	$('#identificacion_representante').val(data.establecimientos.adicional.cedula);

				            	if (data.datosEmpresa.obligado_llevar_contabilidad == 'NO') {
				            		$("#obligacion").prop("checked", false);
				            	} else {
				            		if (data.datosEmpresa.obligado_llevar_contabilidad == 'SI') {
				            			$("#obligacion").prop("checked", true);	
				            		}	
				            	}				            		
				            }
		            	}
	                }
	            });	
	    	} 
	    }
	    // fin

		// validacion formulario empresa
		$('#form_empresa').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			ignore: "",
			rules: {
				ruc: {
					required: true,
					digits: true,
					minlength: 13				
				},
				razon_social: {
					required: true				
				},
				nombre_comercial: {
					required: true				
				},
				representante_legal: {
					required: true				
				},
				identificacion_representante: {
					required: true				
				},
				telefono2: {
					required: true,
					minlength: 10				
				},
				ciudad: {
					required: true				
				},
				direccion_matriz: {
					required: true				
				},
				direccion_establecimiento: {
					required: true				
				},
				establecimiento: {
					required: true				
				},
				punto_emision: {
					required: true				
				},	
			},
			messages: {
				ruc: {
					required: "Por favor, Indique una identificación",
					digits: "Por favor, ingrese solo dígitos",
					minlength: "Por favor, Especifique mínimo 13 digitos"
				},
				razon_social: { 	
					required: "Por favor, Indique la Razón Social",			
				},
				nombre_comercial: { 	
					required: "Por favor, Indique Nombre Comercial",			
				},
				representante_legal: { 	
					required: "Por favor, Indique Representante Legal",			
				},
				identificacion_representante: { 	
					required: "Por favor, Indique Identificación Representante",			
				},
				telefono2: {
					required: "Por favor, Indique número celular",
					minlength: "Por favor, Especifique mínimo 10 digitos"
				},
				ciudad: {
					required: "Por favor, Indique una ciudad",
				},
				direccion_matriz: {
					required: "Por favor, Indique Dirección Matriz",
				},
				direccion_establecimiento: {
					required: "Por favor, Indique Dirección Establecimiento",
				},
				establecimiento: {
					required: "Por favor, Indique Código Establecimiento",
				},
				punto_emision: {
					required: "Por favor, Indique Punto Emisión",
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

		// datos empresa
		function datos_empresa() {
			$.ajax({
                type: "POST",
                async: true,
                cache: false,
                url: "data/empresa/app.php",          
                data:{consulta_empresa:'consulta_empresa'},
                dataType: 'json',
                success: function(data) {
                	if (data == null) {
                		$('#btn_1').attr('disabled', true);
                		$("#logo").attr("src", "data/empresa/logo/defaul.png");
                	} else {
                		$('#btn_1').attr('disabled', false);
                		$('#btn_0').attr('disabled', true);

                		$('#id').val(data.id);
                		$("#logo").attr("src", "data/empresa/logo/"+ data.logo);	
                		$('#ruc').val(data.ruc);
                		$('#razon_social').val(data.razon_social);
                		$('#nombre_comercial').val(data.nombre_comercial);
                		$('#actividad_economica').val(data.actividad_economica);
                		$('#representante_legal').val(data.representante_legal);
                		$('#identificacion_representante').val(data.identificacion_representante);
                		$('#telefono1').val(data.telefono1);
                		$('#telefono2').val(data.telefono2);
                		$('#ciudad').val(data.ciudad);
                		$('#direccion_matriz').val(data.direccion_matriz);
                		$('#direccion_establecimiento').val(data.direccion_establecimiento);
                		$('#establecimiento').val(data.establecimiento);
                		$('#punto_emision').val(data.punto_emision);

                		if(data.obligacion == "SI") {
				    	$("#obligacion").prop("checked",true);
					    } else {
					    	$("#obligacion").prop("checked",false);
					    }
					    $('#contribuyente').val(data.contribuyente);
					    $('#autorizacion').val(data.autorizacion);
					    $('#clave_token').val(data.clave);
                		$('#correo').val(data.correo);
                		$('#sitio_web').val(data.sitio_web);
                		$('#slogan').val(data.slogan);
                	}
                }
            });		
		}
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

		// validaciones al iniciar
		datos_empresa();
		$('#ruc').focus();
		$("#ruc").attr("maxlength", "13");
    	$("#ruc").keypress(ValidNum);
    	$("#identificacion_representante").attr("maxlength", "13");
    	$("#identificacion_representante").keypress(ValidNum);
    	$("#establecimiento").attr("maxlength", "3");
    	$("#establecimiento").keypress(ValidNum);
    	$("#punto_emision").attr("maxlength", "3");
    	$("#punto_emision").keypress(ValidNum);
    	$("#obligacion").prop("checked",false);
    	$("#contribuyente").keypress(ValidNum);
    	$("#autorizacion").keypress(ValidNum);
    	// fin

		// guardar formulario
		$('#btn_0').click(function() {
			var respuesta = $('#form_empresa').valid();

			if (respuesta == true) {
				$('#btn_0').attr('disabled', true);
				var formData = new FormData(document.getElementById("form_empresa"));
				formData.append('Guardar', "Guardar");

				$.ajax({
			        url: "data/empresa/app.php",
			        data: formData,
			        type: "POST",
			        contentType: false,
			        processData: false,
	  				cache: false,
			        success: function (data) {
			        	if(data == "1") {
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
			var respuesta = $('#form_empresa').valid();

			if (respuesta == true) {
				$('#btn_1').attr('disabled', true);
				var formData = new FormData(document.getElementById("form_empresa"));
				formData.append('Modificar', "Modificar");

				$.ajax({
			        url: "data/empresa/app.php",
			        data: formData,
			        type: "POST",
			        contentType: false,
			        processData: false,
	  				cache: false,
			        success: function(data) {
			        	if(data == "2") {
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

		// refrescar formulario
		$('#btn_3').click(function() {
			location.reload(true);
		});
		// fin
	});
});