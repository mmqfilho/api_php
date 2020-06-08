<?php
/**
 * API Front-end
 * 
 * DOMAIN/PATH/?QUERIES
 */

set_time_limit(0);
//error_reporting(0);
//ini_set("display_errors", 0 );

// dados 
include_once('core/data.php');

// classe de url e rotas 
include_once('core/uri_routes.php');
$r = new UriRoutes();

// classe da api
include_once('core/api.php');
include_once('core/template.php');
$api = new Api($r);

// verifica se a versão existe (array de versoes)
if (in_array(intval($r->info['version']), array(1))){

    // verifica se a classe existe
    if (isset($r->info['path'][1]) && file_exists('api/'.$r->info['path'][0].'/'.$r->info['path'][1].'.php')){
        include_once('api/'.$r->info['path'][0].'/'.$r->info['path'][1].'.php');

        // chama classe (verificação e autoexecução)
        $nome_servico = ucfirst($r->info['path'][1]);
        $servico = new $nome_servico($r);

    }else{
        $api->badRequest('Serviço não encontrado');
    }

}else{
    $api->badRequest('Versão da API inválida');
}
