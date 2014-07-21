<?php
$config['peso_max_upl'] = '2500'; // 1 Mb = 1000
$config['peso_max_arqs'] = '2500'; // 50 Mb
$config['upl_ext'] = 'jpg|gif|png|JPG|GIF|PNG';// só para imagens
$config['upl_ext_arqs'] = 'jpg|gif|png|flv|pdf|doc|docx|swf|mp4|zip|xls|xlsx';// arquivos em geral
$config['imagem_mini_w'] = '200';
$config['imagem_mini_h'] = '200';
$config['imagem_med_w'] = '460';
$config['imagem_med_h'] = '400';
$config['imagem_max_w'] = '800';
$config['imagem_max_h'] = '500';
$config['imagem_limit_w'] = '1200';// tamanho limite
$config['imagem_limit_h'] = '1200';// tamanho limite
$config['pagination_limits'] = array(20, 35, 50, 80);

/* ------------------------------------------------
 *  REVIÇÕES DE CONTEÚDO
 * ------------------------------------------------
 * | O campo 'tipo' tem o formato ID-revision-N
 */
$config['revisions_limit'] = 2; // quantidade de revisões que serão armazenadas

/*
 * Nomes das opções das imagens no CMS
 * Usar false para não exibir tag
 */
$config['tag_opt_1'] = 'Imagem de capa';
$config['tag_opt_2'] = 'Não inserir na galeria';
$config['tag_opt_3'] = false;
$config['tag_opt_4'] = false;
$config['tag_opt_5'] = false;

/*
  |--------------------------------------------------------------------------
  | STATUS DOS POSTS
  |--------------------------------------------------------------------------
  |
  | Tipos de status disponíveis
  |
 */
$config['post_status'] = array(
    
    0 => 'inativo',
    1 => 'ativo',     
    2 => 'editando'     
);

/*
  |--------------------------------------------------------------------------
  | Prioridade do conteúdo
  |--------------------------------------------------------------------------
  |
  | Opção para ordenar conteúdos
  | Salvo como meta: meta_key: priority
  |
 */
$config['post_priority'] = array(
    
    1 => '1) Muito alta',
    2 => '2) Alta',     
    3 => '3) Média',     
    4 => '4) Baixa',     
    5 => '5) Muito baixa',     
);

/*
  |--------------------------------------------------------------------------
  | EXPORTAÇÃO DE DADOS PERSONALIZADA NO CMS
  |--------------------------------------------------------------------------
  |
  | o primeiro item é o label do link, o restante será concatenado
  |
 */
$config['co7'][] = array(
    'ID e título ativos',
    'campos:id-titulo',
    'stt:1',
    'extra:raca'
);
$config['co7'][] = array(
    'ID e título INativos',
    'campos:id-titulo',
    'stt:0'
);

/*
  |--------------------------------------------------------------------------
  | TIPOS DE METADADOS
  |--------------------------------------------------------------------------
  |
  | Registra as metadados para edição de conteúdo do CMS
  |
 */
$config['metadados'] = array(
    
    // diz se o conteúdo pode ser deletado
    'meta_no_delete' => array(0,1), 
    
    // ID do módulo responsável por gerar o conteúdo
    'meta_modulo_content' => ''     
);

/*
  |--------------------------------------------------------------------------
  | TIPOS DE DESCONTO
  |--------------------------------------------------------------------------
  |
  | Registra os tipos de regra para descontos e cupons
  |
 */
$config['regra_tipo_desconto'] = array(
    'acima-de'       => 'Compras acima de R$...', // padrão
    'cada-n-pedidos' => 'A cada N pedidos',
    'quantidade'     => 'Quantidade de produtos'
);
$config['regra_tipo_cupom'] = array(
    'R$' => 'R$',
    '%'  => '%'
);