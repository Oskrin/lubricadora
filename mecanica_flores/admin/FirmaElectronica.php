<?php
    class FirmaElectronica{
    private $config; ///< Configuración de la firma electrónica
    private $certs; ///< Certificados digitales de la firma
    private $data; ///< Datos del certificado digial

    public function __construct(array $config = [],$password,$token) { 
        $this->config = array_merge([
            'file' => '../../data/empresa/token/'.$token,
            'pass' => $password,
            'data' => null,
            'wordwrap' => 76,
            'serial' => null,
        ], $config); 

        // cargar firma electrónica desde el contenido del archivo .p12      
        if (!$this->config['data'] and $this->config['file']) {
            if (is_readable($this->config['file'])) {
                $this->config['data'] = file_get_contents($this->config['file']);
            } else {
                // echo 'Archivo de la firma electronica '.basename($this->config['file']).' no puede ser leído';
                echo 5;
            }
        } 
        // leer datos de la firma electrónica
        if ($this->config['data'] and openssl_pkcs12_read($this->config['data'], $this->certs, $this->config['pass'])===false) {
            //echo 'No fue posible leer los datos de la firma electrónica (verificar la contraseña)';
            echo 6;
        }            
        $this->data = openssl_x509_parse($this->certs['cert']);
     
        $this->config['serial'] = $this->data['serialNumber'];
        unset($this->config['data']);

    }         
    public function getID() {  // obtengo el finger print para el tercer Digest              
        $x509 = new File_X509();        
        $cert = $x509->loadX509($this->certs['extracerts'][2]);           
        $cert = base64_encode(openssl_x509_fingerprint($this->certs['cert'],"sha1",true));    
        return $cert;      
    }
 
    public function getCertificate($clean = false) {
        if ($clean) {
            $cert = $this->certs['cert'];            
            $cert = str_replace("-----BEGIN CERTIFICATE-----\n", "", $cert);
            $cert = str_replace("-----END CERTIFICATE-----\n", "", $cert);
            $cert = str_replace("\n", "", $cert);            
            $cert = wordwrap($cert, $this->config['wordwrap'], "\n", true);
            return $cert;
        } else {
            return $this->certs['cert'];
        }       
    }
    
    public function getModulus() {
        $details = openssl_pkey_get_details(openssl_pkey_get_private($this->certs['pkey']));
        return wordwrap(base64_encode($details['rsa']['n']), $this->config['wordwrap'], "\n", true);
    }

    public function getData() {
        return $this->data;
    }    

    public function getExponent() {
        $details = openssl_pkey_get_details(openssl_pkey_get_private($this->certs['pkey']));
        return wordwrap(base64_encode($details['rsa']['e']), $this->config['wordwrap'], "\n", true);
    }             

    public function p_obtener_aleatorio() {
        return floor(rand() * 999000) + 990;    
    }

    public function sign($data, $signature_alg = OPENSSL_ALGO_SHA1) {              
        openssl_sign($data,$signature,$this->certs['pkey'],$signature_alg);
        return base64_encode($signature);               
    }

    public function rsa_verify_sign($key, $signature, $package) {
        $rsa = new Crypt_RSA(); 
        $rsa->setHash("sha256"); 
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1); 
        $rsa->loadKey($key); 
        $verify = $rsa->verify(base64_decode($package), $signature); 
        return $verify;
    }

    public function signXML($xml, $reference = '', $tag = null, $xmlns_xsi = false,$tipoDocumento,$clave) {
        $formatXades = getXades();                
        $CertDigest = $this->getID();
        $data = $this->getData();  
        $serial = 'CN='.$data['issuer']['CN'].',L='.$data['issuer']['L'].',OU='.$data['issuer']['OU'].',O='.$data['issuer']['O'].',C='.$data['issuer']['C'];        
        $serialNumber = $this->config['serial'];
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadXML($xml);

        if (!$doc->documentElement) {
            return 'No fue posible obtener el documentElement desde el XML a firmar';
        }

        $digestComprobante = base64_encode(sha1($doc->saveHTML($doc->getElementsByTagName($tipoDocumento)->item(0)),true));
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML($formatXades);
        $doc->getElementsByTagName($tipoDocumento)->item(0)->appendChild($fragment);
        $doc->formatOutput = TRUE;        
        date_default_timezone_set('America/Guayaquil');        
        $timestamp = new DateTime();
        $fecha =  $timestamp->format('c'); // Returns ISO8601 el formato propio xades
        $doc->getElementsByTagName('SigningTime')->item(0)->nodeValue = $fecha;
        $doc->getElementsByTagName('DigestValue')->item(3)->nodeValue = $CertDigest;
        $doc->getElementsByTagName('X509IssuerName')->item(0)->nodeValue = $serial;
        $doc->getElementsByTagName('X509SerialNumber')->item(0)->nodeValue = $serialNumber;        
        $digestSignedProperties = base64_encode(sha1($doc->getElementsByTagName('SignedProperties')->item(0)->C14N(), true));        
        $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue = $digestSignedProperties;
        $doc->getElementsByTagName('Modulus')->item(0)->nodeValue = "\n".$this->getModulus()."\n";
        $doc->getElementsByTagName('Exponent')->item(0)->nodeValue = $this->getExponent();
        $doc->getElementsByTagName('X509Certificate')->item(0)->nodeValue = "\n".$this->getCertificate(true)."\n";
        $digestCertificate = base64_encode(sha1($doc->getElementsByTagName('KeyInfo')->item(0)->C14N(), true));        
        $doc->getElementsByTagName('DigestValue')->item(1)->nodeValue = $digestCertificate;        
        $doc->getElementsByTagName('DigestValue')->item(2)->nodeValue = $digestComprobante;   
        $dataToSign = $doc->getElementsByTagName('SignedInfo')->item(0)->C14N();
        $firma = $this->sign($dataToSign);
        $signature = wordwrap($firma, $this->config['wordwrap'], "\n", true);
        $doc->getElementsByTagName('SignatureValue')->item(0)->nodeValue = "\n".$signature."\n";
        $pub_key  = openssl_pkey_get_details(openssl_pkey_get_public ($this->certs['cert'] ));
        $private_key = openssl_pkey_get_details(openssl_get_privatekey($this->certs['pkey']));       

        return $doc->saveXML();
    }   
}