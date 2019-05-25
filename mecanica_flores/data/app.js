var app = angular.module('scotchApp', ['ngRoute','ngResource','ngStorage']);

//app.directive('hcChart', function () {
//    return {
//        restrict: 'E',
//        template: '<div></div>',
//        scope: {
//            options: '='
//        },
//        link: function (scope, element) {
//            Highcharts.chart(element[0], scope.options);
//        }
//    };
//})

app.directive('hcPieChart', function () {
    return {
        restrict: 'E',
        template: '<div></div>',
        scope: {
            title: '@',
            data: '='
        },
        link: function (scope, element) {
            Highcharts.chart(element[0], {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: scope.title
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'Total',
                    data: scope.data
                }]
            });
        }
    };
})

app.directive('hcColumnChart', function () {
    return {
        restrict: 'E',
        template: '<div></div>',
        scope: {
            title: '@',
            data: '='
        },
        link: function (scope, element) {
            Highcharts.chart(element[0], {
                chart: {
                    type: 'column'
                },
                title: {
                    text: scope.title
                },
                //xAxis: {
                //    categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
                //},
                xAxis: {
                    categories: ['Sep', 'Oct', 'Nov', 'Dic']
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} $'
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'Total Ventas',
                    colorByPoint: true,
                    data: scope.data,
                    showInLegend: false
                }]
            });
        }
    };
})

