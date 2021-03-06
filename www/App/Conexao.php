<?php
namespace Framework\App;
/** #UPDATE
 * COLOCAR NA CONEXAO, A TRIGGER
 * DELIMITER $$
 
CREATE TRIGGER Tgr_ItensVenda_Insert AFTER INSERT
ON ItensVenda
FOR EACH ROW
BEGIN
    UPDATE Produtos SET Estoque = Estoque - NEW.Quantidade
WHERE Referencia = NEW.Produto;
END$$
 
CREATE TRIGGER Tgr_ItensVenda_Delete AFTER DELETE
ON ItensVenda
FOR EACH ROW
BEGIN
    UPDATE Produtos SET Estoque = Estoque + OLD.Quantidade
WHERE Referencia = OLD.Produto;
END$$
 
DELIMITER ;

 */



/**
 * Class Responsavel pela CONEXAO MYSQL e todas suas query,
 * assim como detectar erro e repara-los automaticamente
 * 
 * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
 * @version 0.22
 */
final class Conexao
{
    protected $host                     =  '';
    protected $usuario                  =  '';
    protected $senha                    =  '';
    protected $banco                    =  '';
    
    protected $_Registro                =  '';
    protected $_Cache                   =  '';
    
    protected $mysqli;
    static    $tabelas;
    static    $tabelas_siglas           = Array();
    static    $tabelas_ext              = Array();
    static    $tabelas_Links            = Array();
    static    $tabelas_Links_Invertido  = Array();
    
    /**
     * Construtor
     * 
     * @name __construct
     * @access public
     * 
     * @uses \Framework\App\Conexao::$host
     * @uses \Framework\App\Conexao::$usuario
     * @uses \Framework\App\Conexao::$senha
     * @uses \Framework\App\Conexao::$banco
     * @uses mysqli
     * @uses mysqli::$query
     * 
     * @return void
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function __construct()
    {
        $imprimir = new \Framework\App\Tempo('Conexao');
        $this->host     =   SIS_SERVER;
        $this->usuario  =   SIS_USUARIO;
        $this->senha    =   SIS_SENHA;
        $this->banco    =   SIS_BANCO;
        
        // Recupera Objetos
        $this->_Registro    = &\Framework\App\Registro::getInstacia();
        $this->_Cache       = &$this->_Registro->_Cache;        
        
        @$this->mysqli  =   new \mysqli($this->host, $this->usuario, $this->senha, $this->banco);
        if (mysqli_connect_errno()) {
            throw new \Exception('Erro de Conexão: '.mysqli_connect_error(),3100);
        }
        /* change character set to utf8 */
        if (!$this->mysqli->set_charset("utf8")) {
            throw new \Exception('Erro mysql set_charset utf8: '.$this->mysqli->error,3105);
        }
        if (!$this->mysqli->query("SET NAMES 'utf8'")) {
            throw new \Exception('Erro mysql SET NAMES utf8: '.$this->mysqli->error,3101);
        }
        if (!$this->mysqli->query("SET character_set_connection=utf8")) {
            throw new \Exception('Erro character_set_connection=utf8: '.$this->mysqli->error,3102);
        }
        if (!$this->mysqli->query("SET character_set_client=utf8")) {
            throw new \Exception('Erro character_set_client=utf8: '.$this->mysqli->error,3103);
        }
        if (!$this->mysqli->query("SET character_set_results=utf8")) {
            throw new \Exception('Erro character_set_results=utf8: '.$this->mysqli->error,3104);
        }
        
        
	// Se não houver a variavel no cache ou já tiver expirado
        if(SISTEMA_DEBUG===TRUE){
            $this->Tabelas();
            $this->Tabelas_Variaveis_Gerar();
        }else{
            // Inicia Tabelas Verificando se a mesma já contem no cache
            // Ja que são funcoes que demandam tempo e sempre retorna mesmo resultado.
            $cache_conexao = $this->_Cache->Ler('Conexao', true);
            if (!$cache_conexao) {
                // REaliza Consulta DOS DAO
                $this->Tabelas();
                $this->Tabelas_Variaveis_Gerar();
                $cache_conexao = Array();
                // Salva Variaveis para passar pro cache
                $cache_conexao[] = &self::$tabelas;
                $cache_conexao[] = &self::$tabelas_siglas;
                $cache_conexao[] = &self::$tabelas_Links;
                $cache_conexao[] = &self::$tabelas_Links_Invertido;
                $cache_conexao[] = &self::$tabelas_ext;

                $this->_Cache->Salvar('Conexao', $cache_conexao, '1200', true);
            }else{
                // Recupera Cache
                list(
                    self::$tabelas,
                    self::$tabelas_siglas,
                    self::$tabelas_Links,
                    self::$tabelas_Links_Invertido,
                    self::$tabelas_ext
                ) = $cache_conexao;
                unset($cache_conexao);
            }
        }
        
