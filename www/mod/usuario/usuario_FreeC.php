<?php
class usuario_FreeControle extends usuario_Controle
{
    /**
    * Construtor
    * 
    * @name __construct
    * @access public
    * 
    * @uses usuarios_ListarModelo Carrega usuarios Modelo
    * @uses usuarios_ListarVisual Carrega usuarios Visual
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
    * Função Main, Principal
    * 
    * @name Main
    * @access public
    * 
    * @uses usuarios_FreeControle::$usuarios_lista
    * @uses usuarios_FreeControle::$marcas_carregajanelaadd
    * @uses \Framework\App\Visual::$Json_Start
    * 
    * @return void 
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function Main($tipo = 'associado'){
        $tipocadastro = \anti_injection($tipo);
        if($tipocadastro!='' AND $tipocadastro!='cliente'){
            // = associado
            $tipocadastro = 'associado';
        }else{
            // = ''; // cliente
            $tipocadastro = 'cliente';
            
        }
        // carrega form de cadastro de usuarios
        $this->usuarios_carregajanelaadd($tipocadastro);
        // ORGANIZA E MANDA CONTEUDO
        $this->_Visual->Json_Info_Update('Titulo','Usuários');        
    }
    /**
    * Cria Janela Do Formulario de Cadastro de usuarios
    * 
    * @name usuarios_carregajanelaadd
    * @access public
    * 
    * @param Class &$controle Classe Controle Atual passada por Ponteiro
    * @param Class &$modelo Modelo Passado por Ponteiro
    * @param Class &$Visual Visual Passado por Ponteiro
    * 
    * @uses usuario_social_Modelo Carrega Persona Modelo
    * @uses usuario_social_Modelo::$retorna_usuario_social Retorna Pessoas
    * @uses financeiroControle::$usuarios_formcadastro retorna Formulario de Cadastro de usuarios
    * @uses \Framework\App\Visual::$blocar Add html ao bloco de conteudo
    * @uses \Framework\App\Visual::$Bloco_Menor_CriaJanela Add html do bloco a uma Janela Lateral Direita
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function usuarios_carregajanelaadd($tipocadastro=false){
        // Carrega Config
        $formlink   = 'usuario/Free/usuarios_inserir/'.$tipocadastro;
        $campos = Usuario_DAO::Get_Colunas();
        if($tipocadastro===false){
            $titulo1    = 'Se tornar Usuário';
            $titulo2    = 'Salvar Usuário';
            $formid     = 'form_free_cadastroUsuario';
            $formbt     = 'Salvar Usuário';
            self::DAO_Campos_Retira($campos,'grupo');
        }elseif($tipocadastro=='cliente'){
            $titulo1    = 'Se tornar Cliente';
            $titulo2    = 'Salvar Cliente';
            $formid     = 'form_free_cadastroCliente';
            $formbt     = 'Salvar';
            self::DAO_Campos_Retira($campos,'grupo');
        }else{
            $titulo1    = 'Se tornar Associado';
            $titulo2    = 'Salvar Associado';
            $formid     = 'form_free_cadastroAssociado';
            $formbt     = 'Salvar Associado';
        }
        \Framework\App\Controle::Gerador_Formulario_Janela($titulo1,$titulo2,$formlink,$formid,$formbt,$campos);
        
        if($tipocadastro=='associado'){
            $this->_Visual->Blocar($this->Show_ConhecaOsPlanos());
            $this->_Visual->Bloco_Maior_CriaTitulo('Conheça os Planos');
            $this->_Visual->bloco_maior_criaconteudo();
        }
    }
    /**
     * Inseri usuarios no Banco de dados
     * 
     * @name usuarios_inserir
     * @access public
     * 
     * @global Array $language
     * 
     * @return void
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 2.0
     */
    public function usuarios_inserir($tipo = 'cliente'){
        global $language;
        
        if(!isset($_POST['email']) || !isset($_POST['login'])){
            
            $mensagens = array(
                "tipo" => 'erro',
                "mgs_principal" => $language['mens_erro']['erro'],
                "mgs_secundaria" => 'Preencha Login e Email'
            );
            $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
            $this->_Visual->Javascript_Executar('$("#email").css(\'border\', \'2px solid #FFAEB0\').focus();$("#login").css(\'border\', \'2px solid #FFAEB0\');');
        }else{
            $existeemail = usuario_Modelo::VerificaExtEmail($this->_Modelo,\anti_injection($_POST['email']));
            $existelogin = usuario_Modelo::VerificaExtLogin($this->_Modelo,\anti_injection($_POST['login']));
            if(\Framework\App\Sistema_Funcoes::Control_Layoult_Valida_Email(\anti_injection($_POST['email']))===false){
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => 'Email Inválido'
                );
                $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
                $this->_Visual->Javascript_Executar('$("#email").css(\'border\', \'2px solid #FFAEB0\').focus();');
             }else if($existeemail===true){
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => 'Email Ja Existe'
                );
                $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
                $this->_Visual->Javascript_Executar('$("#email").css(\'border\', \'2px solid #FFAEB0\').focus();');
            }else if($existelogin===true){
                $mensagens = array(
                    "tipo" => 'erro',
                    "mgs_principal" => $language['mens_erro']['erro'],
                    "mgs_secundaria" => 'Login ja existe'
                );
                $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
                $this->_Visual->Javascript_Executar('$("#login").css(\'border\', \'2px solid #FFAEB0\').focus();');
            }else{
                $tipousuario = \anti_injection($tipo);

                // atualiza todos os valores por get, retirando o nivel admin
                //self::mysql_Campos_Retira($this->_Modelo->campos,'nivel_admin',0);
                self::mysql_AtualizaValores($this->_Modelo->campos);

                // confere senha
                if($_POST['senha']==''){
                    $mensagens = array(
                        "tipo" => 'erro',
                        "mgs_principal" => $language['mens_erro']['erro'],
                        "mgs_secundaria" => 'Senha Inválida'
                    );
                    $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens);
                    return;
                }

