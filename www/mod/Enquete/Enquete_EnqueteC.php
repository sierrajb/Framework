<?php
class Enquete_EnqueteControle extends Enquete_Controle
{
    public function __construct(){
        parent::__construct();
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses enquete_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Enquete/Enquete/Enquetes');
        return false;
    }
    static function Endereco_Enquete($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($true===true){
            $_Controle->Tema_Endereco('Enquetes','Enquete/Enquete/Enquetes');
        }else{
            $_Controle->Tema_Endereco('Enquetes');
        }
    }
    static function Enquetes_Tabela(&$enquetes){
        $registro   = \Framework\App\Registro::getInstacia();
        $Modelo     = &$registro->_Modelo;
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($enquetes)) $enquetes = Array(0=>$enquetes);
        reset($enquetes);
        foreach ($enquetes as &$valor) {
            $resp_votos = $Modelo->db->Sql_Select('Enquete_Voto',Array(
                'enquete'   =>  $valor->id
            ));
            if($resp_votos===false){
                $resp_votos_res = 0;
            }else if(is_object($resp_votos)){
                $resp_votos_res = 1;
            }else{
                $resp_votos_res = count($resp_votos);
            }
            $tabela['Tipo de Enquete'][$i]  = $valor->categoria2;
            $tabela['Pergunta'][$i]         = $valor->nome;
            $tabela['Nº de Votos'][$i]      = ($resp_votos_res==1)?$resp_votos_res.' Voto':$resp_votos_res.' Votos';
            $tabela['Observação'][$i]       = $valor->obs;
            $tabela['Data Registrado'][$i]  = $valor->log_date_add;
            $tabela['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar Enquete'    ,'Enquete/Resposta/Respostas/'.$valor->id.'/'    ,'')).
                                              $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Enquete'        ,'Enquete/Enquete/Enquetes_Edit/'.$valor->id.'/'    ,'')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Enquete'       ,'Enquete/Enquete/Enquetes_Del/'.$valor->id.'/'     ,'Deseja realmente deletar essa Enquete ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes($export=false){
        self::Endereco_Enquete(false);
        $i = 0;
        // Botao Add
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Enquete',
                'Enquete/Enquete/Enquetes_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Enquete/Enquete/Enquetes',
            )
        )));
        // Conexao
        $enquetes = $this->_Modelo->db->Sql_Select('Enquete');
        if($enquetes!==false && !empty($enquetes)){
            list($tabela,$i) = self::Enquetes_Tabela($enquetes);
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Enquetes');
            }else{
                $this->_Visual->Show_Tabela_DataTable(
                    $tabela,     // Array Com a Tabela
                    '',          // style extra
                    true,        // true -> Add ao Bloco, false => Retorna html
                    true,        // Apagar primeira coluna ?
                    Array(       // Ordenacao
                        Array(
                            0,'desc'
                        )
                    )
                );
            }
            unset($tabela);
        }else{            
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Enquete</font></b></center>');
        }
        $titulo = 'Listagem de Enquetes ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Enquetes');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes_Add(){
        self::Endereco_Enquete();
        // Carrega Config
        $titulo1    = 'Adicionar Enquete';
        $titulo2    = 'Salvar Enquete';
        $formid     = 'form_Sistema_Admin_Enquetes';
        $formbt     = 'Salvar';
        $formlink   = 'Enquete/Enquete/Enquetes_Add2/';
        $campos = Enquete_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes_Add2(){
        $titulo     = 'Enquete Adicionada com Sucesso';
        $dao        = 'Enquete';
        $funcao     = '$this->Enquetes();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Enquete cadastrada com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes_Edit($id){
        self::Endereco_Enquete();
        // Carrega Config
        $titulo1    = 'Editar Enquete (#'.$id.')';
        $titulo2    = 'Alteração de Enquete';
        $formid     = 'form_Sistema_AdminC_EnqueteEdit';
        $formbt     = 'Alterar Enquete';
        $formlink   = 'Enquete/Enquete/Enquetes_Edit2/'.$id;
        $editar     = Array('Enquete',$id);
        $campos = Enquete_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes_Edit2($id){
        $titulo     = 'Enquete Editada com Sucesso';
        $dao        = Array('Enquete',$id);
        $funcao     = '$this->Enquetes();';
        $sucesso1   = 'Enquete Alterada com Sucesso.';
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);   
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Enquetes_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa enquete e deleta
        $enquete = $this->_Modelo->db->Sql_Select('Enquete', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($enquete);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Enquete deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Enquetes();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Enquete deletada com Sucesso');
        $this->_Visual->Json_Info_Update('Historico', false);
    }
}
?>
