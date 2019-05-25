<?php
    date_default_timezone_set('America/Guayaquil');
    require_once "../../phpexcel/PHPExcel.php";
    include_once('../../admin/class.php');
    $class = new constante();
    session_start();

    // VARIABLES DE PHP
    $objPHPExcel = new PHPExcel();
    $Archivo = "resumen_ventas.xls";

    // Propiedades de archivo Excel
    $objPHPExcel->getProperties()->setCreator("FACTSERVICE")
                ->setLastModifiedBy("FACTSERVICE")
                ->setTitle("Reporte XLS")
                ->setSubject("RESUMEN FACTURAS VENTA GENERAL")
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);

    $y = 6;
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B" . $y, 'Nº')
                ->setCellValue("C" . $y, 'RUC/C.I.')
                ->setCellValue("D" . $y, 'Razon Social')
                ->setCellValue("E" . $y, 'Fecha Emisión')
                ->setCellValue("F" . $y, 'Nº Factura')
                ->setCellValue("G" . $y, 'Subtotal')
                ->setCellValue("H" . $y, 'Tarifa 12%')
                ->setCellValue("I" . $y, 'Tarifa 0%')
                ->setCellValue("J" . $y, 'Iva ...%')
                ->setCellValue("K" . $y, 'Descuento')
                ->setCellValue("L" . $y, 'Total');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B6:L6')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFEEEEEE');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B6:L6')->getAlignment()
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
                ->getStyle('B6:L6')
                ->applyFromArray($borders);

    // Cabezera
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B2", 'RESUMEN FACTURAS VENTA GENERAL');

    $objPHPExcel->getActiveSheet()
                ->getStyle('B2:L2')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('B2:L2');

    $objPHPExcel->getActiveSheet()
                ->getStyle("B2:L2")
                ->getFont()
                ->setBold(true)
                ->setName('Verdana')
                ->setSize(18);
    // fin

    // Fecha Inicio
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B3", 'DESDE:   ' .$_GET['inicio']);

    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('B3:B3');

    $objPHPExcel->getActiveSheet()
            ->getStyle("B3:B3")
            ->getFont()
            ->setBold(true)
            ->setName('Verdana')
            ->setSize(10);
    // fin

    // Fecha Hasta
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("B4", 'HASTA:  '.$_GET['fin']);

    $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('B4:B4');

    $objPHPExcel->getActiveSheet()
            ->getStyle("B4:B4")
            ->getFont()
            ->setBold(true)
            ->setName('Verdana')
            ->setSize(10);
    // fin

    $subtotal = 0;
    $tarifa = 0;
    $tarifa0 = 0;
    $iva = 0;
    $descuento = 0;
    $total = 0;

    $resultado = $class->consulta("SELECT F.id, F.fecha_emision, F.secuencial, F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_descuento, F.total_venta, C.identificacion, C.razon_social FROM factura_venta F, clientes C WHERE F.id_cliente = C.id AND F.fecha_emision BETWEEN '$_GET[inicio]' AND '$_GET[fin]' ORDER BY F.id");

    while ($row = $class->fetch_array($resultado)) {
        $subtotal = $subtotal +  $row[3];
        $tarifa = $tarifa +  $row[4];
        $tarifa0 = $tarifa0 +  $row[5];
        $iva = $iva +  $row[6];
        $descuento = $descuento +  $row[7];
        $total = $total +  $row[8]; 
        $y++;
        //BORDE DE LA CELDA
        $objPHPExcel->setActiveSheetIndex(0)
                    ->getStyle('B' . $y . ":L" . $y)
                    ->applyFromArray($borders);
        //MOSTRAMOS LOS VALORES
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("B" . $y, $row[0])
                    ->setCellValue("C" . $y, $row[9])
                    ->setCellValue("D" . $y, $row[10])
                    ->setCellValue("E" . $y, $row[1])
                    ->setCellValue("F" . $y, $row[2])
                    ->setCellValue("G" . $y, $row[3])
                    ->setCellValue("H" . $y, $row[4])
                    ->setCellValue("I" . $y, $row[5])
                    ->setCellValue("J" . $y, $row[6])
                    ->setCellValue("K" . $y, $row[7])
                    ->setCellValue("L" . $y, $row[8]);

        $objPHPExcel->getActiveSheet()
                    ->getStyle('B' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('C' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('E' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('F' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('G' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('H' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('I' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('J' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('K' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('L' . $y)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
    $styleArray = array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
            ),
        ),
    );
    $y++;

    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit("B" . $y, 'Totales:')
                ->setCellValueExplicit("G" . $y, (number_format($subtotal, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("H" . $y, (number_format($tarifa, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("I" . $y, (number_format($tarifa0, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("J" . $y, (number_format($iva, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("K" . $y, (number_format($descuento, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit("L" . $y, (number_format($total, 2, ',', '.')), PHPExcel_Cell_DataType::TYPE_STRING);

    $objPHPExcel->getActiveSheet()->getStyle("B" . $y . ":L" . $y)->getFont()->setBold(true)->setName('Verdana')        ->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle("B" . $y . ":L" . $y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // DATOS DE LA SALIDA DEL EXCEL
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $Archivo . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    exit;
?>

