<?php 
	if(!isset($_SESSION)) {
        session_start();        
    }
	include_once('../../admin/class.php');
	$class = new constante();
	$fecha = $class->fecha_hora();

	//generar formulario104a
	if (isset($_POST['Guardar']) == "Guardar") {
		$resultado = $class->consulta("SELECT ruc, razon_social FROM empresa");
		while ($row = $class->fetch_array($resultado)) {
			$ruc_empresa = $row[0];
			$razonSocial = $row[1];
		}

		$anio = $_POST['select_anio'];
		$mes = $_POST['select_mes'];

		// totales ventas locales
		$resultado = $class->consulta("SELECT COUNT(*)as comprobantes, SUM(CAST(subtotal as float)) subtotal, SUM(CAST(tarifa as float)) tarifa, SUM(CAST(tarifa0 as float)) tarifa0, SUM(CAST(iva as float)) iva FROM factura_venta where TO_CHAR(fecha_creacion::date,'YYYY')='$anio' AND TO_CHAR(fecha_creacion::date,'MM')='$mes'");
		while ($row = $class->fetch_array($resultado)) {
			$comprobantes_venta = $row[0];
			$subtotal_venta = $row[1];
			$tarifa_venta = $row[2];
			$tarifa0_venta = $row[3];
			$iva_venta = $row[4];
		}
		// fin

		// totales adquisiciones locales
		$resultado = $class->consulta(" SELECT COUNT(*)as comprobantes, SUM(CAST(subtotal as float)) subtotal, SUM(CAST(tarifa as float)) tarifa, SUM(CAST(tarifa0 as float)) tarifa0, SUM(CAST(iva as float)) iva FROM factura_compra where TO_CHAR(fecha_creacion::date,'YYYY')='$anio' AND TO_CHAR(fecha_creacion::date,'MM')='$mes'");
		while ($row = $class->fetch_array($resultado)) {
			$comprobantes_compra = $row[0];
			$subtotal_compra = $row[1];
			$tarifa_compra = $row[2];
			$tarifa0_compra = $row[3];
			$iva_compra = $row[4];
		}
		//fin

		if ($comprobantes_venta == "0") {
			$data =  "0";
		} else {
			// contador formulario104
			$id_formulario104 = 0;
			$resultado = $class->consulta("SELECT max(id) FROM formulario104");
			while ($row = $class->fetch_array($resultado)) {
				$id_formulario104 = $row[0];
			}
			$id_formulario104++;
			// fin

			// registrar
			$class->consulta("INSERT INTO formulario104 VALUES ('$id_formulario104','1','$anio','$mes','".$anio.$mes.".xml"."','1','$fecha')");
			// fin

			$xml = new DomDocument('1.0', 'UTF-8');
			$xml->xmlStandalone = true;

			$formulario = $xml->createElement('formulario'. "\0");
			$formulario->setAttribute('version','0.2');
			$formulario = $xml->appendChild($formulario);

			$cabecera = $xml->createElement('cabecera');
	    	$cabecera = $formulario->appendChild($cabecera);

	    	$codigo_version_formulario = $xml->createElement('codigo_version_formulario','04201703');
	    	$codigo_version_formulario = $cabecera->appendChild($codigo_version_formulario);

	    	$ruc = $xml->createElement('ruc', $ruc_empresa);
	    	$ruc = $cabecera->appendChild($ruc);

	    	$codigo_moneda = $xml->createElement('codigo_moneda','1');
	    	$codigo_moneda = $cabecera->appendChild($codigo_moneda);

	    	$detalle = $xml->createElement('detalle');
	    	$detalle = $formulario->appendChild($detalle);

	    	// campo 102
		    $campo = $xml->createElement('campo',$anio);
		    $campo->setAttribute('numero', '102');
		    $campo = $detalle->appendChild($campo);

		    // campo 101
		    $campo = $xml->createElement('campo',($mes*1));
		    $campo->setAttribute('numero', '101');
		    $campo = $detalle->appendChild($campo);

		    // campo 31
		    $campo = $xml->createElement('campo','O');
		    $campo->setAttribute('numero', '31');
		    $campo = $detalle->appendChild($campo);

		    // campo 104
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '104');
		    $campo = $detalle->appendChild($campo);

		    // campo 202
		    $campo = $xml->createElement('campo',$razonSocial);
		    $campo->setAttribute('numero', '202');
		    $campo = $detalle->appendChild($campo);

		    // campo 201
		    $campo = $xml->createElement('campo',$ruc_empresa);
		    $campo->setAttribute('numero', '201');
		    $campo = $detalle->appendChild($campo);

		    // campo 401
		    if ($tarifa_venta != "") {
		    	$campo = $xml->createElement('campo',$tarifa_venta);
			    $campo->setAttribute('numero', '401');
			    $campo = $detalle->appendChild($campo);
		    } else {
		    	$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '401');
			    $campo = $detalle->appendChild($campo);	
		    }
		    
		    // campo 421
		    if ($iva_venta != "") {
			    $campo = $xml->createElement('campo',$iva_venta);
			    $campo->setAttribute('numero', '421');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '421');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 411
		    if ($tarifa_venta != "") {
			    $campo = $xml->createElement('campo',$tarifa_venta);
			    $campo->setAttribute('numero', '411');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '411');
			    $campo = $detalle->appendChild($campo);
			}

		    // campo 402
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '402');
		    $campo = $detalle->appendChild($campo);

		    // campo 422
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '422');
		    $campo = $detalle->appendChild($campo);

		    // campo 412
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '412');
		    $campo = $detalle->appendChild($campo);

		    // campo 423
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '423');
		    $campo = $detalle->appendChild($campo);

		    // campo 424
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '424');
		    $campo = $detalle->appendChild($campo);

		    // campo 413
		    if ($tarifa0_venta != "") {
			    $campo = $xml->createElement('campo',$tarifa0_venta);
			    $campo->setAttribute('numero', '413');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '413');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 403
		    if ($tarifa0_venta != "") {
			    $campo = $xml->createElement('campo',$tarifa0_venta);
			    $campo->setAttribute('numero', '403');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '403');
			    $campo = $detalle->appendChild($campo);
			}

		    // campo 404
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '404');
		    $campo = $detalle->appendChild($campo);

		    // campo 414
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '414');
		    $campo = $detalle->appendChild($campo);

		    // campo 405
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '405');
		    $campo = $detalle->appendChild($campo);

		    // campo 415
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '415');
		    $campo = $detalle->appendChild($campo);

		    // campo 406
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '406');
		    $campo = $detalle->appendChild($campo);

		    // campo 416
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '416');
		    $campo = $detalle->appendChild($campo);

		    // campo 429
		    if ($iva_venta != "") {
			    $campo = $xml->createElement('campo',$iva_venta);
			    $campo->setAttribute('numero', '429');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '429');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 419
		    if ($subtotal_venta != "") {
			    $campo = $xml->createElement('campo',$subtotal_venta);
			    $campo->setAttribute('numero', '419');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '419');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 409
		    if ($subtotal_venta != "") {
			    $campo = $xml->createElement('campo',$subtotal_venta);
			    $campo->setAttribute('numero', '409');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '409');
			    $campo = $detalle->appendChild($campo);	
			}    

		    // campo 441
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '441');
		    $campo = $detalle->appendChild($campo);

		    // campo 431
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '431');
		    $campo = $detalle->appendChild($campo);

		    // campo 442
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '442');
		    $campo = $detalle->appendChild($campo);

		    // campo 453
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '453');
		    $campo = $detalle->appendChild($campo);

		    // campo 443
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '443');
		    $campo = $detalle->appendChild($campo);

		    // campo 434
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '434');
		    $campo = $detalle->appendChild($campo);

		    // campo 454
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '454');
		    $campo = $detalle->appendChild($campo);

		    // campo 444
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '444');
		    $campo = $detalle->appendChild($campo);

		    // campo 499
		    if ($iva_venta != "") {
			    $campo = $xml->createElement('campo',$iva_venta);
			    $campo->setAttribute('numero', '499');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '499');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 485
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '485');
		    $campo = $detalle->appendChild($campo);

		    // campo 484
		    if ($iva_venta != "") {
			    $campo = $xml->createElement('campo',$iva_venta);
			    $campo->setAttribute('numero', '484');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '484');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 483
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '483');
		    $campo = $detalle->appendChild($campo);

		    // campo 482
		    if ($iva_venta != "") {
			    $campo = $xml->createElement('campo',$iva_venta);
			    $campo->setAttribute('numero', '482');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '482');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 481
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '481');
		    $campo = $detalle->appendChild($campo);

		    // campo 480
		    if ($tarifa_venta != "") {
			    $campo = $xml->createElement('campo',$tarifa_venta);
			    $campo->setAttribute('numero', '480');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '480');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 113
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '113');
		    $campo = $detalle->appendChild($campo);

		    // campo 111
		    if ($comprobantes_venta != "0") {
		    	$campo = $xml->createElement('campo',$comprobantes_venta);
			    $campo->setAttribute('numero', '111');
			    $campo = $detalle->appendChild($campo);
		    } else {
		    	$campo = $xml->createElement('campo','');
			    $campo->setAttribute('numero', '111');
			    $campo = $detalle->appendChild($campo);	
		    }
		    
		    // campo 510
		    if ($tarifa_compra != "") {
			    $campo = $xml->createElement('campo',$tarifa_compra);
			    $campo->setAttribute('numero', '510');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '510');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 520
		    if ($iva_compra != "") {
			    $campo = $xml->createElement('campo',$iva_compra);
			    $campo->setAttribute('numero', '520');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '520');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 500
		    if ($tarifa_compra != "") {
		    	$campo = $xml->createElement('campo',$tarifa_compra);
			    $campo->setAttribute('numero', '500');
			    $campo = $detalle->appendChild($campo);
		    } else {
		    	$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '500');
			    $campo = $detalle->appendChild($campo);	
		    }

		    // campo 501
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '501');
		    $campo = $detalle->appendChild($campo);

		    // campo 511
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '511');
		    $campo = $detalle->appendChild($campo);

		    // campo 521
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '521');
		    $campo = $detalle->appendChild($campo);

		    // campo 512
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '512');
		    $campo = $detalle->appendChild($campo);

		    // campo 502
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '502');
		    $campo = $detalle->appendChild($campo);

		    // campo 522
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '522');
		    $campo = $detalle->appendChild($campo);

		    // campo 526
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '526');
		    $campo = $detalle->appendChild($campo);

		    // campo 527
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '527');
		    $campo = $detalle->appendChild($campo);

		    // campo 517
		    if ($tarifa0_compra != "") {
			    $campo = $xml->createElement('campo',$tarifa0_compra);
			    $campo->setAttribute('numero', '517');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '517');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 507
		    if ($tarifa0_compra != "") {
		    	$campo = $xml->createElement('campo',$tarifa0_compra);
			    $campo->setAttribute('numero', '507');
			    $campo = $detalle->appendChild($campo);
		    } else {
			    $campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '507');
			    $campo = $detalle->appendChild($campo);
			}

		    // campo 518
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '518');
		    $campo = $detalle->appendChild($campo);

		    // campo 508
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '508');
		    $campo = $detalle->appendChild($campo);

		    // campo 529
		    if ($iva_compra != "") {
		    	$campo = $xml->createElement('campo',$iva_compra);
			    $campo->setAttribute('numero', '529');
			    $campo = $detalle->appendChild($campo);
		    } else {
		    	$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '529');
			    $campo = $detalle->appendChild($campo);	
		    }
		    
		    // campo 519
		    if ($subtotal_compra != "") {
			    $campo = $xml->createElement('campo',$subtotal_compra);
			    $campo->setAttribute('numero', '519');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '519');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 509
		    if ($subtotal_compra != "") {
			    $campo = $xml->createElement('campo',$subtotal_compra);
			    $campo->setAttribute('numero', '509');
			    $campo = $detalle->appendChild($campo);
			} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '509');
			    $campo = $detalle->appendChild($campo);	
			}

		    // campo 541
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '541');
		    $campo = $detalle->appendChild($campo);

		    // campo 531
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '531');
		    $campo = $detalle->appendChild($campo);

		    // campo 542
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '542');
		    $campo = $detalle->appendChild($campo);

		    // campo 532
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '532');
		    $campo = $detalle->appendChild($campo);

		    // campo 543
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '543');
		    $campo = $detalle->appendChild($campo);

		    // campo 544
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '544');
		    $campo = $detalle->appendChild($campo);

		    // campo 554
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '554');
		    $campo = $detalle->appendChild($campo);

		    // campo 555
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '555');
		    $campo = $detalle->appendChild($campo);

		    // campo 545
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '545');
		    $campo = $detalle->appendChild($campo);

		    // campo 535
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '535');
		    $campo = $detalle->appendChild($campo);

		    // campo 563
		    //$factor_proporcion = "";
		    //if($tarifa_venta != '')
		    //	$tarifa_venta = "0.00";
		    //if ($subtotal_venta != '')
		    //	$subtotal_venta = "0.00"
		    //echo $tarifa_venta;
		    //echo $subtotal_venta;
		    //$factor_proporcion = $tarifa_venta / $subtotal_venta;

			//if ($factor_proporcion != "") {
			//    $campo = $xml->createElement('campo',$factor_proporcion);
			//    $campo->setAttribute('numero', '563');
			//    $campo = $detalle->appendChild($campo);
			//} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '563');
			    $campo = $detalle->appendChild($campo);	
			//}    
			    
		    // campo 564
		    //if ($iva_compra != '')
		    //	$iva_compra = "0.00";
		    //if ($factor_proporcion != '')
		    	$factor_proporcion = "0.00";

		    //$credito_tributario = $iva_compra * $factor_proporcion;
		    //if ($credito_tributario != "") {
			//    $campo = $xml->createElement('campo',$credito_tributario);
			//    $campo->setAttribute('numero', '564');
			//    $campo = $detalle->appendChild($campo);
			//} else {
				$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '564');
			    $campo = $detalle->appendChild($campo);	
			//}    

		    // campo 115
		    if ($comprobantes_compra != "0") {
		    	$campo = $xml->createElement('campo',$comprobantes_compra);
			    $campo->setAttribute('numero', '115');
			    $campo = $detalle->appendChild($campo);
		    } else {
		    	$campo = $xml->createElement('campo','');
			    $campo->setAttribute('numero', '115');
			    $campo = $detalle->appendChild($campo);	
		    }
			    
		    // campo 117
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '117');
		    $campo = $detalle->appendChild($campo);

		    // campo 119
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '119');
		    $campo = $detalle->appendChild($campo);

		    // campo 601
		    //if ($iva_venta != '')
		    //	$iva_venta = "0.00";
		    //if ($credito_tributario != '')
		    //	$credito_tributario = "0.00";

		    //$impuesto_causado = $iva_venta - $credito_tributario;

		    //if ($comprobantes_compra != "") {
			//   $campo = $xml->createElement('campo',$impuesto_causado);
			//    $campo->setAttribute('numero', '601');
			//    $campo = $detalle->appendChild($campo);
			//} else {
				$campo = $xml->createElement('campo',"0.00");
			    $campo->setAttribute('numero', '601');
			    $campo = $detalle->appendChild($campo);
			//}

		    // campo 602
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '602');
		    $campo = $detalle->appendChild($campo);

		    // campo 603
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '603');
		    $campo = $detalle->appendChild($campo);

		    // campo 604
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '604');
		    $campo = $detalle->appendChild($campo);

		    // campo 605
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '605');
		    $campo = $detalle->appendChild($campo);

		    // campo 606
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '606');
		    $campo = $detalle->appendChild($campo);

		    // campo 607
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '607');
		    $campo = $detalle->appendChild($campo);

		    // campo 608
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '608');
		    $campo = $detalle->appendChild($campo);

		    // campo 609
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '609');
		    $campo = $detalle->appendChild($campo);

		    // campo 610
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '610');
		    $campo = $detalle->appendChild($campo);

		    // campo 611
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '611');
		    $campo = $detalle->appendChild($campo);

		    // campo 612
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '612');
		    $campo = $detalle->appendChild($campo);

		    // campo 613
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '613');
		    $campo = $detalle->appendChild($campo);

		    // campo 614
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '614');
		    $campo = $detalle->appendChild($campo);

		    // campo 615
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '615');
		    $campo = $detalle->appendChild($campo);

		    // campo 617
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '617');
		    $campo = $detalle->appendChild($campo);

		    // campo 618
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '618');
		    $campo = $detalle->appendChild($campo);

		    // campo 619
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '619');
		    $campo = $detalle->appendChild($campo);

		    // campo 620
		   	//if ($impuesto_causado != "") {
		   	// 	$campo = $xml->createElement('campo',$impuesto_causado);
			//    $campo->setAttribute('numero', '620');
			//    $campo = $detalle->appendChild($campo);
		   	//} else {
		   		$campo = $xml->createElement('campo','0.00');
			    $campo->setAttribute('numero', '620');
			    $campo = $detalle->appendChild($campo);	
		   	//}

		    // campo 621
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '621');
		    $campo = $detalle->appendChild($campo);

		    // campo 699
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '699');
		    $campo = $detalle->appendChild($campo);

		    // campo 890
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '890');
		    $campo = $detalle->appendChild($campo);

		    // campo 899
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '899');
		    $campo = $detalle->appendChild($campo);

		    // campo 897
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '897');
		    $campo = $detalle->appendChild($campo);

		    // campo 898
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '898');
		    $campo = $detalle->appendChild($campo);

		    // campo 902
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '902');
		    $campo = $detalle->appendChild($campo);

		    // campo 903
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '903');
		    $campo = $detalle->appendChild($campo);

		    // campo 904
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '904');
		    $campo = $detalle->appendChild($campo);

		    // campo 999
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '999');
		    $campo = $detalle->appendChild($campo);

		    // campo 905
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '905');
		    $campo = $detalle->appendChild($campo);

		    // campo 906
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '906');
		    $campo = $detalle->appendChild($campo);

		    // campo 907
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '907');
		    $campo = $detalle->appendChild($campo);

		    // campo 925
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '925');
		    $campo = $detalle->appendChild($campo);

		    // campo 908
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '908');
		    $campo = $detalle->appendChild($campo);

		    // campo 910
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '910');
		    $campo = $detalle->appendChild($campo);

		    // campo 912
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '912');
		    $campo = $detalle->appendChild($campo);

		    // campo 909
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '909');
		    $campo = $detalle->appendChild($campo);

		    // campo 911
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '911');
		    $campo = $detalle->appendChild($campo);

		    // campo 913
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '913');
		    $campo = $detalle->appendChild($campo);

		    // campo 915
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '915');
		    $campo = $detalle->appendChild($campo);

		    // campo 918
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '918');
		    $campo = $detalle->appendChild($campo);

		    // campo 916
		    $campo = $xml->createElement('campo','');
		    $campo->setAttribute('numero', '916');
		    $campo = $detalle->appendChild($campo);

		    // campo 919
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '919');
		    $campo = $detalle->appendChild($campo);

		    // campo 917
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '917');
		    $campo = $detalle->appendChild($campo);

		    // campo 920
		    $campo = $xml->createElement('campo','0.00');
		    $campo->setAttribute('numero', '920');
		    $campo = $detalle->appendChild($campo);

		    // campo 198
		    $campo = $xml->createElement('campo',$ruc_empresa);
		    $campo->setAttribute('numero', '198');
		    $campo = $detalle->appendChild($campo);

		    // campo 922
		    $campo = $xml->createElement('campo','89');
		    $campo->setAttribute('numero', '922');
		    $campo = $detalle->appendChild($campo);

		    $xml->formatOutput = true;
		    $el_xml = $xml->saveXML();
		    $xml->save('formularios/'.$anio.$mes.".xml");
		    $data =  "1";
		}
		echo $data;

	}
?>