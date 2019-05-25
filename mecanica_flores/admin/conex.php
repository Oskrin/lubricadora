<?php
    class BaseDeDato {
        private $Servidor;
        private $Puerto;
        private $Nombre;
        private $Usuario;
        private $Clave;

        function __construct($Servidor, $Puerto, $Nombre, $Usuario, $Clave) {
            $this -> Servidor = $Servidor;
            $this -> Puerto = $Puerto;
            $this -> Nombre = $Nombre;
            $this -> Usuario = $Usuario;
            $this -> Clave = $Clave;
        }

        function Conectar() {
            $BaseDato = pg_connect("host=$this->Servidor port=$this->Puerto dbname=$this->Nombre user=$this->Usuario password=$this->Clave");
            return $BaseDato;
        }

        function Consultas($Consulta) {
            $valor = $this -> Conectar();
            if (!$valor)
                return 0; //Si no se pudo conectar
            else {
            //valor es resultado de base de dato y Consulta es la Consulta a realizar
            $resultado = pg_query($valor, $Consulta);
            return $resultado; // retorna si fue afectada una fila
            }
        }
    }
?>