<?php
class Evento_EventoControle extends Evento_Controle
{
    public function __construct(){
        parent::__construct();
    }
    static function Endereco_Evento($true=true){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($true===true){
            $_Controle->Tema_Endereco('Eventos','Evento/Evento/Eventos');
        }else{
            $_Controle->Tema_Endereco('Eventos');
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses evento_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Evento/Evento/Eventos');
        return false;
    }
    static function Eventos_Tabela(&$eventos){
        $registro   = \Framework\App\Registro::getInstacia();
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($eventos)) $eventos = Array(0=>$eventos);
        reset($eventos);
        foreach ($eventos as &$valor) {
            $tabela['Nome do Evento'][$i]           =   $valor->nome;
            $tabela['Local'][$i]                    =   $valor->local2;
            $tabela['Data Inicio'][$i]              =   $valor->data_inicio;
            $tabela['Data Fim'][$i]                 =   $valor->data_fim;
            $tabela['Data Registrado'][$i]          =   $valor->log_date_add;
            if($valor->status==1 || $valor->status=='1'){
                $valor->status='1';
                $texto = 'Ativado';
            }else{
                $valor->status='0';
                $texto = 'Desativado';
            }
            $destaque                                     = $valor->destaque;
            $tabela['Funções'][$i]                   =   '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Evento/Evento/Status/'.$valor->id.'/'    ,'')).'</span>';
            if($destaque==1){
                $destaque = 1;
                $texto = 'Em Destaque';
            }else{
                $destaque = 0;
                $texto = 'Não está em destaque';
            }
            $tabela['Funções'][$i]      .= '<span id="destaques'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Destaque'.$destaque   ,Array($texto   ,'Evento/Evento/Destaques/'.$valor->id.'/'    ,'')).'</span>'.            
                                            $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Evento'        ,'Evento/Evento/Eventos_Edit/'.$valor->id.'/'    ,'')).
                                            $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Evento'       ,'Evento/Evento/Eventos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Evento ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Eventos($export=false){
        $i = 0;
        self::Endereco_Evento(false);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Evento',
                'Evento/Evento/Eventos_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Evento/Evento/Eventos',
            )
        )));
        $eventos = $this->_Modelo->db->Sql_Select('Evento');
        if($eventos!==false && !empty($eventos)){
            list($tabela,$i) = self::Eventos_Tabela($eventos);
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Eventos');
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
                $mensagem = 'Nenhum Evento para exportar';
            }else{
                $mensagem = 'Nenhum Evento';
            }     
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Eventos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Eventos');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Eventos_Add(){
        self::Endereco_Evento();
        // Carrega Config
        $titulo1    = 'Adicionar Evento';
        $titulo2    = 'Salvar Evento';
        $formid     = 'form_Sistema_Admin_Eventos';
        $formbt     = 'Salvar';
        $formlink   = 'Evento/Evento/Eventos_Add2/';
        $campos = Evento_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Eventos_Add2(){
        $titulo     = 'Evento Adicionado com Sucesso';
        $dao        = 'Evento';
        $funcao     = '$this->Main();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Evento cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Eventos_Edit($id){
        // Chama o Endereco
        self::Endereco_Evento();
        
        // Carrega Variaveis de Config
        $titulo1    = 'Editar Evento (#'.$id.')';
        $titulo2    = 'Alteração de Evento';
        $formid     = 'form_Sistema_AdminC_EventoEdit';  // identificador do formulario
        $formbt     = 'Alterar Evento'; // Nome do Botao
        $formlink   = 'Evento/Evento/Eventos_Edit2/'.$id; // Link de edicao
        $editar     = Array('Evento',$id); // Nome do DAO , $identificador
        // Chama COlunas Do Evento
        $campos = Evento_DAO::Get_Colunas();
        // Metodo que Gera Toda a Pagina Em cima Disso
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Eventos_Edit2($id){
        $titulo     = 'Evento Editado com Sucesso';
        $dao        = Array('Evento',$id); // Nome do DAO , $identificador
        $funcao     = '$this->Eventos();'; // Funcao executada depois de editar e mandar mensagem pro usuario
        $sucesso1   = 'Evento Alterado com Sucesso.';
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
    public function Eventos_Del($id){
        global $language;
        
    	$id = (int) $id;
        
        // Puxa do DAO, com o identificador e armazena na variavel evento
        $evento = $this->_Modelo->db->Sql_Select('Evento', Array('id'=>$id));
        // Envia evento pro metodo deletar do sql, e grava boleano em variavel sucesso
        $sucesso =  $this->_Modelo->db->Sql_Delete($evento);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Evento deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Eventos();
        // Titulo
        $this->_Visual->Json_Info_Update('Titulo', 'Evento deletado com Sucesso');
        // Bota pra nao gravar historico
        $this->_Visual->Json_Info_Update('Historico', false);
    }
    public function Status($id=false){
        if($id===false){
            throw new \Exception('Evento não informado:'. $id, 404);
        }
        // Pesquisa o evento no dao
        $resultado = $this->_Modelo->db->Sql_Select('Evento', Array('id'=>$id),1);
        // Caso falso, da erro 404
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse Evento não existe:'. $id, 404);
        }
        // Altera o Valor do Status
        if($resultado->status==1 || $resultado->status=='1'){
            $resultado->status='0';
        }else{
            $resultado->status='1';
        }
        // Faz Alteracao Update no MYSQL 
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            // CASO TENHA SUCESSO EXIBE TEXTO E MANDA PRO USUARIO NOVO FORMATO
            if($resultado->status==1 || $resultado->status=='1'){
                $texto = 'Ativado';
            }else{
                $texto = 'Desativado';
            }
            // ARRAY COM CONTEUDO
            $conteudo = array(
                'location' => '#status'.$resultado->id,
                'js' => '',
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Evento/Evento/Status/'.$resultado->id.'/'    ,''))
            );
            // MANDA CONTEUDO PRO USUARIO
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
            // MUDA O TITULO
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
        $resultado = $this->_Modelo->db->Sql_Select('Evento', Array('id'=>$id),1);
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
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Destaque'.$resultado->destaque     ,Array($texto        ,'Evento/Evento/Destaques/'.$resultado->id.'/'    ,''))
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
