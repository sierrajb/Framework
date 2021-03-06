<?php
class comercio_PropostaControle extends comercio_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses comercio_rede_PerfilModelo::Carrega Rede Modelo
    * @uses comercio_rede_PerfilVisual::Carrega Rede Visual
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
     * 
     * @param type $true
     * @param type $tema
     */
    static function Endereco_Proposta($true=true,$tema='Propostas'){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($tema=='Propostas'){
            $titulo = 'Propostas';
            $link = 'comercio/Proposta/Propostas/'.$tema;
        }else{
            $titulo = CFG_TXT_COMERCIO_OS;
            $link = 'comercio/Proposta/Propostas/'.$tema;
        }
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @param type $true
     * @param type $tema
     * @param type $proposta
     */
    static function Endereco_Proposta_Comentario($true=true,$tema='Propostas',$proposta=false){
        self::Endereco_Proposta();
        // Pega ID
        if(is_object($proposta)){
            $propostaid = $proposta->id;
        }else{
            $propostaid = $proposta;
        }
        // Recupera Controle
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Comentários';
        $link = 'comercio/Proposta/Propostas_Comentario/'.$propostaid.'/'.$tema;
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @param type $true
     * @param type $tema
     * @param type $proposta
     */
    static function Endereco_Proposta_Sub($true=true,$tema='Propostas',$proposta=false){
        self::Endereco_Proposta();
        // Pega ID
        if(is_object($proposta)){
            $propostaid = $proposta->id;
        }else{
            $propostaid = $proposta;
        }
        // Recupera Controle
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Sub '.$tema;
        $link = 'comercio/Proposta/Propostas_Sub/'.$propostaid.'/'.$tema;
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @param type $true
     */
    static function Endereco_CheckList($true=true){
        self::Endereco_Proposta();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Checklist';
        $link = 'comercio/Proposta/Checklists';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @param type $true
     */
    static function Endereco_Visita($true=true){
        self::Endereco_Proposta();
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Folhas de Visitas';
        $link = 'comercio/Proposta/Visitas';
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * 
     * @param type $true
     * @param type $visita
     */
    static function Endereco_Visita_Comentario($true=true,$visita=false){
        self::Endereco_Visita();
        // Pega ID
        if(is_object($visita)){
            $visitaid = $visita->id;
        }else{
            $visitaid = $visita;
        }
        // Recupera Controle
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        $titulo = 'Comentários';
        $link = 'comercio/Proposta/Visitas_Comentario/'.$visitaid;
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    /**
     * Deleta Campos de Propostas 
     * @param type $campos
     * @param type $tema
     */
    static function Campos_Deletar(&$campos,$tema='Propostas'){
        // Retira Padroes
        if(!(\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio_servicos'))){
            self::DAO_Campos_Retira($campos, 'Serviço');
            self::DAO_Campos_Retira($campos, 'Serviços'); // Tipo de Servico
        }else{
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_servicos_ServicoTipo')){
                self::DAO_Campos_Retira($campos, 'Serviço');
            }else{
                self::DAO_Campos_Retira($campos, 'Serviços'); // Tipo de Servico
            }
        }
        
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_MaodeObra')){
            self::DAO_Campos_Retira($campos, 'Mão de Obra');
        }
        
        
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Imposto') || $tema!=='Propostas'){
            self::DAO_Campos_Retira($campos, 'imposto');
        }        
        
        
        // Retira de Instalação
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_servicos_Instalacao') || !(\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio_servicos'))){
            // Retira Tipo de Proposta
            self::DAO_Campos_Retira($campos, 'propostatipo');
            self::DAO_Campos_RetiraAlternados($campos);
            self::DAO_Campos_Retira($campos, 'Btus');
        }
        // Caso Contrario Bota Instalacao como Padrao
        else {
            self::DAO_Campos_TrocaAlternados($campos);
            self::mysql_AtualizaValor($campos, 'propostatipo', 1);
        }
        
        // Retira que nao tiver nas opcoes
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Referencia')){
            self::DAO_Campos_Retira($campos, 'referencia');
        }
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Telefone')){
            self::DAO_Campos_Retira($campos, 'telefone');
        }
        
        
        // Retira se nao for OS
        if($tema=='Propostas'){
            self::DAO_Campos_Retira($campos, 'Funcionários');
            self::DAO_Campos_Retira($campos, 'Checklist');
 
            if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
                self::DAO_Campos_Retira($campos, 'pagar_lucro');
            }
            if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Desconto')){
                self::DAO_Campos_Retira($campos, 'pagar_desconto');
            }
        
            if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorFinal')){
                self::DAO_Campos_Retira($campos, 'valor_fixo');
            }
            // Se tiver Comissao Add
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Comissao')===false){
                self::DAO_Campos_Retira($campos, 'comissao');
            }   
            if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorExtra')){
                self::DAO_Campos_Retira($campos, 'valor_extra');
            }
        }else{
            self::DAO_Campos_Retira($campos, 'validade');
            self::DAO_Campos_Retira($campos, 'forma_pagar');
            self::DAO_Campos_Retira($campos, 'condicao_pagar');
            self::DAO_Campos_Retira($campos, 'pagar_lucro');
            self::DAO_Campos_Retira($campos, 'pagar_desconto');
            self::DAO_Campos_Retira($campos, 'status');
            if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Checklist')){
                self::DAO_Campos_Retira($campos, 'Checklist');
            }
            self::DAO_Campos_Retira($campos, 'valor_fixo');
            self::DAO_Campos_Retira($campos, 'comissao');
            self::DAO_Campos_Retira($campos, 'valor_extra');
        }
    }
    /**
    * Main
    * 
    * @name Main
    * @access public
    * 
    * @uses comercio_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main($tema='Propostas'){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'comercio/Proposta/Propostas/'.$tema);
        return false;
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas($tema='Propostas',$export=false){
        self::Endereco_Proposta(false,$tema);
        if($tema!='Propostas'){
            $where = Array(
                'INstatus' => Array(1,2,'1','2')
            );
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $adicionar = false;
        }else{
            $where = Array(
                'NOTINstatus' => Array(2,3,'2','3')
            );
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $adicionar = Array(
                'Adicionar nova '.$titulo,
                'comercio/Proposta/Propostas_Add/'.$tema.'',
                ''
            );
        }
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            $adicionar,
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Proposta/Propostas/'.$tema,
            )
        )));
        //
        $i = 0;
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',$where,0,''/*,
                'id,propostatipo,cliente,cliente2,cuidados,cuidados2,condicao_pagar,condicao_pagar2,forma_pagar,forma_pagar2,pagar_lucro,pagar_desconto,valor'*/);
        if($proposta!==false && !empty($proposta)){
            if(is_object($proposta)) $proposta = Array(0=>$proposta);
            reset($proposta);
            foreach ($proposta as $indice=>&$valor) {
                $tabela['Número'][$i]       =  '#'.$valor->id;
                if($valor->propostatipo==1 || $valor->propostatipo=='1'){
                    if(SQL_MAIUSCULO===true){
                        $propostatipo = 'INSTALAÇÃO';
                    }else{
                        $propostatipo = 'Instalação';
                    }
                }else{
                    if(SQL_MAIUSCULO===true){
                        $propostatipo = 'SERVIÇO';
                    }else{
                        $propostatipo = 'Serviço';
                    }
                } // Retira de Instalação
                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_servicos_Instalacao') || !(\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio_servicos'))){
                    $tabela['Tipo de '.$titulo][$i]         =  $propostatipo;
                }
                $tabela['Cliente'][$i]                      =  $valor->cliente2;
                $tabela['Vendedor'][$i]                     =  $valor->cuidados2;
                if($tema!='Propostas') {
                    
                } else {
                    if(SQL_MAIUSCULO===true){
                        $pagamento = $valor->condicao_pagar2.' EM '.$valor->forma_pagar2;
                    }else{
                        $pagamento = $valor->condicao_pagar2.' em '.$valor->forma_pagar2;
                    }
                    $tabela['Pagamento'][$i]                    =  $pagamento;
                    if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
                        $tabela['Lucro'][$i]                        =  $valor->pagar_lucro;
                    }
                    if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Desconto')){
                        $tabela['Desconto'][$i]                     =  $valor->pagar_desconto;
                    }
                    $tabela['Valor Total'][$i]                  =  $valor->valor;
                }
                $tabela['Status'][$i]                       = '<span class="status'.$valor->id.'">'.self::label($valor,$tema).'</span>';
                $tabela['Funções'][$i]   =  $this->_Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar '.$titulo         ,'comercio/Proposta/Propostas_View/'.$valor->id.'/'.$tema.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Personalizado'    ,Array('Sub '.$titulo    ,'comercio/Proposta/Propostas_Sub/'.$valor->id.'/'.$tema.'/'    ,'','collapse','warning')).
                                            $this->_Visual->Tema_Elementos_Btn('Personalizado'    ,Array('Histórico da '.$titulo    ,'comercio/Proposta/Propostas_Comentario/'.$valor->id.'/'.$tema.'/'    ,'','file','inverse')).
                                            $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar '.$titulo             ,'comercio/Proposta/Propostas_Edit/'.$valor->id.'/'.$tema.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar '.$titulo            ,'comercio/Proposta/Propostas_Del/'.$valor->id.'/'.$tema.'/'     ,'Deseja realmente deletar essa Proposta ? Isso irá afetar o sistema!'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, $titulo_plural);
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
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma '.$titulo.'</font></b></center>');
        }
        $titulo = 'Listagem de '.$titulo_plural.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        // Propostas/OS antigas
        $this->_Visual->Blocar('<span id="proposta_outras" carregado="0"><center><font color="#FF0000" size="5">Carregando...</font></center></span>');
        $bloco_identificador =  $this->_Visual->Bloco_Unico_CriaJanela('Outras '.$titulo_plural.'','',5,false,true);
        $javascript_executar =  '$(document).on("click", \'a#'.$bloco_identificador.'_max\', function () {'. 
                                'console.log("oi",$(\'#proposta_outras\').attr("carregado")); if($(\'#proposta_outras\').attr("carregado")!==\'1\' && $(\'#proposta_outras\').attr("carregado")!==1){'.
                                    '$(\'#proposta_outras\').attr("carregado",\'1\');'.
                                    'Sierra.Modelo_Ajax_Chamar(\'comercio/Proposta/Outros/'.$tema.'\',\'\',\'get\',true,false,true);'.
                                '}});';
        $this->_Visual->Javascript_Executar($javascript_executar);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar '.$titulo);
    }
    /**
     * Outras Propostas/Os
     * @param type $tema
     * @param type $export
     */
    public function Outros($tema='Propostas',$export=false){
        self::Endereco_Proposta(false,$tema);
        if($tema!='Propostas'){
            $where = Array(
                'NOTINstatus' => Array(1,2,'1','2')
            );
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
        }else{
            $where = Array(
                'INstatus' => Array(2,3,'2','3')
            );
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
        }
        //
        $i = 0;
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',$where);
        if($proposta!==false && !empty($proposta)){
            if(is_object($proposta)) $proposta = Array(0=>$proposta);
            reset($proposta);
            foreach ($proposta as $indice=>&$valor) {
                $tabela['Número'][$i]       =  '#'.$valor->id;
                if($valor->propostatipo==1 || $valor->propostatipo=='1'){
                    if(SQL_MAIUSCULO===true){
                        $propostatipo = 'INSTALAÇÃO';
                    }else{
                        $propostatipo = 'Instalação';
                    }
                }else{
                    if(SQL_MAIUSCULO===true){
                        $propostatipo = 'SERVIÇO';
                    }else{
                        $propostatipo = 'Serviço';
                    }
                } // Retira de Instalação
                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_servicos_Instalacao') || !(\Framework\App\Sistema_Funcoes::Perm_Modulos('comercio_servicos'))){
                    $tabela['Tipo de '.$titulo][$i]         =  $propostatipo;
                }
                $tabela['Cliente'][$i]                      =  $valor->cliente2;
                $tabela['Vendedor'][$i]                     =  $valor->cuidados2;
                if($tema!='Propostas') {
                    
                } else {
                    if(SQL_MAIUSCULO===true){
                        $pagamento = $valor->condicao_pagar2.' EM '.$valor->forma_pagar2;
                    }else{
                        $pagamento = $valor->condicao_pagar2.' em '.$valor->forma_pagar2;
                    }
                    $tabela['Pagamento'][$i]                    =  $pagamento;
                    if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
                        $tabela['Lucro'][$i]                        =  $valor->pagar_lucro;
                    }
                    $tabela['Desconto'][$i]                     =  $valor->pagar_desconto;
                    $tabela['Valor Total'][$i]                  =  $valor->valor;
                }
                $tabela['Status'][$i]                       =  '<span class="status'.$valor->id.'">'.self::label($valor,$tema,($tema!='Propostas' && $valor->status=='3'?true:false)).'</span>';
                $tabela['Funções'][$i]   =  $this->_Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar '.$titulo         ,'comercio/Proposta/Propostas_View/'.$valor->id.'/'.$tema.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Personalizado'    ,Array('Histórico da '.$titulo    ,'comercio/Proposta/Propostas_Comentario/'.$valor->id.'/'.$tema.'/'    ,'','file','inverse'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, $titulo_plural);
            }else{
                $html = $this->_Visual->Show_Tabela_DataTable(
                    $tabela,     // Array Com a Tabela
                    '',          // style extra
                    false,        // true -> Add ao Bloco, false => Retorna html
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
            $html = '<center><b><font color="#FF0000" size="5">Nenhuma '.$titulo.'</font></b></center>';
        }
        
        
        $conteudo = array(
            'location' => '#proposta_outras',
            'js' => '',
            'html' =>  $html
        );
        $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_View($id,$tema='Propostas',$layoult='Unico'){
        // Se for Excell Transfere
        if($layoult=='Excel'){
            $this->Propostas_View_Excell($id,$tema);
            return true;
        }
        
        
        $html = '<span style="text-transform:uppercase;">';
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        
        // Verifica Permissao e Puxa Usuário
        $identificador = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$id),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Proposta não Existe',404);
        }
        $id = $identificador->id;
        
        $cliente = $this->_Modelo->db->Sql_Select('Usuario',Array('id'=>$identificador->cliente),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Proposta não Existe',404);
        }
        if($cliente===false){
            throw new \Exception('Cliente não existe',404);
        }
        
        
        
        if($layoult!=='Imprimir'){            
            $html .= $this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
                false,
                Array(
                    'Print'     => true,
                    'Pdf'       => false,
                    'Excel'     => true,
                    'Link'      => 'comercio/Proposta/Propostas_View/'.$id.'/'.$tema,
                )
            ));
        }
        
        
        // Pega Endereço
        $endereco = '';
        if($cliente->endereco!==''){
            $endereco .= ' '.$cliente->endereco;
        }
        if($cliente->numero!==''){
            $endereco .= ', '.$cliente->numero;
        }
        if($cliente->bairro2!==''){
            $endereco .= ', '.$cliente->bairro2;
        }
        if($cliente->complemento!==''){
            $endereco .= ', '.$cliente->complemento;
        }
        
        // Pega Telefones
        $emailcliente = '';
        if($cliente->email!=''){
            $emailcliente .= ' '.$cliente->email;
        }
        // Pega Telefones
        $telefone = '';
        if($cliente->telefone!=''){
            $telefone .= ' '.$cliente->telefone;
        }
        if($cliente->telefone2!=''){
            $telefone .= ' '.$cliente->telefone2;
        }
        if($cliente->telefone3!=''){
            $telefone .= ' '.$cliente->telefone3;
        }
        if($cliente->celular!=''){
            $telefone .= ' '.$cliente->celular;
        }
        if($cliente->celular2!=''){
            $telefone .= ' '.$cliente->celular2;
        }
        // Começa a escrever Dados Básicos
        $html .= '<h3>Dados Principais</h3>';
        // Tipo de Proposta
        if($identificador->propostatipo===1 || $identificador->propostatipo==='1'){
            $propostatipo = 'Instalação';
        }else{
            $propostatipo = 'Serviço';
        }
        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">'.$titulo.':</label> '.$identificador->id.'</p>';
        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Tipo de '.$titulo.':</label> '.$propostatipo.'</p>';
        // Clientes
        if($identificador->cliente2!='')   $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Cliente:</label> '.$identificador->cliente2.'</p>';
        if($endereco!=='')                 $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Endereço do Cliente:</label> '.$endereco.'</p>';
        if($telefone!=='')                 $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Telefone do Cliente:</label> '.$telefone.'</p>';
        if($emailcliente!=='')             $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Email do Cliente:</label> '.$emailcliente.'</p>';
        
        // Vendedor
        if($identificador->cuidados2!=='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Vendedor:</label> '.$identificador->cuidados2.'</p>';
        
        // Alterna
        if($tema!='Propostas'){
            
        }else{
            // Calcula Valor de Custo
            $t = \Framework\App\Sistema_Funcoes::Tranf_Real_Float($identificador->valor);
            $l = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_lucro);
            $d = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_desconto);
            $valor_inicial = \Framework\App\Sistema_Funcoes::Tranf_Float_Real($t/(1+$l-$d*$l));
            unset($t);unset($l);unset($d);
            
            
            if($identificador->validade!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Validade da Proposta:</label> '.$identificador->validade.'</p>';
            if($identificador->forma_pagar!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Forma de Pagamento:</label> '.$identificador->forma_pagar2.'</p>';
            if($identificador->condicao_pagar!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Condição de Pagamento:</label> '.$identificador->condicao_pagar2.'</p>';
            $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Valor de Custo:</label> '.$valor_inicial.'</p>';
            
            if($identificador->pagar_lucro!='' && \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro'))  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Lucro da Proposta:</label> '.$identificador->pagar_lucro.'</p>';
            if($identificador->pagar_desconto!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Desconto da Proposta:</label> '.$identificador->pagar_desconto.'</p>';
            if($identificador->valor!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Valor Total da Proposta:</label> '.$identificador->valor.'</p>';
        }
        //Status
        if($identificador->status!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Status:</label> <span class="status'.$identificador->id.'">'.self::label($identificador,$tema,false).'</span></p>';
        
        //Status
        if($identificador->obs!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Observação:</label>'.$identificador->obs.'</p>';
        
        
        
        
        
        
        // Depende da Proposta
        if($identificador->propostatipo==1 || $identificador->propostatipo=='1'){
            $identificador->propostatipo=1;
        
            // Captura o Serviço de Instalaçao
            $produtos_reg  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_Produto', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Serviço de Instalaçao
            if($produtos_reg!==false){
                if(is_object($produtos_reg)) $produtos_reg = Array($produtos_reg);
                foreach($produtos_reg as &$valor){
                    // Captura o Serviço de Instalaçao
                    $valor_reg  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Produto', 
                        Array(
                            'id'     =>  $valor->produto
                        ),
                        1
                    );
                    // Exibe
                    $html .= '<div class="space15"></div>';
                    $html .= '<h3>Produto '.ucfirst($valor_reg->nome).'</h3>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Preço Atual:</label> '.$valor_reg->preco.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Quantidade:</label> '.$valor->prod_qnt.'</p>';
                }
            }
                
                
                
            // Captura o Serviço de Instalaçao
            $instalacao  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_ServicoInstalacao', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            $i = 0;
            // Pega os Valores do Serviço de Instalaçao
            if($instalacao!==false){
                if(is_object($instalacao)) $instalacao = Array($instalacao);
                foreach($instalacao as &$valor){
                    ++$i;
                    // Repete Impressao
                    if(CFG_IMPRESSAO_TITULO_REPETIR===true && $i<=1 && $layoult==='Imprimir'){
                        $html .= self::Export_Imprimir_Rodape().'<div style="page-break-before: always" style="page-break-before: always;"></div>'.self::Export_Imprimir_Titulo();
                    }
                    
                    
                    // Pega dados
                    if($valor->distancia==1){
                        $valor->distancia = $valor->distancia.' metro';
                    }else{
                        $valor->distancia = $valor->distancia.' metros';
                    }
                    // Formata
                    if($valor->infra==0||$valor->infra=='0'){
                        $infra = 'Não existente';
                    }else{
                        $infra = 'Existente';
                    }
                    
                    if($valor->tipocondensadora==0||$valor->tipocondensadora=='0'){
                        $tipocondensadora = 'Vertical';
                    }else if($valor->tipocondensadora==1||$valor->tipocondensadora=='1'){
                        $tipocondensadora = 'Horizontal';
                    }else{
                        $tipocondensadora = 'Janela';
                    }
                    
                    if($valor->tipoevaporadora==0||$valor->tipoevaporadora=='0'){
                        $tipoevaporadora = 'Hi Wall';
                    }else if($valor->tipoevaporadora==1||$valor->tipoevaporadora=='1'){
                        $tipoevaporadora = 'Piso Teto';
                    }else{
                        $tipoevaporadora = 'Cassete';
                    }
                    
                    if($valor->tipodreno==0||$valor->tipodreno=='0'){
                        $tipodreno = 'Tubulado';
                    }else if($valor->tipodreno==1||$valor->tipodreno=='1'){
                        $tipodreno = 'Livre';
                    }else{
                        $tipodreno = 'Mangueira';
                    }
                    
                    // Exibe
                    $html .= '<div class="space15"></div>';
                    $html .= '<h3>Instalação de '.ucfirst($valor->btu2).'</h3>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Infra:</label> '.$infra.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Tipo de Condensadora:</label> '.$tipocondensadora.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Tipo de Evaporadora:</label> '.$tipoevaporadora.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Tipo de Dreno:</label> '.$tipodreno.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Adicional de Linha:</label> '.$valor->distancia.'</p>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Suporte:</label> '.$valor->suporte2.'</p>';
                    if($tema=='Propostas'){

                        // Captura Preço do Equipamento
                        $instalacao_btu  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Btu', 
                            Array(
                                'id'           =>  $valor->btu
                            ),
                            1
                        );
                        if($instalacao_btu!==false){
                            $instalacao_btu_ar      = $instalacao_btu->valor_ar;
                            $instalacao_btu_linha   = $instalacao_btu->valor_linha;
                            $instalacao_btu_gas     = $instalacao_btu->valor_gas;
                        }else{
                            $instalacao_btu_ar      = 'R$ 0,00';
                            $instalacao_btu_linha   = 'R$ 0,00';
                            $instalacao_btu_gas     = 'R$ 0,00';
                        }
                        
                        // Captura Preço do SUPORTE
                        $instalacao_suporte  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Suporte', 
                            Array(
                                'id'            =>  $valor->suporte
                            ),
                            1
                        );
                        if($instalacao_suporte!==false){
                            $instalacao_suporte = $instalacao_suporte->valor;
                        }else{
                            $instalacao_suporte = 'R$ 0,00';
                        }
                        
                        // Adiciona Valor da Linha para acima de 5 metros
                        if($valor->distancia>5){
                            $instalacao_btu_linha = \Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_linha)*($valor->distancia-5));
                        }else{
                            $instalacao_btu_gas     = 'R$ 0,00';
                            $instalacao_btu_linha = 'R$ 0,00';
                        }

                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo do Suporte:</label> '.$instalacao_suporte.'</p>';
                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo da Instalação Básica:</label> '.$instalacao_btu_ar.'</p>';
                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo do Add de Linha:</label> '.$instalacao_btu_linha.'</p>';
                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo do Add de Gás:</label> '.$instalacao_btu_gas.'</p>';
                    }
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Observação:</label> '.$valor->obs.'</p>';
                    if($layoult==='Imprimir'){
                        if($tema!=='Propostas'){
                            $html .= '<br><p style="text-align:justify; width:700px; line-height:30px; margin: 10px;">' .
                            'N° de Serie EVAP: ______________________________ '. 
                            'N° de Serie Cond: ______________________________<br>'. 
                            'Temp. de Retorno: ____________ '. 
                            'Temp. de Insuflamento: _________ '. 
                            'Diferencial: _________________<br>'. 
                            'Pressão de Baixa: ______________________________ '. 
                            'Pressão de Alta: _______________________________<br>'. 
                            'Corrente: __________________________________________________________________________________________<br>'. 
                            'Obs: _______________________________________________________________________________________________<br>'. 
                            '____________________________________________________________________________________________________<br>'. 
                            '____________________________________________________________________________________________________<br>'. 
                            '____________________________________________________________________________________________________<br>'. 
                            '____________________________________________________________________________________________________</p><br>';
                        }
                    }
                }
            }
        }else{
            // Coloca Campos do Tipo
            if($identificador->referencia!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Referência:</label> '.$identificador->referencia.'</p>';
            if($identificador->telefone!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Telefone:</label> '.$identificador->telefone.'</p>';
        
            $identificador->propostatipo=0;
            // Captura Tipo de Serviço
            $servicotipo  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_ServicoTipo', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Tipo de Serviço
            if($servicotipo!==false){
                if(is_object($servicotipo)) $servicotipo = Array($servicotipo);
                foreach($servicotipo as &$valor){
                    if($valor->servicotipo2===NULL) continue;
                    // Exibe
                    $html .= '<div class="space15"></div>';
                    $html .= '<h3>Serviço de '.ucfirst($valor->servicotipo2).'</h3>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Quantidade de Diárias:</label> '.$valor->diarias_qnt.'</p>';
                    if($tema==='Propostas'){
                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo da Diária:</label> '.$valor->diarias_valor.'</p>';
                    }
                }
            }

            // Captura o Serviço
            $servico  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_Servico', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Serviço
            if($servico!==false){
                if(is_object($servico)) $servico = Array($servico);
                foreach($servico as &$valor){
                    if($valor->servico2===NULL) continue;
                    // Captura Preço do SErviço
                    $servico2  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Servicos_Servico', 
                        Array(
                            'id'     =>  $valor->servico
                        ),
                        1
                    );
                    // Exibe
                    $html .= '<div class="space15"></div>';
                    $html .= '<h3>Serviço de '.ucfirst($valor->servico2).'</h3>';
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Quantidade da mão de Obra:</label> '.$valor->qnt.'</p>';
                    if($tema==='Propostas'){
                        $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Custo do Serviço:</label> '.$servico2->preco.'</p>';
                    }
                }
            }

        }
        
        
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Checklist')){
            // Captura Checklist
            $checklist  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_Checklist', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            if($checklist!==false){
                if(is_object($checklist)) $checklist = Array($checklist);
                $html .= '<div class="space15"></div>';
                $html .= '<h3>Checklists</h3>';
                $i = 1;
                foreach($checklist as &$valor){
                    // Exibe
                    $html .= '<p><label style="width:250px; float:left; margin-right:5px;">'.$i.'º :</label> '.$valor->checklist2.'</p>';
                    ++$i;
                }
            }
        }
        
        // Bota Espaço
        $html .= '<div class="space15"></div>';
        
        $html .= '</span>';
        // Caso seja pra Imprimir
        if($layoult==='Imprimir'){
            if($tema!=='Propostas'){
                $html .= '<br><p style="text-align:justify; width:600px; line-height:30px; margin: 10px;">' .
                'Executor: _______________________________________________________________________________________<br>'.
                'Data Inicio: ____________________ Data Fim: _____________________________________________________<br>'.
                'Aprovado por: ___________________________ Função: ___________________________________________<br>'.
                'Observação: ____________________________________________________________________________________ '.
                '____________________________________________________________________________________________________ '. 
                '____________________________________________________________________________________________________ '. 
                '____________________________________________________________________________________________________</p><br>';
            }
            self::Export_Todos($layoult,$html, $titulo.' #'.$identificador->id);
        }else{
            // Identifica tipo e cria conteudo
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Biblioteca')===true && \Framework\App\Sistema_Funcoes::Perm_Modulos('biblioteca')===true){
                
                // Coloca Endereco
                self::Endereco_Proposta(true,$tema);
                $this->Tema_Endereco('Visualizar '.$titulo);
                
                
                
                $this->_Visual->Bloco_Customizavel(Array(
                    Array(
                        'span'      =>      5,
                        'conteudo'  =>  Array(Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      $titulo.' #'.$identificador->id,
                            'html'      =>      $html,
                        ),),
                    ),
                    Array(
                        'span'      =>      7,
                        'conteudo'  =>  Array(Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      'Pasta da '.$titulo.' #'.$identificador->id.' na Biblioteca',
                            'html'      =>      '<span id="proposta_'.$identificador->id.'">'.biblioteca_BibliotecaControle::Biblioteca_Dinamica('comercio_Proposta',$identificador->id,'proposta_'.$identificador->id).'</span>',
                        )/*,Array(
                            'div_ext'   =>      false,
                            'title_id'  =>      false,
                            'title'     =>      'Sub-'.$tema,
                            'html'      =>      ' Aqui tem !',
                        ),*/),
                    )
                ));
            }else if(LAYOULT_IMPRIMIR=='AJAX'){
                // Coloca Conteudo em Popup
                $popup = array(
                    'id'        => 'popup',
                    'title'     => $titulo.' #'.$identificador->id,
                    /*'botoes'    => array(
                        '0'         => array(
                            'text'      => 'Fechar Janela',
                            'clique'    => '$(this).dialog(\'close\');'
                        )
                    ),*/
                    'html'      => $html
                );
                $this->_Visual->Json_IncluiTipo('Popup',$popup);
            }else{
                // Coloca Endereco
                self::Endereco_Proposta(true,$tema);
                $this->Tema_Endereco('Visualizar '.$titulo);
                // Coloca COnteudo em Janelas
                $this->_Visual->Blocar($html);
                if($layoult==='Unico'){
                    $this->_Visual->Bloco_Unico_CriaJanela($titulo.' #'.$identificador->id);
                }else if($layoult==='Maior'){
                    $this->_Visual->Bloco_Maior_CriaJanela($titulo.' #'.$identificador->id);
                }else{
                    $this->_Visual->Bloco_Menor_CriaJanela($titulo.' #'.$identificador->id);
                }
            }
            
            
            
            
            
            //Carrega Json
            $this->_Visual->Json_Info_Update('Titulo',$titulo.' #'.$identificador->id);
        }
    }
    public function Propostas_View_Excell($id,$tema='Propostas'){
        // Definimos o nome do arquivo que será exportado
        
        
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        
        // Verifica Permissao e Puxa Usuário
        $identificador = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$id),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Proposta não Existe',404);
        }
        $id = $identificador->id;
        $total_geral = 0;
        
        // Cabecario Excell
        $arquivo_nome = $titulo.$id.'.xls';
        // Criamos uma tabela HTML com o formato da planilha
        $html = '<table>';
        // Configurações header para forçar o download
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
	//header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header ("Content-type: application/x-msexcel; charset=UTF-8");
        header ("Content-Disposition: attachment; filename=\"{$arquivo_nome}\"" );
        header ("Content-Description: PHP Generated Data" );
        
        // Busca Cliente
        $cliente = $this->_Modelo->db->Sql_Select('Usuario',Array('id'=>$identificador->cliente),1); // Banco DAO, Condicao e LIMITE
        // Verifica se Existe e Continua
        if($identificador===false){
            throw new \Exception('Proposta não Existe',404);
        }
        if($cliente===false){
            throw new \Exception('Cliente não existe',404);
        }
        
        // Lucro
        if($identificador->pagar_lucro!='' && \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
            $pagar_lucro_porc = 1+\Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_lucro);
        }else{
            $pagar_lucro_porc = 1;
        }
        $data_criada = explode(' ',$identificador->log_date_add);
        $html .= '<tr><td rowspan="6" colspan="4"><img style="max-height: 80px;" alt="'.SISTEMA_NOME.'" src="'.ARQ_URL.'_Sistema/logo.png"></td>
            <td colspan="8" style="text-align:right;">'.$titulo.$id.'</td>	
            <td colspan="8" style="text-align:right;">'.$identificador->cuidados2.'</td>	
            <tr><td colspan="8" style="text-align:right;">Tel.: (21) 2427-5425</td></tr>	
            <tr><td colspan="8" style="text-align:right;">Data:  '.$data_criada[0].'</td></tr>	
            <tr><td colspan="8">&nbsp;</td>	
            <tr><td colspan="8">&nbsp;</td></tr><tr><td colspan="12">&nbsp;</td></tr>';

        // Pega Endereço
        $endereco = '';
        if($cliente->endereco!==''){
            $endereco .= ' '.$cliente->endereco;
        }
        if($cliente->numero!==''){
            $endereco .= ', '.$cliente->numero;
        }
        if($cliente->bairro2!==''){
            $endereco .= ', '.$cliente->bairro2;
        }
        if($cliente->complemento!==''){
            $endereco .= ', '.$cliente->complemento;
        }
        
        // Pega Telefones
        $emailcliente = '';
        if($cliente->email!=''){
            $emailcliente .= ' '.$cliente->email;
        }
        // Pega Telefones
        $telefone = '';
        if($cliente->telefone!=''){
            $telefone .= ' '.$cliente->telefone;
        }
        if($cliente->telefone2!=''){
            $telefone .= ' '.$cliente->telefone2;
        }
        if($cliente->telefone3!=''){
            $telefone .= ' '.$cliente->telefone3;
        }
        if($cliente->celular!=''){
            $telefone .= ' '.$cliente->celular;
        }
        if($cliente->celular2!=''){
            $telefone .= ' '.$cliente->celular2;
        }
        // Começa a escrever Dados Básicos
        $html .= '<tr><td colspan="12" rowspan="2" bgcolor="#000000" style="color:#FFFFFF;text-align:center;"><b>ORÇAMENTO</b></td></tr><tr><td colspan="12">&nbsp;</td></tr>';
        $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;"><b>DADOS DO CLIENTE</b></td></tr>';
        // Tipo de Proposta
        if($identificador->propostatipo===1 || $identificador->propostatipo==='1'){
            $propostatipo = 'Instalação';
        }else{
            $propostatipo = 'Serviço';
        }
        //$html .= '<tr><td colspan="12"><b>'.$titulo.':</b> '.$identificador->id.'</td></tr>';
        //$html .= '<tr><td colspan="12"><b>Tipo de '.$titulo.':</b> '.$propostatipo.'</td></tr>';
        //
        // Clientes
        if($identificador->cliente2!='')   $html .= '<tr><td colspan="12"><b>Nome / Razão Social:</b> '.$identificador->cliente2.'</td></tr>';
        //if($endereco!=='')                 $html .= '<tr><td colspan="12"><b>Endereço do Cliente:</b> '.$endereco.'</td></tr>';
        if($telefone!=='')                 $html .= '<tr><td colspan="7"><b>Telefone(s):</b> '.$telefone.'</td>';
        if($emailcliente!=='')             $html .= '<td colspan="5"><b>Email:</b> '.$emailcliente.'</td></tr>';
        
        // Vendedor
        //if($identificador->cuidados2!=='')  $html .= '<tr><td colspan="12"><b>Vendedor:</b> '.$identificador->cuidados2.'</td></tr>';
        
        //Status
        //if($identificador->status!='')  $html .= '<tr><td colspan="12"><b>Status:</b> <span class="status'.$identificador->id.'">'.self::label($identificador,$tema,false).'</span></td></tr>';
        
        //Status
        //if($identificador->obs!='')  $html .= '<tr><td colspan="12"><b>Observação:</b>'.$identificador->obs.'</td></tr>';
        
        
        
        
        
        
        // Depende da Proposta
        if($identificador->propostatipo==1 || $identificador->propostatipo=='1'){
            $identificador->propostatipo=1;
        
            // Captura os Produtos Utilizados na Proposta
            $produtos_reg  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_Produto', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Serviço de Instalaçao
            if($produtos_reg!==false){
                $total = 0;
                if($tema==='Propostas'){
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS EQUIPAMENTOS</td></tr>'.
                         '<tr><td colspan="3"><b>Produto</b></td><td colspan="3"><b>Unitário</b></td><td colspan="3"><b>Quantidade</b><td colspan="3"><b>R$ Total</b></td></tr>';
                }else{
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS EQUIPAMENTOS</td></tr>'.
                         '<tr><td colspan="6"><b>Produto</b></td><td colspan="6"><b>Quantidade</b></tr>';
                }
                if(is_object($produtos_reg)) $produtos_reg = Array($produtos_reg);
                foreach($produtos_reg as &$valor){
                    // Captura o Serviço de Instalaçao
                    $valor_reg  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Produto', 
                        Array(
                            'id'     =>  $valor->produto
                        ),
                        1
                    );
                    // Exibe
                    
                    if($tema==='Propostas'){
                        $html .= '<tr><td colspan="3">'.ucfirst($valor_reg->nome).'</td>';
                        $html .= '<td colspan="3">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor_reg->preco)*$pagar_lucro_porc).'</td>';
                        $html .= '<td colspan="3">'.$valor->prod_qnt.'</td>';
                        $total = $total+(Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor_reg->preco)*$valor->prod_qnt*$pagar_lucro_porc);
                        $html .= '<td colspan="3">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor_reg->preco)*$valor->prod_qnt*$pagar_lucro_porc).'</td></tr>';
                    }else{
                        $html .= '<tr><td colspan="6">'.ucfirst($valor_reg->nome).'</td>';
                        $html .= '<td colspan="6">'.$valor->prod_qnt.'</td></tr>';
                    }
                }
                if($tema==='Propostas'){
                    $total_geral = $total_geral+$total;
                    $html .= '<tr><td colspan="6">&nbsp;</td><td colspan="3"><b>TOTAL R$</b></td><td colspan="3"><b>'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total).'</b></td></tr>';
                }
                
            }
                
                
                
            // Captura o Serviço de Instalaçao
            $instalacao  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_ServicoInstalacao', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            $i = 0;
            // Pega os Valores do Serviço de Instalaçao
            if($instalacao!==false){
                $total = 0;
                $instalacao_html = '';
                /*$equipamentos_nome = Array();
                $equipamentos_qnt = Array();
                $equipamentos_precos = Array();
                if($tema=='Propostas'){
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS EQUIPAMENTOS</td></tr>'.
                    '<tr><td colspan="3"><b>Descrição</b></td><td colspan="3"><b>Unitário</b></td><td colspan="3"><b>Quantidade</b></td><td colspan="3"><b>R$ Total</b></td></tr>';

                }else{
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS EQUIPAMENTOS</td></tr>'.
                    '<tr><td colspan="6"><b>Descrição</b></td><td colspan="6"><b>Quantidade</b></td></tr>';

                }*/
                if(is_object($instalacao)) $instalacao = Array($instalacao);
                foreach($instalacao as &$valor){
                    ++$i;
                    $instalacao_html .= '<tr>';
                    // Pega dados
                    if($valor->distancia==1){
                        $valor->distancia = $valor->distancia.' metro';
                    }else{
                        $valor->distancia = $valor->distancia.' metros';
                    }
                    // Formata
                    if($valor->infra==0||$valor->infra=='0'){
                        $infra = 'Não existente';
                    }else{
                        $infra = 'Existente';
                    }
                    
                    if($valor->tipocondensadora==0||$valor->tipocondensadora=='0'){
                        $tipocondensadora = 'Vertical';
                    }else if($valor->tipocondensadora==1||$valor->tipocondensadora=='1'){
                        $tipocondensadora = 'Horizontal';
                    }else{
                        $tipocondensadora = 'Janela';
                    }
                    
                    if($valor->tipoevaporadora==0||$valor->tipoevaporadora=='0'){
                        $tipoevaporadora = 'Hi Wall';
                    }else if($valor->tipoevaporadora==1||$valor->tipoevaporadora=='1'){
                        $tipoevaporadora = 'Piso Teto';
                    }else{
                        $tipoevaporadora = 'Cassete';
                    }
                    
                    if($valor->tipodreno==0||$valor->tipodreno=='0'){
                        $tipodreno = 'Tubulado';
                    }else if($valor->tipodreno==1||$valor->tipodreno=='1'){
                        $tipodreno = 'Livre';
                    }else{
                        $tipodreno = 'Mangueira';
                    }
                    
                    // add Equipamento
                    /*if(isset($equipamentos_qnt[$valor->btu])){
                        ++$equipamentos_qnt[$valor->btu];
                    }else{
                        $equipamentos_qnt[$valor->btu] = 1;
                        $equipamentos_nome[$valor->btu] = ucfirst($valor->btu2);
                        $equipamentos_precos[$valor->btu] = false;
                    }*/
                    
                    if($tema=='Propostas'){

                        $instalacao_html .= '<td colspan="3">Instalação de '.ucfirst($valor->btu2).'</td>';
                        // Captura Preço do Equipamento
                        $instalacao_btu  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Btu', 
                            Array(
                                'id'           =>  $valor->btu
                            ),
                            1
                        );
                        if($instalacao_btu!==false){
                            $instalacao_btu_ar      = $instalacao_btu->valor_ar;
                            $instalacao_btu_linha   = $instalacao_btu->valor_linha;
                            $instalacao_btu_gas     = $instalacao_btu->valor_gas;
                        }else{
                            $instalacao_btu_ar      = 'R$ 0,00';
                            $instalacao_btu_linha   = 'R$ 0,00';
                            $instalacao_btu_gas     = 'R$ 0,00';
                        }
                        
                        // Acrescenta na outra tabela
                        //$equipamentos_precos[$valor->btu] = $instalacao_btu_ar;
                        
                        
                        // Captura Preço do SUPORTE
                        $instalacao_suporte  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Suporte', 
                            Array(
                                'id'            =>  $valor->suporte
                            ),
                            1
                        );
                        if($instalacao_suporte!==false){
                            $instalacao_suporte = $instalacao_suporte->valor;
                        }else{
                            $instalacao_suporte = 'R$ 0,00';
                        }
                        
                        // Adiciona Valor da Linha para acima de 5 metros
                        if($valor->distancia>5){
                            $instalacao_btu_linha = \Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_linha)*($valor->distancia-5));
                        }else{
                            $instalacao_btu_gas     = 'R$ 0,00';
                            $instalacao_btu_linha = 'R$ 0,00';
                        }

                        $instalacao_html .= '<td colspan="3">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($pagar_lucro_porc*(Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_ar)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_gas)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_suporte)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_linha))).'</td>';
                        $instalacao_html .= '<td colspan="3">1</td>';
                        $instalacao_html .= '<td colspan="3">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($pagar_lucro_porc*(Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_ar)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_gas)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_suporte)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_linha))).'</td>';
                        
                        $total = $total+$pagar_lucro_porc*(Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_ar)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_gas)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_suporte)
                                +Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu_linha));
                        
                    }else{
                        $html .= '<td colspan="6">Instalação de '.ucfirst($valor->btu2).'</td>';
                        $html .= '<td colspan="6">1</td>';
                    }
                   
                }
                // Instalaçao
                if($tema==='Propostas'){
                    $total_geral = $total_geral+$total;
                    $instalacao_html .= '<tr><td colspan="6">&nbsp;</td><td colspan="3"><b>TOTAL R$</b></td><td colspan="3"><b>'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total).'</b></td>';
                }

                /*// Equipamentos
                if(!empty($equipamentos_qnt)){
                    $total = 0;
                    foreach($equipamentos_qnt as $indice=>$valor){
                        if($tema==='Propostas'){
                            $html .= '<tr><td colspan="3">'.$equipamentos_nome[$indice].'</td><td colspan="3">'.$equipamentos_precos[$indice].'</td><td colspan="3">'.$valor.'</td><td colspan="3">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($valor*Framework\App\Sistema_Funcoes::Tranf_Real_Float($equipamentos_precos[$indice])).'</td></tr>';
                            
                            $total = $total+$valor*Framework\App\Sistema_Funcoes::Tranf_Real_Float($equipamentos_precos[$indice]);
                        }else{
                            $html .= '<tr><td colspan="6">'.$equipamentos_nome[$indice].'</td><td colspan="6">'.$valor.'</td></tr>';
                        }
                    }
                    // Instalaçao
                    if($tema==='Propostas'){
                        $total_geral = $total_geral+$total;
                        $html .= '<tr><td colspan="6">&nbsp;</td><td colspan="3"><b>TOTAL R$</b></td><td colspan="3"><b>'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total).'</b></td>';
                    }
                }*/
                
                
                // INstalacao
                if($tema==='Propostas'){
                    
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DA INSTALAÇÃO</td></tr>'.
                         '<tr><td colspan="3"><b>Descrição</b></td><td colspan="3"><b>Unitário</b></td><td colspan="3"><b>Quantidade</b></td><td colspan="3"><b>R$ Total</b></td></tr>'.$instalacao_html;
                }else{
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DA INSTALAÇÃO</td></tr>'.
                         '<tr><td colspan="6"><b>Descrição</b></td><td colspan="6"><b>Quantidade</b></td></tr>'.$instalacao_html;

                }
            }
        }else{
            // Coloca Campos do Tipo
            //if($identificador->referencia!='')  $html .= '<tr><td colspan="12"><b>Referência:</b> '.$identificador->referencia.'</td></tr>';
            //if($identificador->telefone!='')  $html .= '<tr><td colspan="12"><b>Telefone:</b> '.$identificador->telefone.'</td></tr>';
        
            $identificador->propostatipo=0;
            // Captura Tipo de Serviço
            $servicotipo  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_ServicoTipo', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Tipo de Serviço
            if($servicotipo!==false){
                $total = 0;
                if($tema==='Propostas'){
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS SERVIÇOS</td></tr>'.
                     '<tr><td colspan="8"><b>Descrição</b></td><td colspan="2"><b>Quantidade de Diárias</b></td><td colspan="2"><b>Custo da Diária</b></td></tr>';
                }else{
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS SERVIÇOS</td></tr>'.
                     '<tr><td colspan="6"><b>Descrição</b></td><td colspan="6"><b>Quantidade de Diárias</b></td></tr>';
                
                }
                if(is_object($servicotipo)) $servicotipo = Array($servicotipo);
                foreach($servicotipo as &$valor){
                    if($valor->servicotipo2===NULL) continue;
                    // Exibe
                    if($tema==='Propostas'){
                        $html .= '<tr><td colspan="8">Serviço de '.ucfirst($valor->servicotipo2).'</td>';
                        $html .= '<td colspan="2">'.$valor->diarias_qnt.'</td>';
                        $html .= '<td colspan="2">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->diarias_valor)*$pagar_lucro_porc).'</td></tr>';
                        
                        $total = $total+(Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->diarias_valor)*$valor->diarias_qnt*$pagar_lucro_porc);
                    }else{
                        $html .= '<tr><td colspan="6">Serviço de '.ucfirst($valor->servicotipo2).'</td>';
                        $html .= '<td colspan="6">'.$valor->diarias_qnt.'</td></tr>';
                    }
                }
                if($tema==='Propostas'){
                    $total_geral = $total_geral+$total;
                    $html .= '<tr><td colspan="6">&nbsp;</td><td colspan="3"><b>TOTAL R$</b></td><td colspan="3"><b>'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total).'</b></td>';
                }
                
                // Financeiro
                if($tema==='Propostas'){
                    $pagar = '';
                    if($identificador->condicao_pagar!='')  $pagar .= $identificador->condicao_pagar2;
                    if($identificador->forma_pagar!=''){
                        if($identificador->condicao_pagar!='')  $pagar .= ' em ';
                        $pagar .= $identificador->forma_pagar2;
                    }

                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">CONDIÇÕES COMERCIAIS DOS SERVIÇOS</td></tr>
                    <tr><td colspan="12"><b>Forma de Pagamento:</b> '.$pagar.'</td></tr>
                    <tr><td colspan="12"><b>Prazo da Instalação:</b>  CHEGADA DO EQUIPAMENTO COM CONFIRMAÇÃO DO CLIENTE</td></tr>
                    <tr><td colspan="12"><b>Prazo de Garantia:</b> 180 DIAS</td></tr>';
                }
            }

            // Captura o Serviço
            $servico  = $this->_Modelo->db->Sql_Select(
                'Comercio_Proposta_Servico', 
                Array(
                    'proposta'     =>  $identificador->id
                )
            );
            // Pega os Valores do Serviço
            if($servico!==false){
                $total = 0;
                if($tema==='Propostas'){
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS SERVIÇOS</td></tr>'.
                     '<tr><td colspan="8"><b>Descrição</b></td><td colspan="2"><b>Quantidade da mão de Obra</b></td><td colspan="2"><b>Custo do Serviço</b></td></tr>';
                }else{
                    $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">ORÇAMENTO DOS SERVIÇOS</td></tr>'.
                     '<tr><td colspan="6"><b>Descrição</b></td><td colspan="6"><b>Quantidade da mão de Obra</b></td></tr>';
                
                }
                if(is_object($servico)) $servico = Array($servico);
                foreach($servico as &$valor){
                    if($valor->servico2===NULL) continue;
                    // Captura Preço do SErviço
                    $servico2  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Servicos_Servico', 
                        Array(
                            'id'     =>  $valor->servico
                        ),
                        1
                    );
                    // Exibe
                    
                    if($tema==='Propostas'){
                        $html .= '<tr><td colspan="8">Serviço de '.ucfirst($valor->servico2).'</td>';
                        $html .= '<td colspan="2">'.$valor->qnt.'</td>';
                        $html .= '<td colspan="2">'.Framework\App\Sistema_Funcoes::Tranf_Float_Real(Framework\App\Sistema_Funcoes::Tranf_Real_Float($servico2->preco)*$pagar_lucro_porc).'</td></tr>';
                        
                        $total = $total+(Framework\App\Sistema_Funcoes::Tranf_Real_Float($servico2->preco)*$valor->qnt*$pagar_lucro_porc);
                        
                    }else{
                        $html .= '<tr><td colspan="6">Serviço de '.ucfirst($valor->servico2).'</td>';
                        $html .= '<td colspan="6">'.$valor->qnt.'</td></tr>';
                    }
                }
                if($tema==='Propostas'){
                    $total_geral = $total_geral+$total;
                    $html .= '<tr><td colspan="6">&nbsp;</td><td colspan="3"><b>TOTAL R$</b></td><td colspan="3"><b>'.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total).'</b></td>';
                }
        

            }

        }
        
        
        // Financeiro Equipamentos
        if($tema==='Propostas'){
            $pagar = '';
            if($identificador->condicao_pagar!='')  $pagar .= $identificador->condicao_pagar2;
            if($identificador->forma_pagar!=''){
                if($identificador->condicao_pagar!='')  $pagar .= ' em ';
                $pagar .= $identificador->forma_pagar2;
            }
            // Calcula Valor de Custo
            $t = \Framework\App\Sistema_Funcoes::Tranf_Real_Float($identificador->valor);
            $l = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_lucro);
            $d = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_desconto);
            $valor_inicial = \Framework\App\Sistema_Funcoes::Tranf_Float_Real($t/(1+$l-$d*$l));
            unset($t);unset($l);unset($d);
            
            
            //if($identificador->validade!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Validade da Proposta:</label> '.$identificador->validade.'</p>';
            //if($identificador->forma_pagar!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Forma de Pagamento:</label> '.$identificador->forma_pagar2.'</p>';
            //if($identificador->condicao_pagar!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Condição de Pagamento:</label> '.$identificador->condicao_pagar2.'</p>';
            //$html .= '<p><label style="width:250px; float:left; margin-right:5px;">Valor de Custo:</label> '.$valor_inicial.'</p>';
            
            //if($identificador->pagar_desconto!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Desconto da Proposta:</label> '.$identificador->pagar_desconto.'</p>';
            //if($identificador->valor!='')  $html .= '<p><label style="width:250px; float:left; margin-right:5px;">Valor Total da Proposta:</label> '.$identificador->valor.'</p>';

            $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">CONDIÇÕES COMERCIAIS DOS PRODUTOS</td></tr>
            <tr><td colspan="12"><b>Forma de Pagamento:</b> '.$pagar.'</td></tr>
            <tr><td colspan="12"><b>Validade:</b> '.$identificador->validade.'</td></tr>
            <tr><td colspan="12"><b>Valor Total:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($total_geral).'</td></tr>
            <tr><td colspan="12"><b>Desconto:</b> '.$identificador->pagar_desconto.'</td></tr>
            <tr><td colspan="12"><b>Valor a Pagar:</b> '.$identificador->valor.'</td></tr>';
        }
        
        // Obrigatoriedade dos Clientes
        $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">SERVIÇOS NÃO INCLUSOS</td></tr>
        <tr><td colspan="12"><b>. Fornecer  ponto de força elétrica independente e protegido com disjuntor que atenda as normas do fabricante.</b></td></tr>
        <tr><td colspan="12"><b>. Fornecer  ponto de dreno que atenda as normas do fabricante.</b></td></tr>
        <tr><td colspan="12"><b>. Autorização de qualquer tipo de órgão oficiais ou não (condomínio), para a instalação das unidades internas e externa.</b></td></tr>
        <tr><td colspan="12"><b>. Todo e qualquer serviço referente a construção civil, alvenaria, carpintaria, pintura, furação e recomposição de parede, forro, teto, lajes, e etc.</b></td></tr>
        <tr><td colspan="12"><b>. Suporte especiais, dutos, andaimes, material elétrico e de acabamento exigidos em função das características do local.</b></td></tr>
        <tr><td colspan="12"><b>. Desinstalação e retirada de qualquer equipamento existente que interfira na instalação do equipamento proposto.</b></td></tr>';

        // Observações
        $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr></table><table border="1"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">OBSERVAÇÔES</td></tr>
        <tr><td colspan="12"><b>Prazo da Instalação:</b>  10 DIAS</td></tr>
        <tr><td colspan="12"><b>Prazo de Garantia:</b> 180 DIAS</td></tr>';
        
        // Assinatura do Cliente
        $html .= '</table><table><tr><td colspan="12">&nbsp;</td></tr><tr><td colspan="12">&nbsp;</td></tr></table><table border="0"><tr><td colspan="12" bgcolor="#000000" style="color:#FFFFFF;text-align:center;">Aceite do Cliente</td></tr>
        <tr><td colspan="12">&nbsp;</td></tr><tr><td colspan="12">&nbsp;</td></tr><tr><td colspan="12">________________________________________________________________________________________________________________________</td></tr>';
        
        // Envia o conteúdo do arquivo
        echo utf8_decode($html).'</table>';
        //echo $html.'</table>';
        // Trava Sistema
        self::Tema_Travar();
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Add($tema='Propostas'){
        self::Endereco_Proposta(true,$tema);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Carrega Config
        $titulo1    = 'Adicionar '.$titulo;
        $titulo2    = 'Salvar '.$titulo;
        $formid     = 'form_Sistema_Admin_'.$titulo_unico;
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Propostas_Add2/'.$tema;
        $campos = Comercio_Proposta_DAO::Get_Colunas();
        self::Campos_Deletar($campos,$tema);
        
        if($tema==='Propostas'){
            $this->Proposta_Atualizar_Valor_Dinamico_Janela($formid);
            $posicao = 'left';
        }else{
            $posicao = 'All';
        }
        
        // Chama Formulario
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,false,$posicao);
        
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Add2($tema='Propostas'){
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = $titulo.' Adicionada com Sucesso';
        $dao        = 'Comercio_Proposta';
        $funcao     = false;
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = $titulo.' cadastrada com sucesso.';
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
        if($sucesso===true && $tema==='Propostas'){
            // Pega o ID
            $identificador  = $this->_Modelo->db->Sql_Select('Comercio_Proposta', Array(),1,'id DESC');
            $this->Proposta_Atualizar_Valor($identificador);
            
            // Se tiver essa opcao, cria a pasta automaticamente na biblioteca
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Biblioteca')===true && \Framework\App\Sistema_Funcoes::Perm_Modulos('biblioteca')===true && \Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Biblioteca_Automatico')===true){
                
                
            }
        }
        // Recarrega
        $this->Propostas($tema);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Edit($id,$tema='Propostas'){
        $id = (int) $id;
        self::Endereco_Proposta(true,$tema);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Carrega Config
        $titulo1    = 'Editar '.$titulo.' (#'.$id.')';
        $titulo2    = 'Alteração de '.$titulo;
        $formid     = 'form_Sistema_AdminC_'.$titulo_unico.'Edit'.$id;
        $formbt     = 'Alterar '.$titulo;
        $formlink   = 'comercio/Proposta/Propostas_Edit2/'.$id.'/'.$tema;
        $campos = Comercio_Proposta_DAO::Get_Colunas();
        self::Campos_Deletar($campos,$tema);
        
        // Valor da Proposta
        $editar = $this->_Modelo->db->Sql_Select('Comercio_Proposta', Array('id'=>$id),1);
        
        
        
        if($tema==='Propostas'){
            $this->Proposta_Atualizar_Valor_Dinamico_Janela($formid,true);
            $posicao = 'left';
        }else{
            $posicao = 'All';
        }
        
        // Gerar Formulario
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar,$posicao);  
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Edit2($id,$tema='Propostas'){
        $id = (int) $id;
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = $titulo.' Editada com Sucesso';
        $dao        = Array('Comercio_Proposta',$id);
        $funcao     = false;
        $sucesso1   = $titulo.' Alterada com Sucesso.';
        $sucesso2   = '#'.$id.' teve a alteração bem sucedida';
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
        if($sucesso===true && $tema==='Propostas'){
            // Pega o ID
            $identificador  = $this->_Modelo->db->Sql_Select('Comercio_Proposta', Array('id'=>$id),1);
            $this->Proposta_Atualizar_Valor($identificador);
        }
        // Recarrega
        $this->Propostas($tema);
    }
    /**
     * 
     * @param type $time
     * @return boolean
     */
    public function Proposta_Atualizar_Valor_Dinamico($time){
        if(!isset($_POST['status'])) return false;
        
        $html = '';
        
        // Pega Campos Gerais
        if(!isset($_POST['propostatipo'])){
            $propostatipo   = 0;
        }else{
            $propostatipo   = (int) $_POST['propostatipo'];
        }
        
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro') && isset($_POST['pagar_lucro'])){
            $lucro    = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float(\anti_injection($_POST['pagar_lucro']));
        }else{
            $lucro    = 0.0;
        }
        
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Desconto') && isset($_POST['pagar_desconto'])){
            $desconto    = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float(\anti_injection($_POST['pagar_desconto']));
        }else{
            $desconto    = 0.0;
        }
        
        // Zera Valor
        $valortotal = 0.0;

        // Pega o Tipo
        if($propostatipo==1){
            $propostatipo=1;
            
            // Pega os BTUS de instalacao
            $instalacao = \anti_injection($_POST['btu']);
            // Pega os Valores do Serviço de Instalaçao
            if(!empty($instalacao)){
                foreach($instalacao as &$valor){
                    $valor = (int) $valor;
                    if($valor===0 || $valor===NULL) continue;
                    // Captura Preço do Gas
                    $instalacao_btu  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Servicos_Btu', 
                        Array(
                            'id'           =>  $valor
                        ),
                        1
                    );
                    if($instalacao_btu===false) continue;
                    $distancia  = (int) $_POST['distancia_'.$valor];
                    $suporte    = (int) $_POST['suporte_'.$valor];
                    // Adiciona Valor da Linha para acima de 5 metros
                    if($distancia>5){
                        $valortotal =   $valortotal + (Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_linha)*($distancia-5))
                                        + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_gas);
                    }
                    // Add valor do Ar e Gas
                    $valortotal =   $valortotal
                                    //+ \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_gas)
                                    + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_ar);
                    
                    // Captura Preço do SUPORTE
                    $instalacao_suporte  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Servicos_Suporte', 
                        Array(
                            'id'            =>  $suporte
                        ),
                        1
                    );
                    if($instalacao_suporte===false) continue;
                    $valortotal = $valortotal + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_suporte->valor);
                }
            }
            
            // Produtos
            $produto = \anti_injection($_POST['produto']);
            // Pega os Valores do Serviço de Instalaçao
            if(!empty($produto)){
                foreach($produto as &$valor){
                    $valor = (int) $valor;
                    if($valor===0 || $valor===NULL) continue;
                    // Captura Preço do SUPORTE
                    $produto_registro  = $this->_Modelo->db->Sql_Select(
                        'Comercio_Produto', 
                        Array(
                            'id'            =>  $valor
                        ),
                        1
                    );
                    if(is_object($produto_registro) && $produto_registro->preco!=NULL){
                        $prod_qnt    = (int) $_POST['prod_qnt_'.$valor];
                        //$prod_preco  = \anti_injection($_POST['prod_preco_'.$valor]);
                        // Add valor do Produto
                        $valortotal =   $valortotal
                                        + (Framework\App\Sistema_Funcoes::Tranf_Real_Float($produto_registro->preco)*$prod_qnt);
                    }
                }
            }
            
            
        }else{
            $propostatipo=0;
            // Captura Tipo de Serviço
            // Nao Altera Mais o Valor
            /*if(isset($_POST['servicotipo'])){
                $servicotipo = \anti_injection($_POST['servicotipo']);
                // Pega os Valores do Tipo de Serviço
                if(!empty($servicotipo)){
                    foreach($servicotipo as &$valor){
                        $valor = (int) $valor;
                        if($valor===0 || $valor===NULL) continue;
                        $diarias_qnt = (int) $_POST['diarias_qnt_'.$valor];
                        $diarias_valor = \anti_injection($_POST['diarias_valor_'.$valor]);
                        $valortotal = $valortotal + $diarias_qnt*Framework\App\Sistema_Funcoes::Tranf_Real_Float($diarias_valor);
                    }
                }
            }*/

            // Captura o Serviço
            if(isset($_POST['servico'])){
                $servico = \anti_injection($_POST['servico']);
                // Pega os Valores do Serviço
                if(!empty($servico)){
                    foreach($servico as &$valor){
                        $valor = (int) $valor;
                        if($valor===0 || $valor===NULL) continue;
                        // Captura Preço do SErviço
                        $servico2  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Servico', 
                            Array(
                                'id'     =>  $valor
                            ),
                            1
                        );
                        $qnt = (int) $_POST['qnt_'.$valor];

                        $valortotal = $valortotal + $qnt*Framework\App\Sistema_Funcoes::Tranf_Real_Float($servico2->preco);
                    }
                }
            }

        }
        
        
        // SE tiver Mao de Obra Calcula e Soma ao Custo
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_MaodeObra')){
            $valor_maodeobra_total = 0;
            if(isset($_POST['grupo'])){
                $maodeobra = \anti_injection($_POST['grupo']);
                // Pega os Valores do Tipo de Serviço
                if(!empty($maodeobra)){
                    foreach($maodeobra as &$valor){
                        $valor = (int) $valor;
                        if($valor===0 || $valor===NULL) continue;
                        
                        
                        $maodeobra_qnt          = (int) $_POST['maodeobra_qnt_'.$valor];
                        $maodeobra_dias         = (int) $_POST['maodeobra_dias_'.$valor];
                        $maodeobra_diaria       = \anti_injection($_POST['maodeobra_diaria_'.$valor]);
                        $maodeobra_depreciacao  = \anti_injection($_POST['maodeobra_depreciacao_'.$valor]);
                        $maodeobra_passagem     = \anti_injection($_POST['maodeobra_passagem_'.$valor]);
                        $maodeobra_alimentacao  = \anti_injection($_POST['maodeobra_alimentacao_'.$valor]);
                        
                        $valor_maodeobra = $maodeobra_qnt*$maodeobra_dias*
                            (
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($maodeobra_diaria)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($maodeobra_depreciacao)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($maodeobra_passagem)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($maodeobra_alimentacao)
                            );
                        
                        $valor_maodeobra_total = $valor_maodeobra_total + $valor_maodeobra;
                    }
                }
            }
            $html .= '<b>Custo da Mão de Obra:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($valor_maodeobra_total).'<br>';
            $valortotal = $valortotal+$valor_maodeobra_total;
        }
        
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorExtra')!==false){
            $valortotal = $valortotal+Framework\App\Sistema_Funcoes::Tranf_Real_Float(\anti_injection($_POST['valor_extra']));
            $html .= '<b>Custo Extra:</b> '.(\anti_injection($_POST['valor_extra'])?\anti_injection($_POST['valor_extra']):'R$ 0,00').'<br>';
        }
        
        // Calcula Valor de Custo da Proposta
        $html .= '<b>Valor Total de Custo:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($valortotal).'<br><br>';
        
        
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorFinal')){
            // Calcula Lucro
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
                $html .= '<b>Lucro:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($lucro*$valortotal).'<br>';
                $valortotal = $valortotal+($lucro*$valortotal);
            }
            // Calcula Desconto
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Desconto')){
                $html .= '<b>Desconto:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($desconto*$valortotal).'<br>';
                $valortotal = $valortotal-($desconto*$valortotal);
            }
            // Converte Valor Total para Real e Imprime   
        
            $html .= '<b>Valor Total:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($valortotal);  
        }else{
            $valor_total_form = \anti_injection($_POST['valor_fixo']);
            $valor_total_form_lucro = \Framework\App\Sistema_Funcoes::Tranf_Float_Real((Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor_total_form)-$valortotal));
            $html .= '<b>Lucro:</b> '.
                    $valor_total_form_lucro
                    .'<br>';
            $valortotal = \Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor_total_form);
            $html .= '<b>Valor Total:</b> '.  $valor_total_form.'<br>';
        }
        
        // Se tiver IMPOSTO Add
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Imposto')!==false){
            $imposto = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float(\anti_injection($_POST['imposto']));
            $html .= '<b>Imposto:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($imposto*$valortotal).'<br>';
            $valortotal_semimposto = $valortotal-($imposto*$valortotal);
        }   
        
        // Se tiver Comissao Add
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Comissao')!==false){
            $comissao = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float(\anti_injection($_POST['comissao']));
            $html .= '<b>Comissão:</b> '.Framework\App\Sistema_Funcoes::Tranf_Float_Real($comissao*$valortotal_semimposto).'<br>';
            //$valortotal = $valortotal+($comissao*$valortotal);
        }        
        
        
        // Json
        $conteudo = array(
            'location'  =>  '#valortemporario'.$time,
            'js'        =>  '',
            'html'      =>  $html
        );
        $this->_Visual->Json_IncluiTipo('Conteudo',$conteudo);
        $this->_Visual->Json_Info_Update('Historico', false);  
        return true;
    }
    /**
     * 
     * @param type $form_id
     * @param type $valor
     */
    public function Proposta_Atualizar_Valor_Dinamico_Janela($form_id,$recalcular=false){
        $time = round(TEMPO_COMECO);
        if($recalcular===false){
            $valor ='R$ 0,00';
        }else{
            $valor ='Calculando';
            $this->_Visual->Javascript_Executar('params'.$time.'=$(\'#'.$form_id.'\').serialize();'
                            . 'Sierra.Modelo_Ajax_Chamar(\'comercio/Proposta/Proposta_Atualizar_Valor_Dinamico/'.$time.'\',params'.$time.',\'POST\',true,false,false);');
        }
        // Janela De Valor Temporario
        $this->_Visual->Javascript_Executar('function Valor_Dinamico_Rodar() {var params'.$time.' = $(\'#'.$form_id.'\').serialize();'
            . 'var intervalo'.$time.' = window.setInterval(function () {console.log(\'SierraTecnologia\',params'.$time.');'
                . 'if($(\'#'.$form_id.'\').length){'
                    . 'if(params'.$time.'!==$(\'#'.$form_id.'\').serialize()){'
                        . 'params'.$time.'=$(\'#'.$form_id.'\').serialize();'
                        . 'Sierra.Modelo_Ajax_Chamar(\'comercio/Proposta/Proposta_Atualizar_Valor_Dinamico/'.$time.'\',params'.$time.',\'POST\',true,false,false);'
                    . '}'
                . '}else{'
                    . 'clearInterval(intervalo'.$time.');'
                . '}'
            . '}, 1000);} Valor_Dinamico_Rodar();');
        $this->_Visual->Blocar(/*'<script LANGUAGE="JavaScript" TYPE="text/javascript">'
            . 
            . '</script>'
            . */'<span id="valortemporario'.$time.'"><b>Valor Total:</b> '.$valor.'</span>'
            . '<br>');
        $this->_Visual->Bloco_Menor_CriaJanela('Informações Temporárias');
    }
    /**
     * Pega uma Proposta/Os e Calcula o seu valor
     * @param type $identificador
     * @return boolean
     */
    private function Proposta_Atualizar_Valor(&$identificador){
        if(!is_object($identificador)){
            return false;
        }
        
        // Zera Valor
        $valortotal = 0.0;        
        
        if(!\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorFinal')){
            
            // Pega o Tipo
            if($identificador->propostatipo==1){
                $identificador->propostatipo=1;

                // Captura o Serviço de Instalaçao
                $instalacao  = $this->_Modelo->db->Sql_Select(
                    'Comercio_Proposta_ServicoInstalacao', 
                    Array(
                        'proposta'     =>  $identificador->id
                    )
                );
                // Pega os Valores do Serviço de Instalaçao
                if($instalacao!==false){
                    if(is_object($instalacao)) $instalacao = Array($instalacao);
                    foreach($instalacao as &$valor){
                        // Captura Preço do Gas
                        $instalacao_btu  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Btu', 
                            Array(
                                'id'           =>  $valor->btu
                            ),
                            1
                        );
                        // Adiciona Valor da Linha para acima de 5 metros
                        if($valor->distancia>5){
                            $valortotal =   $valortotal + (Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_linha)*($valor->distancia-5))
                                            + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_gas);
                        }
                        // Add valor do Ar e Gas
                        $valortotal =   $valortotal
                                        //+ \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_gas)
                                        + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_btu->valor_ar);

                        // Captura Preço do SUPORTE
                        $instalacao_suporte  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Suporte', 
                            Array(
                                'id'            =>  $valor->suporte
                            ),
                            1
                        );
                        $valortotal = $valortotal + \Framework\App\Sistema_Funcoes::Tranf_Real_Float($instalacao_suporte->valor);
                    }
                }

                // Captura o Serviço de Instalaçao
                $produto  = $this->_Modelo->db->Sql_Select(
                    'Comercio_Proposta_Produto', 
                    Array(
                        'proposta'     =>  $identificador->id
                    )
                );
                // Pega os Valores do Serviço de Instalaçao
                if($produto!==false){
                    if(is_object($produto)) $produto = Array($produto);
                    foreach($produto as &$valor){
                        // Captura Preço do SUPORTE
                        $produto_registro  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Produto', 
                            Array(
                                'id'            =>  $valor->produto
                            ),
                            1
                        );
                        if(is_object($produto_registro) && $produto_registro->preco!=NULL){
                            // Add valor do Produto
                            $valortotal =   $valortotal
                                            + (Framework\App\Sistema_Funcoes::Tranf_Real_Float($produto_registro->preco)*$valor->prod_qnt);
                        }
                    }
                }


            }else{
                $identificador->propostatipo=0;
                // Captura Tipo de Serviço
                /* NAO TEM MAIS MUDANCA DE VALOR COM SERVICOTIPO
                 * $servicotipo  = $this->_Modelo->db->Sql_Select(
                    'Comercio_Proposta_ServicoTipo', 
                    Array(
                        'proposta'     =>  $identificador->id
                    )
                );
                // Pega os Valores do Tipo de Serviço
                if($servicotipo!==false){
                    if(is_object($servicotipo)) $servicotipo = Array($servicotipo);
                    foreach($servicotipo as &$valor){
                        $valortotal = $valortotal + $valor->diarias_qnt*Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->diarias_valor);
                    }
                }*/

                // Captura o Serviço
                $servico  = $this->_Modelo->db->Sql_Select(
                    'Comercio_Proposta_Servico', 
                    Array(
                        'proposta'     =>  $identificador->id
                    )
                );
                // Pega os Valores do Serviço
                if($servico!==false){
                    if(is_object($servico)) $servico = Array($servico);
                    foreach($servico as &$valor){
                        // Captura Preço do SErviço
                        $servico2  = $this->_Modelo->db->Sql_Select(
                            'Comercio_Servicos_Servico', 
                            Array(
                                'id'     =>  $valor->servico
                            ),
                            1
                        );
                        if($servico2===false)                        continue;
                        $valortotal = $valortotal + $valor->qnt*Framework\App\Sistema_Funcoes::Tranf_Real_Float($servico2->preco);
                    }
                }

            }

            // SE tiver Mao de Obra Calcula e Soma ao Custo
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_MaodeObra')){
                $valor_maodeobra_total = 0;
                // Captura o Serviço
                $maodeobra  = $this->_Modelo->db->Sql_Select(
                    'Comercio_Proposta_MaodeObra', 
                    Array(
                        'proposta'     =>  $identificador->id
                    )
                );
                // Pega os Valores do Serviço
                if($maodeobra!==false){
                    if(is_object($maodeobra)) $maodeobra = Array($maodeobra);
                    foreach($maodeobra as &$valor){
                        $valor_maodeobra = $valor->maodeobra_qnt*$valor->maodeobra_dias*
                            (
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->maodeobra_diaria)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->maodeobra_depreciacao)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->maodeobra_passagem)+
                                \Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->maodeobra_alimentacao)
                            );
                        $valor_maodeobra_total = $valor_maodeobra_total + $valor_maodeobra;
                    }
                }
                $valortotal = $valortotal+$valor_maodeobra_total;
            }
        
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_ValorExtra')!==false){
                $valortotal = $valortotal+Framework\App\Sistema_Funcoes::Tranf_Real_Float($valor->valor_extra);
            }
            
            // Calcula Lucro
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Lucro')){
                $lucro    = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_lucro);
                $valortotal = $valortotal+($lucro*$valortotal);
            }
            // Calcula Desconto
            if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Desconto')){
                $desconto    = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->pagar_desconto);
                $valortotal = $valortotal-($desconto*$valortotal);
            }
            // Converte Valor Total para Real e Imprime     
        }else{
            $valortotal = \Framework\App\Sistema_Funcoes::Tranf_Real_Float($identificador->valor_fixo);
        }
        
        /*// Se tiver Comissao Add
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Proposta_Comissao')!==false){
            $comissao = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->comissao);
            $valortotal = $valortotal+($comissao*$valortotal);
        }   
        
        // Se tiver Imposto Add
        if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Imposto')!==false){
            $imposto = \Framework\App\Sistema_Funcoes::Tranf_Porc_Float($identificador->imposto);
            $valortotal = $valortotal+($imposto*$valortotal);
        }*/
        
        // Atualiza Valor Total
        $identificador->valor = \Framework\App\Sistema_Funcoes::Tranf_Float_Real($valortotal);
        $this->_Modelo->db->Sql_Update($identificador);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Del($id,$tema='Propostas'){
        global $language;
        
    	$id = (int) $id;
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Puxa fornecedor e deleta
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta', Array('id'=>$id));
        $mov      = $this->_Modelo->db->Sql_Select('Financeiro_Pagamento_Interno', Array('motivo' => 'comercio_Proposta', 'motivoid'=>$id));
        $sucesso1 =  $this->_Modelo->db->Sql_Delete($proposta);
        $sucesso2 =  $this->_Modelo->db->Sql_Delete($mov);
        // Mensagem
    	if($sucesso1===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => $titulo.' Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Propostas($tema);
        
        $this->_Visual->Json_Info_Update('Titulo', $titulo.' deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @param type $id
     * @param type $tema
     * @throws Exception
     */
    public function StatusPropostas($id=false,$tema='Propostas'){
        
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $id = (int) $id;
        $resultado = $this->_Modelo->db->Sql_Select('Comercio_Proposta', Array('id'=>$id),1);
        
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        
        if($tema=='Propostas'){
            if($resultado->status=='1'){
                $resultado->status='4'; // De Aprovada para Recusada
            }else if($resultado->status=='4'){ // De Recusada para Pendente
                $resultado->status='0';
            }else{
                $resultado->status='1';// De Pendente para Aprovada
            }
        }else{
            // Para ORdem de Serviço
            if($resultado->status=='1'){ // de aprovada para Aprovada em Execução
                $resultado->status='2';
                // Passa tudo pra Contas a Receber
                Financeiro_PagamentoControle::Condicao_GerarPagamento(
                    $resultado->condicao_pagar,   // Condição de Pagamento
                    'comercio_Proposta',          // Motivo
                    $id,                          // MotivoID
                    'Usuario',                    // Entrada_Motivo
                    $resultado->cliente,          // Entrada_MotivoID
                    'Servidor',                   // Saida_Motivo
                    SRV_NAME_SQL,                 // Saida_MotivoID
                    $resultado->valor,            // Valor
                    APP_DATA_BR                   // Data Inicial
                );
            }else if($resultado->status=='2'){ // de Aprovada em Execução para Finalizada
                
                if(\Framework\App\Acl::Sistema_Modulos_Configs_Funcional('comercio_Propostas_Biblioteca')===true && \Framework\App\Sistema_Funcoes::Perm_Modulos('biblioteca')===true){
                    $mensagens = array(
                        "tipo"              => 'erro',
                        "mgs_principal"     => 'Erro',
                        "mgs_secundaria"    => 'Essa OS não pode ser finalizada.'
                    );
                    $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);

                    $this->_Visual->Json_Info_Update('Historico', false);
                    return true;
                    
                }
                
                // Se
                $resultado->status='3';
                // Inserir Pagamento de Funcionários
                /*$funcionarios = $this->_Modelo->db->Sql_Select('Comercio_Proposta_Funcionario', Array('proposta'=>$id));
                if(is_object($funcionarios)) $funcionarios = Array($funcionarios);
                if($funcionarios!==false){
                    foreach($funcionarios as &$valor){
                        $funcionarios_registro = $this->_Modelo->db->Sql_Select('Usuario', Array('id'=>$valor->funcionario),1);
                        $valor_a_pagar = $valor->dias * (\Framework\App\Sistema_Funcoes::Tranf_Real_Float($funcionarios_registro->salariobase));
                        // Caso não consiga Criar Financeiro da Erro
                        if(!Financeiro_Controle::FinanceiroInt(
                            'comercio_Proposta',
                            $id,
                            'Servidor',          // Entrada_Motivo
                            SRV_NAME_SQL,        // Entrada_MotivoID
                            'Usuario',           // Saida_Motivo
                            $valor->funcionario, // Saida_MotivoID
                            $valor_a_pagar,APP_DATA_BR,0
                        )){
                            $mensagens = array(
                                "tipo"              => 'erro',
                                "mgs_principal"     => 'Erro',
                                "mgs_secundaria"    => 'Condição de Pagamento Inválido na Proposta.'
                            );
                            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                            $this->_Visual->Json_Info_Update('Titulo','Erro'); 
                            $this->_Visual->Json_Info_Update('Historico', false);
                            return false;
                        }
                    }
                }*/
                
            }else if($resultado->status=='3'){ // de Finalizada em Execução para Aprovada
                $resultado->status='1';
            }
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
                'html' =>  self::label($resultado,$tema)
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
     * @param type $tema
     * @param type $link
     * @return string
     */
    public static function label($objeto,$tema='Propostas',$link=true){
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
        if(/*$status!='3' && */$objeto->condicao_pagar!=NULL && $link===true && \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('comercio/Proposta/StatusPropostas')!==false){
            $html = '<a href="'.URL_PATH.'comercio/Proposta/StatusPropostas/'.$id.'/'.$tema.'" border="1" class="lajax explicar-titulo" title="'.$nometipo.'" acao="" confirma="Deseja Realmente alterar o Status?">'.$html.'</a>';
        }
        return $html;
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario($proposta_id = false,$tema='Propostas',$export=false){
        if($proposta_id==='false') $proposta_id = false;
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        if($proposta_id===false){
            $where = Array();
        }else{
            $where = Array('proposta'=>$proposta_id);
        }
        self::Endereco_Proposta_Comentario(false,$tema,$proposta_id);
        $i = 0;
        // BOTAO IMPRIMIR / ADD
        if($proposta_id===false){
            $proposta_id_ir = 'false';
        }
        else{
            $proposta_id_ir = $proposta_id;
        }
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Comentário à '.$titulo,
                'comercio/Proposta/Propostas_Comentario_Add/'.$proposta_id_ir.'/'.$tema,
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Proposta/Propostas_Comentario/'.$proposta_id_ir.'/'.$tema,
            )
        )));
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Proposta_Comentario',$where);
        if($comentario!==false && !empty($comentario)){
            if(is_object($comentario)) $comentario = Array(0=>$comentario);
            reset($comentario);
            foreach ($comentario as $indice=>&$valor) {
                $tabela['#Id'][$i]          =   '#'.$valor->id;
                $tabela['Comentário'][$i]   =   nl2br($valor->comentario);
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário'        ,'comercio/Proposta/Propostas_Comentario_Edit/'.$proposta_id.'/'.$valor->id.'/'.$tema    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário'       ,'comercio/Proposta/Propostas_Comentario_Del/'.$proposta_id.'/'.$valor->id.'/'.$tema     ,'Deseja realmente deletar esse Comentário dessa '.$titulo.' ?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Comercio ('.$titulo.') - Comentários');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela,'', true, false, Array(Array(0,'desc')));
            }
            
            unset($tabela);
        }else{            
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Comentário sobre a '.$titulo.'</font></b></center>');
        }
        $titulo = 'Comentários da '.$titulo.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Comentários da '.$titulo.'');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario_Add($proposta_id = false,$tema='Propostas'){
        // Proteção E chama Endereço
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$proposta_id), 1);
        if($proposta===false) throw new \Exception('Proposta não existe:'.$proposta_id,404);
        self::Endereco_Proposta_Comentario(true,$tema,$proposta);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Começo
        $proposta_id = (int) $proposta_id;
        // Carrega Config
        $titulo1    = 'Adicionar Comentário da '.$titulo;
        $titulo2    = 'Salvar Comentário da '.$titulo;
        $formid     = 'form_Sistema_Admin_Propostas_Comentario';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Propostas_Comentario_Add2/'.$proposta_id.'/'.$tema;
        $campos = Comercio_Proposta_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'proposta');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario_Add2($proposta_id = false,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informada',404);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = 'Comentário sobre a Proposta Adicionado com Sucesso';
        $dao        = 'Comercio_Proposta_Comentario';
        $funcao     = '$this->Propostas_Comentario('.$proposta_id.',\''.$tema.'\');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Comentário de Proposta cadastrado com sucesso.';
        $alterar    = Array('proposta'=>$proposta_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario_Edit($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        // Proteção E chama Endereço
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$proposta_id), 1);
        if($proposta===false) throw new \Exception('Proposta não existe:'.$proposta_id,404);
        self::Endereco_Proposta_Comentario(true,$tema,$proposta);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Começo
        // Carrega Config
        $titulo1    = 'Editar Comentário sobre a '.$titulo.' (#'.$id.')';
        $titulo2    = 'Alteração de Comentário sobre a '.$titulo;
        $formid     = 'form_Sistema_AdminC_PropostaEdit';
        $formbt     = 'Alterar Comentário da '.$titulo;
        $formlink   = 'comercio/Proposta/Propostas_Comentario_Edit2/'.$proposta_id.'/'.$id.'/'.$tema;
        $editar     = Array('Comercio_Proposta_Comentario',$id);
        $campos = Comercio_Proposta_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'proposta');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario_Edit2($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = 'Comentário de '.$titulo.' Editada com Sucesso';
        $dao        = Array('Comercio_Proposta_Comentario',$id);
        $funcao     = '$this->Propostas_Comentario('.$proposta_id.',\''.$tema.'\');';
        $sucesso1   = 'Comentário de '.$titulo.' Alterada com Sucesso.';
        $sucesso2   = '#'.$proposta_id.' teve a alteração bem sucedida';
        $alterar    = Array('proposta'=>$proposta_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Comentario_Del($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informada',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Proposta_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Comentário sobre a '.$titulo.' Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Propostas_Comentario($proposta_id,$tema);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Comentário da '.$titulo.' deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub($proposta_id = false,$tema='Propostas',$export=false){
        if($proposta_id==='false') $proposta_id = false;
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        if($proposta_id===false){
            $where = Array();
        }else{
            $where = Array('proposta'=>$proposta_id);
        }
        self::Endereco_Proposta_Sub(false,$tema,$proposta_id);
        $i = 0;
        // BOTAO IMPRIMIR / ADD
        if($proposta_id===false){
            $proposta_id_ir = 'false';
        }
        else{
            $proposta_id_ir = $proposta_id;
        }
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Sub proposta à '.$titulo,
                'comercio/Proposta/Propostas_Sub_Add/'.$proposta_id_ir.'/'.$tema,
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Proposta/Propostas_Sub/'.$proposta_id_ir.'/'.$tema,
            )
        )));
        $sub = $this->_Modelo->db->Sql_Select('Comercio_Proposta_Sub',$where);
        if($sub!==false && !empty($sub)){
            if(is_object($sub)) $sub = Array(0=>$sub);
            reset($sub);
            foreach ($sub as $indice=>&$valor) {
                $tabela['#Id'][$i]          =   '#'.$valor->id;
                $tabela['Obs'][$i]          =   nl2br($valor->obs);
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Sub proposta'        ,'comercio/Proposta/Propostas_Sub_Edit/'.$proposta_id.'/'.$valor->id.'/'.$tema    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Sub proposta'       ,'comercio/Proposta/Propostas_Sub_Del/'.$proposta_id.'/'.$valor->id.'/'.$tema     ,'Deseja realmente deletar esse Sub proposta dessa '.$titulo.' ?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Comercio ('.$titulo.') - Sub propostas');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela,'', true, false, Array(Array(0,'desc')));
            }
            
            unset($tabela);
        }else{            
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Sub proposta sobre a '.$titulo.'</font></b></center>');
        }
        $titulo = 'Sub propostas da '.$titulo.' ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Sub propostas da '.$titulo.'');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub_Add($proposta_id = false,$tema='Propostas'){
        // Proteção E chama Endereço
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$proposta_id), 1);
        if($proposta===false) throw new \Exception('Proposta não existe:'.$proposta_id,404);
        self::Endereco_Proposta_Sub(true,$tema,$proposta);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Começo
        $proposta_id = (int) $proposta_id;
        // Carrega Config
        $titulo1    = 'Adicionar Sub proposta da '.$titulo;
        $titulo2    = 'Salvar Sub proposta da '.$titulo;
        $formid     = 'form_Sistema_Admin_Propostas_Sub';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Propostas_Sub_Add2/'.$proposta_id.'/'.$tema;
        $campos = Comercio_Proposta_Sub_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'proposta');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub_Add2($proposta_id = false,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informada',404);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = 'Sub proposta sobre a Proposta Adicionado com Sucesso';
        $dao        = 'Comercio_Proposta_Sub';
        $funcao     = '$this->Propostas_Sub('.$proposta_id.',\''.$tema.'\');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Sub proposta de Proposta cadastrado com sucesso.';
        $alterar    = Array('proposta'=>$proposta_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub_Edit($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        if($id         == 0   ) throw new \Exception('Sub proposta não informado',404);
        // Proteção E chama Endereço
        $proposta = $this->_Modelo->db->Sql_Select('Comercio_Proposta',Array('id'=>$proposta_id), 1);
        if($proposta===false) throw new \Exception('Proposta não existe:'.$proposta_id,404);
        self::Endereco_Proposta_Sub(true,$tema,$proposta);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Começo
        // Carrega Config
        $titulo1    = 'Editar Sub proposta sobre a '.$titulo.' (#'.$id.')';
        $titulo2    = 'Alteração de Sub proposta sobre a '.$titulo;
        $formid     = 'form_Sistema_AdminC_PropostaEdit';
        $formbt     = 'Alterar Sub proposta da '.$titulo;
        $formlink   = 'comercio/Proposta/Propostas_Sub_Edit2/'.$proposta_id.'/'.$id.'/'.$tema;
        $editar     = Array('Comercio_Proposta_Sub',$id);
        $campos = Comercio_Proposta_Sub_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'proposta');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub_Edit2($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informado',404);
        if($id         == 0   ) throw new \Exception('Sub proposta não informado',404);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        $titulo     = 'Sub proposta de '.$titulo.' Editada com Sucesso';
        $dao        = Array('Comercio_Proposta_Sub',$id);
        $funcao     = '$this->Propostas_Sub('.$proposta_id.',\''.$tema.'\');';
        $sucesso1   = 'Sub proposta de '.$titulo.' Alterada com Sucesso.';
        $sucesso2   = '#'.$proposta_id.' teve a alteração bem sucedida';
        $alterar    = Array('proposta'=>$proposta_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Propostas_Sub_Del($proposta_id = false,$id = 0,$tema='Propostas'){
        if($proposta_id===false) throw new \Exception('Proposta não informada',404);
        if($id         == 0   ) throw new \Exception('Sub proposta não informado',404);
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Proposta_Sub', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Nomes
        if($tema!='Propostas'){
            $titulo             = CFG_TXT_COMERCIO_OS;
            $titulo_plural      = CFG_TXT_COMERCIO_OS_PLURAL;
            $titulo_unico       = 'ordemdeservico';
        }else{
            $titulo             = 'Proposta';
            $titulo_plural      = 'Propostas';
            $titulo_unico       = 'propostas';
        }
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Sub proposta sobre a '.$titulo.' Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Propostas_Sub($proposta_id,$tema);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Sub proposta da '.$titulo.' deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * Veio de OSC.php
     */
    public function Checklists($export=false){
        self::Endereco_CheckList(false);
        //
        $i = 0;
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Checklist',
                'comercio/Proposta/Checklists_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Proposta/Checklists',
            )
        )));
        $checklist = $this->_Modelo->db->Sql_Select('Comercio_Checklist');
        if($checklist!==false && !empty($checklist)){
            if(is_object($checklist)) $checklist = Array(0=>$checklist);
            reset($checklist);
            foreach ($checklist as $indice=>&$valor) {
                $tabela['Tipo de Equipamento'][$i]      =   $valor->categoria2;
                $tabela[CFG_TXT_EQUIPAMENTOS_NOME][$i]  =   $valor->nome;
                $tabela['Validade'][$i]                 =   $valor->validade;
                $tabela['Observações'][$i]              =   $valor->obs;
                $tabela['Status'][$i]     =  '<span class="statusChecklists'.$valor->id.'">'.self::labelChecklists($valor).'</span>';
                $tabela['Funções'][$i]   =  $this->_Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar CheckList'                          ,'comercio/Proposta/Checklists_Edit/'.$valor->id.'/'    ,'')).
                                            $this->_Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar CheckList'                         ,'comercio/Proposta/Checklists_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse checklist?'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Checklists');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }else{           
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Checklist</font></b></center>');
        }
        $titulo = 'Listagem de Checklists ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Checklists');
    }
    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function StatusChecklists($id=false){
        
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $id = (int) $id;
        $resultado = $this->_Modelo->db->Sql_Select('Comercio_Checklist', 'id = '.$id,1);
        
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        
        if($resultado->status=='0'){ // de aprovada para Aprovada em Execução
            $resultado->status='1';
        }else if($resultado->status=='1'){ // de Aprovada em Execução para Finalizada
            $resultado->status='2';

        }else if($resultado->status=='2'){ // de Finalizada em Execução para Aprovada em Execução
            $resultado->status='3';
        }else{
            $resultado->status='0';
        }
        $sucesso = $this->_Modelo->db->Sql_Update($resultado);
        if($sucesso){
            $conteudo = array(
                'location' => '.statusChecklists'.$resultado->id,
                'js' => '',
                'html' =>  self::labelChecklists($resultado)
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
    public static function labelChecklists($objeto,$link=true){
        $status = $objeto->status;
        $id = $objeto->id;
        if($status=='0'){
            $tipo = 'danger';
            $nometipo = 'Descartado';
        }
        else if($status=='1'){
            $tipo = 'success';
            $nometipo = 'Liberado';
        }
        else if($status=='2'){
            $tipo = 'info';
            $nometipo = 'Em Uso';
        }
        else {
            $tipo = 'important';
            $nometipo = 'Em Quarentena';
        }
        $html = '<span class="badge badge-'.$tipo.'">'.$nometipo.'</span>';
        if($link===true && \Framework\App\Registro::getInstacia()->_Acl->Get_Permissao_Url('comercio/Proposta/StatusChecklists')!==false){
            $html = '<a href="'.URL_PATH.'comercio/Proposta/StatusChecklists/'.$id.'" border="1" class="lajax explicar-titulo" title="'.$nometipo.'" acao="" confirma="Deseja Realmente alterar o Status?">'.$html.'</a>';
        }
        return $html;
    }
    /**
     * 
     */
    public function Checklists_Add(){
        self::Endereco_CheckList(true);
        // Carrega Config
        $titulo1    = 'Adicionar Checklist';
        $titulo2    = 'Salvar Checklist';
        $formid     = 'form_Sistema_Admin_Checklists';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Checklists_Add2/';
        $campos = Comercio_Checklist_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
        
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Checklists_Add2(){
        $titulo     = 'Checklist Adicionado com Sucesso';
        $dao        = 'Comercio_Checklist';
        $funcao     = '$this->Checklists();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Checklist cadastrado com sucesso.';
        $alterar    = Array();
        $sucesso = $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Checklists_Edit($id){
        self::Endereco_CheckList(true);
        // Carrega Config
        $titulo1    = 'Editar Checklist (#'.$id.')';
        $titulo2    = 'Alteração de Checklist';
        $formid     = 'form_Sistema_AdminC_OsEdit';
        $formbt     = 'Alterar Checklist';
        $formlink   = 'comercio/Proposta/Checklists_Edit2/'.$id;
        $editar     = Array('Comercio_Checklist',$id);
        $campos = Comercio_Checklist_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);  
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Checklists_Edit2($id){
        $titulo     = 'Checklist Editado com Sucesso';
        $dao        = Array('Comercio_Checklist',$id);
        $funcao     = '$this->Checklists();';
        $sucesso1   = 'Checklist Alterado com Sucesso.';
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
    public function Checklists_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa fornecedor e deleta
        $checklist = $this->_Modelo->db->Sql_Select('Comercio_Checklist', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($checklist);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Checklist Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Checklists();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Checklist deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @param type $export
     */
    public function Visitas($export=false){
        self::Endereco_Visita(false);
        //
        $i = 0;
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Agenda de Visita',
                'comercio/Proposta/Visitas_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'comercio/Proposta/Visitas',
            )
        )));
        $visita = $this->_Modelo->db->Sql_Select('Comercio_Visita');
        if($visita!==false && !empty($visita)){
            if(is_object($visita)) $visita = Array(0=>$visita);
            reset($visita);
            foreach ($visita as $indice=>&$valor) {
                $tabela['Cliente'][$i]         =  $valor->clientepossivel;
                $tabela['Responsável'][$i]     =  $valor->responsavel2;
                $tabela['Data do Contato'][$i] =  $valor->data;
                $tabela['Próximo Contato'][$i] =  $valor->data_proximo;
                $tabela['Funções'][$i]    =   $this->_Visual->Tema_Elementos_Btn('Personalizado'    ,Array('Histórico da Visita'    ,'comercio/Proposta/Visitas_Comentario/'.$valor->id    ,'','file','inverse'));
                $tabela['Funções'][$i]   .=   $this->_Visual->Tema_Elementos_Btn('Editar'           ,Array('Editar Agenda de Visita'                          ,'comercio/Proposta/Visitas_Edit/'.$valor->id.'/'    ,'')).
                                              $this->_Visual->Tema_Elementos_Btn('Deletar'          ,Array('Deletar Agenda de Visita'                         ,'comercio/Proposta/Visitas_Del/'.$valor->id.'/'     ,'Deseja realmente deletar essa agenda de Visita!'));
                ++$i;
            }
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Agenda de Visitas');
            }else{
                $this->_Visual->Show_Tabela_DataTable($tabela);
            }
            unset($tabela);
        }else{           
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhuma Agenda de Visita</font></b></center>');
        }
        $titulo = 'Listagem de Visitas ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Visitas');
    }
    /**
     * 
     */
    public function Visitas_Add(){
        self::Endereco_Visita(true);
        // Carrega Config
        $titulo1    = 'Adicionar Agenda de Visita';
        $titulo2    = 'Salvar Agenda de Visita';
        $formid     = 'form_Sistema_Admin_Visitas';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Visitas_Add2/';
        $campos = Comercio_Visita_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
        
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Add2(){
        $titulo     = 'Agenda de Visita Adicionada com Sucesso';
        $dao        = 'Comercio_Visita';
        $funcao     = '$this->Visitas();';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Agenda de Visita cadastrada com sucesso.';
        $alterar    = Array();
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
     
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Edit($id){
        self::Endereco_Visita(true);
        // Carrega Config
        $titulo1    = 'Editar Agenda de Visita (#'.$id.')';
        $titulo2    = 'Alteração de Agenda de Visita';
        $formid     = 'form_Sistema_AdminC_OsEdit';
        $formbt     = 'Alterar Agenda de Visita';
        $formlink   = 'comercio/Proposta/Visitas_Edit2/'.$id;
        $editar     = Array('Comercio_Visita',$id);
        $campos = Comercio_Visita_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);  
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Edit2($id){
        $titulo     = 'Agenda de Visita Editada com Sucesso';
        $dao        = Array('Comercio_Visita',$id);
        $funcao     = '$this->Visitas();';
        $sucesso1   = 'Sucesso';
        $sucesso2   = 'Agenda de Visita teve a alteração bem sucedida';
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
    public function Visitas_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa fornecedor e deleta
        $visita = $this->_Modelo->db->Sql_Select('Comercio_Visita', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($visita);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Agenda de Visita Deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Visitas();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Agenda de Visita deletada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario($visita_id = false){
        if($visita_id===false){
            $where = Array();
        }else{
            $where = Array('visita'=>$visita_id);
        }
        self::Endereco_Visita_Comentario(false,$visita_id);
        $i = 0;
        $this->_Visual->Blocar('<a title="Adicionar Comentário à Visita" class="btn btn-success lajax explicar-titulo" acao="" href="'.URL_PATH.'comercio/Proposta/Visitas_Comentario_Add/'.$visita_id.'">Adicionar novo comentário nesse Visita</a><div class="space15"></div>');
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Visita_Comentario',$where);
        if($comentario!==false && !empty($comentario)){
            if(is_object($comentario)) $comentario = Array(0=>$comentario);
            reset($comentario);
            foreach ($comentario as $indice=>&$valor) {
                $tabela['#Id'][$i]          =   '#'.$valor->id;
                $tabela['Comentário'][$i]   =   nl2br($valor->comentario);
                $tabela['Data'][$i]         =   $valor->log_date_add;
                $tabela['Funções'][$i]      =   $this->_Visual->Tema_Elementos_Btn('Editar'          ,Array('Editar Comentário de Visita'        ,'comercio/Proposta/Visitas_Comentario_Edit/'.$visita_id.'/'.$valor->id    ,'')).
                                                $this->_Visual->Tema_Elementos_Btn('Deletar'         ,Array('Deletar Comentário de Visita'       ,'comercio/Proposta/Visitas_Comentario_Del/'.$visita_id.'/'.$valor->id     ,'Deseja realmente deletar esse Comentário desse Visita ?'));
                ++$i;
            }
            $this->_Visual->Show_Tabela_DataTable($tabela,'', true, false, Array(Array(0,'desc')));
            unset($tabela);
        }else{            
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">Nenhum Comentário sobre a Visita</font></b></center>');
        }
        $titulo = 'Comentários do Visita ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo,'',10);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Administrar Comentários do Visita');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario_Add($visita_id = false){
        // Proteção E chama Endereço
        if($visita_id===false) throw new \Exception('Visita não informado',404);
        $visita = $this->_Modelo->db->Sql_Select('Comercio_Visita',Array('id'=>$visita_id), 1);
        if($visita===false) throw new \Exception('Visita não existe:'.$visita_id,404);
        self::Endereco_Visita_Comentario(true,$visita);
        // Começo
        $visita_id = (int) $visita_id;
        // Carrega Config
        $titulo1    = 'Adicionar Comentário de Visita';
        $titulo2    = 'Salvar Comentário de Visita';
        $formid     = 'form_Sistema_Admin_Visitas_Comentario';
        $formbt     = 'Salvar';
        $formlink   = 'comercio/Proposta/Visitas_Comentario_Add2/'.$visita_id;
        $campos = Comercio_Visita_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'visita');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario_Add2($visita_id = false){
        if($visita_id===false) throw new \Exception('Visita não informado',404);
        $titulo     = 'Comentário sobre a Visita Adicionado com Sucesso';
        $dao        = 'Comercio_Visita_Comentario';
        $funcao     = '$this->Visitas_Comentario('.$visita_id.');';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Comentário de Visita cadastrado com sucesso.';
        $alterar    = Array('visita'=>$visita_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario_Edit($visita_id = false,$id = 0){
        if($visita_id===false) throw new \Exception('Visita não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        // Proteção E chama Endereço
        $visita = $this->_Modelo->db->Sql_Select('Comercio_Visita',Array('id'=>$visita_id), 1);
        if($visita===false) throw new \Exception('Visita não existe:'.$visita_id,404);
        self::Endereco_Visita_Comentario(true,$visita);
        // Começo
        // Carrega Config
        $titulo1    = 'Editar Comentário sobre a Visita (#'.$id.')';
        $titulo2    = 'Alteração de Comentário sobre a Visita';
        $formid     = 'form_Sistema_AdminC_VisitaEdit';
        $formbt     = 'Alterar Comentário de Visita';
        $formlink   = 'comercio/Proposta/Visitas_Comentario_Edit2/'.$visita_id.'/'.$id;
        $editar     = Array('Comercio_Visita_Comentario',$id);
        $campos = Comercio_Visita_Comentario_DAO::Get_Colunas();
        self::DAO_Campos_Retira($campos, 'visita');
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario_Edit2($visita_id = false,$id = 0){
        if($visita_id===false) throw new \Exception('Visita não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        $titulo     = 'Comentário de Visita Editado com Sucesso';
        $dao        = Array('Comercio_Visita_Comentario',$id);
        $funcao     = '$this->Visitas_Comentario('.$visita_id.');';
        $sucesso1   = 'Comentário de Visita Alterado com Sucesso.';
        $sucesso2   = '#'.$visita_id.' teve a alteração bem sucedida';
        $alterar    = Array('visita'=>$visita_id);
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);      
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Visitas_Comentario_Del($visita_id = false,$id = 0){
        if($visita_id===false) throw new \Exception('Visita não informado',404);
        if($id         == 0   ) throw new \Exception('Comentário não informado',404);
        global $language;
        
    	$id = (int) $id;
        // Puxa linha e deleta
        $where = Array('id'=>$id);
        $comentario = $this->_Modelo->db->Sql_Select('Comercio_Visita_Comentario', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($comentario);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Comentário sobre a Visita Deletado com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Visitas_Comentario($visita_id);
        
        $this->_Visual->Json_Info_Update('Titulo', 'Comentário de Visita deletado com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>
