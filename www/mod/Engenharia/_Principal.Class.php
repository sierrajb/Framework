<?php
class Engenharia_Principal implements PrincipalInterface
{
    /**
     * Função Home para o modulo mensagem aparecer na pagina HOME
     * 
     * @name Home
     * @access public
     * @static
     * 
     * @param Class &$controle Classe Controle Atual passada por Ponteiro
     * @param Class &$modelo Modelo Passado por Ponteiro
     * @param Class &$Visual Visual Passado por Ponteiro
     *
     * @uses Engenharia_Controle::$num_Indicados
     * 
     * @return void 
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    static function Home(&$controle, &$modelo, &$Visual){
        self::Widgets();
        return true;
    }
    /**
     * 
     * @return boolean
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    static function Config(){
        return false;
    }
    
    static function Relatorio($data_inicio,$data_final,$filtro=false){
        return false;
    }
    
    static function Estatistica($data_inicio,$data_final,$filtro=false){
        return false;
    }
    
    static function Busca(&$controle, &$modelo, &$Visual,$busca){
        $i = 0;
        // Busca Engenharias
        $result = self::Busca_Empreendimentos($controle, $modelo, $Visual, $busca);
        if($result!==false){
            $i = $i + $result;
        }
        // Busca Unidades
        $result = self::Busca_Unidades($controle, $modelo, $Visual, $busca);
        if($result!==false){
            $i = $i + $result;
        }
        // Retorna
        if(is_int($i) && $i>0){
            return $i;
        }else{
            return false;
        }
    }
    
    /***********************
     * BUSCAS
     */
    static function Busca_Empreendimentos($controle, $modelo, $Visual, $busca){
        $where = Array(Array(
          'nome'                    => '%'.$busca.'%',
          'unidades'                => '%'.$busca.'%',
          'cep'                     => '%'.$busca.'%',
          'obs'                     => '%'.$busca.'%',
          'data_inicio'             => '%'.$busca.'%',
          'data_fim'                => '%'.$busca.'%',
          'data_entrega'            => '%'.$busca.'%',
            
        ));
        $i = 0;
        $empreendimentos = $modelo->db->Sql_Select('Engenharia_Empreendimento',$where);
        if($empreendimentos===false) return false;
        // Botao Add
        $Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Empreendimento',
                'Engenharia/Empreendimento/Empreendimentos_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Engenharia/Empreendimento/Empreendimentos',
            )
        )));
        if(is_object($empreendimentos)) $empreendimentos = Array(0=>$empreendimentos);
        if($empreendimentos!==false && !empty($empreendimentos)){
            list($tabela,$i) = Engenharia_EmpreendimentoControle::Empreendimentos_Tabela($empreendimentos);
            $Visual->Show_Tabela_DataTable($tabela);
        }else{             
            $Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Empreendimento na Busca '.$busca.'</font></b></center>');
        }
        $titulo = 'Busca de Empreendimentos: '.$busca.' ('.$i.')';
        $Visual->Bloco_Unico_CriaJanela($titulo);
        return $i;
    }
    static function Busca_Unidades($controle, $modelo, $Visual, $busca){
        $where = Array(Array(
          'unidade'                 => '%'.$busca.'%',
          'metragem'                => '%'.$busca.'%',
          'quartos'                 => '%'.$busca.'%',
          'banheiros'               => '%'.$busca.'%'
        ));
        $i = 0;
        $unidades = $modelo->db->Sql_Select('Engenharia_Empreendimento_Unidade',$where);
        if($unidades===false) return false;
        // Botao Add
        $Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Unidade de Empreendimento',
                'Engenharia/Unidade/Unidades_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Engenharia/Unidade/Unidades',
            )
        )));
        if(is_object($unidades)) $unidades = Array(0=>$unidades);
        if($unidades!==false && !empty($unidades)){
            list($tabela,$i) = Engenharia_UnidadeControle::Unidades_Tabela($unidades);
            $Visual->Show_Tabela_DataTable($tabela);
        }else{    
            $Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Unidade de Empreendimento na Busca '.$busca.'</font></b></center>');
        }
        $titulo = 'Busca de Unidades de Empreendimentos: '.$busca.' ('.$i.')';
        $Visual->Bloco_Unico_CriaJanela($titulo);
        return $i;
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public static function Widgets(){
        $Registro = &\Framework\App\Registro::getInstacia();
        $modelo = $Registro->_Modelo;
        $Visual = $Registro->_Visual;
        // Empreendimentos
        $where = Array();
        $empreendimento = $modelo->db->Sql_Select('Engenharia_Empreendimento',$where);
        if(is_object($empreendimento)) $empreendimento = Array(0=>$empreendimento);
        if($empreendimento!==false && !empty($empreendimento)){
            reset($empreendimento);
            $empreendimento_qnt = count($empreendimento);
        }else{
            $empreendimento_qnt = 0;
        }
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Empreendimentos', 
            'Engenharia/Empreendimento/Empreendimentos', 
            'building', 
            $empreendimento_qnt, 
            'block-red', 
            false, 
            400
        );
        // Unidades
        $where = Array();
        $unidade = $modelo->db->Sql_Select('Engenharia_Empreendimento_Unidade',$where);
        if(is_object($unidade)) $unidade = Array(0=>$unidade);
        if($unidade!==false && !empty($unidade)){
            reset($unidade);
            $unidade_qnt = count($unidade);
        }else{
            $unidade_qnt = 0;
        }
        // Adiciona Widget a Pagina Inicial
        \Framework\App\Visual::Layoult_Home_Widgets_Add(
            'Unidades', 
            'Engenharia/Unidade/Unidades', 
            'home', 
            $unidade_qnt, 
            'block-red', 
            false, 
            398
        );
    }
    
}
?>