<?php
    include_once('../../admin/class.php');
    $class = new constante();
    date_default_timezone_set('America/Guayaquil');
    setlocale (LC_TIME,"spanish");

    $page = $_GET['page'];
    $limit = $_GET['rows'];
    $sidx = $_GET['sidx'];
    $sord = $_GET['sord'];
    $search = $_GET['_search'];
    if (!$sidx)
        $sidx = 1;
    
    $count = 0;
    if($_GET['estado'] == "0") {
        $resultado = $class->consulta("SELECT  COUNT(*) AS count from nota_credito");
    } else {
        $resultado = $class->consulta("SELECT  COUNT(*) AS count from nota_credito WHERE estado = '".$_GET['estado']."'");
    }

    while ($row = $class->fetch_array($resultado)) {
        $count = $row[0];    
    }
       
    if ($count > 0 && $limit > 0) {
        $total_pages = ceil($count / $limit);
    } else {
        $total_pages = 0;
    }
    
    if ($page > $total_pages)
        $page = $total_pages;
    $start = $limit * $page - $limit;
    if ($start < 0)
        $start = 0;
    
    if($_GET['estado'] == "0") {
        if ($search == 'false') {
            $SQL = "SELECT N.id, N.numero_autorizacion, N.fecha_emision, C.nombre_comercial, N.estado, N.fecha_autorizacion, N.clave_acceso, N.total_nota FROM nota_credito N, factura_venta F, clientes C WHERE N.id_factura_venta = F.id AND F.id_cliente = C.id ORDER BY $sidx $sord limit $limit offset $start";
        } 
    } else {
        if ($search == 'false') {
            $SQL = "SELECT N.id, N.numero_autorizacion, N.fecha_emision, C.nombre_comercial, N.estado, N.fecha_autorizacion, N.clave_acceso, N.total_nota FROM nota_credito N, factura_venta F, clientes C WHERE N.id_factura_venta = F.id AND F.id_cliente = C.id AND N.estado = '".$_GET['estado']."' ORDER BY $sidx $sord limit $limit offset $start";
        }    
    }
    
    $resultado = $class->consulta($SQL); 
    header("Content-Type: text/html;charset=utf-8");   
    $s = "<?xml version='1.0' encoding='utf-8'?>";
    $s .= "<rows>";
    $s .= "<page>" . $page . "</page>";
    $s .= "<total>" . $total_pages . "</total>";
    $s .= "<records>" . $count . "</records>";
    while ($row = $class->fetch_array($resultado)) {
        $s .= "<row id='" . $row[0] . "'>";            
        $s .= "<cell>" . $row[0] . "</cell>";     
        $s .= "<cell>" . $row[1] . "</cell>";     
        if($row[4] == 1) {
            $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_1" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-success" data-toggle="tooltip" title="Visualizar PDF"><i class="ace-icon fa fa-file-pdf-o bigger-120"></i></button> <button id="btn_2" data-ids="'.$row[0].'"  data-xml="'.$row[1].'" class="boton btn btn-xs btn-info" data-toggle="tooltip" title="Descargar XML"><i class="ace-icon fa fa-cloud-download bigger-120"></i></button><button id="btn_3" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-warning" alt="" data-toggle="tooltip" title="Enviar Correo"><i class="ace-icon fa fa-envelope bigger-120"></i></button></div>]]></cell>';
        } else {
            if($row[4] == 2) {
                $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_1" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-success" data-toggle="tooltip" title="Visualizar PDF"><i class="ace-icon fa fa-file-pdf-o bigger-120"></i></button> <button id="btn_4" data-ids="'.$row[0].'" data-xml="'.$row[1].'"  class="boton btn btn-xs btn-danger" data-toggle="tooltip" title="Generar Archivos" ><i class="ace-icon fa fa-file-excel-o bigger-120"></i></button></div>]]></cell>';
            } else {
                if($row[4] == 3) {
                    $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_1" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-success" data-toggle="tooltip" title="Visualizar PDF"><i class="ace-icon fa fa-file-pdf-o bigger-120"></i></button> <button id="btn_2" data-ids="'.$row[0].'"  data-xml="'.$row[1].'" class="boton btn btn-xs btn-info" data-toggle="tooltip" title="Descargar XML"><i class="ace-icon fa fa-cloud-download bigger-120"></i></button><button id="btn_5" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-warning" alt="" data-toggle="tooltip" title="Reenviar Correo"><i class="ace-icon fa fa-envelope bigger-120"></i></button></div>]]></cell>';
                } else {
                    if($row[4] == 7) {
                        $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_1" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-success" data-toggle="tooltip" title="Visualizar PDF"><i class="ace-icon fa fa-file-pdf-o bigger-120"></i></button> <button id="btn_6" data-ids="'.$row[0].'" data-xml="'.$row[1].'"  class="boton btn btn-xs btn-danger" data-toggle="tooltip" title="Rechazado volver a validar Archivos"><i class="ace-icon fa fa-pencil bigger-120"></i></button></div>]]></cell>';
                    } else {
                        if($row[4] == 8) {
                            $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_7" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-danger" data-toggle="tooltip" title="Error en el WebService volver a Enviar"><i class="ace-icon fa fa-pencil bigger-120"></i></button></div>]]></cell>';
                        } else {    
                            if($row[4] == 9) { 
                                $s .= '<cell><![CDATA[<div class="hidden-sm hidden-xs btn-group"><button id="btn_9" data-ids="'.$row[0].'" data-xml="'.$row[1].'" class="boton btn btn-xs btn-danger" data-toggle="tooltip" title="Firmar y Generar"><i class="ace-icon fa fa-check-square-o bigger-120"></i></button></div>]]></cell>';
                            }     
                        }
                    }
                }
            }
        } 

        if($row[4] == 1) {
            $s .= "<cell>" . "AUTORIZADO". "</cell>";
        } else {
            if($row[4] == 2) {
                $s .= "<cell>" . "AUTORIZADO ,ERROR AL GENERADAR DOCUMENTOS". "</cell>";
            } else {
                if($row[4] == 3) {
                    $s .= "<cell>" . "AUTORIZADO CORREO NO ENVIADO". "</cell>";
                } else {
                    if($row[4] == 7) {
                        $s .= "<cell>" . "RECHAZADO  NO AUTORIZADO". "</cell>";
                    } else {
                        if($row[4] == 8) {
                            $s .= "<cell>" . "SIN FIRMAR, ERROR EN EL WEB SERVICE". "</cell>";
                        } else {
                            if($row[4] == 9) {
                                $s .= "<cell>" . "SIN FIRMAR, ARCHIVO SOLO GUARDADO". "</cell>";
                            }   
                        }
                    }
                }
            }               
        } 

        $s .= "<cell>" . $row[2] . "</cell>";     
        $s .= "<cell>" . $row[3] . "</cell>";  
        $s .= "<cell>" . $row[4] . "</cell>";                      
        $s .= "<cell>" . $row[6] . "</cell>";  
        $s .= "<cell>" . $row[7] . "</cell>";  
        $s .= "</row>";
    }
    $s .= "</rows>";

    echo $s;    
?>