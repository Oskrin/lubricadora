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
    $resultado = $class->consulta("SELECT COUNT(*) AS count FROM factura_venta WHERE TO_CHAR(fecha_creacion::date,'YYYY')='".$_GET['anio']."' AND TO_CHAR(fecha_creacion::date,'MM')='".$_GET['mes']."'");         
    while ($row = $class->fetch_array($resultado)) {
        $count = $count + $row[0];    
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
    
    if ($search == 'false') {
        $SQL = "SELECT F.id, C.razon_social, F.fecha_emision, F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_venta FROM factura_venta F, clientes C WHERE F.id_cliente = C.id AND TO_CHAR(F.fecha_creacion::date,'YYYY')='".$_GET['anio']."' AND TO_CHAR(F.fecha_creacion::date,'MM')='".$_GET['mes']."' ORDER BY $sidx $sord offset $start limit $limit";
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
        $s .= "<cell>" . $row[2] . "</cell>";
        $s .= "<cell>" . $row[3] . "</cell>";
        $s .= "<cell>" . $row[4] . "</cell>";
        $s .= "<cell>" . $row[5] . "</cell>";
        $s .= "<cell>" . $row[6] . "</cell>";
        $s .= "<cell>" . $row[7] . "</cell>";
        $s .= "</row>";
    }
    
    $s .= "</rows>";
    echo $s;    
?>