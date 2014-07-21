<?php

/**
 * Opções de configuração para pesquisas
 */

$config['survey'] = array(
    
    /**
     * Tipos de opções da pesquisa
     * (*) no início indica pré-seleção
     */
    'answer' => array(
        '5levels' => array(
            5 => 'Excelente',
            4 => 'Bom',
            3 => 'Previsível',
            2 => 'Ruim',
            1 => 'Péssimo'
        ),
        '5and1' => array(
            5 => 'Excelente',
            4 => 'Bom',
            3 => 'Previsível',
            2 => 'Ruim',
            1 => 'Péssimo',
            0 => 'Não sei avaliar' 
        ),
        'binary' => array(1 => "Sim", 0 => "Não"),
        // combo com 11 opções
        'range10' => array(10 => 10, 9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => '*5', 4 => 4, 3 => 3, 2 => 2, 1 => 1, 0 => 0),
        // campo texto
        'input' => '',
        // campo livre de comentários
        'text' => '',
        // o admin irá publicar as opções
        'singleOptions' => array(),
        // o admin irá publicar as opções
        'multiOptions' => array(),
    ),
    
    /*
     * ID da categoria de usuários, quando usuário não existir
     */
    'user_category' => 12
);
