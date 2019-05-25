<?php    
    include_once('../../admin/FirmaElectronica.php');  
    include_once('../../admin/nusoap.php');      
    include_once('../../phpseclib/Crypt/RSA.php');
    include_once('../../phpseclib/File/X509.php');
    include_once('../../phpseclib/Math/BigInteger.php');      

    function generarFirma($xmlDoc,$clave,$tipoDocumento,$pass,$token,$ambiente) {           
        $firma = new FirmaElectronica($config = [],$pass,$token);                
        $result = $firma->signXML($xmlDoc,'', null, false,$tipoDocumento,$clave);
        return $result;
    } 

    function generarFirmaLote($xmlDoc,$clave,$tipoDocumento,$pass,$token,$ambiente) {        
        $firma = new FirmaElectronica($config = [],$pass,$token);
        $result = $firma->signXML($xmlDoc,'', null, false,$tipoDocumento,$clave);
        return $result;
    }   

    function webService($result,$ambiente,$clave,$xmlDoc,$tipoDocumento,$pass,$token,$lote) {        
        if($ambiente == 1) {
            $url = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl";
            $slAutorWs = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl";
        } else {
            $url = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl";
            $slAutorWs = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl";
        }
        $xml = base64_encode($result);                    
             
        $client = new nusoap_Client($url,true);                        
        $resp = $client->call('validarComprobante',array("xml"=> "$xml"));
          
        return $resp;
    }

    function consultarComprobante($ambiente,$clave) {          
        if($ambiente == '1') {            
            $slAutorWs = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl";
        } else {            
            $slAutorWs = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl";
        }            
        $olClient = new SoapClient($slAutorWs, array('encoding'=>'UTF-8'));
        $olResp = $olClient->autorizacionComprobante(array('claveAccesoComprobante'=> $clave));
       
        return $olResp;
    }
?>