<?php
$config_Modulo = function(){
    return Array(
        'Nome'                      =>  'categoria',
        'Descrição'                 =>  '',
        'System_Require'            =>  '2.21.1',
        'Version'                   =>  '0.0.1',
        'Dependencias'              =>  false,
    );
};
$config_Menu = function (){
    return Array(
        'Avançado' => Array(
            'Filhos'                => Array('Categorias' =>Array(
                'Nome'                  => 'Categorias',
                'Link'                  => 'categoria/Admin/Categorias',
                'Gravidade'             => 10,
                'Img'                   => 'turboadmin/m-dashboard.png',
                'Icon'                  => 'barcode',
                'Permissao_Func'        => Array(// Permissoes NEcessarias
                    'categoria_visualizar' => true
                ),
                'Filhos'                => false,
            ),),
        ),
    );
};
$config_Permissoes = function (){
    return Array(
        Array(
            'Nome'                  => 'Categorias - Listagem',
            'Desc'                  => '',
            'Chave'                 => 'categoria_Admin_Categorias',
            'End'                   => 'categoria/Admin/Categorias', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'categoria', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Categorias',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'categoria_visualizar' => true
            ),
        ),
        Array(
            'Nome'                  => 'Categorias - Add',
            'Desc'                  => '',
            'Chave'                 => 'categoria_Admin_Categorias_Add', // CHave unica nunca repete, chave primaria
            'End'                   => 'categoria/Admin/Categorias_Add', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'categoria', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Categorias_Add,categorias_Add2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'categoria_visualizar' => true
            ),
        ),
        Array(
            'Nome'                  => 'Categorias - Editar',
            'Desc'                  => '',
            'Chave'                 => 'categoria_Admin_Categorias_Edit', // CHave unica nunca repete, chave primaria
            'End'                   => 'categoria/Admin/Categorias_Edit', // Endereco que deve conter a url para permitir acesso // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'categoria', // Modulo Referente // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente   // Submodulo Referente
            'Metodo'                => 'Categorias_Edit,Categorias_Edit2',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'categoria_visualizar' => true
            ),
        ),
        Array(
            'Nome'                  => 'Categorias - Deletar',
            'Desc'                  => '',
            'Chave'                 => 'categoria_Admin_Categorias_Del', // CHave unica nunca repete, chave primaria
            'End'                   => 'categoria/Admin/Categorias_Del', // Endereco que deve conter a url para permitir acesso
            'Modulo'                => 'categoria', // Modulo Referente
            'SubModulo'             => 'Admin',   // Submodulo Referente
            'Metodo'                => 'Categorias_Del',  // Metodos referentes separados por virgula
            'Permissao_Func'        => Array(// Permissoes NEcessarias
                'categoria_visualizar' => true
            ),
        ),
    );
};
/**
 * Serve Para Personalizar o Modulo de Acordo com o gosto de cada "Servidor"
 * @return type
 * 
 * @author Ricardo Sierra <web@ricardosierra.com.br>
 */
$config_Funcional = function (){
    return Array(
        'categoria_visualizar'    => Array(
            'Nome'                      => 'Se Pagina de Categoria Pode Ser Vista pelo Usuario',
            'Desc'                      => '',
            'chave'                     => 'categoria_visualizar',
            'Valor'                     => false,
        ),
        'categoria_parent_extra'    => Array(
            'Nome'                      => 'Categoria Pai para Cadastros Externos',
            'Desc'                      => '',
            'chave'                     => 'categoria_parent_extra',
            'Valor'                     => false,
        ),
    );
};
?>
