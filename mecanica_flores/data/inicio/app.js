app.controller('mainController', function ($scope, $route, $timeout) {
    $scope.$route = $route;

    jQuery(function($) {
    // funcion ventas mensual
    function ventas_mensual() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_ventas_mensual:'cargar_ventas_mensual'},
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data == null) {
                    $scope.columnData = [{
                        name: "SIN NOTAS",
                        y: 0
                    }]
                } else {
                    $scope.columnData = data;
                }
            }
        });
    }
    // fin

    // funcion producto mas vendido
    function productos_vendidos() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_productos_vendidos:'cargar_productos_vendidos'},
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data == null) {
                    $scope.pieData = [{
                        name: "SIN NOTAS",
                        y: 0
                    }]
                } else {
                    $scope.pieData = data;
                }
            }
        });
    }
    // fin

    // funcion total proformas
    function proformas() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_proformas:'cargar_proformas'},
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.total_proforma == null) {
                    $scope.proformas = '0.00';
                } else {
                    $scope.proformas = parseFloat(data.total_proforma).toFixed(2);  
                }   
            }
        });
    }
    // fin

    // funcion total facturas venta
    function factura_venta() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_facturas_venta:'cargar_facturas_venta'},
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.total_venta == null) {
                    $scope.factura_venta = '0.00';
                } else {
                    $scope.factura_venta = parseFloat(data.total_venta).toFixed(2); 
                }                
            }
        });
    }
    // fin

    // funcion total notas venta
    function nota_venta() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_notas_venta:'cargar_notas_venta'},
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.total_nota == null) {
                    $scope.nota_venta = '0.00';
                } else {
                    $scope.nota_venta = parseFloat(data.total_nota).toFixed(2); 
                }                
            }
        });
    }
    // fin

    // funcion informacion
    function informacion() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_informacion:'cargar_informacion'},
            dataType: 'json',
            async: false,
            success: function(data) {
                $scope.usuario = data.usuario;
                $scope.conexion = data.fecha_creacion;               
            }
        });
    }
    // fin

    function cargar_stock_minimo() {
        $.ajax({
            url: 'data/inicio/app.php',
            type: 'post',
            data: {cargar_tabla:'cargar_tabla'},
            dataType: 'json',
            success: function(response) { 
                $scope.productos = response;
            }
        });
    }

    function cargar_datos() {
        $.ajax({
            url: 'data/inicio/app.php',
            type: 'post',
            data: {cargar_tabla2:'cargar_tabla2'},
            dataType: 'json',
            success: function(data) { 
                $scope.disponibles = data;
            }
        });
    }

    // funcion chat
    function chat() {
        $.ajax({
            type: "POST",
            url: "data/inicio/app.php",
            data: {cargar_chat:'cargar_chat'},
            dataType: 'json',
            async: false,
            success: function(data) {
                $scope.datos = data;              
            }
        });
    }
    // fin

    // funcion  guardar chat
    function save_chat() {
        if ($('#message').val() == '') {
            $.gritter.add({
                title: 'Error... Ingrese un mensaje',
                class_name: 'gritter-error gritter-center',
                time: 1000,
            });
            $('#message').focus(); 
        } else {
            $.ajax({
                type: "POST",
                url: "data/inicio/app.php",
                data: {guardar_chat:'guardar_chat', mensaje: $('#message').val()},
                dataType: 'json',
                async: false,
                success: function(data) {
                    if (data == 1) {
                        $('#message').val('');
                        $('#message').focus();
                        chat();
                    }
                }
            });
        }    
    }
    // fin

    // scroll final
    function scroll_buttom_chat() {
        // $timeout(function() {
        //     var scroller = document.getElementById("style-5");
        //     scroller.scrollTop = scroller.scrollHeight;
        // }, 0, false);
    }
    // fin

    // enviar chat
    $scope.enviar_chat = function (data, event) {
        save_chat();    
    }
    // fin

    // funcion enter
    $scope.myFunction = function(keyEvent) {
      if (keyEvent.which === 13)
        save_chat();
        // scroll_buttom_chat();
    }
    // fin

    // incio funciones
    cargar_datos();
    ventas_mensual();
    productos_vendidos();
    proformas();
    factura_venta();
    nota_venta();
    informacion();
    cargar_stock_minimo();
    scroll_buttom_chat();
    chat();
    // fin
    }); 
});