        return true;
    }     
    /*Evita que a classe seja clonada*/
    private function __clone(){}

    public static function &Dao_GetColunas($nome){
        return self::$tabelas[$nome]['colunas'];
    }
    /**
     * Carrega Query
     * 
     * @name query
     * @access public
     * 
     * @uses mysqli::$query
     * 
     * @return Array $re
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function query($sql,$autoreparo=true) 
    {
        $tempo = new \Framework\App\Tempo('Conexao Query');
        $passar = true;
        //echo "\n\n<br><br>".$sql;
        while(!$re = $this->mysqli->query($sql)) {
            $erro = $this->mysqli->error;
            if(SISTEMA_DEBUG===true){
                echo '#QUERY:'.$sql.'n\n<br \>ERRO:'.$erro."\n\n<br \><br \>";
            }
            if($passar && $autoreparo){
                $passar = $this->autoreparo_query($sql, $erro);
            }else{
                throw new \Exception('Erro de Query: '.$erro,3110);
            }
        }
        //var_dump($sql,'final',$re);
        return $re;
    }
    public function multi_query($sql,$autoreparo=true) 
    {
        $tempo = new \Framework\App\Tempo('Conexao MultiQuery');
        $passar = true;
        //echo "\n\n<br><br>".$sql;
        if ($this->mysqli->multi_query($sql/*,MYSQLI_ASYNC*/)) {
            //return true;
            do {
                /* store first result set */
                if ($result = $this->mysqli->store_result()) {
                    /*while ($row = $result->fetch_row()) {
                        printf("%s\n", $row[0]);
                    }*/
                    $result->free();
                }
                /* print divider */
                if ($this->mysqli->more_results()) {
                    //printf("-----------------\n");
                }else break;
            } while ($this->mysqli->next_result());
        }else{
            $erro = $this->mysqli->error;
            if(SISTEMA_DEBUG===true){
                echo '#QUERY:'.$sql.'n\n<br \>ERRO:'.$erro."\n\n<br \><br \>";
            }
            throw new \Exception('Erro de Query MULTIPLA: '.$erro,3110);
        }
        //var_dump($sql,'final',$re);
        return true;
        
    }
    public function ultimo_id(){
        return $this->mysqli->insert_id;
    }
    /**
     * 
     * @param type $sql
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     * 
     * 
     * #update ... Invez de fazxer varias conexoes, armazena tudo em uma variavel, e faz uma conexao só no final
     */
    protected function Sql_Log($tabela,$campos,$valores,$tipo='Update'){
        $tempo = new \Framework\App\Tempo('Log');
        $query = 'INSERT INTO '.MYSQL_LOG_SQL.' (tabela,campos,valores) VALUES (\''.$tabela.'\',\''.$campos.'\',\''.$valores.'\',\''.APP_HORA.'\')';
        $this->query($query,true);
        
    }
    /**
     * <Erros> Frequentes de query com Reparação Automatica
     * Unknown column 'foto' in 'field list'
     * Unknown column 'login' in 'where clause'
     * Unknown column 'C.local' in 'on clause'
     * Table 'Projeto_Framework.usuario_social' doesn't exist
     * <Exemplos> De erros sem Reparaçaão Automatica
     * You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'ANDD senha='e10adc3949ba59abbe56e057f20f883e' AND ativado='1'' at line 1
     * 
     * @param type $query
     * @param type $erro
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     * @return bolean Verdadeiro se tiver consertado, falso caso contrario
     */
    public function autoreparo_query($query, $erro){
        if(strpos($erro, 'Failed to read auto-increment value from storage engine ')!==false){
            
            // Puxa nome da tabela de acordo com a query
            if(strpos(strtolower($query), "insert into")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(trim(stristr(trim(stristr($query, 'into' )), ' ')), $multi, true);
                }else{
                    // Criado para funcionar Abaixo do PHP 5.3
                    $tabela = explode($multi,trim(stristr(trim(stristr($query, 'into' )), ' ')));
                    $tabela = $tabela[0];
                }
            }else if(strpos(strtolower($query), "select")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(trim(stristr(trim(stristr($query, 'from' )), ' ')), $multi, true); 
                }else{
                    // Criado para funcionar Abaixo do PHP 5.3
                    $tabela = explode($multi,trim(stristr(trim(stristr($query, 'from' )), ' ')));
                    $tabela = $tabela[0];
                }
            }else if(strpos(strtolower($query), "insert")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(
                        trim(
                            stristr(
                                trim(
                                    stristr(
                                        $query, 
                                        'nsert' 
                                    )
                                ), 
                                    ' '
                            )
                         ), 
                        $multi, 
                        true
                    );
                }
            }else{
                return false;
            }
            return $this->query('ALTER TABLE `'.$tabela.'` AUTO_INCREMENT =1;',true);

        }else if(strpos($erro, 'Unknown column')!==false){
            /*
             * ALTER TABLE clientes ADD email char(80) not null AFTER fone;
             * ALTER TABLE clientes DROP email;  //eliminar email
             * ALTER TABLE clientes CHANGE fone fone char(30) not null; 
             */
            // Trata Erro e acha o campo errado
            if(strpos($erro, "in 'field list'")!==false){
                $erro = str_replace('in \'field list\'', '', $erro);
            }else if(strpos($erro, 'in \'where clause\'')!==false){
                $erro = str_replace('in \'where clause\'', '', $erro);
            }else if(strpos($erro, 'in \'on clause\'')!==false){
                $erro = str_replace('in \'on clause\'', '', $erro);
            }else{
                $erro = explode(' in ',$erro);
                $erro = $erro[0];
            }
            $campo = explode("'",$erro);
            $campo = $campo[1];
            
            // Caso nao seja alterado, se trata de uma tabela só
            $multi = ' ';
            $tabelanova = '';
            if(strpos($campo, '.')!==false){
                $campo = explode('.',$campo);
                $multi .= $campo[0];
                // Verifica se foi erro numa tabela que foi incluida automaticamente
                // pelo sistema na query de SelECT
                if(strpos($campo[0], 'EXT')!==false){
                    $capturar_externa = explode(' AS '.$campo[0],$query);
                    $capturar_externa = explode(' JOIN ',$capturar_externa[0]);
                    $cont = count($capturar_externa);
                    $tabelanova = $capturar_externa[$cont-1];
                }else{
                    $tabelanova = self::Tabelas_GetSiglas_Recolher($campo[0]);
                    $tabelanova = $tabelanova['tabela'];
                }
                $campo  = $campo[1];
                if(strpos($query, $multi.' ')!==false) $multi .= ' ';
                if(strpos($query, $multi.',')!==false) $multi .= ',';
            }
            // Trava bug quando query é muito simples e nao tem final
            if(strpos(strtolower($query), "where")===false && strpos(strtolower($query), "limit")===false && strpos(strtolower($query), "order")===false)
            {
                $query = $query. ' LIMIT 1';
            }
            
            // Puxa nome da tabela de acordo com a query
            if(strpos(strtolower($query), "insert into")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(trim(stristr(trim(stristr($query, 'into' )), ' ')), $multi, true);
                }else{
                    // Criado para funcionar Abaixo do PHP 5.3
                    $tabela = explode($multi,trim(stristr(trim(stristr($query, 'into' )), ' ')));
                    $tabela = $tabela[0];
                }
            }else if(strpos(strtolower($query), "select")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(trim(stristr(trim(stristr($query, 'from' )), ' ')), $multi, true); 
                }else{
                    // Criado para funcionar Abaixo do PHP 5.3
                    $tabela = explode($multi,trim(stristr(trim(stristr($query, 'from' )), ' ')));
                    $tabela = $tabela[0];
                }
            }else if(strpos(strtolower($query), "insert")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(
                        trim(
                            stristr(
                                trim(
                                    stristr(
                                        $query, 
                                        'nsert' 
                                    )
                                ), 
                                    ' '
                            )
                         ), 
                        $multi, 
                        true
                    );
                }
            }else if(strpos(strtolower($query), "update")!==false){
                if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                    $tabela = stristr(
                        trim(
                            stristr(
                                trim(
                                    stristr(
                                        $query, 
                                        'pdate' 
                                    )
                                ), 
                                    ' '
                            )
                         ), 
                        $multi, 
                        true
                    );
                }
            }else{
                return false;
            }
            // Se tiverem varias tabelas escolhe a correta
            $tabela_multi = explode(' ',$tabela);
            if(count($tabela_multi)>1){
               $tabela = $tabela_multi[0];
            }
            if($tabelanova!=''){
                $tabela = $tabelanova;
            }
            
            if($campo=='log_user_add' || $campo=='log_user_edit' || $campo=='log_user_del'){
                return $this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'` int(11) DEFAULT NULL;',false);
            }else if($campo=='log_date_add' || $campo=='log_date_edit' || $campo=='log_date_del'){
                return $this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'` datetime;',false,false);
            }else if($campo=='deletado'){
                return $this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'`  INT(3) NOT NULL DEFAULT \'0\';',false);
            }else if($campo=='servidor'){
                return $this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'`  VARCHAR(45) NOT NULL DEFAULT \''.SRV_NAME_SQL.'\' FIRST;',false);
            }else if($campo=='id'){
                return $this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'`  INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT \'Chave Primária\';',false);
            }else{
                $colunas = &self::$tabelas[$tabela]["colunas"];
                $existe = false;
                if(isset($colunas) && !empty($colunas)){
                    foreach($colunas as &$valor){
                        if($existe===false && isset($valor['mysql_titulo']) && ($valor['mysql_titulo']==$campo || $valor['mysql_titulo']==$campo.'[]')){
                            $existe = true;
                            $inf_campo = '';

                            $valor['mysql_tipovar'] = strtoupper($valor['mysql_tipovar']);
                            $inf_campo .= $valor['mysql_tipovar'];		
                            // text e blog nao aceita default
                            if(strpos($valor['mysql_tipovar'], 'TEXT')===false && strpos($valor['mysql_tipovar'], 'BLOG')===false){
                                if(isset($valor['mysql_tamanho']) && $valor['mysql_tamanho']  !==false && $valor['mysql_tipovar']!='DATETIME' && $valor['mysql_tipovar']!='DATE' && $valor['mysql_tipovar']!='TIME'){
                                    $inf_campo .= '('.$valor['mysql_tamanho'].')';
                                }
                                if($valor['mysql_null'] === false && $valor['mysql_default'] !== false){
                                    $inf_campo .= ' NOT NULL DEFAULT \''.$valor['mysql_default'].'\'';
                                }
                                else{
                                    $inf_campo .= ' DEFAULT NULL';
                                }
                                if($valor['mysql_autoadd']!==false){
                                    $inf_campo .= ' AUTO_INCREMENT';
                                }
                            }
                            // Adiciona Comentário 
                            if($valor['mysql_comment']!==false){
                                $inf_campo .= ' COMMENT \''.$valor['mysql_comment'].'\'';
                            }
                        }
                    }
                }
                // SE exister faz query e retorna se nao retorna falso
                return $existe?$this->query('ALTER TABLE `'.$tabela.'` ADD `'.$campo.'` '.$inf_campo.';',true):false;
            }
        }else if(strpos($erro, 'Table')!==false AND strpos($erro, 'doesn\'t exist')!==false){
            /*$tabela = explode('.',stristr($erro, $this->banco.'.'));
            if (\Framework\App\Sistema_Funcoes::VersionPHP('5.3.10')){
                $tabela = stristr($tabela[1], '\' doesn\'t exist',true);
            }else{
                $tabela = explode('\' doesn\'t exist', $tabela[1]);
                $tabela = $tabela[0];
            }
            
            if(!array_key_exists(strtolower($tabela), array_change_key_case(self::$tabelas))){
                throw new \Exception('Erro de Query: '.$erro,3200);
            }else{
                // Acha a Tabela mesmo se tiver maiusculas e minusculas diferenciando
                foreach(self::$tabelas as $indice=>&$valor){
                    if(strtolower($tabela)==strtolower($indice)){
                        $tabela = $indice;
                    }
                }
            }*/
            // Conserta query
            $this->Sql_Query('criar tabela', self::$tabelas);
            return true;
        }
        return false;
    }
    /**
     * Executa query de reparação de acordo com a Instrução dada
     * 
     * @param type $comando
     * @param type $tabela
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Sql_Query($comando, &$tabela, $executar_query = true){
        // Declara Variaveis
        $query          = (string) '';
        $primaria       = (string) ''; 
        $indices        = (string) ''; 
        $indice_unico   = Array(); 
        $autoincrement  = false;
        // Declara Varaiveis funcoes
        $autoincrement_sql = function($primaria,$autoincrement) {
            if($primaria!='' && $autoincrement===false){
                $primaria = ','.$primaria;
            }
            return ($autoincrement)?$primaria:'`servidor`'.$primaria;
        };
        // Se tabela não é um array return false;
        if(!is_array($tabela)){
            return false;
        }
        if(isset($tabela['colunas']) && is_array($tabela['colunas'])){
            // Verifica comando
            if($comando == 'criar tabela'){
                // Começa a criar tabela
                $query .= 'CREATE TABLE IF NOT EXISTS `'.$tabela['nome'].'` (';
                if($tabela['static']===false || !isset($tabela['static'])){
                    $tabela['static'] = false;
                    $query .= '`servidor` VARCHAR(45) NOT NULL DEFAULT \''.SRV_NAME_SQL.'\',';
                }
                // Logs de Usuarios e datas
                $query .= '`log_user_add` int(11) DEFAULT NULL,';
                $query .= '`log_user_edit` int(11) DEFAULT NULL,';
                $query .= '`log_user_del` int(11) DEFAULT NULL,';
                $query .= '`log_date_add` datetime,';
                $query .= '`log_date_edit` datetime,';
                $query .= '`log_date_del` datetime,';
                $query .= '`deletado` INT(3) NOT NULL DEFAULT \'0\',';
                // Contadores
                $i      = 0; // contagem colunas, servidor nao conta
                $p      = 0; // contagem de primarias
                $i_u    = Array(); // contagem de indices unicos
                // Começa a PUTARIA
                foreach($tabela['colunas'] as &$valor){
                    if(isset($valor['mysql_titulo'])){
                        $valor['mysql_tipovar'] = strtoupper($valor['mysql_tipovar']);
                        if($i!=0) $query .= ',';
                        $query .= '`'.$valor['mysql_titulo'].'` '.$valor['mysql_tipovar'];		
                        // text e blog nao aceita default
                        if(strpos($valor['mysql_tipovar'], 'TEXT')===false && strpos($valor['mysql_tipovar'], 'BLOG')===false){
                            if(isset($valor['mysql_tamanho']) && $valor['mysql_tamanho']  !==false && $valor['mysql_tipovar']!='DATETIME' && $valor['mysql_tipovar']!='DATE' && $valor['mysql_tipovar']!='TIME'){
                                $query .= '('.$valor['mysql_tamanho'].')';
                            }
                            if($valor['mysql_null'] === false && $valor['mysql_default'] !== false && $valor['mysql_autoadd']===false){
                                $query .= ' NOT NULL DEFAULT \''.$valor['mysql_default'].'\'';
                            }else if($valor['mysql_null'] === false && $valor['mysql_autoadd']===false){
                                $query .= ' NOT NULL';
                            } else{
                                $query .= ' DEFAULT NULL';
                            }
                            if($valor['mysql_autoadd']!==false){
                                $query .= ' AUTO_INCREMENT';
                                $autoincrement = true;
                            }
                            // Verifica Primarias...
                            if($valor['mysql_primary']!==false){
                                if($p!=0) $primaria .= ',';
                                $primaria .= '`'.$valor['mysql_titulo'].'`';
                                ++$p;
                            }
                            // Verifica Indices Unicos
                            if(isset($valor['mysql_indice_unico']) && $valor['mysql_indice_unico']!==false && is_string($valor['mysql_indice_unico'])){
                                if(isset($i_u[$valor['mysql_indice_unico']]) && is_int($i_u[$valor['mysql_indice_unico']]) && $i_u[$valor['mysql_indice_unico']]>0){
                                    $indice_unico[$valor['mysql_indice_unico']] .= ',';
                                }else{
                                    if($tabela['static']===false){
                                        $indice_unico[$valor['mysql_indice_unico']] = (string)  '`servidor`,'  ;
                                    }else{
                                        $indice_unico[$valor['mysql_indice_unico']] = (string)  ''  ;
                                    }
                                    $i_u[$valor['mysql_indice_unico']]          = (int)     0   ;
                                }
                                $indice_unico[$valor['mysql_indice_unico']] .= '`'.$valor['mysql_titulo'].'`';
                                ++$i_u[$valor['mysql_indice_unico']];
                            }
                        }
                        // Adiciona Comentário 
                        if($valor['mysql_comment']!==false){
                            $query .= ' COMMENT \''.$valor['mysql_comment'].'\'';
                        }
                        ++$i;
                    }
                }
                // Add Primarias a Indice..
                if($primaria!=''){
                    if($tabela['static']===false){
                        $indices .= ', PRIMARY KEY ('.$autoincrement_sql($primaria,$autoincrement).')';
                    }else{
                        $indices .= ', PRIMARY KEY ('.$autoincrement_sql($primaria,true).')';
                    }
                }
                // Adiciona Unicos a Indice 
                if(is_array($indice_unico) && count($indice_unico)>0){
                    foreach($indice_unico AS $indice_novo => &$valor_novo){
                        $indices .= ', UNIQUE KEY `'.$indice_novo.'` ('.$valor_novo.')';
                    }
                }
                // Termina Query 
                $query .= $indices.')'.' ENGINE='.$tabela['engine'].' DEFAULT CHARSET='.$tabela['charset'].' AUTO_INCREMENT='.$tabela['autoadd'].' ;';
            }else
            // Comando = Reparar Tabela
                // #update fazer reparar
            if($comando == 'reparar'){
                    
            }
        }else{
            // Se for um conjunto de tabelas, faz em todas, recursivamente
            foreach($tabela as &$valor) {
                $this->Sql_Query($comando,$valor);
                /*$query_somar = $this->Sql_Query($comando,$valor,false);
                if($query_somar!==false){
                    $query .= $query_somar;
                }*/
            }
            return true;
        }
        // Executa ou retorna query
        return ($executar_query)?$this->query($query,true):$query;
    }
    /***************
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */
    
    /**
     * 
     * @param type $Objeto
     * @return int
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Sql_Inserir(&$Objeto,$tempo=true,$retorna=false){
        if($tempo){
            $temponome  = 'Inserir';
            $tempo2   = new \Framework\App\Tempo($temponome);
        }
        if(is_object($Objeto)){
            $sql = 'Insert '.call_user_func(array($Objeto, 'Get_Nome')).' ';

            // Divide por Servidores
            if(call_user_func(array($Objeto, 'Get_StaticTable'))===false){
                $sql1 = '(servidor';
                $sql2 = '(\'' .SRV_NAME_SQL.'\'';
            }else{
                $sql1 = '';
                $sql2 = '';
            }

            //Add Log
            $Objeto->log_date_add = APP_HORA_BR;
            $Objeto->log_user_add = \Framework\App\Acl::Usuario_GetID_Static();
            $Objeto->deletado     = 0;
            // Pega Atributos
            $valores = $Objeto->Get_Object_Vars();
            
            // Faz query com valores 
            foreach ($valores as $indice =>&$valor){
                if($valor!==false){
                    // Transforma pra pasar pra mysql
                    $valor_add = $Objeto->bd_set($indice);
                    // SE for int nao precisa de aspas
                    if(!is_int($valor_add)){
                        $valor_add = '\'' .$valor_add.'\'';
                    }
                    
                    if($sql1=='')    $sql1 = '('   .$indice;
                    else            $sql1 .= ', '  .$indice;
                    if($sql2=='')    $sql2 = '(' .$valor_add;
                    else            $sql2 .= ', '.$valor_add;
                }
            }
            $sql .= $sql1.') VALUES '.$sql2.');';
            //echo $sql;
            if($retorna){
                return $sql;
            }else{
                return $this->query($sql);
            }
            
            return true;
        }else if(is_array($Objeto)){
            $sql = '';
            foreach($Objeto as &$valor){
                $sql .= $this->Sql_Inserir($valor,true,true);
            }
            if($retorna){
                return $sql;
            }else{
                return $this->multi_query($sql);
            }
            
            return true;
        }else{
            return false;
        }
    }
    /**
     * Pega um Objeto DAO ou um Array deles e deleta todos do banco de dados
     * 
     * @param Dao $Objeto
     * @param bolean $deletar (Caso verdadeiro, deleta do banco de dados, 
     * caso falso, apenas nao conta mais no sistema como existente
     * @return int
     * @throws Exception
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Sql_Delete(&$objetos,$deletar=false){
        if($objetos===false)    return false;
        if(is_object($objetos)) $objetos = Array($objetos);
        if(empty($objetos))     return false;
        if($deletar===false){
            foreach($objetos as &$Objeto){
                $Objeto->deletado       = 1;
                $Objeto->log_date_del   = APP_HORA_BR;
            }
            $this->Sql_Update($objetos,false);
        }else{
            foreach($objetos as &$Objeto){
                $valores = $Objeto->Get_Object_Vars();
                $sql = 'DELETE FROM  '.call_user_func(array($Objeto, 'Get_Nome'));
                $primarias = $Objeto->Get_Primaria();
                $contador = 0;
                if(empty($primarias)) throw new \Exception('Não Existe chave primaria: '.call_user_func(array($Objeto, 'Get_Nome')),3120);
                foreach($primarias as &$valor){
                    if($contador==0){
                        $sql .= ' WHERE ';
                    }else{
                        $sql .= ' AND ';
                    }
                    $sql .= $valor.'=\''.$Objeto->$valor.'\'';
                    ++$contador;
                }
                $sql .= '; ';
            }
            // Executa as Querys
            $this->multi_query($sql,true);
        }
        return true;
    }
    /**
     * @param type $Objeto
     * Array de Strings ou Objetos,
     * Objeto,
     * String formato = ClasseDAO|SET|WHERE;
     * 
     * @param bolean $log Indica se armazena Log ou nao !
     * @param bolean $tempo  Indica Se Armazena Tempo ou nao 
     * 
     * @return int
     * @throws Exception
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Sql_Update(&$Objeto, $log=true,$tempo=true,$retornar=false){
        if($tempo){
            $temponome  = 'Update';
            $tempo2   = new \Framework\App\Tempo($temponome);
        }
        $tempo = new \Framework\App\Tempo('Conexao_Update');  
        if(is_array($Objeto)){
            reset($Objeto);
            $sql = '';
            while (key($Objeto) !== null) {
                $sql .= $this->Sql_Update($Objeto[key($Objeto)],true,true,true);
                next($Objeto);
            }            
            // Executa ou retorna sql
            if($retornar){
                return $sql;
            }else{
                return $this->multi_query($sql,true);
            }
            return true;
        }else if(is_object($Objeto)){
            // Captura Variaveis do Objeto e Primarias
            $valores = $Objeto->Get_Object_Vars();
            $primarias = $Objeto->Get_Primaria();
            if(empty($primarias)) throw new \Exception('Não Existe chave primaria: '.call_user_func(array($Objeto, 'Get_Nome')),3120);
            // Começa Query
            $sql    = '';
            $i      = 0;
            $j      = 0;
            reset($valores);
            while (list($indice,$valor) = each($valores)) {
                if($valor!==false && strpos($indice, '2')===false && (array_search($indice, $primarias)===false ||  array_search($indice, $primarias)===NULL)){
                    if($sql!=''){
                        $sql .=  ', ';
                    }
                    $sql .= $indice.'=\''.$Objeto->bd_set($indice).'\'';
                    ++$i;
                }
                ++$j;
            }
            // SE nao tiver valores da erro, se nao tiver nenhum alterado nao faz update
            if($j==0) throw new \Exception('Falta Valores de SET no mysql',3130);
            if($i==0){ 
                return true;
            }
            
            $sql = 'UPDATE '.call_user_func(array($Objeto, 'Get_Nome')).' SET '.$sql.', log_date_edit=\''.APP_HORA.'\', log_user_edit=\''.\Framework\App\Acl::Usuario_GetID_Static().'\'';
            $contador = 0;
            reset($primarias);
            while (key($primarias) !== null) {
                $current = current($primarias);
                if($contador==0){
                    $sql .= ' WHERE ';
                    ++$contador;
                }else{
                    $sql .= ' AND ';
                }
                $sql .= $current.'=\''.$Objeto->$current.'\'';
                next($primarias);
            }
            // Executa query #update
            if($retornar){
                return $sql.';';
            }else{
                return $this->query($sql,true);
            }
            return true;
        }else{
            $edicao = explode('|', $Objeto);
            
            $sql = 'UPDATE '.call_user_func(array($edicao[0].'_DAO', 'Get_Nome')).' SET '.$edicao[1].', log_date_edit=\''.APP_HORA.'\', log_user_edit=\''.\Framework\App\Acl::Usuario_GetID_Static().'\' WHERE '.$edicao[2];
            if($retornar){
                return $sql.';';
            }else{
                return $this->query($sql,true);
            }
        }
    }
    private function Sql_Select_Comeco($class_dao,$campos){
        $campos_todos = false;
        
        // Campos Usados da Principal e das Extrangeiras
        $principal = Array();
        $extrangeiras = Array();
        
        // Trata Campos se forem pedidos
        if($campos!==false && $campos!==true && $campos!=='*'){
            $campos = explode(',', $campos);
            foreach($campos as $valor){
                if($valor==='*'){
                    $campos_todos = true;
                }else if(strripos($valor, '.')===false){
                    $principal[$valor] = true;
                }else{
                    $quebrado = explode('.', $valor);
                    if(!isset($extrangeiras[$quebrado[0]])){
                        $extrangeiras[$quebrado[0]] = Array();
                    }
                    $extrangeiras[$quebrado[0]][$quebrado[1]] = true;
                }
            }
        }else{
            $campos_todos = true;
        }
        
        
        ///////////////////
        //
        // COMEÇA CACHE, USANDO CLASS e campos
        //
        //
                            
        // CArrega Variaveis Basicas
        $sql            = (string) 'SELECT ';
        $sql_tabela     = (string) '';
        $sql_condicao   = (string) '';
        
        // Para extrangeiras
        $tabelas_extrangeiras = Array();
        $join     = '';
        $ext_campos      = '';
        $cont = 0;
        
        // Todas as tabelas usadas ficam registradas aqui para controle
        $tabelas_usadas = Array();
        
        // Carrega Objeto e Asvariaveis dele
        $resultado_unico = new $class_dao;
        $valores = $resultado_unico->Get_Object_Vars();
        $extrangeira    = $resultado_unico->Get_Extrangeiras();
        
        // Escolhe Tabela
        $sql_tabela_nome    = call_user_func(array($class_dao, 'Get_Nome'));
        $sql_tabela_sigla   = call_user_func(array($class_dao, 'Get_Sigla'));
        $sql_tabela   = $sql_tabela_nome.' '.$sql_tabela_sigla;
        $tabelas_usadas[$sql_tabela_sigla] = $sql_tabela_nome;
        
        
        
        // Trata os Campos das Extrangeiras da Tabela do Select        
        if($extrangeira!==false && (!empty($extrangeiras) || $campos_todos===true)){
            foreach($extrangeira as &$valor){
                if(strpos($valor['titulo'], '[]')===false ){
                    
                    // Se nao tiver autorizado nem chama
                    if($campos_todos===false && (empty($extrangeiras) || !isset($extrangeiras[$valor['titulo']]) || $extrangeiras[$valor['titulo']]===false || empty($extrangeiras[$valor['titulo']]))) continue;

                    // Faz Tratamento de Extrangeira
                    list($ligacao,$mostrar,$extcondicao) = $this->Extrangeiras_Quebra($valor['conect']);
                    
                    // Lista Sigla a ser usada e a tabela
                    $tabela = self::Tabelas_GetSiglas_Recolher($ligacao[0]);
                    $sigla = 'EXT'.\Framework\App\Sistema_Funcoes::Letra_Aleatoria($cont);
                    
                    // SE tabela for vazio retorna erro
                    if(!is_array($tabela)) throw new \Exception('Tabela nao retornou nada: '.$ligacao[0],3250);
                    
                    //Faz o Campo de Procura
                    if($cont!=0){
                        $ext_campos .= ', ';
                    }
                    // Trata se Tiver varias opcoes
                    if(is_array($mostrar[0])){
                        $nomedacoluna = $mostrar[0][1];
                        //$ext_campos .= $sigla.'.'.$nomedacoluna.' AS '.$valor['titulo'].'2';
                        $ext_campos .= '('.
                        'CASE ';
                        reset($mostrar);
                        while (key($mostrar) !== null) {
                            $current = current($mostrar);
                            $ext_campos .= 'WHEN '.$sigla.'.'.$current[1].' != \'\' THEN '.$sigla.'.'.$current[1].' ';
                            next($mostrar);
                        }
                        $ext_campos .= 'ELSE 1 END) AS '.$valor['titulo'].'2';
                        
                        // Se pedir campo extra, acrescenta
                        if(isset($extrangeiras[$valor['titulo']])){
                            foreach($extrangeiras[$valor['titulo']] as $indice2=>$valor2){
                                if($valor2===true){
                                    $ext_campos .= $sigla.'.'.$indice2;
                                }
                            }
                            
                        }
                        
                        
                        
                        /**
                         * , 
    (
    CASE 
        WHEN qty_1 <= '23' THEN price
        WHEN '23' > qty_1 && qty_2 <= '23' THEN price_2
        WHEN '23' > qty_2 && qty_3 <= '23' THEN price_3
        WHEN '23' > qty_3 THEN price_4
        ELSE 1
    END) AS total
                         */
                    }else{
                        $ext_campos .= $sigla.'.'.$mostrar[1].' AS '.$valor['titulo'].'2';
                    }
                    // Escreve as Tabelas
                    $join .= ' LEFT JOIN '.$tabela['tabela'].' AS '.$sigla;
                    $tabelas_usadas[$sigla] = $tabela['tabela'];
                    $join  .= ' ON '.$sql_tabela_sigla.'.'.$valor['titulo'].' = '.$sigla.'.'.$ligacao[1];
                    ++$cont;
                }
            }
        }
        
        /**
         * FAZ UM WHILE E COLOCA CADA UM DOS VALORES PARA SER MOSTRADO NO SELECT
         */
        // Continua
        $i = 0;
        while (list($indice,$valor) = each($valores)){    
            
            // Se nao tiver que colocar nao coloca
            if($campos_todos===false && (empty($principal) || !isset($principal[$indice]) || $principal[$indice]===false)) continue;
            
            if($i==0){
                $sql .= $sql_tabela_sigla.'.'.$indice;
            }else{
                $sql .= ','.$sql_tabela_sigla.'.'.$indice;
            }
            ++$i;
        }
        // Remove Arrays
        $sql = str_replace('[]', '', $sql);
        // Continua
        $numero_de_campos_nativos = $i;
        // WHERE  -> Adiciona Servidor se Tabela nao for estatica
        $j = 0;
        if(call_user_func(array($class_dao, 'Get_StaticTable'))===false){
            $sql_condicao .= $sql_tabela_sigla.'.servidor = \''.SRV_NAME_SQL.'\'';
            ++$j;
        }
        // Nao deletado
        if($j!=0) $sql_condicao .= ' AND ';
        $sql_condicao .= $sql_tabela_sigla.'.deletado != \'1\'';
        ++$j;
        
        // Adiciona Virgula caso tenha variaiveis a buscar 
        if($numero_de_campos_nativos!=0 && $ext_campos!=''){
            $sql.= ', ';
        }
        $sql .= $ext_campos.' FROM '.$sql_tabela.$join;
        return Array($sql,$sql_tabela_sigla,$sql_condicao,$valores,$tabelas_usadas,$j);
    }
    public function Sql_Contar($class_dao, $where = false){
        $tempo = new \Framework\App\Tempo('Conexao_Contar');   
        // Expections
        if($class_dao==''){
            throw new \Exception('Dao não existe: '.$class_dao,3251);
        }else{
            $class_dao = $class_dao.'_DAO';
        }
        // Captura Nome da Tabela
        $sql_tabela_nome    = call_user_func(array($class_dao, 'Get_Nome'));
        
        // Ve se tem servidor
        $condicao = '';
        if(call_user_func(array($class_dao, 'Get_StaticTable'))===false){
            $condicao .= ' && servidor = \''.SRV_NAME_SQL.'\'';
        }
        
        // Se pedir mais condicoes acrescenta
        if($where!==false){
            $condicao .= ' && ('.$where.')';
        }
        
        // Faz Contas
        $query_result = $this->query('SELECT COUNT(*) as total FROM '.$sql_tabela_nome.' WHERE deletado=0'.$condicao);
        
        $row = $query_result->fetch_object();
        return $row->total;
    }
    /**
     * 
     * @param type $class_dao
     * @param type $condicao
     * @param type $limit
     * @param type $campos * todos ou campos separados por ,
     * @param type $tempo = Medicao de TEmpo
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     * 
     * 
     * $condicao = Array(
     *      $indice             => $valor,
     *      '!'.$indice2        => $valor2,
     *      Array(  // arrays de 2 nivel é para OR
     *          $indice3        => $valor3,
     *          '!'.$indice4    => $valor4
     *      )
     * );    WHERE servidor='' 
     *       and $indice=$valor 
     *       and $indice2!=$valor2 and ($indice3=$valor3 OR $indice4!=$valor4);
     * 
     */
    public function Sql_Select($class_dao,$condicao = false,$limit = 0, $order_by='', $campos = '*', $tempo = true){
        if($limit===false) $limit = 0;
        /*if($tempo){
            $temponome  = $class_dao.' - '.  serialize($condicao).$limit.$order_by;
            $tempo2   = new \Framework\App\Tempo('Select Completo');
        }*/
        //echo "\n\n\n".$class_dao;
        $tempo = new \Framework\App\Tempo('Conexao_Select');   
        // Expections
        if($class_dao==''){
            throw new \Exception('Dao não existe: '.$class_dao,3251);
        }
        $j = 0;
        // CArrega Variaveis FIXAS
        $maior      = ' > ';
        $menor      = ' < ';
        $maiorigual = ' >= ';
        $menorigual = ' <= ';
        $class_dao      = $class_dao.'_DAO';
        
        // Carrega Cache
        $Cache = $this->_Cache->Ler('Select-'.$class_dao.$campos);
        if (!$Cache) {
            $Cache = $this->Sql_Select_Comeco($class_dao, $campos);
            list($sql,$sql_tabela_sigla,$sql_condicao,$valores,$tabelas_usadas,$j) = $Cache;
            $this->_Cache->Salvar('Select-'.$class_dao.$campos, $Cache);
        }else{
            list($sql,$sql_tabela_sigla,$sql_condicao,$valores,$tabelas_usadas,$j) = $Cache;
        }
        
        // Roda todas as condicoes afim de nao trazer nada a mais..
        //if($tempo){
            //$condicaotempo = new \Framework\App\Tempo('Select Condicao');
        //}
        if(is_array($condicao)){
            foreach($condicao as $indice => &$valor){
                // SE for array dentro de array entro usa OR
                if(is_array($valor) && strpos($indice, 'IN')===false && strpos($indice, 'NOTIN')===false && (!empty($indice)|| $indice==0)){
                    if($j!=0) $sql_condicao .= ' AND';
                    $sql_condicao .= ' ( ';
                    $i = 0;
                    foreach($valor as $indice2 => &$valor2){
                        $indice2 = \anti_injection($indice2);
                        $valor2 = \anti_injection($valor2);
                        // busca ?
                        if(strpos($valor2, '%')===0){
                            $igual      = ' LIKE ';
                            $diferente  = ' NOT LIKE ';
                        }else{
                            $igual      = ' = ';
                            $diferente  = ' != ';
                        }
                        if($i!=0) $sql_condicao .= ' OR';  
                        // Caso de NOT IN (Multiplos Valores)
                        if(strpos($indice2, 'NOTIN')===0){
                            $indice2 = substr($indice2, 5);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.' NOT IN ('.implode(',',$valor2).')';
                        }else 
                        // Para Busca Diferente
                        if(strpos($indice2, 'IN')===0){
                            $indice2 = substr($indice2, 2);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.' IN ('.implode(',',$valor2).')';
                        }else 
                        // Maior igual que
                        if(strpos($indice2, '>=')===0){
                            $indice2 = substr($indice2, 2);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$maiorigual.'\''.$valor2.'\'';
                        }else if(strpos($indice2, '<=')===0){
                            $indice2 = substr($indice2, 2);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$menorigual.'\''.$valor2.'\'';
                        }else if(strpos($indice2, '>')===0){
                            $indice2 = substr($indice2, 1);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$maior.'\''.$valor2.'\'';
                        }else if(strpos($indice2, '<')===0){
                            $indice2 = substr($indice2, 1);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$menor.'\''.$valor2.'\'';
                        }else if(strpos($indice2, '!')===0){
                            $indice2 = substr($indice2, 1);
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$diferente.'\''.$valor2.'\'';
                        }else{
                        // Para Busca IGUAL
                            $sql_condicao .= ' ';
                            // Verifica se nao é extrangeira
                            if(strpos($indice2, '.')===false){
                                $sql_condicao .= $sql_tabela_sigla.'.';
                            }
                            $sql_condicao .= $indice2.$igual.'\''.$valor2.'\'';
                        }
                        ++$i;
                    }
                    if($i<=1)  throw new \Exception('Query Select contém apenas 1 campo em condição OR:'.$erro,3121);
                    $sql_condicao .= ' )';
                }else
                // SE nao for Array simples é usado AND
                if((!empty($valor)|| $valor==0) && (!empty($indice)|| $indice==0)){
                    $indice = \anti_injection($indice);
                    $valor = \anti_injection($valor);
                    // busca ?
                    if(!is_array($valor)){
                        if(strpos($valor, '%')===0){
                            $igual      = ' LIKE ';
                            $diferente  = ' !LIKE ';
                        }else{
                            $igual      = ' = ';
                            $diferente  = ' != ';
                        }
                    }
                    
                    // controle
                    // Caso de NOT IN (Multiplos Valores)
                    if(strpos($indice, 'NOTIN')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        $indice = substr($indice, 5);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.' NOT IN ('.implode(',',$valor).')';
                    }else 
                    // Caso de IN (Multiplos Valores)
                    if(strpos($indice, 'IN')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        $indice = substr($indice, 2);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.' IN ('.implode(',',$valor).')';
                    }else 
                    // Caso de where ser Diferente 
                    if(strpos($indice, '>=')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        // Completa
                        $indice = substr($indice, 2);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.$maiorigual.'\''.$valor.'\'';
                    }else 
                    // Caso de where ser Diferente 
                    if(strpos($indice, '<=')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        // Completa
                        $indice = substr($indice, 2);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.$menorigual.'\''.$valor.'\'';
                    }else 
                    // Caso de where ser Diferente 
                    if(strpos($indice, '>')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        // Completa
                        $indice = substr($indice, 1);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.$maior.'\''.$valor.'\'';
                    }else 
                    // Caso de where ser Diferente 
                    if(strpos($indice, '<')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        // Completa
                        $indice = substr($indice, 1);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.$menor.'\''.$valor.'\'';
                    }else 
                    // Caso de where ser Diferente 
                    if(strpos($indice, '!')===0){
                        if($j!=0) $sql_condicao .= ' AND';
                        // Completa
                        $indice = substr($indice, 1);
                        $sql_condicao .= ' ';
                        // Verifica se nao é extrangeira
                        if(strpos($indice, '.')===false){
                            $sql_condicao .= $sql_tabela_sigla.'.';
                        }
                        $sql_condicao .= $indice.$diferente.'\''.$valor.'\'';
                    }else{
                        if($j!=0) $sql_condicao .= ' AND';
                        // Coloca Sigla se nao tiver
                        if(strpos($indice, '.')===false){
                            $indice = $sql_tabela_sigla.'.'.$indice;
                        }else{
                            $sigla = explode('.', $indice);
                            $sigla = $sigla[0];
                            // Caso Sigla nao esteja na query, importa para ela !
                            if(!isset($tabelas_usadas[$sigla])){
                                $link = self::Tabelas_GetLinks_Recolher($sql_tabela_sigla);
                                if(!isset($link[$sigla])){
                                    throw new \Exception('Tabela não esta na query: '.$sigla,3121);
                                }else{
                                    // Escreve as Tabelas
                                    $tabela = self::Tabelas_GetSiglas_Recolher($sigla);
                                    $sql .= ' LEFT JOIN '.$tabela['tabela'].' AS '.$sigla;
                                    $tabelas_usadas[$sigla] = $tabela['tabela'];
                                    //Faz o Campo de Procura
                                    $sql  .= ' ON '.$sql_tabela_sigla.'.id = '.$sigla.'.'.$link[$sigla];
                                }
                            }
                        }
                        // Completa
                        $sql_condicao .= ' '.$indice.$igual    .'\''.$valor.'\'';
                    }
                }
                ++$j;
            }
        }else if(strpos($condicao, '{sigla}')!==false){
            if($sql_condicao!='') $sql_condicao .= ' AND ';
            $sql_condicao .= str_replace('{sigla}', $sql_tabela_sigla.'.', $condicao);
        }else if($condicao!==false){
            if($sql_condicao!='') $sql_condicao .= ' AND ';
            $sql_condicao .= (string) $condicao;
        }        
        
        // CHAMA TABELA E MONTA A QUERY
        if($sql_condicao!=''){
            $sql .= ' WHERE '.$sql_condicao;
        }
        // ORDENA
        if($order_by!=''){
            $sql .= ' ORDER BY '.$order_by;
        }
        // FAZ LIMITE
        if($limit!=0){
            $sql .= ' LIMIT '.$limit;
        }
        
        
        // Executa query
        // 
        //echo $sql."<br><br>\n\n\n";
        if($tempo){
            unset($condicaotempo);
        }
        //echo "\n\n<br><BR>".$sql;
        $query_result = $this->query($sql,true);
        if($tempo){
            $imprimir4 = new \Framework\App\Tempo('Select Manipulacao');
        }
        // Guarda o Resultado e VOLTA pro cliente
        $contador = 0;
        $resultado = Array();
        
        
        // Puxa Tudo de Uma vez Só
        //var_dump($query_result->fetch_all(MYSQLI_ASSOC));
        
        $start_memory = memory_get_usage();
        // Puxa Resultado a Resultado e o transforma em um objeto
        while ($campo = $query_result->fetch_object()) {
            $resultado[] = new $class_dao;
            $this->Sql_Manipulacao_CarregarObjeto($resultado[$contador], $campo, $valores);
            ++$contador;
        }
        //echo "\n<br>Usado:".(round((memory_get_usage() - $start_memory)/1024/1024,4)).'MB';
        
        if($contador==0)        return false;
        else if($contador==1)   return $resultado[0];
        else                    return $resultado;
    }
    /**
     * #update -> if(isset nao faz sentido)
     * @param type $objeto
     * @param type $campo
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.2
     * 
     * Como é usado dentro de varios looping, nao faz sentido executar $objeto->Get_Object_Vars()
     * toda hora, entao ja o vem por refenrencia
     * 
     */
    private function Sql_Manipulacao_CarregarObjeto(&$objeto, &$campo, &$valores1){
        // Carrega Variaveis
        //$valores1 = $objeto->Get_Object_Vars();
        $valores2 = get_object_vars($campo);
        reset($valores2);
        while (key($valores2) !== null) {
            /*if(isset($valores1[key($valores2)])){
                $objeto->bd_get(key($valores2),current($valores2));
            }else{
                // Cria Campo*/
                $objeto->bd_get(key($valores2),current($valores2));
            //}
            next($valores2);
        }
    }
    /**
     * CAmpos Linkados, pega Tabela
     * 
     * Boleano MUltiplo
     * Se tiver valor padrao ele pega os selecionados, se nao todos deselecionados
     * 
     * @param type $objeto
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     * 
     * #UPDATE FAZER VALOR_PADRAO PARA PEGAR DE EDICAO
     */
    public function Tabelas_CapturaLinkadas(&$objeto){
        $capturatabela = function(&$limpar){
            if(empty($limpar)){
                return false;
            }else{
                foreach($limpar as $indice=>&$valor){
                    if($indice!='Array'){ 
                        return Array($indice,$valor);
                    }else if(is_array($valor)){
                        return $valor;
                    }
                }
            }
            return false;
        };
        if($objeto['formtipo']=='SelectMultiplo'){
            $select = &$objeto['SelectMultiplo'];
            
            // Captura REsultado da Extrangeira
            $resultado = $this->Tabelas_CapturaExtrangeiras($select['Extrangeira']);
            
            // Pega Tabela LINK
            $tabela_link = self::Tabelas_GetSiglas_Recolher($objeto['Tabela']);
            
            // Captura Colunas da tabela
            $class_Executar = $tabela_link['classe'].'_DAO';
            // Escolhe os Selecionados
            $selecionado    = Array();
            $where          = Array();
            
            // PEga Colunas
            $colunas = $class_Executar::Gerar_Colunas();
            $colunas_retirar = Array();
            
            // VErifica se tem Preencehr
            if(isset($objeto['Preencher']) && $objeto['Preencher']!==false){
                foreach($objeto['Preencher'] as $indice3=>&$valor3){
                    // Acrescenta ao SElect
                    $where[$indice3] = $valor3;
                    // Remove de Colunas Extras
                    $colunas_retirar[] = $indice3;
                }
            }
            
            foreach ($colunas as $indice=>&$valor){
                if($valor["mysql_titulo"]==$select["Linkar"] || $valor["mysql_titulo"]==$select["Linkado"] || in_array($valor["mysql_titulo"], $colunas_retirar)){
                    unset($colunas[$indice]);
                }
            }
            
            
            // SE tiver vazio é false
            if(empty($colunas)){
                $colunas = false;
            }
            
            // BUsca Selecionados
            if(is_numeric($objeto['valor_padrao'])){
                // Condicao
                $where[$select['Linkar']] = $objeto['valor_padrao'];
                $opcoes = $this->Sql_Select($tabela_link['classe'], $where);
                if(is_object($opcoes)) $opcoes = Array($opcoes);
            }else{
                $opcoes = false;
            }
            // Caso tenha resultado
            if($opcoes!==false){
                // Captura Selects da Chave Extrangeira
                foreach ($opcoes as &$valor){
                        $selecionado[] = $valor->$select['Linkado'];
                }
            }
            // Retorna Array Todos, Selecionados e COlunas a mais
            return Array($selecionado,$resultado, $colunas);
            
        }else 
            
            
        /**
         *  Boleanos Multiplos
         */
        // zera opcoes
        $opcoes = false;
        if($objeto['formtipo']=='BoleanoMultiplo'){
            $boleanomultiplo    = &$objeto['BoleanoMultiplo'];
            $ovalor             = &$boleanomultiplo['Valor'];
            // Pega Tabela LINK
            $linkado = self::Tabelas_GetLinks_Recolher($objeto['Tabela'],true  );
            if(!array_key_exists($objeto['Pai'], $linkado)){ 
                return false;
            }
            // Caso Tenha Grupo faz a conexao
            $selecionado        = Array();
            $nao_selecionado    = Array();
            // Caso seja EDICAO
            // Pela Sigla pega a tabela
            $tabela_utilizada = self::Tabelas_GetSiglas_Recolher($objeto['Tabela']);
            if($objeto['valor_padrao']!==false){
                
                
                // Condicao da Query
                if($ovalor!==false){
                    // CAso tenha campo de alteracao, esse campo tem que estar positivo
                    // e ter o valor padrao do pai incluido
                    $where = Array(
                        $linkado[$objeto['Pai']]    =>  $objeto['valor_padrao'],
                        $ovalor                     =>  '1',
                    );
                }else{
                    // Nao tem campo de alteracao, somente o valor do pai
                    $where = Array(
                        $linkado[$objeto['Pai']]    =>  $objeto['valor_padrao'],
                    ); 
                }
                
                // Recupera os Selecionados
                $opcoes = $this->Sql_Select($tabela_utilizada['classe'], $where);
                if(is_object($opcoes)) $opcoes = Array($opcoes);
                if(empty($opcoes)) $opcoes = false;
            }
            
            
            // Deleta Tabela Usada
            unset($linkado[$objeto['Pai']]);
            $temp = $capturatabela($linkado);
            if($temp===false ){ 
                return false;
            }
            // Pega tabela e campo
            list($tabela_secundaria,$campo_secundaria) = $temp;

            // Caso Secundaria seja array, entao nao tem a 3 tabela
            if(is_array($campo_secundaria)){
                $campo_secundaria2  = $campo_secundaria;
                $tipo = 'Array';
            }else{
                $campo_secundaria2  = $campo_secundaria.'2';
                $tipo = 'Tabela';
            }
            // PEga Classe para Percorrer valores do selectmultiplo selecionados
            $classe_dao = $tabela_utilizada['classe'].'_DAO';
            // Guarda OS SELECIONADOS
            if($opcoes!==false){
                foreach($opcoes as &$valor){
                    if($tipo==='Tabela'){
                        $selecionado[$valor->$campo_secundaria] = $valor->$campo_secundaria2;
                    }else{
                        $selecionado[$valor->$tabela_secundaria] = $classe_dao::Mod_Acesso_Get_Nome($valor->$tabela_secundaria);
                    }
                }
            }
            
            /*******************************
             * NAO SELECIONADOS
             */
            // Depende do tipo
            if($tipo==='Tabela'){
                // Pela Sigla pega a tabela
                $tabela_utilizada_secundaria = self::Tabelas_GetSiglas_Recolher($tabela_secundaria);
                $where = Array();
                // BUsca Resultados
                $opcoes = $this->Sql_Select($tabela_utilizada_secundaria['classe'], $where);
                if(is_object($opcoes)) $opcoes = Array($opcoes);
                // Guarda Opcoes
                if(!empty($opcoes)){
                    foreach($opcoes as &$valor){
                        $nao_selecionado[$valor->$boleanomultiplo['campoid']] = $valor->$boleanomultiplo['campomostrar'];
                    }
                }
            }else if($tipo=='Array'){
                // Guarda Opcoes
                if(!empty($campo_secundaria)){
                    foreach($campo_secundaria as &$valor){
                        $nao_selecionado[$valor] = $classe_dao::Mod_Acesso_Get_Nome($valor);
                    }
                }
            }
            $nao_selecionado = array_diff($nao_selecionado, $selecionado);
            // Retorna Array Selecionado e Nao
            return Array($selecionado,$nao_selecionado);
        }
        return false;
    }
    
    
    
    /**
     * Captura todos os lances da chave extrangeira pra colocar no Formulario 
     * dentro de um select
     * @param type $objeto
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Tabelas_CapturaExtrangeiras(&$objeto){
        
        // Captura Tipo e armazena extrangeira
        if(isset($objeto['mysql_estrangeira']) && is_array($objeto) && strlen($objeto['mysql_estrangeira'])>2){
            $extrangeira = $objeto['mysql_estrangeira'];
            $tipo = 'objeto';
        }else{
            $extrangeira = $objeto;
            $tipo = 'condicao';
        }
        
        // Quebra a extrangeira para utiliza-lo
        list($ligacao,$mostrar,$condicao) = $this->Extrangeiras_Quebra($extrangeira);
        
        // Captura a Tabela a ser Utilizada
        $tabela_utilizada = self::Tabelas_GetSiglas_Recolher($ligacao[0]);
        if(!isset($tabela_utilizada['classe'])){
            throw new \Exception('Faltando dados no dao ou tabelas extrangeiras'.$ligacao[0],3250);
        }
        
        // Cria Condicoes para Achar as extrangeiras
        /*if($tipo=='objeto'){
            $where = Array($ligacao[1]=>$objeto['edicao']['valor_padrao']);
        }else{*/
            $where = Array();
        //}
        
        // SE nao tiver identificador do valor padrao, remove
        //if(!isset($where[$ligacao[1]]) || $where[$ligacao[1]]=='' || !is_string($where[$ligacao[1]])){
            //$where = false;
        //}
        
        // Add condição
        if($condicao!==false){
        
            // Trata condicao
            if(is_array($condicao[0])){
                foreach ($condicao as &$valor_cond){
                    if(strpos($valor_cond[1], '.')===false){
                        $where[$valor_cond[0]] = $valor_cond[1];
                    }
                }
            }else{
                if(strpos($condicao[1], '.')===false){
                    $where[$condicao[0]] = $condicao[1];
                }
            }

        
        
        
        }
        
        // Trata Mostrar
        if(is_array($mostrar[0])){
            $orderby = &$mostrar[0][1];
        }else{
            $orderby = &$mostrar[1];
        }
        
        // Procura
        $opcoes = $this->Sql_Select($tabela_utilizada['classe'], $where,0, $orderby);
        //Guarda Resultados
        $resultado = Array();
        if($opcoes!==false && !empty($opcoes)){
            if(is_object($opcoes)) $opcoes = Array(0=>$opcoes);
            reset($opcoes);
            while (key($opcoes) !== null) {
                $ob = current($opcoes);
                if(is_array($mostrar[0])){
                    reset($mostrar);
                    while (key($mostrar) !== null) {
                        $valor = current($mostrar);
                        $valor = $valor[1];
                        // Pega e Armazena por referencia
                        if($ob->$valor!=''){
                            $resultado[$ob->$ligacao[1]]       = $ob->$valor;
                        }
                        next($mostrar);
                    }
                }else{
                    $resultado[$ob->$ligacao[1]]       = $ob->$orderby;
                }
                next($opcoes);
            }
        }
        return $resultado;
    }
    /**
     * Quebra Parametro das Extrangeiras
     * @param type $objeto
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public function Extrangeiras_Quebra($extrangeiro){
        // Divide string e recupera valores importantes
        $extrangeiro    = explode('|', $extrangeiro);
        $ligacao        = explode('.', $extrangeiro[0]);
        $mostrar        = Array();
        if(strpos($extrangeiro[1], '-')!==false){
            $mostrar_foreach        = explode('-', $extrangeiro[1]);
            reset($mostrar_foreach);
            while (key($mostrar_foreach) !== null) {
                // Pega e Armazena por referencia
                $mostrar[] = explode('.', current($mostrar_foreach));
                next($mostrar_foreach);
            }
        }else{
            $mostrar        = explode('.', $extrangeiro[1]);
        }
        if(count($extrangeiro)>=3){
            if(strpos($extrangeiro[2], '-')!==false){
                $mostrar_foreach        = explode('-', $extrangeiro[2]);
                reset($mostrar_foreach);
                while (key($mostrar_foreach) !== null) {
                    // Pega e Armazena por referencia
                    $condicao[] = explode('=', current($mostrar_foreach));
                    next($mostrar_foreach);
                }
            }else{
                $condicao        = explode('=', $extrangeiro[2]);
            }
        }else{
            $condicao       = false;
        }
        return Array($ligacao,$mostrar,$condicao);
    }
    /**
     * 
     * @param type $sigla
     * @return boolean
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    private static function Tabelas_Variaveis_Gerar(){
        $tempo = new \Framework\App\Tempo('Conexao - Processar Tabelas Gerar Variaveis');
        $tabelas        = &self::$tabelas;
        $siglas         = &self::$tabelas_siglas;
        $links          = &self::$tabelas_Links;
        $links2         = &self::$tabelas_Links_Invertido;
        reset($tabelas);
        while (key($tabelas) !== null) {
            // Pega e Armazena por referencia
            $current = current($tabelas);
            $Link = &$current['Link'];
            $sigla = &$current['sigla'];
            
            $siglas[$sigla] = Array(
                'tabela'    => key($tabelas),
                'classe'    => $current['class']
            );
            if($Link!==false){
                if(!empty($Link) && is_array($Link)){
                    reset($Link);
                    while (key($Link) !== null) {
                        $current2 = current($Link);
                        // SE nome do Indice for Array, nao tem a 3 tabela
                        if(key($Link)!='Array'){
                            $links[key($Link)][$sigla]         = $current2;
                            $links2[$sigla][key($Link)]        = $current2;
                        }else{
                            $links2[$sigla][$current2[0]]      = $current2[1];
                        }
                        next($Link);
                    }
                }
            }
            next($tabelas);
        }
    }
    /**
     * Entra com a sigla e retorna um Array(
     *      [tabela] => Nome da Tabela
     *      [classe] => Nome da Classe
     * );
     * @param type $sigla
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public static function &Tabelas_GetSiglas_Recolher($sigla){
        return self::$tabelas_siglas[$sigla];
    }
    /**
     * 
     * @param type $sigla
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public static function &Tabelas_GetCampos_Recolher($sigla){
        $array = \Framework\App\Conexao::Tabelas_GetSiglas_Recolher($sigla);
        return self::$tabelas[$array['tabela']]['colunas'];
    }
    /**
     * 
     * @param type $sigla
     * @param type $invertido
     * @param type $sigla
     * @param type $sigla
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public static function Tabelas_GetLinks_Recolher($sigla,$invertido=false){
        if($invertido){
            if(isset(self::$tabelas_Links_Invertido[$sigla])){
                return self::$tabelas_Links_Invertido[$sigla];
            }
        }else{
            if(isset(Conexao::$tabelas_Links[$sigla])){
                return self::$tabelas_Links[$sigla];
            }
        }
        return false;
    }
    /**
     * 
     * @param type $class
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    public static function Load($class,$nome=false){
        if($nome===false){
            $nome = $class::Get_Nome();
        }
        $tab = Array (
            'class'     => $class::Get_Class(),
            'nome'      => $nome,
            'sigla'     => $class::Get_Sigla(),
            'colunas'   => $class::Get_Colunas(),
            'engine'    => $class::Get_Engine(),
            'charset'   => $class::Get_Charset(),
            'autoadd'   => $class::Get_Autoadd(),
            'Link'      => $class::Get_LinkTable(),
            'static'    => $class::Get_StaticTable(),
        );
        return $tab;
    }
    static function GetSigla($classe){
        $tabelas        = &self::$tabelas;
        return $tabelas[$classe]['sigla'];
    }
    /**
     * 
     * @return type
     * 
     * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
     * @version 0.0.1
     */
    protected function Tabelas(){
        $tabelas        = &self::$tabelas;
        $tabelas_ext    = &self::$tabelas_ext;
        if(defined('TEMP_DEPENDENCIA_DAO')){
            $arquivos = unserialize(TEMP_DEPENDENCIA_DAO);
            var_dump($arquivos);
            if(!empty($arquivos)){
                foreach($arquivos as $arquivo){
                    $arquivo = $arquivo.'_DAO';
                    $arquivo_nome           = $arquivo::Get_Nome();
                    $tabelas[$arquivo_nome] = self::Load($arquivo,$arquivo_nome);
                    $sigla = &$tabelas[$arquivo_nome]['sigla'];

                    // Aproveita o while e Pega as extrangeiras
                    $resultado_unico = new $arquivo();
                    $extrangeira    = $resultado_unico->Get_Extrangeiras();
                    if($extrangeira!==false){
                        reset($extrangeira);
                        while (key($extrangeira) !== null) {
                            $current = current($extrangeira);
                            list($ligacao,$mostrar,$extcondicao) = $this->Extrangeiras_Quebra($current['conect']);

                            // ARMAZENA NA VARIAVEL DE CONTROLE AS SIGLAS
                            $tabelas_ext[$ligacao[0]][$sigla] = $sigla;
                            next($extrangeira);
                        }
                    }
                }
            }
        }else{
            $tempo = new \Framework\App\Tempo('Conexao - Processar Tabelas');
            // Carrega Todos os DAO
            $diretorio = dir(DAO_PATH);
            // Percorre Diretório
            while($arquivo = $diretorio -> read()){
                if(strpos($arquivo, 'DAO.php')!==false){
                    $arquivo                = str_replace(Array('.php','.'), Array('','_') , $arquivo);
                    $arquivo_nome           = $arquivo::Get_Nome();
                    $tabelas[$arquivo_nome] = self::Load($arquivo,$arquivo_nome);
                    $sigla = &$tabelas[$arquivo_nome]['sigla'];

                    // Aproveita o while e Pega as extrangeiras
                    $resultado_unico = new $arquivo();
                    $extrangeira    = $resultado_unico->Get_Extrangeiras();
                    if($extrangeira!==false){
                        reset($extrangeira);
                        while (key($extrangeira) !== null) {
                            $current = current($extrangeira);
                            list($ligacao,$mostrar,$extcondicao) = $this->Extrangeiras_Quebra($current['conect']);

                            // ARMAZENA NA VARIAVEL DE CONTROLE AS SIGLAS
                            $tabelas_ext[$ligacao[0]][$sigla] = $sigla;
                            next($extrangeira);
                        }
                    }
                }
            }
            $diretorio -> close();
        }
    }
    /**
    * Retorna Subcategorias que sao de outras tabelas
    * 
    * @name Categorias_RetornaSub
    * @access public
    * 
    * @param int $categoria Id da Categoria Pai
    * @param string $subtab Nome da Tabela
    * @param Array $acesso
    * 
    * @uses \Framework\App\Modelo::$db
    * @uses \Framework\App\Modelo::$usuario
    * 
    * @return Array $array
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    private function logurl(){
        global $_SERVER;
        $idusuario = \Framework\App\Acl::Usuario_GetID_Static();
        $cliente_navegador      = $_SERVER["HTTP_USER_AGENT"];
        $cliente_ip             = $_SERVER['REMOTE_ADDR'];
        $server_post            = serialize($_POST);
        $server_get             = serialize($_GET);
        $server_requisitado     = $_SERVER['HTTP_HOST'];
        $server_scriptname      = $_SERVER['SCRIPT_FILENAME'];
        $this->query('INSERT INTO '.MYSQL_LOGURL.' (servidor,ip,cliente_pc,url,server_get,server_post,user,tempo,cfg_localscript,log_date_add) VALUES (\''.$server_requisitado.'\',\''.$cliente_ip.'\',\''.$cliente_navegador.'\',\''.SERVER_URL.'\',\''.$server_get.'\',\''.$server_post.'\',\''.$idusuario.'\',\''.(microtime(true)-TEMPO_COMECO).'\',\''.$server_scriptname.'\',\''.APP_HORA.'\')');
    }
    /**
    * Destruidor
    * 
    * @name __destruct
    * @access public
     * 
    * @uses mysqli::$close
    * 
    * @return void
    * 
    * @author Ricardo Rebello Sierra <web@ricardosierra.com.br>
    * @version 2.0
    */
    public function __destruct()
    {
        $this->logurl();
        $this->mysqli->close();
    }
}
/**
 * 
 * INSERT INTO %tabela% (%colunas%) VALUES (%valores%)
 * INSERT INTO %tabela% VALUES (%valores%)
 * DELETE FROM %tabela% WHERE %condições%
 * UPDATE %tabela% SET %alteraçoes% WHERE %condições%

O formato das alterações é esse: `coluna` = <valor>, `coluna` = <valor>, `coluna` = <valor>
 */


/**/
?>
