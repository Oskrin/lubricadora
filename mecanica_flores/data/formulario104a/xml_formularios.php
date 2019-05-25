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
    $resultado = $class->consulta("SELECT COUNT(*) AS count FROM formulario104");
       
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
        $SQL = "SELECT F.id, E.ruc, E.razon_social, F.anio, F.mes, F.formulario FROM formulario104 F, empresa E WHERE F.id_empresa = E.id ORDER BY $sidx $sord limit $limit offset $start";
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
        $s .= '<cell><![CDATA[<div class="btn-group"><button id="btn_2" data-ids="'.$row[0].'"  data-xml="'.$row[5].'" class="boton btn btn-sm btn-info" data-toggle="tooltip" title="Descargar XML"><i class="ace-icon fa fa-cloud-download bigger-120"></i></button></div>]]></cell>';

        $s .= "<cell>" . $row[1] . "</cell>";     
        $s .= "<cell>" . $row[2] . "</cell>";  
        $s .= "<cell>" . $row[3] . "</cell>";
        $s .= "<cell>" . $row[4] . "</cell>"; 
        $s .= "</row>";
    }
    $s .= "</rows>";

    echo $s;    
?>