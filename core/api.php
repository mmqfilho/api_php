<?php
Class Api {

    /**
     * Varivel de teste para debug
     */
    protected $debug = false;

    /**
     * Códigos de retorno
     */
    protected static $messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * Container de dados de retorno
     */
    protected $container = array(
        'error' => false,
        'code' => null,
        'message' => null,
        'internal_message' => null,
        'count' => null,
        'results' => null
    );

    /**
     * Container de dados de retorno de erro
     */
    protected $container_erro = array(
        'error' => true,
        'code' => null,
        'message' => null,
        'internal_message' => null
    );

    public function __construct($r){
        $this->debug = $r->info['debug'];
    }
    /**
     * Retorno padrão de erro de método
     */
    public function methodNotAllowed(){
        $code = 405;
        $this->container_erro['error'] = true;
        $this->container_erro['code'] = $code;
        $this->container_erro['message'] = self::$messages[$code];

        $this->dataReturn($this->container_erro, $code);
    }

    /**
     * Retorno padrão de erro de requisição
     * @param $msg String - Mensagem de erro
     */
    public function badRequest($msg = null){
        $code = 400;
        $this->container_erro['error'] = true;
        $this->container_erro['code'] = $code;
        $this->container_erro['message'] = self::$messages[$code];
        $this->container_erro['internal_message'] = $msg;

        $this->dataReturn($this->container_erro, $code);
    }

    /**
     * Retorno de dados via json
     * @param $dados Array - Array de resposta
     * @param $code Integer - Código de status http
     */
    public function dataReturn($dados, $code = 200){
        header_remove();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($dados);
        exit();
    }

    /**
     * Verifica se é GET
     */
    public function isGet(){
		return (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') ? true : false;
	}

    /**
     * Verifica se é POST
     */
	public function isPost(){
		return (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') ? true : false;
	}

    /**
     * Verifica se é PUT
     */
	public function isPut(){
		return (strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT') ? true : false;
    }
    
    /**
     * Verifica se é DELETE
     */
	public function isDelete(){
		return (strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE') ? true : false;
    }
    
    /**
     * Faz conexao
     * @param $url String - Url da conexão
     * @param $data String - Dados em formato json
     * @param $header Array - Array de string dos headers
     */
    public function curlConnection($url, $data = null, $header = null){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        if ($this->debug == true){
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }
        
        if (!is_null($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $headers = array();
        if (!is_null($header)) {
            foreach ($header as $i => $v){
                $headers[] = $i . ': ' . $v;
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $ret['response_data'] = curl_exec($ch);
        $ret['response_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);       

        if ($this->debug == true){
            $info = curl_getinfo($ch);
            echo '<pre>';
            print_r($info);
            die();
        }
        
        curl_close($ch);
        return $ret;
    }

    /**
     * Remove a acentuação de uma string
     * @param $str String - String a ter acentuação removida
     * @param $upper Bool - Retorna ou não a string em caixa alta
     */
    public function removeAccentuation($str, $upper = false){
        $withAcc    = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $wiyhoutAcc = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
        return ($upper) ? strtoupper(str_replace($withAcc, $wiyhoutAcc, $str)) : str_replace($withAcc, $wiyhoutAcc, $str);
    }

    /**
	 * Retira caracteres e deixa apenas numeros
	 */
	public function onlyNumbers($n){
		return preg_replace('/[^0-9]/', '', $n);
	}
}
