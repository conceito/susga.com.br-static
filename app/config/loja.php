<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Status de transações
  |--------------------------------------------------------------------------
  |
  | Status possíveis para transações comerciais baseados no retorno do PagSeguro
  |
 */
$config['status_transacao'] = array(
    1 => 'Aguardando pagamento', // padrão
    2 => 'Em análise',
    3 => 'Paga', // concluído
    4 => 'Disponível',
    5 => 'Em disputa',
    6 => 'Devolvida',
    7 => 'Cancelada'
);

/*
  |--------------------------------------------------------------------------
  | PagSeguro
  |--------------------------------------------------------------------------
  |
  | Configurações de integração PagSeguro
  |
 */
$config['pagseguro_email'] = 'cobranca@arteeweb.com.br';
$config['pagseguro_token'] = 'E7F9554D3AC74EA49D9928641937F080';

/*
  |--------------------------------------------------------------------------
  | Estoque
  |--------------------------------------------------------------------------
  |
  | Configurações de estoque.
  | $config['estoque_alert'] = limite de produtos para emitir alerta
  |
 */
$config['estoque_alert'] = 5;

/*
  |--------------------------------------------------------------------------
  | Fatura e pedidos
  |--------------------------------------------------------------------------
  |
  | Ao gerar a fatura concatena o prefixo com o ID do extrato.
  | 
  |
 */
$config['fatura_preffix'] = 'FAT'.date("Y").'-';

/*
  |--------------------------------------------------------------------------
  | Carrinho de compras
  |--------------------------------------------------------------------------
  |
  | 'max_per_prod' => quantidade máxima por produtos no carrinho
  | 
  |
 */
$config['max_per_prod'] = 99;

