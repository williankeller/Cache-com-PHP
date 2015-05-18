<?php

/**
 * Sistema de cache
 *
 */
class Cache {

    /**
     * Tempo padrão de cache
     *
     * @var string
     */
    private static $tempo = '1440 minutes';

    /**
     * Local onde o cache será salvo
     *
     * Definido pelo construtor
     *
     * @var string
     */
    private $pasta = '/site/cache/';
    /**
     * Construtor
     *
     * Inicializa a classe e permite a definição de onde os arquivos
     * serão salvos. Se o parâmetro $pasta for ignorado o local dos
     * arquivos temporários do sistema operacional será usado
     *
     * @param string $pasta Local para salvar os arquivos de cache (opcional)
     * @return void
     */
    function __construct($pasta = null) {
		
        $this->definePasta(!is_null($pasta) ? $pasta : sys_get_temp_dir());
    }

    /**
     * Define onde os arquivos de cache serão salvos
     *
     * Irá verificar se a pasta existe e pode ser escrita, caso contrário
     * uma mensagem de erro será exibida
     *
     * @param string $pasta Local para salvar os arquivos de cache (opcional)
     * @return void
     */
    private function definePasta($pasta) {

        // Se a pasta existir, for uma pasta e puder ser escrita
        if (file_exists($pasta) && is_dir($pasta) && is_writable($pasta)) {

            $this->pasta = $pasta;
        } else {
            trigger_error('Não foi possível acessar a pasta de cache', E_USER_ERROR);
        }
    }

    /**
     * Gera o local do arquivo de cache baseado na chave informada
     *
     * @param string $chave Uma chave para identificar o arquivo
     * @return string Local do arquivo de cache
     */
    private function localArquivo($chave) {

        return $_SERVER['DOCUMENT_ROOT'] . $this->pasta . sha1($chave) . '.tmp';
    }
	
	private function comprimeDados($conteudo){
		
		return str_replace(array("\r\n", "\r", "\n", "\t", "    ", "   ", "  "), '', $conteudo);
	}

    /**
     * Cria um arquivo de cache
     *
     * @param string $chave Uma chave para identificar o arquivo
     * @param string $conteudo Conteúdo do arquivo de cache
     * @return boolean Se o arquivo foi criado
     */
    private function criaArquivo($chave, $conteudo) {

        // Gera o nome do arquivo
        $arquivo = $this->localArquivo($chave);
	
		$dados = $this->comprimeDados($conteudo);
		
        // Cria o arquivo com o conteúdo
        return file_put_contents($arquivo, $dados) OR trigger_error('Não foi possível criar o arquivo de cache', E_USER_ERROR);
    }

    /**
     * Salva um valor no cache
     *
     * @param string $chave Uma chave para identificar o valor cacheado
     * @param mixed $conteudo Conteúdo/variável a ser salvo(a) no cache
     * @param string $tempo Quanto tempo até o cache expirar (opcional)
     * @return boolean Se o cache foi salvo
     */
    public function salva($chave, $conteudo, $tempo = null) {
        
        $tempo = strtotime(!is_null($tempo) ? $tempo : self::$tempo);

        $conteudo = serialize(array(
            'expira' => $tempo,
            'conteudo' => $conteudo));

        return $this->criaArquivo($chave, $conteudo);
    }

    /**
     * Recupera um valor do cache
     *
     * @param string $chave Uma chave para identificar o valor em cache
     * @return mixed Se o cache foi encontrado retorna o seu valor.
     * Caso contrário retorna NULL
     */
    final public function recupera($chave) {
        
        $arquivo = $this->localArquivo($chave);
        
        if (file_exists($arquivo) && is_readable($arquivo)) {
            
            $cache = unserialize(file_get_contents($arquivo));
            
            if ($cache['expira'] > time()) {
                
                return $cache['conteudo'];
            } else {
                unlink($arquivo);
            }
        }
        return null;
    }

}