<?php
class Musica_MusicaControle extends Musica_Controle
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
    * @uses musica_Controle::$comercioPerfil
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main($artista = false,$album=false){
        \Framework\App\Sistema_Funcoes::Redirect(URL_PATH.'Musica/Musica/Musicas');
        return false;
    }
    static function Endereco_Musica($true=true,$artista=false, $album=false){
        $registro = \Framework\App\Registro::getInstacia();
        $_Controle = $registro->_Controle;
        if($artista===false){
            $titulo = 'Todas as Musicas';
            $link   = 'Musica/Musica/Musicas';
        }else{
            $titulo = $artista->nome;
            $link   = 'Musica/Album/Albuns/'.$artista->id;
            if($album!==false){
                Musica_AlbumControle::Endereco_Album(true, $artista);
                $_Controle->Tema_Endereco($titulo,$link);
                $titulo = $album->nome;
                $link   = 'Musica/Musica/Musicas/'.$artista->id.'/'.$album->id;
            }else{
                Musica_ArtistaControle::Endereco_Artista();
            }
        }
        if($true===true){
            $_Controle->Tema_Endereco($titulo,$link);
        }else{
            $_Controle->Tema_Endereco($titulo);
        }
    }
    static function Musicas_Tabela(&$musicas,$artista=false,$album=false){
        $registro   = \Framework\App\Registro::getInstacia();
        $Modelo     = &$registro->_Modelo;
        $Visual     = &$registro->_Visual;
        $tabela = Array();
        $i = 0;
        if(is_object($musicas)) $musicas = Array(0=>$musicas);
        reset($musicas);
        foreach ($musicas as &$valor) {
            if($artista===false || $artista==0){
                $tabela['Artista'][$i]   = $valor->artista2;
                $tabela['Album'][$i]   = $valor->album2;
                $view_url   = 'Musica/Video/Videos/'.$valor->artista.'/';
                $edit_url   = 'Musica/Musica/Musicas_Edit/'.$valor->id.'/';
                $del_url    = 'Musica/Musica/Musicas_Del/'.$valor->id.'/';
            }else{
                if($album===false || $album==0){
                    $tabela['Album'][$i]   = $valor->album2;
                    $view_url   = 'Musica/Video/Videos/'.$valor->artista.'/'.$valor->album.'/';
                    $edit_url   = 'Musica/Musica/Musicas_Edit/'.$valor->id.'/'.$valor->artista.'/';
                    $del_url    = 'Musica/Musica/Musicas_Del/'.$valor->id.'/'.$valor->artista.'/';
                }else{
                    $view_url   = 'Musica/Video/Videos/'.$valor->artista.'/'.$valor->album.'/'.$valor->id.'/';
                    $edit_url   = 'Musica/Musica/Musicas_Edit/'.$valor->id.'/'.$valor->artista.'/'.$valor->album.'/';
                    $del_url    = 'Musica/Musica/Musicas_Del/'.$valor->id.'/'.$valor->artista.'/'.$valor->album.'/'.$valor->i.'/';
                }
            }
            $tabela['Musica'][$i]           = $valor->nome;
            $tabela['Data Registrada no Sistema'][$i]  = $valor->log_date_add;
            $status                                 = $valor->status;
            if($status!=1){
                $status = 0;
                $texto = 'Desativado';
            }else{
                $status = 1;
                $texto = 'Ativado';
            }
            $tabela['Funções'][$i]          = $Visual->Tema_Elementos_Btn('Visualizar' ,Array('Visualizar Videos da Musica'    ,$view_url    ,'')).
                                              '<span id="status'.$valor->id.'">'.$Visual->Tema_Elementos_Btn('Status'.$status     ,Array($texto        ,'Musica/Musica/Status/'.$valor->id.'/'    ,'')).'</span>'.
                                              $Visual->Tema_Elementos_Btn('Editar'     ,Array('Editar Musica'        ,$edit_url    ,'')).
                                              $Visual->Tema_Elementos_Btn('Deletar'    ,Array('Deletar Musica'       ,$del_url     ,'Deseja realmente deletar essa Musica ?'));
            ++$i;
        }
        return Array($tabela,$i);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Musicas($artista=false,$album=false,$export=false){
        if($artista ==='false' || $artista ===0)  $artista    = false;
        if($album ==='false' || $album ===0)  $album      = false;
        if($artista!==false){
            $artista = (int) $artista;
            if($artista==0){
                $musica_registro = $this->_Modelo->db->Sql_Select('Musica',Array(),1,'id DESC');
                if($musica_registro===false){
                    throw new \Exception('Essa musica não existe', 404);
                }
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$musica_registro->artista),1);
                if($artista_registro===false){
                    throw new \Exception('Não existe nenhum artista com esse id:', 404);
                }
                $artista = $artista_registro->id;
            }else{
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
                if($artista_registro===false){
                    throw new \Exception('Esse Artista não existe:', 404);
                }
            }
            $where = Array(
                'artista'   => $artista,
            );
            if($album!==false){
                $album = (int) $album;
                if($album==0){
                    $musica_registro = $this->_Modelo->db->Sql_Select('Musica',Array(),1,'id DESC');
                    if($musica_registro===false){
                        throw new \Exception('Essa musica não existe', 404);
                    }
                    $album_registro = $this->_Modelo->db->Sql_Select('Musica_Album',Array('id'=>$musica_registro->album,'artista'=>$artista),1);
                    if($album_registro===false){
                        throw new \Exception('Não existe nenhum album com esse id nesse Artista', 404);
                    }
                    $album = $album_registro->id;
                }else{
                    $album_registro = $this->_Modelo->db->Sql_Select('Musica_Album',Array('id'=>$album,'artista'=>$artista),1);
                    if($album_registro===false){
                        throw new \Exception('Esse Album não existe.', 404);
                    }
                }
                $where['album'] = $album;
                self::Endereco_Musica(false, $artista_registro, $album_registro);
                $titulo_add = 'Adicionar nova Musica desse Album';
                $url_add = '/'.$artista.'/'.$album;
                $titulo = 'Listagem de Musicas do Album '.$album_registro->nome;
                $erro = 'Nenhuma Musica nesse Album';
            }else{
                $where = Array();
                self::Endereco_Musica(false, $artista_registro, false);
                $titulo_add = 'Adicionar nova Musica ao Artista: '.$artista_registro->nome;
                $url_add = '/'.$artista.'/false';
                $titulo = 'Listagem de Musicas: '.$artista_registro->nome;
                $erro = 'Nenhuma Musica desse Artista';
            }
        }else{
            $where = Array();
            self::Endereco_Musica(false, false, false);
            $titulo_add = 'Adicionar nova Musica';
            $url_add = '/false/false';
            $titulo = 'Listagem de Musicas em Todos os Artistas';
            $erro = 'Nenhuma Musica nos Artistas';
        }
        $add_url = 'Musica/Musica/Musicas_Add'.$url_add;
        $i = 0;
        $this->_Visual->Blocar($this->_Visual->Tema_Elementos_Btn('Superior'     ,Array(
            Array(
                $titulo_add,
                $add_url,
                ''
            ),
            Array(
                'Print'     => true,
                'Pdf'       => true,
                'Excel'     => true,
                'Link'      => 'Musica/Musica/Musicas'.$url_add,
            )
        )));
        $musicas = $this->_Modelo->db->Sql_Select('Musica',$where);
        if($musicas!==false && !empty($musicas)){
            list($tabela,$i) = self::Musicas_Tabela($musicas,$artista,$album);
            $titulo = $titulo.' ('.$i.')';
            if($export!==false){
                self::Export_Todos($export,$tabela, $titulo);
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
            $titulo = $titulo.' ('.$i.')';
            $this->_Visual->Blocar('<center><b><font color="#FF0000" size="5">'.$erro.'</font></b></center>');
        }
        $this->_Visual->Bloco_Unico_CriaJanela($titulo);
        
        //Carrega Json
        $this->_Visual->Json_Info_Update('Titulo',$titulo);
    }
    /**
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Musicas_Add($artista = false,$album=false){
        if($artista==='false') $artista = false;
        if($album==='false') $album = false;
        
        // Carrega Config
        $formid     = 'form_Sistema_Admin_Musicas';
        $formbt     = 'Salvar';
        $campos     = Musica_DAO::Get_Colunas();
        if($artista===false){
            $formlink   = 'Musica/Musica/Musicas_Add2';
            $titulo1    = 'Adicionar Musica';
            $titulo2    = 'Salvar Musica';
            self::Endereco_Musica(true, false, false);
        }else{
            $artista = (int) $artista;
            if($artista==0){
                $musica_registro = $this->_Modelo->db->Sql_Select('Musica',Array(),1,'id DESC');
                if($musica_registro===false){
                    throw new \Exception('Essa musica não existe', 404);
                }
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$musica_registro->artista),1);
                if($artista_registro===false){
                    throw new \Exception('Não existe nenhuma artista:', 404);
                }
                $artista = $artista_registro->id;
            }else{
                $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
                if($artista_registro===false){
                    throw new \Exception('Esse Artista não existe:', 404);
                }
            }
            self::DAO_Campos_Retira($campos,'artista');
            if($album===false){
                self::DAO_Ext_Alterar($campos,'album',$artista);
                $formlink   = 'Musica/Musica/Musicas_Add2/'.$artista;
                $titulo1    = 'Adicionar Musica ao Artista: '.$artista_registro->nome ;
                $titulo2    = 'Salvar Musica ao Artista: '.$artista_registro->nome ;
                self::Endereco_Musica(true, $artista_registro, false);
            }else{
                $album = (int) $album;
                if($album==0){
                    $musica_registro = $this->_Modelo->db->Sql_Select('Musica',Array(),1,'id DESC');
                    if($musica_registro===false){
                        throw new \Exception('Essa musica não existe', 404);
                    }
                    $album_registro = $this->_Modelo->db->Sql_Select('Musica_Album',Array('id'=>$musica_registro->album,'artista'=>$artista),1);
                    if($album_registro===false){
                        throw new \Exception('Não existe nenhum Album:', 404);
                    }
                    $album = $album_registro->id;
                }else{
                    $album_registro = $this->_Modelo->db->Sql_Select('Musica_Album',Array('id'=>$album,'artista'=>$artista),1);
                    if($album_registro===false){
                        throw new \Exception('Esse Album não existe:', 404);
                    }
                }
                $formlink   = 'Musica/Musica/Musicas_Add2/'.$artista.'/'.$album;
                self::DAO_Campos_Retira($campos,'album');
                $titulo1    = 'Adicionar Musica ao Album '.$album_registro->nome ;
                $titulo2    = 'Salvar Musica ao Album '.$album_registro->nome ;
                self::Endereco_Musica(true, $artista_registro, $album_registro);
            }
        }
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
    }
    /**
     * 
     * @global Array $language
     *
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Musicas_Add2($artista=false,$album=false){
        if($artista==='false') $artista = false;
        if($album==='false') $album = false;
        
        $titulo     = 'Musica Adicionada com Sucesso';
        $dao        = 'Musica';
        $sucesso1   = 'Inserção bem sucedida';
        $sucesso2   = 'Musica cadastrada com sucesso.';
        // Recupera Musicas
        if($artista!==false){
            $artista = (int) $artista;
            $alterar    = Array('artista'=>$artista);
            if($album!==false){
                $album = (int) $album;
                $funcao     = '$this->Musicas('.$artista.','.$album.');';
                $alterar['album'] = $album;
            }else{
                $funcao     = '$this->Musicas('.$artista.');';
            }
        }else{
            $alterar    = Array();
            $funcao     = '$this->Musicas(0,0);';
        }
        $this->Gerador_Formulario_Janela2($titulo,$dao,$funcao,$sucesso1,$sucesso2,$alterar);
    }
    /**
     * 
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Musicas_Edit($id,$artista = false,$album=false){
        if($artista==='false') $artista = false;
        if($album==='false') $album = false;
        if($id===false){
            throw new \Exception('Musica não existe:'. $id, 404);
        }
        $id         = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
        }
        // Carrega Config
        $titulo1    = 'Editar Musica (#'.$id.')';
        $titulo2    = 'Alteração de Musica';
        $formid     = 'form_Sistema_AdminC_MusicaEdit';
        $formbt     = 'Alterar Musica';
        $campos = Musica_DAO::Get_Colunas();
        if($artista!==false){
            $artista_registro = $this->_Modelo->db->Sql_Select('Musica_Album_Artista',Array('id'=>$artista),1);
            if($artista_registro===false){
                throw new \Exception('Esse Artista não existe:', 404);
            }
            if($album!==false){
                $album_registro = $this->_Modelo->db->Sql_Select('Musica_Album',Array('id'=>$artista,'artista'=>$artista_registro->id),1);
                if($album_registro===false){
                    throw new \Exception('Esse Artista não existe:', 404);
                }
                $formlink   = 'Musica/Musica/Musicas_Edit2/'.$id.'/'.$artista.'/'.$album;
                self::DAO_Campos_Retira($campos,'artista');
                self::DAO_Campos_Retira($campos,'album');
                self::Endereco_Musica(true, $artista_registro);
            }else{
                self::DAO_Ext_Alterar($campos,'album',$artista);
                $formlink   = 'Musica/Musica/Musicas_Edit2/'.$id.'/'.$artista;
                self::DAO_Campos_Retira($campos,'artista');
                self::Endereco_Musica(true, $artista_registro);
            }
        }else{
            $formlink   = 'Musica/Musica/Musicas_Edit2/'.$id;
            self::Endereco_Musica(true, false);
        }
        $editar     = Array('Musica',$id);
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos,$editar);
    }
    /**
     * 
     * @global Array $language
     * @param type $id
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function Musicas_Edit2($id,$artista = false,$album=false){
        if($artista==='false') $artista = false;
        if($album==='false') $album = false;
        if($id===false){
            throw new \Exception('Musica não existe:'. $id, 404);
        }
        $id         = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
        }
        if($album!==false){
            $album    = (int) $album;
        }
        $titulo     = 'Musica Editada com Sucesso';
        $dao        = Array('Musica',$id);
        // Recupera Musicas
        if($artista!==false){
            if($album!==false){
                $funcao     = '$this->Musicas('.$artista.','.$album.');';
            }else{
                $funcao     = '$this->Musicas('.$artista.');';
            }
        }else{
            $funcao     = '$this->Musicas();';
        }
        $sucesso1   = 'Musica Alterada com Sucesso.';
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
    public function Musicas_Del($id = false,$artista=false,$album=false){
        if($artista==='false') $artista = false;
        if($album==='false') $album = false;
        global $language;
        if($id===false){
            throw new \Exception('Musica não existe:'. $id, 404);
        }
        // Antiinjection
    	$id = (int) $id;
        if($artista!==false){
            $artista    = (int) $artista;
            $where = Array('artista'=>$artista,'id'=>$id);
        }else{
            $where = Array('id'=>$id);
        }
        // Puxa musica e deleta
        $musica = $this->_Modelo->db->Sql_Select('Musica', $where);
        $sucesso =  $this->_Modelo->db->Sql_Delete($musica);
        // Mensagem
    	if($sucesso===true){
            $mensagens = array(
                "tipo" => 'sucesso',
                "mgs_principal" => 'Deletado',
                "mgs_secundaria" => 'Musica deletada com sucesso'
            );
    	}else{
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => $language['mens_erro']['erro']
            );
        }
        $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
        // Recupera Musicas
        if($artista!==false){
            if($album!==false){
                $this->Musicas($artista,$album);
            }else{
                $this->Musicas($artista);
            }
        }else{
            $this->Musicas();
        }
        
        $this->_Visual->Json_Info_Update('Titulo', 'Musica deletada com Sucesso');
        $this->_Visual->Json_Info_Update('Historico', false);
    }
    public function Status($id=false){
        if($id===false){
            throw new \Exception('Registro não informado:'. $raiz, 404);
        }
        $resultado = $this->_Modelo->db->Sql_Select('Musica', Array('id'=>$id),1);
        if($resultado===false || !is_object($resultado)){
            throw new \Exception('Esse registro não existe:'. $raiz, 404);
        }
        if($resultado->status=='1'){
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
                'html' =>  $this->_Visual->Tema_Elementos_Btn('Status'.$resultado->status     ,Array($texto        ,'Musica/Musica/Status/'.$resultado->id.'/'    ,''))
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
}
?>
