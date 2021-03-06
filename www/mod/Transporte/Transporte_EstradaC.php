<?php

class Transporte_EstradaControle extends Transporte_Controle
{
    public function __construct(){
        parent::__construct();
    }
    static function Endereco_Noticia($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Estradas';
        $link = 'Transporte/Estrada/Estradas';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Transporte/Estrada/Estradas');
        return false;
    }
    static function Estradas_Tabela(&$estrada){
        $registro   = \Framework\App\Registro::getInstacia();
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($estrada)) $estrada = Array(0=>$estrada);reset($estrada);
        foreach ($estrada as &$valor) {                
            $tabela['Id'][$i]           = '#'.$valor->id;
            $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
            $tabela['Estrada'][$i]       = $valor->nome;
            if($valor->status==1 || $valor->status=='1'){
                $texto = 'Ativado';
                $valor->status='1';
            }else{
                $texto = 'Desativado';
                $valor->status='0';
            }
            $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Estrada/Status/'.$valor->id.'/'    ,'')).'</span>';
            if($valor->destaque==1){
                $texto = 'Em Destaque';
            }else{
                $texto = 'Não está em destaque';
            }
            $tabela['Funções'][$i]      .= '<span id="destaques'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Destaque'.$valor->destaque   ,Array($texto   ,'Transporte/Estrada/Destaques/'.$valor->id.'/'    ,'')).'</span>';
            $tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Dica de Estrada'        ,'Transporte/Estrada/Estradas_Edit/'.$valor->id.'/'    ,'')).
                                           $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Dica de Estrada'       ,'Transporte/Estrada/Estradas_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Dica de Estrada ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Estradas($export=false){
        $i = 0;
        self::Endereco_Noticia(false);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Dica de Estrada',
                'Transporte/Estrada/Estradas_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Transporte/Estrada/Estradas',
            )
        )));
        $estrada = $this->_Modelo->db->Sql_Select('Transporte_Estrada');
        if(is_object($estrada)) $estrada = Array(0=>$estrada);
        if($estrada!==false && !empty($estrada)){
            list($tabela,$i) = self::Estradas_Tabela($estrada);
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Dicas de Estradas');
            }else{
                $this->_Visual->Show_Tabela_DataTable(
                    $tabela,     // Array Com a Tabela
                    '',          // style extra
                    true,        // true -> Add ao Bloco, false => Retorna html
                    false,        // Apagar primeira coluna ?
                    Array(       // Ordenacao
                        Array(
                            0,'desc'
                        )
                    )
                );
            }
            unset($tabela);
        }else{
            if($export!==false){
                $mensagem = 'Nenhuma Dica de Estrada Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Dica de Estrada Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Estradas ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Estradaistrar Estradas');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Estradas_Add(){
        self::Endereco_Noticia(true);
        // Carrega Config
        $titulo1    = 'Adicionar Dica de Estrada';
        $titulo2    = 'Salvar Dica de Estrada';
        $formid     = 'formTransporte_Estrada_Noticia';
        $formbt     = 'Salvar';
        $formlink   = 'Transporte/Estrada/Estradas_Add2/';
        $campos = Transporte_Estrada_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Estradas_Add2(){
        $titulo     = 'Dica de Estrada Adicionada com Sucesso';
        $dao        = 'Transporte_Estrada';
        $funcao     = '$this->Estradas();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Dica de Estrada cadastrada com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Estradas_Edit($id){
        self::Endereco_Noticia(true);
        // Carrega Config
        $titulo1    = 'Editar Dica de Estrada (#'.$id.')';
        $titulo2    = 'Alteração de Dica de Estrada';
        $formid     = 'formTransporte_EstradaC_NoticiaEdit';
        $formbt     = 'Alterar Dica de Estrada';
        $formlink   = 'Transporte/Estrada/Estradas_Edit2/'.$id;
        $editar     = Array('Transporte_Estrada',$id);
        $campos = Transporte_Estrada_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);   
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Estradas_Edit2($id){
        $id = (int) $id;
        $titulo     = 'Dica de Estrada Alterada com Sucesso';
        $dao        = Array('Transporte_Estrada',$id);
        $funcao     = '$this->Estradas();';
        $sucesso1   = 'Dica de Estrada Alterada com Sucesso';
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
    public function Estradas_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $estrada    =  $this->_Modelo->db->Sql_Select('Transporte_Estrada', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($estrada);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Dica de Estrada Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Estradas();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Dica de Estrada deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    public function Status($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Estrada', Array('id'=>$id),1);
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        if($resultado->status==1 || $resultado->status=='1'){
            $resultado->status='0';
        }else{
            $resultado->status='1';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            if($resultado->status==1){
                $texto = 'Ativado';
            }else{
                $texto = 'Desativado';
            }
            $conteudo = array(
                'location' => '#status'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Transporte/Estrada/Status/'.$resultado->id.'/'    ,''))
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            $this->_Visual->Json_Info_Update('Titulo','Status Alterado'); 
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Erro',
                "mgs_secundaria"    => 'Ocorreu um Erro.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);

            $this->_Visual->Json_Info_Update('Titulo','Erro'); 
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    public function Destaques($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Estrada', Array('id'=>$id),1);
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        if($resultado->destaque==1 || $resultado->destaque=='1'){
            $resultado->destaque='0';
        }else{
            $resultado->destaque='1';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            if($resultado->destaque==1){
                $texto = 'Em destaque';
            }else{
                $texto = 'Não está em destaque';
            }
            $conteudo = array(
                'location' => '#destaques'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Destaque'.$resultado->destaque     ,Array($texto        ,'Transporte/Estrada/Destaques/'.$resultado->id.'/'    ,''))
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            $this->_Visual->Json_Info_Update('Titulo','Destaque Alterado'); 
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Erro',
                "mgs_secundaria"    => 'Ocorreu um Erro.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);

            $this->_Visual->Json_Info_Update('Titulo','Erro'); 
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