// configure our routes
app.config(function($routeProvider) {
    $routeProvider
        // route page initial
        .when('/', {
            templateUrl : 'data/inicio/index.html',
            // controller  : 'mainController',
            activetab: 'inicio'
        })
        // route empresa
        .when('/empresa', {
            templateUrl : 'data/empresa/index.html',
            controller  : 'empresaController',
            activetab: 'empresa'
        })
        // route auditoria
        .when('/auditoria', {
            templateUrl : 'data/auditoria/index.html',
            controller  : 'auditoriaController',
            activetab: 'auditoria'
        })
        // route ambiente
        .when('/tipo_ambiente', {
            templateUrl : 'data/tipo_ambiente/index.html',
            controller  : 'tipo_ambienteController',
            activetab: 'tipo_ambiente'
        })
        // route emision
        .when('/tipo_emision', {
            templateUrl : 'data/tipo_emision/index.html',
            controller  : 'tipo_emisionController',
            activetab: 'tipo_emision'
        })
        // route tipo comprobante
        .when('/tipo_comprobante', {
            templateUrl : 'data/tipo_comprobante/index.html',
            controller  : 'tipo_comprobanteController',
            activetab: 'tipo_comprobante'
        })
        // route tipo documento
        .when('/tipo_documento', {
            templateUrl : 'data/tipo_documento/index.html',
            controller  : 'tipo_documentoController',
            activetab: 'tipo_documento'
        })
        // route tipo producto
        .when('/tipo_producto', {
            templateUrl : 'data/tipo_producto/index.html',
            controller  : 'tipo_productoController',
            activetab: 'tipo_producto'
        })
        // route tipo impuesto
        .when('/tipo_impuesto', {
            templateUrl : 'data/tipo_impuesto/index.html',
            controller  : 'tipo_impuestoController',
            activetab: 'tipo_impuesto'
        })
        // route tarifa impuesto
        .when('/tarifa_impuesto', {
            templateUrl : 'data/tarifa_impuesto/index.html',
            controller  : 'tarifa_impuestoController',
            activetab: 'tarifa_impuesto'
        })
        // route formas pago
        .when('/formas_pago', {
            templateUrl : 'data/formas_pago/index.html',
            controller  : 'formas_pagoController',
            activetab: 'formas_pago'
        })
        // route secuencia comprobantes
        .when('/secuencia_comprobantes', {
            templateUrl : 'data/secuencia_comprobantes/index.html',
            controller  : 'secuencia_comprobantesController',
            activetab: 'secuencia_comprobantes'
        })
        // route categorias
        .when('/categorias', {
            templateUrl : 'data/categorias/index.html',
            controller  : 'categoriasController',
            activetab: 'categorias'
        })
        // route marcas
        .when('/marcas', {
            templateUrl : 'data/marcas/index.html',
            controller  : 'marcasController',
            activetab: 'marcas'
        })
        // route medida
        .when('/medida', {
            templateUrl : 'data/medida/index.html',
            controller  : 'medidaController',
            activetab: 'medida'
        })
        // route bodegas
        .when('/bodegas', {
            templateUrl : 'data/bodegas/index.html',
            controller  : 'bodegasController',
            activetab: 'bodegas'
        })
        // route clientes
        .when('/clientes', {
            templateUrl : 'data/clientes/index.html',
            controller  : 'clientesController',
            activetab: 'clientes'
        })
        // route proveedores
        .when('/proveedores', {
            templateUrl : 'data/proveedores/index.html',
            controller  : 'proveedoresController',
            activetab: 'proveedores'
        })
        // route transportistas
        .when('/transportistas', {
            templateUrl : 'data/transportistas/index.html',
            controller  : 'transportistasController',
            activetab: 'transportistas'
        })
        // route productos
        .when('/productos', {
            templateUrl : 'data/productos/index.html',
            controller  : 'productosController',
            activetab: 'productos'
        })
        // route importar
        .when('/importar', {
            templateUrl : 'data/importar/index.html',
            controller  : 'importarController',
            activetab: 'importar'
        })
         // route inventario
        .when('/inventario', {
            templateUrl : 'data/inventario/index.html',
            controller  : 'inventarioController',
            activetab: 'inventario'
        })
        // route movimientos
        .when('/movimientos', {
            templateUrl : 'data/movimientos/index.html',
            controller  : 'movimientosController',
            activetab: 'movimientos'
        })
          // route login
        .when('/login', {
            templateUrl : 'data/login/index.html',
            controller  : 'loginController',
        })
        // proceso proformas
        .when('/proformas', {
            templateUrl : 'data/proformas/index.html',
            controller  : 'proformasController',
            activetab: 'proformas'
        })
        // proceso factura compra
        .when('/factura_compra', {
            templateUrl : 'data/factura_compra/index.html',
            controller  : 'factura_compraController',
            activetab: 'factura_compra'
        })
        // proceso retencion
        .when('/retencion', {
            templateUrl : 'data/retencion/index.html',
            controller  : 'retencionController',
            activetab: 'retencion'
        })
        // proceso factura venta
        .when('/factura_venta', {
            templateUrl : 'data/factura_venta/index.html',
            controller  : 'factura_ventaController',
            activetab: 'factura_venta'
        })
        // proceso registro venta
        .when('/registro_ventas', {
            templateUrl : 'data/registro_ventas/index.html',
            controller  : 'registro_ventasController',
            activetab: 'registro_ventas'
        })
        // proceso nota venta
        .when('/nota_venta', {
            templateUrl : 'data/nota_venta/index.html',
            controller  : 'nota_ventaController',
            activetab: 'nota_venta'
        })
        // proceso nota credito
        .when('/nota_credito', {
            templateUrl : 'data/nota_credito/index.html',
            controller  : 'nota_creditoController',
            activetab: 'nota_credito'
        })
        // proceso guia remision
        .when('/guia_remision', {
            templateUrl : 'data/guia_remision/index.html',
            controller  : 'guia_remisionController',
            activetab: 'guia_remision'
        })
        // proceso ingreso
        .when('/ingresos', {
            templateUrl : 'data/ingresos/index.html',
            controller  : 'ingresosController',
            activetab: 'ingresos'
        })
        // proceso egreso
        .when('/egresos', {
            templateUrl : 'data/egresos/index.html',
            controller  : 'egresosController',
            activetab: 'egresos'
        })
        // route kardex
        .when('/kardex', {
            templateUrl : 'data/kardex/index.html',
            controller  : 'kardexController',
            activetab: 'kardex'
        })
        // proceso cuentas cobrar
        .when('/cuentas_cobrar', {
            templateUrl : 'data/cuentas_cobrar/index.html',
            controller  : 'cuentas_cobrarController',
            activetab: 'cuentas_cobrar'
        })
        // proceso cuentas pagar
        .when('/cuentas_pagar', {
            templateUrl : 'data/cuentas_pagar/index.html',
            controller  : 'cuentas_pagarController',
            activetab: 'cuentas_pagar'
        })
        // route formulario104a
        .when('/formulario104a', {
            templateUrl : 'data/formulario104a/index.html',
            controller  : 'formulario104aController',
            activetab: 'formulario104a'
        })
        // route cargos
        .when('/cargos', {
            templateUrl : 'data/cargos/index.html',
            controller  : 'cargosController',
            activetab: 'cargos'
        })
        // route usuarios
        .when('/usuarios', {
            templateUrl : 'data/usuarios/index.html',
            controller  : 'usuariosController',
            activetab: 'usuarios'
        })
        // route privilegios
        .when('/privilegios', {
            templateUrl : 'data/privilegios/index.html',
            controller  : 'privilegiosController',
            activetab: 'privilegios'
        })
        // route cuenta
        .when('/cuenta', {
            templateUrl : 'data/cuenta/index.html',
            controller  : 'cuentaController',
            activetab: 'cuenta'
        })
        // proceso reportes varios
        .when('/reporte_varios', {
            templateUrl : 'data/reporte_varios/index.html',
            controller  : 'reporte_variosController',
            activetab: 'reporte_varios'
        })
        // proceso reportes ventas
        .when('/reporte_ventas', {
            templateUrl : 'data/reporte_ventas/index.html',
            controller  : 'reporte_ventasController',
            activetab: 'reporte_ventas'
        })
});

app.factory('Auth', function($location) {
    var user;
    return {
        setUser : function(aUser) {
            user = aUser;
        },
        isLoggedIn : function() {
            var ruta = $location.path();
            var ruta = ruta.replace("/","");
            var accesos = JSON.parse(Lockr.get('users'));
                accesos.push('inicio');
                accesos.push('');

            var a = accesos.lastIndexOf(ruta);
            if (a < 0) {
                return false;    
            } else {
                return true;
            }
        }
    }
});


app.run(['$rootScope', '$location', 'Auth', function ($rootScope, $location, Auth) {
    $rootScope.$on('$routeChangeStart', function (event) {
        var rutablock = $location.path();
        if (!Auth.isLoggedIn()) {
            event.preventDefault();
            swal({
                title: "Lo sentimos acceso denegado",
                type: "warning",
            });
        } else { }
    });
}]);

// consumir servicios sri
app.factory('loaddatosSRI', function($resource) {
    return $resource("http://186.4.167.12/appserviciosnext/public/index.php/getDatos/:id", {
        id: "@id"
    });
});
// fin

    