<?php
    date_default_timezone_set('America/Guayaquil');
    require_once "../../phpexcel/PHPExcel.php";
    include_once('../../admin/class.php');
    $class = new constante();
    session_start();

    // VARIABLES DE PHP
    $objPHPExcel = new PHPExcel();
    $Archivo = "clientes.xls";

    // Propiedades de archivo Excel
    $objPHPExcel->getProperties()->setCreator("FACTSERVICE")
                ->setLastModifiedBy("FACTSERVICE")
                ->setTitle("Reporte XLS")
                ->setSubject("LISTA CLIENTES")
                ->setDescription("")
                ->setKeywords("")
                ->setCategory("");

    // PROPIEDADES DEL  LA CELDA
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Verdana');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

    $y = 6;
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B" . $y, 'Tipo Identificación')
                ->setCellValue("C" . $y, 'Identificación')
                ->setCellValue("D" . $y, 'Razón Social')
                ->setCellValue("E" . $y, 'Nombre Comercial')
                ->setCellValue("F" . $y, 'Teléfono')
                ->setCellValue("G" . $y, 'Movil')
                ->setCellValue("H" . $y, 'Ciudad')
                ->setCellValue("I" . $y, 'Dirección')
                ->setCellValue("J" . $y, 'Correo');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B6:J6')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFEEEEEE');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B6:J6')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $borders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            )
        ),
    );

    $objPHPExcel->getActiveSheet()
                ->getStyle('B6:J6')
                ->applyFromArray($borders);

    // Cabezera
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B2", 'REPORTE CLIENTES');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B2:J2')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('B2:J2');

    $objPHPExcel->getActiveSheet()
                ->getStyle("B2:J2")
                ->getFont()
                ->setBold(true)
                ->setName('Verdana')
                ->setSize(18);
    // fin

    $resultado = $class->consulta("SELECT T.nombre_tipo_documento, C.identificacion, C.razon_social, C.nombre_comercial, C.telefono1, C.telefono2, C.ciudad, C.direccion, C.correo FROM clientes C, tipo_documento T WHERE C.id_tipo_documento = T.id AND C.estado = '1' ORDER BY C.id");

    while ($row = $class->fetch_array($resultado)) { 
        $y++;
        //BORDE DE LA CELDA
        $objPHPExcel->setActiveSheetIndex(0)
                    ->getStyle('B' . $y . ":J" . $y)
                    ->applyFromArray($borders);
        //MOSTRAMOS LOS VALORES
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("B" . $y, $row[0])
                    ->setCellValue("C" . $y, $row[1])
                    ->setCellValue("D" . $y, $row[2])
                    ->setCellValue("E" . $y, $row[3])
                    ->setCellValue("F" . $y, $row[4])
                    ->setCellValue("G" . $y, $row[5])
                    ->setCellValue("H" . $y, $row[6])
                    ->setCellValue("I" . $y, $row[7])
                    ->setCellValue("J" . $y, $row[8]);
    }

    // DATOS DE LA SALIDA DEL EXCEL
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $Archivo . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    exit;
?>

