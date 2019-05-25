app.controller('cuentas_pagarController', function ($scope, $interval) {

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
		// toggle
		$('[data-toggle="tooltip"]').tooltip();
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
		$("#select_tipo_precio").select2({
		  	// allowClear: true
		});
		// fin

	    // event ctrl+b abrir
		Mousetrap.bind(['ctrl+b'], function(e) {
			$('#myModal').modal('show'); 	    
		});
		// fin

		// llenar establecimiento / emision /token
		function llenar_info() {
			$.ajax({
				url: 'data/factura_venta/app.php',
				type: 'post',
				data: {llenar_infomacion:'llenar_infomacion'},
				dataType: 'json',
				success: function(data) {
					$("#btn_0").attr("disabled", false);
					$scope.establecimiento = data.establecimiento;
					$scope.emision = data.emision;

					$("#logo").attr("src", "data/empresa/logo/"+ data.imagen);
					$scope.ruc = data.ruc;
					$scope.razon_social = data.razon_social;
					$scope.est = data.establecimiento;	
					$scope.emi = data.emision;
					$scope.matriz = data.matriz;
					$scope.sucursal = data.sucursal;

					if (data.contribuyentel == null) {
						$scope.contribuyente = "--";	
					} else {
						$scope.contribuyente = data.contribuyente;
					}
					
					$scope.obligado = data.obligacion;
				}
			});
		}
		// fin

		// inicio llamado funciones
		llenar_info();
		// fin

		// modal detalle
		$scope.methodoview = function(id) { 
			$('#myModal').modal('show');
			jQuery("#table2").jqGrid('setGridParam',{url:"data/cuentas_pagar/xml_pagos.php?id_cuenta="+id,page:1}).trigger("reloadGrid");
		}
		// fin

		// modal pagos
		$scope.methodopagos = function(id) {
			$("#id_detalle").val(id);
			$.ajax({
				url: 'data/cuentas_pagar/app.php',
				type: 'post',
				data: {cargar_detalle:'cargar_detalle', id:id},
				dataType: 'json',
				success: function(data) {
					$("#monto").val(data.cuota);	
				}
			});

			//console.log(id)
			$('#myModal2').modal('show');
			var $exampleModal = $("#myModal2"),
		    $exampleModalClose = $(".modal-header button");

		    $exampleModal.on("shown.bs.modal", function() {
		        document.activeElement.blur();
		        $('#descripcion').focus();
		    });
		}
		// fin

		// anular proforma
		$('#btn_pago').click(function() {
			var formulario = $("#form_pago").serialize();
			var submit = "Guardar";

			$.ajax({
                type: "POST",
                url: "data/cuentas_pagar/app.php",
                data: formulario +"&btn_guardar=" + submit,
                success: function(data) {
                	$('#table2').trigger('reloadGrid');
                	var val = data;
                	if(val == 1) {
                		bootbox.alert("Gracias! Por su Información Pago Registrado Correctamente!", function() {
							location.reload();
						});	
                	}		
                }
            });
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

	    // tabla local proformas
	    jQuery(grid_selector).jqGrid({
	        datatype: "xml",
		    url: 'data/cuentas_pagar/xml_cuentas.php',
	        colNames: ['ID','RUC/CI','PROVEEDOR','FACTURA','FECHA CREDITO','TIEMPO','PLAZO','MONTO','SALDO','CUENTA','ACCION'],
	        colModel:[ 
			    {name: 'id',index:'id', frozen:true, align:'left', search:false, hidden: true},
			    {name: 'identificacion', index: 'identificacion', editable: false, search: false, hidden: false, editrules: {edithidden: false}, align: 'center', frozen: true, width: 120},   
	            {name: 'proveedor', index: 'proveedor', editable: false, search: false, hidden: false, editrules: {edithidden: false}, align: 'center', frozen: true, width: 300},
	            {name: 'factura', index: 'factura', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 120},
	            {name: 'fecha_credito', index: 'fecha_credito', editable: true, frozen: true, editrules: {required: true}, align: 'center', width: 100, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return numeros(e)})}}}, 
	            {name: 'tiempo', index: 'tiempo', editable: true, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 110, editoptions:{maxlength: 10, size:15,dataInit: function(elem){$(elem).bind("keypress", function(e) {return punto(e)})}}}, 
	            {name: 'plazo', index: 'plazo', editable: false, frozen: true, editrules: {required: true}, align: 'center', width: 70},
	            {name: 'monto', index: 'monto', editable: false, hidden: false, frozen: true, editrules: {required: true}, align: 'center', width: 90},
	            {name: 'saldo', index: 'saldo', editable: false, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 90},
	            {name: 'estado',index:'estado',frozen : true, hidden: false, align:'left',search:false,width:'100'},
	            {name: 'accion', index: 'accion', editable: false, search: false, frozen: true, editrules: {required: true}, align: 'center', width: 110},
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
				var ids = jQuery(grid_selector).jqGrid('getDataIDs');
				for(var i = 0;i < ids.length;i++) {
					var id_cuenta = ids[i];
					ver = "<a onclick=\"angular.element(this).scope().methodoview('"+id_cuenta+"')\" title='Ver Pagos' ><i class='fa fa fa-external-link' style='cursor:pointer; cursor: hand'> PAGOS</i></a>"; 
					
					jQuery(grid_selector).jqGrid('setRowData',ids[i],{accion:ver});
				}	
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

	    jQuery(grid_selector).jqGrid('navGrid', pager_selector, {   //navbar options
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

	    // buscador pagos
	    jQuery(grid_selector2).jqGrid({
	    	datatype: "xml",        
	        autoencode: false,
	        colNames: ['ID','Nº Documento','Fecha Pago','Descripción','Cuota','Saldo','Estado','Acción'],
	        colModel:[ 
			    {name:'id',index:'id', frozen:true, align:'left', search:false, hidden: true},   
	            {name:'documento',index:'documento', frozen:true, align:'left', search:true, hidden: false, width: '100'},
	            {name:'fecha_pago',index:'fecha_pago',frozen : true,align:'left', search:true, width: '80'},
	            {name:'descipcion',index:'descipcion',frozen : true, hidden: false, align:'left', search:true,width: '260'},
	            {name:'cuota',index:'cuota',frozen : true, align:'left', search:false,width: '80'},
	            {name:'saldo',index:'saldo',frozen : true, align:'left', search:false,width: '80'},
	            {name:'estado',index:'estado',frozen : true, hidden: false, align:'left',search:false,width:'90'},
	            {name:'accion', index:'accion', editable: false, hidden: false, frozen: true, search:false, editrules: {required: true}, align: 'center', width: '80px'}
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
					var id_detalles = ids[i];
					pagar = "<a onclick=\"angular.element(this).scope().methodopagos('"+id_detalles+"')\" title='Asignar Pago' ><i class='fa fa fa-hand-o-right' style='cursor:pointer; cursor: hand'> PAGAR</i></a>"; 				
					jQuery(grid_selector2).jqGrid('setRowData',ids[i],{accion:pagar});
				}	
			},
	        ondblClickRow: function(rowid) {     	            	            
	            var gsr = jQuery(grid_selector2).jqGrid('getGridParam','selrow');                                              
            	var ret = jQuery(grid_selector2).jqGrid('getRowData',gsr);

				//$('#myModal').modal('hide'); 
		        //$('#btn_0').attr('disabled', true);
		        //$("#btn_3").attr("disabled", false);           
	        },
	        caption: "LISTA PAGOS"
	    });

	    $(window).triggerHandler('resize.jqGrid'); // cambiar el tamaño para hacer la rejilla conseguir el tamaño correcto

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
	        beforeShowForm: function(e){
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
});