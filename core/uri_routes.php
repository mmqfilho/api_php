<?php
Class UriRoutes{

    public $info;

    public function __construct(){
        $this->info['url']          = $_SERVER['REQUEST_URI'];
        $this->info['method']       = $_SERVER['REQUEST_METHOD'];
        $this->info['parse_url']    = parse_url($_SERVER['REQUEST_URI']);
        $this->info['path']         = preg_split('/\//', parse_url($_SERVER['REQUEST_URI'])['path'], -1, PREG_SPLIT_NO_EMPTY);
        $this->info['debug']        = (isset($_REQUEST['debug']) && $_REQUEST['debug'] == 'adobe') ? true : false;

        if (isset($this->info['parse_url']['query'])){
            $x = preg_split('/\&/', parse_url($_SERVER['REQUEST_URI'])['query'], -1, PREG_SPLIT_NO_EMPTY);

            foreach($x as $valor){
                $v = explode('=', $valor);
                $this->info['query_string'][$v[0]] = (isset($v[1])) ? $v[1] : null;
            }
        }
        
        $this->info['version'] = isset($_GET['version']) ? $_GET['version'] : null;
        if ($this->info['version'] == null){
            
            /**
             * Mensagem de erro ou
            **/
            Header('HTTP/1.1 400 Bad Request');
            echo json_encode(array('status'=>False, 'message'=>'Versão da API não informada'));

            /**
             * Redireciona para swagger se tiver
            **/
            #Header('HTTP/1.1 307 Temporary Redirect');
            #Header('Location: openapi/');
            
            die();
        }
    }
}
