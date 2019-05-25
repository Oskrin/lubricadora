<?php
    require('../../fpdf/fpdf.php');
    include_once('../../admin/class.php');
    include_once('../../admin/funciones_generales.php');
    $class = new constante();   
    date_default_timezone_set('America/Guayaquil'); 
    session_start();

    class PDF extends FPDF {   
        var $widths;
        var $aligns;       
        function SetWidths($w) {            
            $this->widths = $w;
        }

        function Header() {                         
            $this->AddFont('Amble-Regular','','Amble-Regular.php');
            $this->SetFont('Amble-Regular','',10);        
            $fecha = date('Y-m-d', time());
            $this->SetX(1);
            $this->SetY(1);
            $this->Cell(20, 5, $fecha, 0,0, 'C', 0);                         
            $this->Cell(150, 5, "FACTURA VENTA", 0,1, 'R', 0);      
            $this->SetFont('Arial','B',10);                                                    
            $this->Cell(190, 8, $_SESSION['empresa']['nombre_comercial'], 0,1, 'C',0);
            $imagen = $_SESSION['empresa']['imagen'];                                
            $this->Image('../../data/empresa/logo/'.$imagen,1,8,40,30);
            $this->SetFont('Amble-Regular','',10);        
            $this->Cell(180, 5, "PROPIETARIO: ".utf8_decode($_SESSION['empresa']['representante_legal']),0,1, 'C',0);                                
            $this->Cell(85, 5, "TEL.: ".utf8_decode($_SESSION['empresa']['telefono1']),0,0, 'R',0);                                
            $this->Cell(60, 5, "CEL.: ".utf8_decode($_SESSION['empresa']['telefono2']),0,1, 'C',0);                                
            $this->Cell(170, 5, utf8_decode( $_SESSION['empresa']['ciudad']),0,1, 'C',0);                                                                                                              
            $this->SetFont('Arial','B',12);                                                                
            $this->Cell(90, 5, utf8_decode($_GET['inicio']),0,0, 'R',0);                                                                                  
            $this->Cell(40, 5, utf8_decode($_GET['fin']),0,1, 'C',0);                                                                                     
            $this->Cell(190, 5, utf8_decode("RESUMEN DE FACTURAS VENTAS"),0,1, 'C',0);                                                                                                                            
            $this->SetFont('Amble-Regular','',10);        
            $this->Ln(3);
            $this->SetFillColor(255,255,225);            
            $this->SetLineWidth(0.2);                                        
        }

        function Footer() {            
            $this->SetY(-15);            
            $this->SetFont('Arial','I',8);            
            $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C');
        }               
    }

    $pdf = new PDF('P','mm','a4');
    $pdf->AddPage();
    $pdf->SetMargins(0,0,0,0);
    $pdf->AliasNbPages();
    $pdf->AddFont('Amble-Regular');                    
    $pdf->SetFont('Amble-Regular','',10);       
    $pdf->SetFont('Arial','B',9);   
    $pdf->SetX(5);    
    $pdf->SetFont('Amble-Regular','',9); 

    $pdf->SetX(1); 
    $pdf->Cell(10, 6, utf8_decode('Nº'),1,0, 'C',0);                                     
    $pdf->Cell(25, 6, utf8_decode('Fecha Emisión'),1,0, 'C',0);                                     
    $pdf->Cell(30, 6, utf8_decode('Nº Factura'),1,0, 'C',0);                                     
    $pdf->Cell(18, 6, utf8_decode('Subtotal'),1,0, 'C',0);
    $pdf->Cell(18, 6, utf8_decode('Tarifa 12%'),1,0, 'C',0);                                                    
    $pdf->Cell(18, 6, utf8_decode('Tarifa 0%'),1,0, 'C',0);                                                 
    $pdf->Cell(18, 6, utf8_decode('Iva ...%'),1,0, 'C',0);
    $pdf->Cell(18, 6, utf8_decode('Descuento'),1,0, 'C',0);                                      
    $pdf->Cell(18, 6, utf8_decode('Total'),1,1, 'C',0); 
       
    $subtotal = 0;
    $tarifa = 0;
    $tarifa0 = 0;
    $iva = 0;
    $descuento = 0;
    $total = 0;

    $resultado = $class->consulta("SELECT F.id, F.fecha_emision, F.secuencial, F.subtotal, F.tarifa, F.tarifa0, F.iva, F.total_descuento, F.total_venta FROM factura_venta F, clientes C WHERE F.id_cliente = C.id AND F.fecha_emision BETWEEN '$_GET[inicio]' AND '$_GET[fin]' ORDER BY F.id ASC");                        
    while ($row = $class->fetch_array($resultado)) { 
        $subtotal = $subtotal +  $row[3];
        $tarifa = $tarifa +  $row[4];
        $tarifa0 = $tarifa0 +  $row[5];
        $iva = $iva +  $row[6];
        $descuento = $descuento +  $row[7];
        $total = $total +  $row[8];
                                           
        $pdf->SetX(1); 
        $pdf->Cell(10, 6, utf8_decode($row[0]),0,0, 'C',0);                                     
        $pdf->Cell(25, 6, utf8_decode($row[1]),0,0, 'C',0);                    
        $pdf->Cell(30, 6, utf8_decode($row[2]),0,0, 'C',0);
        $pdf->Cell(18, 6, $row[3],0,0, 'L',0);                                  
        $pdf->Cell(18, 6, $row[4],0,0, 'L',0);                    
        $pdf->Cell(18, 6, $row[5],0,0, 'L',0);                    
        $pdf->Cell(18, 6, $row[6],0,0, 'L',0);
        $pdf->Cell(18, 6, $row[7],0,0, 'L',0);
        $pdf->Cell(18, 6, $row[8],0,0, 'L',0);                         
        $pdf->Ln(6);                            
    }                       
                      
    $pdf->SetX(1);                                             
    $pdf->Cell(173, 0, utf8_decode(""),1,1, 'R',0);
    $pdf->Cell(66, 6, utf8_decode("Totales:"),0,0, 'R',0);
    $pdf->Cell(18, 6, number_format($subtotal,3,',','.'),0,0, 'L',0);                                    
    $pdf->Cell(18, 6, number_format($tarifa,3,',','.'),0,0, 'L',0);                                    
    $pdf->Cell(18, 6, number_format($tarifa0,3,',','.'),0,0, 'L',0);                                    
    $pdf->Cell(18, 6, number_format($iva,3,',','.'),0,0, 'L',0);                        
    $pdf->Cell(18, 6, number_format($descuento,3,',','.'),0,0, 'L',0);                        
    $pdf->Cell(18, 6, number_format($total,2,',','.'),0,0, 'L',0);                        
    $pdf->Ln(8);           
    $pdf->Output();
?>