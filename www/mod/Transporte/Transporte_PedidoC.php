<?php

class Transporte_PedidoControle extends Transporte_Controle
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Main(){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Transporte/Pedido/Pedidos');
        return false;
    }
    public function Trans_Ped_Novas_Aceitar($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Transporte_Pedido', Array('id'=>$id),1);
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
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$resultado->id.'/'    ,''))
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
    public function Trans_Ped_Add($pedido = false){
        //self::Endereco_Noticia(true);
        // Carrega Config
        $titulo1    = 'Adicionar Pedido';
        $titulo2    = 'Salvar Pedido';
        $formid     = 'formTransporte_Pedido_Noticia';
        $formbt     = 'Salvar';
        $formlink   = 'Transporte/Pedido/Pedidos_Add2/';
        $campos = Transporte_Transportadora_Pedido_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Trans_Ped_Add2($pedido = false){
        $titulo     = 'Proposta enviada com Sucesso';
        $dao        = 'Transporte_Transportadora_Pedido';
        $funcao     = '$this->Trans_Ped_Novas();';
        $sucesso1   = 'Proposta enviada com Sucesso';
        $sucesso2   = 'Aguarde uma Resposta.';
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
    public function Trans_Ped_Del($pedido = false,$id){
        global $language;
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Pedido', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Pedido Cancelado com Sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Pedidos();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Proposta Cancelada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Trans_Ped_Aceitas($export=false){
        
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    public function Trans_Ped_Novas($export=false){
        
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    public function Trans_Ped_Minhas($export=false){
        $i = 0;
        //self::Endereco_Noticia(false);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Pedido',
                'Transporte/Pedido/Trans_Ped_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    
    public function Trans_Sol_Solicitacoes($export=false){
        
        $i = 0;
        //self::Endereco_Noticia(false);
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                'Adicionar Pedido',
                'Transporte/Pedido/Trans_Ped_Add',
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Transporte/Pedido/Trans_Ped_Minhas',
            )
        )));
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    public function Trans_Sol_PedAceitos($export=false){
        
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    public function Trans_Sol_PedRecusados($export=false){
        
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    public function Trans_Sol_PedPendente($export=false){
        
        $i = 0;
        $pedido = $this->_Modelo->db->Sql_Select('Transporte_Transportadora_Pedido_Lance');
        if(is_object($pedido)) $pedido = Array(0=>$pedido);
        if($pedido!==false && !empty($pedido)){
            $i = 0;
            reset($pedido);
            foreach ($pedido as &$valor) {                
                $tabela['Id'][$i]           = '#'.$valor->id;
                $tabela['Foto'][$i]         = '<img src="'.$valor->foto.'" style="max-width:100px;" />';
                $tabela['Pedido'][$i]       = $valor->nome;
                if($valor->status==2){
                    $texto = 'Recusado';
                    $valor->status='1';
                }else if($valor->status==1){
                    $texto = 'Aceito';
                    $valor->status='1';
                }else{
                    $texto = 'Pendente';
                    $valor->status='0';
                }
                $tabela['Funções'][$i]      = '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$valor->status     ,Array($texto        ,'Transporte/Pedido/Status/'.$valor->id.'/'    ,'')).'</span>';
                /*$tabela['Funções'][$i]      .= $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Pedido'        ,'Transporte/Pedido/Pedidos_Edit/'.$valor->id.'/'    ,'')).
                                               $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Pedido'       ,'Transporte/Pedido/Pedidos_Del/'.$valor->id.'/'     ,'Deseja realmente deletar esse Pedido ?'));*/
                ++$i;
            }
            // SE exportar ou mostra em tabela
            if($export!==false){
                self::Export_Todos($export,$tabela, 'Blocos');
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
                $mensagem = 'Nenhuma Pedido Cadastrada para exportar';
            }else{
                $mensagem = 'Nenhuma Pedido Cadastrada';
            }
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$mensagem.'</font></b></center>');
        }
        $titulo = 'Listagem de Dicas de Pedidos ('.$i.')';
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo','Pedidoistrar Pedidos');
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Trans_Sol_Add(){
        //self::Endereco_Noticia(true);
        // Carrega Config
        $titulo1    = 'Adicionar Proposta de Pedido';
        $titulo2    = 'Salvar Proposta de Pedido';
        $formid     = 'formTransporte_Pedido_Noticia';
        $formbt     = 'Salvar';
        $formlink   = 'Transporte/Pedido/Pedidos_Add2/';
        $campos = Transporte_Transportadora_Pedido_Lance_DAO::Get_Colunas();
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Trans_Sol_Add2(){
        $titulo     = 'Proposta enviada com Sucesso';
        $dao        = 'Transporte_Transportadora_Pedido_Lance';
        $funcao     = '$this->Trans_Ped_Novas();';
        $sucesso1   = 'Proposta enviada com Sucesso';
        $sucesso2   = 'Aguarde uma Resposta.';
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
    public function Trans_Sol_Del($id){
        global $language;
        
    	$id = (int) $id;
        // Puxa Transporte e deleta
        $pedido    =  $this->_Modelo->db->Sql_Select('Transporte_Pedido', Array('id'=>$id));
        $sucesso =  $this->_Modelo->db->Sql_Delete($pedido);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Proposta Cancelada com Sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        
        $this->Pedidos();
        
        $this->_Visual->Json_Info_Update('Titulo', 'Proposta Cancelada com Sucesso');  
        $this->_Visual->Json_Info_Update('Historico', false);  
    }
}
?>