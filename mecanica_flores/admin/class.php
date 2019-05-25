<?php
    include_once("conex.php");
    include("constante.php");

    class constante {
        private $login;
        private $contrasena;
        private $cedula;
        private $tipo;
        private $status;

        public function consulta($q) {
          $BaseDato = new BaseDeDato(SERVIDOR,PUERTO,BD,USUARIO,CLAVE); // declarar el objeto de la clase base de dato
          $result = $BaseDato->Consultas($q);
          return $result;
        }

        public function fetch_array($consulta) {
            return pg_fetch_array($consulta);
        }

        public function num_rows($consulta) {
            return pg_num_rows($consulta);
        }

        public function getTotalConsultas() {
            return $this->total_consultas; 
        }

        public function sqlcon($q) {
            $BaseDato = new BaseDeDato(SERVIDOR,PUERTO,BD,USUARIO,CLAVE); // declarar el objeto de la clase base de dato
            $result = $BaseDato->Consultas($q);
            if(pg_affected_rows($result)>= 0)
            return 1;
                else
            return 0;
        }

        //generador de id unicos
        function idz() {
            date_default_timezone_set('America/Guayaquil');
            $fecha=date("YmdHis");
            return($fecha.uniqid()); 
        }

        function client_ip() {
            $ipaddress = '';
            if ($_SERVER['HTTP_CLIENT_IP'])
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if($_SERVER['HTTP_X_FORWARDED_FOR'])
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if($_SERVER['HTTP_X_FORWARDED'])
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if($_SERVER['HTTP_FORWARDED_FOR'])
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if($_SERVER['HTTP_FORWARDED'])
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if($_SERVER['REMOTE_ADDR'])
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }

        public function edad($fecha) {
            $dias = explode("-", $fecha, 3);
            $dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
            $edad = (int)((time()-$dias)/31556926 );
            return $edad;
        }

        function diaSemana($ano,$mes,$dia) {
            $dia = date("w",mktime(0, 0, 0, $mes, $dia, $ano));
            if ($dia == 1) {
                return 'LUNES';
            } elseif ($dia == 1) {
                return 'LUNES';
            } elseif ($dia == 2) {
                return 'MARTES';
            } elseif ($dia == 3) {
                return 'MIERCOLES';
            } elseif ($dia == 4) {
                return 'JUEVES';
            } elseif ($dia == 5) {
                return 'VIERNES';
            } elseif ($dia == 6) {
                return 'SABADO';
            } elseif ($dia == 7) {
                return 'DOMINGO';
            }  
        }
      
        public function fecha() {
            $fecha = date("Y-m-d");
            return $fecha;
        } 

        public function hora() {
            date_default_timezone_set('America/Guayaquil');
            $hora = date("H:i:s");
            return $hora;
        } 

        public function fecha_hora() {
            date_default_timezone_set('America/Guayaquil');
            $fecha = date("Y-m-d H:i:s");
            return $fecha;
        } 

        public function fecha2() {
            $fecha = date("d/m/Y");
            return $fecha;
        }

        public function clave_aleatoria() {
            $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $cad = "";
            for($i=0; $i<10; $i++) {
                $cad .= substr($str,rand(0,62),1);
            }
            return $cad;
        }

        public function uncdata($xml) {    
            $state = 'out';
            $a = str_split($xml);
            $new_xml = '';
            foreach ($a AS $k => $v) {        
                switch ($state) {
                    case 'out':
                    if ('<' == $v) {
                        $state = $v;
                    } else {
                        $new_xml .= $v;
                    }
                    break;
                     case '<':
                    if ('!' == $v ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<!':
                    if ('[' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![':
                    if ('C' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![C':
                    if ('D' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![CD':
                    if ('A' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![CDA':
                    if ('T' == $v) {
                      $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![CDAT':
                    if ('A' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case '<![CDATA':
                    if ('[' == $v) {
                        $cdata = '';
                        $state = 'in';
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;
                        case 'in':
                    if (']' == $v) {
                        $state = $v;
                    } else {
                        $cdata .= $v;
                    }
                    break;
                        case ']':
                    if (']' == $v) {
                        $state = $state . $v;
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                    break;
                        case ']]':
                    if ('>' == $v) {
                        $new_xml .= str_replace('>','&gt;',
                                    str_replace('>','&lt;',
                                    str_replace('"','&quot;',
                                    str_replace('&','&amp;',
                                    $cdata))));
                        $state = 'out';
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                    break;        
                }
            }    
            return trim($new_xml);
        }

        public static function generateValidXmlFromObj(stdClass $obj, $node_block = 'nodes', $node_name = 'node') {
            $arr = get_object_vars($obj);    
            return self::generateValidXmlFromArray($arr, $node_block, $node_name);
        }

        public static function generateValidXmlFromArray($array, $node_block = 'nodes', $node_name = 'node') {
            $xml = "<?xml version='1.0' encoding='UTF-8' ?>";
            $xml .= self::generateXmlFromArray($array, $node_name);
            return $xml;
        }

        private static function generateXmlFromArray($array, $node_name) {
            $xml = '';
            if (is_array($array) || is_object($array)) {
                foreach ($array as $key=>$value) {
                    if (is_numeric($key)) {
                    $key = $node_name;
                }
                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';          
                }
            } else {
                $xml = htmlspecialchars($array, ENT_QUOTES);
            }
            return $xml;
        }

        // generar password aleatorio
        function generaPass() {
            //Se define una cadena de caractares. Te recomiendo que uses esta.
            $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            //Obtenemos la longitud de la cadena de caracteres
            $longitudCadena = strlen($cadena);
           
            //Se define la variable que va a contener la contraseña
            $pass = "";
            //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
            $longitudPass=10;
           
            //Creamos la contraseña
            for($i=1 ; $i<=$longitudPass ; $i++) {
                //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
                $pos=rand(0,$longitudCadena-1);
           
                //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
                $pass .= substr($cadena,$pos,1);
            }
            return $pass;
        }
        // fin

        function generarClave($id,$tipoComprobante,$ruc,$ambiente,$serie,$numeroDocumento,$fecha, $tipoEmision) {      
            $secuencia = '765432';      
            $ceros = 9;      
            $temp = '';
            $tam = $ceros - strlen($numeroDocumento);

            for ($i = 0; $i < $tam; $i++) {                 
                $temp = $temp .'0';        
            }
            $numeroDocumento = $temp .''. $numeroDocumento;  
              
            $fechaT = explode('/', $fecha);    
            $fecha = $fechaT[0].''.$fechaT[1].''.$fechaT[2];            

            $clave = $fecha.''.$tipoComprobante.''.$ruc.''.$ambiente.''.$serie.''.$numeroDocumento.''.$fecha.''.$tipoEmision;    
          
            $tamSecuencia = strlen($secuencia);      
            $ban = 0;
            $inc = 0;
            $sum = 0;
            for ($i = 0; $i < strlen($clave); $i++) { 
                $sum = $sum  + ($clave[$i] * $secuencia[$ban + $inc]);        
                $inc++;
                if($inc >= $tamSecuencia){ 
                    $inc = 0;
                }
            }

            $resp = $sum % 11;
            $resp = 11 - $resp;      
            if($resp == 10) {
                $resp = 1;
            } else {
                if($resp == 11) {
                    $resp = 0;
                }  
            }
              
            $clave = $clave.$resp; 
            return $clave;      
        } 
    }
?>