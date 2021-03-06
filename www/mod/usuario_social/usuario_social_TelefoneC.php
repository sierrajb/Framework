<?php
class usuario_social_TelefoneControle extends usuario_social_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses usuario_social_rede_PerfilModelo::Carrega Rede Modelo
    * @uses usuario_social_rede_PerfilVisual::Carrega Rede Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses usuario_social_Controle::$acoesPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        return false; 
    }
    static function Endereco_Telefone($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($true===true){
            $_Controle->Tema_Endereco('Telefone','usuario_social/Telefone/Telefone');
        }else{
            $_Controle->Tema_Endereco('Telefone');
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Telefone($export=false){
        self::Endereco_Telefone(false);
        $i = 0;
        // add botao
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Telefone',
                'usuario_social/Telefone/Telefones_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'usuario_social/Telefone/Telefones',
            )
        )));
        $telefones = $this->_Modelo->db->Sql_Select('Usuario_Social_Telefone');
        if($telefones!==false && !empty($telefones)){
            if(is_object($telefones)) $telefones = Array(0=>$telefones);
            reset($telefones);
            foreach ($telefones as $indice=>&$valor) {
                $tabela['Pessoa'][$i]           = $valor->persona2;
                $tabela['Numero'][$i]           = $valor->telefone;
                $tabela['Obs'][$i]              = $valor->obs;
                $tabela['Funções'][$i]          = $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Telefone'        ,'usuario_social/Telefone/Telefones_Edit/'.$valor->id.'/'    ,'')).
                                                  $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Telefone'       ,'usuario_social/Telefone/Telefones_Del/'.$valor->id.'/'     ,'Deseja realmente deletar essa Telefone ?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Telefones');
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
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Telefone</font></b></center>');
        }
        $titulo = 'Listagem de Telefones ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        // Upload de Chamadas
        $this->_Visual->Blocar(
            $this->_Visual->Upload_Janela(
                'usuario_social',
                'Telefone',
                'Telefone',
                0,
                'og3;mp3;',
                'Arquivos de Audio'
            )
        );
        $this->_Visual->Bloco_Unico_CriaJanela( 'Fazer Upload de Audio de Chamada'  ,'',8);

        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Telefones');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Telefones_Add(){
        self:;Endereco_Telefone();
        // Carrega Config
        $titulo1    = 'Adicionar Telefone';
        $titulo2    = 'Salvar Telefone';
        $formid     = 'form_Sistema_Telefone_Telefones';
        $formbt     = 'Salvar';
        $formlink   = 'usuario_social/Telefone/Telefones_Add2/';
        $campos = Usuario_Social_Telefone_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Telefones_Add2(){
        $titulo     = 'Telefone adicionada com Sucesso';
        $dao        = 'Usuario_Social_Telefone';
        $funcao     = '$this->Telefone();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Telefone cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Telefones_Edit($id){
        self:;Endereco_Telefone();
        // Carrega Config
        $titulo1    = 'Editar Telefone (#'.$id.')';
        $titulo2    = 'Alteração de Telefone';
        $formid     = 'form_Sistema_TelefoneC_TelefoneEdit';
        $formbt     = 'Alterar Telefone';
        $formlink   = 'usuario_social/Telefone/Telefones_Edit2/'.$id;
        $editar     = Array('Usuario_Social_Telefone',$id);
        $campos = Usuario_Social_Telefone_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Telefones_Edit2($id){
        $titulo     = 'Telefone editada com Sucesso';
        $dao        = Array('Usuario_Social_Telefone',$id);
        $funcao     = '$this->Telefone();';
        $sucesso1   = 'Telefone Alterado com Sucesso.';
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
    public function Telefones_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa setor e deleta
        $setor = $this->_Modelo->db->Sql_Select('Usuario_Social_Telefone', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($setor);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletada',
                "mgs_secundaria" => 'Telefone deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Telefone();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Telefone deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    public function Telefone_Upload($parent = 0){
        $fileTypes = array(
            // Audio
            'mp3',
            '3gp',
        ); // File extensions
        $dir = 'usuario_social'.DS.'Chamadas_Nao_Contabilizadas'.DS;
        $ext = $this->Upload($dir,$fileTypes,false);
        $this->layoult_zerar = false;
        if($ext!==false){
            $this->_Visual->Json_Info_Update('Titulo', 'Upload com Sucesso');
            $this->_Visual->Json_Info_Update('Historico', false);
        }else{
            $this->_Visual->Json_Info_Update('Titulo', 'Erro com Upload');
            $this->_Visual->Json_Info_Update('Historico', false);
        }
    }
}
?>
