<?php

class Financeiro_FinancaModelo extends Financeiro_Modelo
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    static function Financeiro_Motivo_Exibir($motivoid){
        $motivoid = (int) $motivoid;
        $registro = \Framework\App\Registro::getInstacia();
        $_Modelo = $registro->_Modelo;
        $retirada = $_Modelo->db->Sql_Select('Financeiro_Financa',Array('id'=>$motivoid),1);
        if($retirada===false) return '';
        return  Array('<b>Gasto com Finanças</b>',$retirada->categoria2);
    }
}
?>