                // captura indicado;
                if(!isset($_COOKIE['indicativo_id'])) $_COOKIE['indicativo_id'] = 0;
                self::mysql_AtualizaValor($this->_Modelo->campos,'indicado_por', $_COOKIE['indicativo_id']);

                //insere usuario
                $sucesso =  $this->_Modelo->usuarios_inserir();

                if($sucesso===true){
                    $mensagens = array(
                        "tipo" => 'sucesso',
                        "mgs_principal" => 'Inserção bem sucedida',
                        "mgs_secundaria" => 'Voce foi cadastrado com sucesso.'
                    );
                    // loga usuario
                    $this->_Modelo->Usuario_Logar(\anti_injection($_POST['login']), \Framework\App\Sistema_Funcoes::Form_Senha_Blindar($_POST['senha']));  
                    // boleto
                    // Mensagem Para nao Associados
                    $mgm = '<br>Seja bem vindo a Locaway.'.
                    '<br>Seu cadastro foi registrado com sucesso.'.
                    '<br>Estamos trabalhando para melhor atendê-lo!<br>'.

                    '<br>Atenciosamente,'.
                    '<br>Equipe Locaway';
                    if($tipousuario!='cliente'){
                        $mgm = '<br>Seja bem vindo a Locaway.'.
                        '<br>Seu cadastro foi registrado com sucesso e encontra-se em análise de pagamento.'.
                        '<br>Estamos trabalhando para melhor atendê-lo!<br>'.

                        '<br>Atenciosamente,'.
                        '<br>Equipe Locaway';
                        eval('$valor = CONFIG_CLI_'.\anti_injection($_POST['nivel_usuario']).'_PRECO;');
                        $this->_Visual->Javascript_Executar('window.open(\''.LIBS_URL.'boleto/boleto_itau.php?clientenome='.\anti_injection($_POST['nome']).
                        '&endereco='.\anti_injection($_POST['endereco']).
                        '&numero='.\anti_injection($_POST['numero']).
                        '&complemento='.\anti_injection($_POST['complemento']).
                        '&cpf='.\anti_injection($_POST['cpf']).
                        '&cidade='.\anti_injection($_POST['cidade']).
                        '&bairro='.\anti_injection($_POST['bairro']).
                        '&cep='.\anti_injection($_POST['cep']).
                        '&valor='.$valor.'\',\'_TOP\')');
                    }else{
                        $this->Main();
                    }
                    $email = Mail_Send($this->usuario->nome, $this->usuario->email,SISTEMA_EMAIL,SISTEMA_NOME.' - Cadastro Realizado com Sucesso',$mgm);
                }else{
                    $mensagens = array(
                        "tipo" => 'erro',
                        "mgs_principal" => $language['mens_erro']['erro'],
                        "mgs_secundaria" => $language['mens_erro']['erro']
                    );
                }
                $this->_Visual->Json_IncluiTipo('Mensagens',$mensagens); 
            }
        }
    }
}
?>