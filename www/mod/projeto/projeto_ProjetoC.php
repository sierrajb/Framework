<?php
class projeto_ProjetoControle extends projeto_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses projeto_ListarModelo Carrega projeto Modelo
    * @uses projeto_ListarVisual Carrega projeto Visual
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __construct(){
        parent::__construct();
    }
    protected function Endereco_Projeto($true=true){
        if($true===true){
            $this->Tema_Endereco('Projetos','projeto/Projeto/Projetos');
        }else{
            $this->Tema_Endereco('Projetos');
        }
    }
    protected function Endereco_Projeto_Ver($projeto,$true=true){
        $this->Endereco_Projeto();
        if($true===true){
            $this->Tema_Endereco($projeto->nome,'projeto/Projeto/Projetos_View/'.$projeto->id);
        }else{
            $this->Tema_Endereco($projeto->nome);
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses projeto_Controle::$projetoPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main(){
        return false;
    }
    static function Projetos_Tabela($projetos){
        $registro   = \Framework\App\Registro::getInstacia();
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($projetos)) $projetos = Array(0=>$projetos);
        reset($projetos);
        foreach ($projetos as $indice=>&$valor) {
            $tabela['#Id'][$i]          =   '#'.$valor->id;
            $tabela['Categoria'][$i]    =   $valor->categoria2;
            $tabela['Nome'][$i]         =   $valor->nome;
            $tabela['Valor'][$i]        =   $valor->valor;
            $tabela['Mensalidade'][$i]  =   $valor->mensalidade;
            $tabela['Data Começo'][$i]  =   $valor->datacomeco;
            $tabela['Data Final'][$i]   =   $valor->datafinal;
            $tabela['Destaque'][$i]     = '<span class="destaque'.$valor->id.'">'.self::Destaquelabel($valor).'</span>';
            $tabela['Status'][$i]       = '<span class="status'.$valor->id.'">'.self::Statuslabel($valor).'</span>';
            $tabela['Funções'][$i]      =   $Visual->Tema_Elementos_Btn('Visualizar'      ,Array('Visualizar Projeto'    ,'projeto/Projeto/Projetos_Popup/'.$valor->id.'/'    ,'')).
                                            $Visual->Tema_Elementos_Btn('Zoom'            ,Array('Visualizar Projeto'    ,'projeto/Projeto/Projetos_View/'.$valor->id.'/'    ,'')).
                                            $Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Projeto'        ,'projeto/Projeto/Projetos_Edit/'.$valor->id.'/'    ,'')).
                                            $Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Projeto'       ,'projeto/Projeto/Projetos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Projeto ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos($export=false){
        $this->Endereco_Projeto(false);
        $i = 0;
        // Add BOtao
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Projeto',
                'projeto/Projeto/Projetos_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'projeto/Projeto/Projetos',
            )
        )));
        // Query
        $projetos = $this->_Modelo->db->Sql_Select('Projeto');
        if($projetos!==false && !empty($projetos)){
            list($tabela,$i) = self::Projetos_Tabela($projetos);
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Projetos');
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
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Projeto</font></b></center>');
        }
        $titulo = 'Listagem de Projetos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Projetos');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Add(){
        $this->Endereco_Projeto();
        // Carrega Config
        $titulo1    = 'Adicionar Projeto';
        $titulo2    = 'Salvar Projeto';
        $formid     = 'form_Sistema_Admin_Projetos';
        $formbt     = 'Salvar';
        $formlink   = 'projeto/Projeto/Projetos_Add2/';
        $campos = Projeto_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Add2(){
        $titulo     = 'Projeto Adicionado com Sucesso';
        $dao        = 'Projeto';
        $funcao     = '$this->Projetos();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Projeto cadastrado com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Edit($id){
        $this->Endereco_Projeto();
        // Carrega Config
        $titulo1    = 'Editar Projeto (#'.$id.')';
        $titulo2    = 'Alteração de Projeto';
        $formid     = 'form_Sistema_AdminC_ProjetoEdit';
        $formbt     = 'Alterar Projeto';
        $formlink   = 'projeto/Projeto/Projetos_Edit2/'.$id;
        $editar     = Array('Projeto',$id);
        $campos = Projeto_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Edit2($id){
        $titulo     = 'Projeto Editado com Sucesso';
        $dao        = Array('Projeto',$id);
        $funcao     = '$this->Projetos();';
        $sucesso1   = 'Projeto Alterado com Sucesso.';
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
    public function Projetos_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $linha = $this->_Modelo->db->Sql_Select('Projeto', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($linha);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Projeto Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Projetos();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Projeto deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    
    
    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function Status($id=false){
        
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $id = (int) $id;
        $resultado = $this->_Modelo->db->Sql_Select('Projeto', Array('id'=>$id),1);
        
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        
        // troca Resutlado
        if($resultado->status=='1'){
            $resultado->status='2'; // De Aprovada para Recusada
        }else if($resultado->status=='2'){ // de Aprovada em Execução para Finalizada
            $resultado->status='3';
        }else if($resultado->status=='3'){ // de Finalizada em Execução para Aprovada
            $resultado->status='4';
        }else if($resultado->status=='4'){ // De Recusada para Pendente
            $resultado->status='0';
        }else{
            $resultado->status='1';
        }
            
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => 'Sucesso',
                "mgs_secundaria"    => 'Status Alterado com Sucesso.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
            $conteudo = array(
                'location' => '.status'.$resultado->id,
                'js' => '',
                'html' =>  self::Statuslabel($resultado)
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Erro',
                "mgs_secundaria"    => 'Ocorreu um Erro.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @param type $objeto
     * @param type $link
     * @return string
     */
    public static function Statuslabel($objeto,$link=true){
        $status = $objeto->status;
        $id = $objeto->id;
        if($status=='0'){
            $tipo = 'warning';
            $nometipo = 'Pendente';
        }
        else if($status=='1'){
            $tipo = 'success';
            $nometipo = 'Aprovada';
        }
        else if($status=='2'){
            $tipo = 'info';
            $nometipo = 'Aprovada em Execução';
        }
        else if($status=='3'){
            $tipo = 'inverse';
            $nometipo = 'Finalizada';
        }
        else{
            $tipo = 'important';
            $nometipo = 'Recusada';
        }
        $html = '<span class="badge badge-'.$tipo.'">'.$nometipo.'</span>';
        if($link===true && \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('projeto/Projeto/Status')!==false){
            $html = '<a href="'.URL_PATH.'projeto/Projeto/Status/'.$id.'" border="1" class="lajax explicar-titulo" title="'.$nometipo.'" acao="" confirma="Deseja Realmente alterar o Status?">'.$html.'</a>';
        }
        return $html;
    }
    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function Destaque($id=false){
        
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $id = (int) $id;
        $resultado = $this->_Modelo->db->Sql_Select('Projeto', Array('id'=>$id),1);
        
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        
        // troca Resutlado
        if($resultado->destaque=='1'){
            $resultado->destaque='0'; // De Aprovada para Recusada
        }else{
            $resultado->destaque='1';
        }
            
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            $mensagens = array(
                "tipo"              => 'sucesso',
                "mgs_principal"     => 'Sucesso',
                "mgs_secundaria"    => 'Destaque Alterado com Sucesso.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
            $conteudo = array(
                'location' => '.destaque'.$resultado->id,
                'js' => '',
                'html' =>  self::Destaquelabel($resultado)
            );
            $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
        }else{
            $mensagens = array(
                "tipo"              => 'erro',
                "mgs_principal"     => 'Erro',
                "mgs_secundaria"    => 'Ocorreu um Erro.'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        }
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @param type $objeto
     * @param type $link
     * @return string
     */
    public static function Destaquelabel($objeto,$link=true){
        $destaque = $objeto->destaque;
        $id = $objeto->id;
        if($destaque=='0'){
            $tipo = 'important';
            $nometipo = 'Não Destaque';
        }else{
            $tipo = 'success';
            $nometipo = 'Destaque';
        }
        $html = '<span class="badge badge-'.$tipo.'">'.$nometipo.'</span>';
        if($link===true && \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('projeto/Projeto/Destaque')!==false){
            $html = '<a href="'.URL_PATH.'projeto/Projeto/Destaque/'.$id.'" border="1" class="lajax explicar-titulo" title="'.$nometipo.'" acao="" confirma="Deseja Realmente alterar o Destaque?">'.$html.'</a>';
        }
        return $html;
    }
    
    
    public function Projetos_View($projeto_id = false){
        if($projeto_id===false || $projeto_id==0 || !isset($projeto_id)) throw new \Exception('Projeto não informado',404);
        $projeto = $this->_Modelo->db->Sql_Select('Projeto',Array('id'=>$projeto_id), 1);
        if($projeto===false) throw new \Exception('Projeto não existe:'.$projeto_id,404);
        
        
        
        
        
        $this->Endereco_Projeto_Ver($projeto,false);
        list($titulo,$html) = $this->Projetos_Popup(      $projeto_id  , 'return' );
        list($titulo2,$html2) = $this->Projetos_Comentario( $projeto_id      ,true   );
        
        
        // Biblioteca
        if(\Framework\App\Sistema_Funcoes::Perm_Modulos('biblioteca')===true){
            $this->_Visual->Bloco_Customizavel(Array(
                Array(
                    'span'      =>      5,
                    'conteudo'  =>  Array(Array(
                        'div_ext'   =>      false,
                        'title_id'  =>      false,
                        'title'     =>      $titulo,
                        'html'      =>      $html,
                    ),Array(
                        'div_ext'   =>      false,
                        'title_id'  =>      false,
                        'title'     =>      $titulo2,
                        'html'      =>      $html2,
                    ),),
                ),
                Array(
                    'span'      =>      7,
                    'conteudo'  =>  Array(Array(
                        'div_ext'   =>      false,
                        'title_id'  =>      false,
                        'title'     =>      'Pasta da '.$titulo.' #'.$projeto->id.' na Biblioteca',
                        'html'      =>      '<span id="projeto_'.$projeto->id.'">'.biblioteca_BibliotecaControle::Biblioteca_Dinamica('projeto_Projeto',$projeto->id,'projeto_'.$projeto->id).'</span>',
                    ),Array(
                        'div_ext'   =>      false,
                        'title_id'  =>      false,
                        'title'     =>      'Financeiro',
                        'html'      =>      '<< GERAR PAGAMENTO >>',
                    ),),
                )
            ));
        }else{
            $this->_Visual->Blocar($html);
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',20);
            $this->_Visual->Blocar($html2);
            $this->_Visual->Bloco_Unico_CriaJanela($titulo2,'',10);
        }
        $this->_Visual->Json_Info_Update('Titulo','Visualizar Projeto Completo');
    }
    /**
     * 
     * @param type $projeto_id
     * @param type $popup (true,false, ou 'return')
     * @throws \Exception
     */
    public function Projetos_Popup($projeto_id = false, $popup=true){
        if($projeto_id===false || $projeto_id==0 || !isset($projeto_id)) throw new \Exception('Projeto não informado',404);
        // mostra todas as suas mensagens
        $where = Array(
            'id'    =>  $projeto_id,
        );
        $projeto = $this->_Modelo->db->Sql_Select('Projeto',$where, 1);
        $html  = '<div class="span6">';
        $html .= '<b>Categoria:</b> '.$projeto->categoria2.'<br>';  
        $html .= '<b>Nome:</b> '.$projeto->nome.'<br>';  
        $html .= '<b>Observação:</b> '.$projeto->obs; 
        $html .= '</div>';    
        $titulo = 'Informações do Projeto (#'.$projeto_id.')';
        if($popup=='return'){
            return Array($titulo,'<div class="row-fluid">'.$html.'</div>');
        }else if($popup===true){
            $conteudo = array(
                'id' => 'popup',
                'title' => $titulo,
                'botoes' => array(
                    array(
                        'text' => 'Fechar',
                        'clique' => '$( this ).dialog( "close" );'
                    )
                ),
                'html' => $html
            );
            $this->_Visual->Json_IncluiTipo('Popup',$conteudo);
            $this->_Visual->Json_Info_Update('Titulo','Visualizar Projeto');
        }else{
            $this->_Visual->Blocar('<div class="row-fluid">'.$html.'</div>');
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',20);
            $this->_Visual->Json_Info_Update('Titulo','Visualizar Projeto');
        }
    }
    /**
     * Comentarios dos Projetos
     */
    
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     * 
     * @param type $projeto_id
     * @param type $return
     */
    public function Projetos_Comentario($projeto_id = false,$return = false){
        if($projeto_id===false){
            $where = Array();
        }else{
            $where = Array('projeto'=>$projeto_id);
        }
        
        $i = 0;
        $html = '<a title="Adicionar Comentário de Projeto" class="btn btn-success lajax explicar-titulo" acao="" href="'.URL_PATH.'projeto/Projeto/Projetos_Comentario_Add/'.$projeto_id.'">Adicionar novo comentário nesse Projeto</a><div class="space15"></div>';
        $comentario = $this->_Modelo->db->Sql_Select('Projeto_Comentario',$where);
        if($comentario!==false && !empty($comentario)){
            if(is_object($comentario)) $comentario = Array(0=>$comentario);
            reset($comentario);
            foreach ($comentario as $indice=>&$valor) {
                $tabela['#Id'][$i]          =   '#'.$valor->id;
                $tabela['Comentário'][$i]   =   nl2br($valor->comentario);
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário de Projeto'        ,'projeto/Projeto/Projetos_Comentario_Edit/'.$projeto_id.'/'.$valor->id.'/'    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário de Projeto'       ,'projeto/Projeto/Projetos_Comentario_Del/'.$projeto_id.'/'.$valor->id.'/'     ,'Deseja realmente deletar esse Comentário desse Projeto ?'));
                ++$i;
            }
            $html .= $this->_Visual->Show_Tabela_DataTable($tabela,'', false, false, Array(Array(0,'desc')));
            unset($tabela);
        }else{
            $html .= '<center><b><font color="#FF0000" size="5">Nenhum Comentário do Projeto</font></b></center>';
        }
        
        $titulo = 'Comentários do Projeto ('.$i.')';
        if($return){
            return Array($titulo,$html);
        }else{
            $this->_Visual->Blocar($html);
            $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        }
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Comentários do Projeto');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Comentario_Add($projeto_id = false){
        // Proteção E chama Endereço
        if($projeto_id===false) throw new \Exception('Projeto não informado',404);
        $projeto = $this->_Modelo->db->Sql_Select('Projeto',Array('id'=>$projeto_id), 1);
        if($projeto===false) throw new \Exception('Projeto não existe:'.$projeto_id,404);
        $this->Endereco_Projeto_Ver($projeto);
        // Começo
        $projeto_id = (int) $projeto_id;
        // Carrega Config
        $titulo1    = 'Adicionar Comentário de Projeto';
        $titulo2    = 'Salvar Comentário de Projeto';
        $formid     = 'form_Sistema_Admin_Projetos_Comentario';
        $formbt     = 'Salvar';
        $formlink   = 'projeto/Projeto/Projetos_Comentario_Add2/'.$projeto_id;
        $campos = Projeto_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'projeto');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Comentario_Add2($projeto_id = false){
        if($projeto_id===false) throw new \Exception('Projeto não informado',404);
        $titulo     = 'Comentário do Projeto Adicionado com Sucesso';
        $dao        = 'Projeto_Comentario';
        $funcao     = '$this->Projetos_View('.$projeto_id.');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Comentário de Projeto cadastrado com sucesso.';
        $alterar    = Array('projeto'=>$projeto_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Comentario_Edit($projeto_id = false,$id = 0){
        if($projeto_id===false) throw new \Exception('Projeto não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        // Proteção E chama Endereço
        $projeto = $this->_Modelo->db->Sql_Select('Projeto',Array('id'=>$projeto_id), 1);
        if($projeto===false) throw new \Exception('Projeto não existe:'.$projeto_id,404);
        $this->Endereco_Projeto_Ver($projeto);
        // Começo
        // Carrega Config
        $titulo1    = 'Editar Comentário do Projeto (#'.$id.')';
        $titulo2    = 'Alteração de Comentário do Projeto';
        $formid     = 'form_Sistema_AdminC_ProjetoEdit';
        $formbt     = 'Alterar Comentário de Projeto';
        $formlink   = 'projeto/Projeto/Projetos_Comentario_Edit2/'.$projeto_id.'/'.$id;
        $editar     = Array('Projeto_Comentario',$id);
        $campos = Projeto_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'projeto');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Comentario_Edit2($projeto_id = false,$id = 0){
        if($projeto_id===false) throw new \Exception('Projeto não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        $titulo     = 'Comentário de Projeto Editado com Sucesso';
        $dao        = Array('Projeto_Comentario',$id);
        $funcao     = '$this->Projetos_View('.$projeto_id.');';
        $sucesso1   = 'Comentário de Projeto Alterado com Sucesso.';
        $sucesso2   = ''.$_POST["nome"].' teve a alteração bem sucedida';
        $alterar    = Array('projeto'=>$projeto_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Projetos_Comentario_Del($projeto_id = false,$id = 0){
        if($projeto_id===false) throw new \Exception('Projeto não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Projeto_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Comentário do Projeto Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Projetos_View($projeto_id);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Comentário de Projeto deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
