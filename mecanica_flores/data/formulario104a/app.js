app.controller('formulario104aController', function ($scope, $route) {
	$scope.$route = $route;

	jQuery(function($) {
		$('[data-toggle="tooltip"]').tooltip(); 

		// stilo select2
		$(".select2").css({
		    'width': '100%',
		    allow_single_deselect: true,
		    no_results_text: "No se encontraron resultados",
		    allowClear: true,
		});
		// fin

		// limpiar select2
		$("#select_anio,#select_mes,#select_pago,#select_banco").select2({
		  // allowClear: true
		});
		// fin

		// tabs
		$( "#tabFormulario" ).click(function(event) {
			event.preventDefault();  
		});	

		$("#tabFormulario").on('shown.bs.tab', function(e) {
			$('.chosen-select').each(function() {
				var $this = $(this);
				$this.next().css({'width': $this.parent().width()});
			})	
		});
		// fin

		//validacion formulario 104
		$('#form_104a').validate({
			errorElement: 'div',
			errorClass: 'help-block',
			focusInvalid: false,
			ignore: "",
			rules: {
				select_anio: {
					required: true				
				},
				select_mes: {
					required: true			
				},	
			},
			messages: {
				select_anio: {
					required: "Por favor, Especifique el Año",
				},
				select_mes: {
					required: "Por favor, Especifique el Mes",
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

		// recargar formulario
		function redireccionar() {
			setTimeout(function() {
			    location.reload(true);
			}, 1000);
		}
		// fin

		// guardar formulario
		$('#btn_0').click(function() {
			var respuesta = $('#form_104a').valid();
			
			if (respuesta == true) {
				//$('#btn_0').attr('disabled', true);
				var formData = new FormData(document.getElementById("form_104a"));
				formData.append('Guardar', "Guardar");

				$.ajax({
			        url: "data/formulario104a/app.php",
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
										+' <span> Formulario Creado Correctamente</span>',
								image: 	'dist/images/file_ok-1.png', 
								sticky: false,
								time: 2000											
							});
				    	} else {
				    		if(data == '0') {
					    		$.gritter.add({
		                            title: 	'<span>Mensaje de Error </span>',
		                            text: 	'<span class=""></span>'
		                                	+' <span>Sin Comprobantes</span>',
		                            image: 	'dist/images/file_error.png',
		                            sticky: false,
		                            time: 2000
		                        });
		                    }
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

		// abrir ventas
		$('#btn_1').click(function() {
			jQuery("#table1").jqGrid('setGridParam',{url:"data/formulario104a/xml_ventas.php?anio="+$("#select_anio").val()+"&mes="+ $("#select_mes").val(),page:1}).trigger("reloadGrid");
			$("#myModalVentas").modal('show');
		});
		// fin

		// abrir compras
		$('#btn_2').click(function() {
			jQuery("#table2").jqGrid('setGridParam',{url:"data/formulario104a/xml_compras.php?anio="+$("#select_anio").val()+"&mes="+ $("#select_mes").val(),page:1}).trigger("reloadGrid");
			$("#myModalCompras").modal('show');
		});
		// fin

		// abrir formularios
		$('#btn_3').click(function() {
			jQuery("#table3").jqGrid('setGridParam',{url:"data/formulario104a/xml_formularios.php",page:1}).trigger("reloadGrid");
			$("#myModalFormulario").modal('show');
		});
		// fin
	});

	// tabla local
	jQuery(function($) {
	    var grid_selector1 = "#table1";
	    var pager_selector1 = "#pager1";
	    
	    //cambiar el tamaño para ajustarse al tamaño de la página
	    $(window).on('resize.jqGrid', function () {        
	        $(grid_selector1).jqGrid( 'setGridWidth', $("#myModalVentas .modal-dialog").width()-30);
	    });
	    //cambiar el tamaño de la barra lateral collapse/expand
	    var parent_column = $(grid_selector1).closest('[class*="col-"]');
	    $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
	        if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
	            //para dar tiempo a los cambios de DOM y luego volver a dibujar!!!
	            setTimeout(function() {
	                $(grid_selector1).jqGrid('setGridWidth', parent_column.width());
	            }, 0);
	        }
	    });

	    // buscador total ventas
	    jQuery(grid_selector1).jqGrid({	        
	        datatype: "xml",
	        //url: "data/formulario104a/xml_ventas.php?anio="+$("#select_anio").val()+"&mes="+ $("#select_mes").val(),       
	        colNames: ['ID','CLIENTE','FECHA','SUBTOTAL','TARIFA 12%','TARIFA 0%','IVA', 'TOTAL'],
	        colModel:[      
	            {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},
	            {name:'cliente',index:'cliente',frozen : true,align:'left',search:true,width: '250'},
	            {name:'fecha',index:'fecha',frozen : true,align:'left',search:true,width: '100'},
	            {name:'subtotal',index:'subtotal',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	            {name:'tarifa',index:'tarifa',frozen : true,align:'left',search:false,width: '100'},
	            {name:'tarifa0',index:'tarifa0',frozen : true,align:'left',search:false, hidden: false,width: '100'},
	            {name:'iva',index:'iva',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	            {name:'total',index:'total',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	        ], 
	        rownumbers: false,         
	        rowNum: 10,       
	        //width:600,
	        shrinkToFit :false,
	        height: 'auto',
	        rowList: [10,20,30],
	        pager: pager_selector1,        
	        sortname: 'id',
	        sortorder: 'asc',
	        altRows: true,
	        multiselect: false,
	        multiboxonly: true,
	        viewrecords : true,
	        footerrow: true,
    		userDataOnFooter: true,
	        loadComplete : function() {
	        	$("#cb_" + this.id).click();
	        	var table = this;
	            setTimeout(function(){
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	            }, 0);
	        	var colSub = $(this).jqGrid('getCol','subtotal',false,'sum');
	        	var colTar = $(this).jqGrid('getCol','tarifa',false,'sum');
	        	var colTar0 = $(this).jqGrid('getCol','tarifa0',false,'sum');
	        	var coliva = $(this).jqGrid('getCol','iva',false,'sum');
	        	var colTotal = $(this).jqGrid('getCol','total',false,'sum');
	        	$(this).jqGrid('footerData','set',{cliente:'TOTAL', subtotal:colSub, tarifa:colTar, tarifa0:colTar0, iva:coliva, total:colTotal});
	        },
	        gridComplete: function() {	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector1).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector1).jqGrid('getRowData',gsr);
            	var id = ret.id;
	        },
	        
	        // caption: "LISTA SOLICITUD DE PAGOS"
	    });

	    $(window).triggerHandler('resize.jqGrid');//cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch( cellvalue, options, cell ) {
	        setTimeout(function(){
	            $(cell) .find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector1).jqGrid('navGrid',pager_selector1,
	    {   
	        edit: false,
	        editicon : 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon : 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon : 'ace-icon fa fa-trash-o red',
	        search: false,
	        searchicon : 'ace-icon fa fa-search orange',
	        refresh: true,
	        refreshicon : 'ace-icon fa fa-refresh green',
	        view: false,
	        viewicon : 'ace-icon fa fa-search-plus grey'
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
	        beforeShowForm : function(e) {
	            var form = $(e[0]);
	            if(form.data('styled')) return false;      
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_delete_form(form); 
	            form.data('styled', true);
	        },
	        onClick : function(e) {}
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

	// tabla local
	jQuery(function($) {
	    var grid_selector2 = "#table2";
	    var pager_selector2 = "#pager2";
	    
	    //cambiar el tamaño para ajustarse al tamaño de la página
	    $(window).on('resize.jqGrid', function () {        
	        $(grid_selector2).jqGrid( 'setGridWidth', $("#myModalCompras .modal-dialog").width()-30);
	    });
	    //cambiar el tamaño de la barra lateral collapse/expand
	    var parent_column = $(grid_selector2).closest('[class*="col-"]');
	    $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
	        if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
	            //para dar tiempo a los cambios de DOM y luego volver a dibujar!!!
	            setTimeout(function() {
	                $(grid_selector2).jqGrid('setGridWidth', parent_column.width());
	            }, 0);
	        }
	    });

	    // buscador total ventas
	    jQuery(grid_selector2).jqGrid({	        
	        datatype: "xml",
	        //url: "data/formulario104a/xml_ventas.php?anio="+$("#select_anio").val()+"&mes="+ $("#select_mes").val(),       
	        colNames: ['ID','PROVEEDORES','FECHA','SUBTOTAL','TARIFA 12%','TARIFA 0%','IVA', 'TOTAL'],
	        colModel:[      
	            {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},
	            {name:'cliente',index:'cliente',frozen : true,align:'left',search:true,width: '250'},
	            {name:'fecha',index:'fecha',frozen : true,align:'left',search:true,width: '100'},
	            {name:'subtotal',index:'subtotal',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	            {name:'tarifa',index:'tarifa',frozen : true,align:'left',search:false,width: '100'},
	            {name:'tarifa0',index:'tarifa0',frozen : true,align:'left',search:false, hidden: false,width: '100'},
	            {name:'iva',index:'iva',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	            {name:'total',index:'total',frozen : true,align:'left',search:false, hidden: false, formatter: 'number',width: '100'},
	        ], 
	        rownumbers: false,         
	        rowNum: 10,       
	        //width:600,
	        shrinkToFit :false,
	        height: 'auto',
	        rowList: [10,20,30],
	        pager: pager_selector2,        
	        sortname: 'id',
	        sortorder: 'asc',
	        altRows: true,
	        multiselect: false,
	        multiboxonly: true,
	        viewrecords : true,
	        footerrow: true,
    		userDataOnFooter: true,
	        loadComplete : function() {
	        	$("#cb_" + this.id).click();
	        	var table = this;
	            setTimeout(function(){
	                styleCheckbox(table);
	                updateActionIcons(table);
	                updatePagerIcons(table);
	                enableTooltips(table);
	            }, 0);
	        	var colSub = $(this).jqGrid('getCol','subtotal',false,'sum');
	        	var colTar = $(this).jqGrid('getCol','tarifa',false,'sum');
	        	var colTar0 = $(this).jqGrid('getCol','tarifa0',false,'sum');
	        	var coliva = $(this).jqGrid('getCol','iva',false,'sum');
	        	var colTotal = $(this).jqGrid('getCol','total',false,'sum');
	        	$(this).jqGrid('footerData','set',{cliente:'TOTAL', subtotal:colSub, tarifa:colTar, tarifa0:colTar0, iva:coliva, total:colTotal});
	        },
	        gridComplete: function() {	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector2).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector2).jqGrid('getRowData',gsr);
            	var id = ret.id;
	        },
	        
	        // caption: "LISTA SOLICITUD DE PAGOS"
	    });

	    $(window).triggerHandler('resize.jqGrid');//cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch( cellvalue, options, cell ) {
	        setTimeout(function(){
	            $(cell) .find('input[type=checkbox]')
	            .addClass('ace ace-switch ace-switch-5')
	            .after('<span class="lbl"></span>');
	        }, 0);
	    }	    	   

	    jQuery(grid_selector2).jqGrid('navGrid',pager_selector2,
	    {   
	        edit: false,
	        editicon : 'ace-icon fa fa-pencil blue',
	        add: false,
	        addicon : 'ace-icon fa fa-plus-circle purple',
	        del: false,
	        delicon : 'ace-icon fa fa-trash-o red',
	        search: false,
	        searchicon : 'ace-icon fa fa-search orange',
	        refresh: true,
	        refreshicon : 'ace-icon fa fa-refresh green',
	        view: false,
	        viewicon : 'ace-icon fa fa-search-plus grey'
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
	        beforeShowForm : function(e) {
	            var form = $(e[0]);
	            if(form.data('styled')) return false;      
	            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
	            style_delete_form(form); 
	            form.data('styled', true);
	        },
	        onClick : function(e) {}
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
		var grid_selector3 = "#table3";
	    var pager_selector3 = "#pager3";
	    
	    //cambiar el tamaño para ajustarse al tamaño de la página
	    $(window).on('resize.jqGrid', function () {        
	        $(grid_selector3).jqGrid( 'setGridWidth', $("#myModalFormulario .modal-dialog").width()-30);
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
	        //url: "data/formulario104a/xml_formularios.php",        
	        colNames: ['ID','ACCIONES','RUC','RAZÓN SOCIAL','AÑO','MES'],
	        colModel:[      
	            {name:'id',index:'id', align:'left',search:false,editable: true, hidden: true, editoptions: {readonly: 'readonly'}},
				{name:'acciones',index:'acciones',align:'center',width:140,frozen:true,},
				{name:'ruc',index:'ruc',width:150,frozen:true,},					
				{name:'razon_social',index:'razon_social',width:230,align:'center'},
				{name:'anio',index:'anio', align:'center',width:200},				
				{name:'mes',index:'mes', align:'center',width:170,hidden: false}
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

				    function descargarArchivo(nombreArchivo) {
				    	var rutaCompleta = 'data/formulario104a/formularios/'+nombreArchivo;

						var save = document.createElement('a');
			            save.href = rutaCompleta;
			            save.target = '_blank';
			            save.download = nombreArchivo;

			            var evt = new MouseEvent('click', {
			                'view': window,
			                'bubbles': true,
			                'cancelable': false
			            });
			            save.dispatchEvent(evt);
			            (window.URL || window.webkitURL).revokeObjectURL(save.href);  
					};

		    		if(id == "btn_2") {
		    			descargarArchivo(idxml);
	    			}
		    	});
	        },
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector3).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector3).jqGrid('getRowData',gsr);
            	var id = ret.id;
	        },
	        
	        caption: "LISTA FORMULARIOS 104A"
	    });

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

	    function aceSwitch(cellvalue, options, cell) {
	        setTimeout(function() {
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
});