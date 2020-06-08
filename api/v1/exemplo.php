<?php
/**
 * Classe de exemplo
 */
Class Exemplo extends Api implements Template {

    private $r;

    public function __construct($r){
        parent::__construct($r);
        $this->r = $r->info;
        $this->getExemplo();
    }

    public function getExemplo(){

        if ($this->isGet()){

            // teste 2 parâmetros
            if (isset($this->r['path'][2]) && isset($this->r['path'][3])){
                // remove acentuacao e deixa em caixa alta
                $this->r['path'][3] = $this->removeAccentuation(urldecode($this->r['path'][3]), true);

                $url = BACKEND_URL.'NOME_SERVICO?qs1='.$this->r['path'][2].'&qs2='.urlencode($this->r['path'][3]);
                $res = $this->curlConnection($url);

            // teste 1 parâmetro
            }elseif (isset($this->r['path'][2])){ 
                $url = BACKEND_URL.'NOME_SERVICO?qs1='.$this->r['path'][2];
                $res = $this->curlConnection($url);

            // teste 3 retorna tudo
            }else{ 
                $url = BACKEND_URL.'NOME_SERVICO';
                $res = $this->curlConnection($url);
            }

            // array de dados
            $dados = json_decode($res['response_data'], true);

            if (is_null($dados) || $dados == ''){
                $code = 500;
                $this->container_erro['code'] = $code;
                $this->container_erro['message'] = self::$messages[$code];
                $this->container_erro['internal_message'] = 'Ocorreu um erro inesperado ao buscar dados';

                $this->dataReturn($this->container_erro);
            }

            // sanitiza os dados
            $dados = array_map(function($f){
                foreach(array_keys($f) as $v){
                    $f[$v] = trim($f[$v]);
                    if ($f[$v] == '+') $f[$v] = '';
                }
                
                // removendo campos não necessários
                unset($f['campox']);
                unset($f['campoy']);

                return $f;
            }, $dados);


            if ($res['response_code'] == 200){
                $this->container['code'] = $res['response_code'];
                $this->container['message'] = self::$messages[$res['response_code']];
                $this->container['count'] = count($dados);
                $this->container['results'] = $dados;

                $this->dataReturn($this->container);
            }else{
                $this->container_erro['code'] = $res['response_code'];
                $this->container_erro['message'] = self::$messages[$res['response_code']];
                $this->container_erro['internal_message'] = 'Ocorreu um erro inesperado';

                $this->dataReturn($this->container_erro);
            }

        }else{
            $this->methodNotAllowed();
        }
    }
}
