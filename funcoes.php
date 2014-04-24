<?php
/**
 * Classe com funções de uso geral para as aplicações e sistemas.
 *
 * @author Joubert Guimarães de Assis <joubert@redrat.com.br>
 * @subpackage Classes
 * @copyright 2012 (c) RedRat Consultoria.
 * @version 1.0
 */

class Funcoes
{
	/**
	 * Função de limpeza de textos, reponsável por remover espaços desnecessários.
	 * Exemplo: Funcoes::limpalize("O    gato             foi dormir  la        na      rua");
	 * O exemplo acima retornará "O gato foi dormir la na rua"
	 *
	 * @param string $texto Texto a ser limpo.
	 * @return string Retorna o texto devidamento corrigido.
	 * @see http://php-eduluz.blogspot.com.br/2008/03/removendo-espaos-do-php.html
	 */
	public static function limpalize($texto)
	{
		$str = trim($texto);
		// Now remove any doubled-up whitespace
		$str = preg_replace('/\s(?=\s)/', '', $str);
		// Finally, replace any non-space whitespace, with a space
		$str = preg_replace('/[\n\r\t]/', ' ', $str);
		return $str;
	}

	/**
	 * Função de limpeza de textos, reponsável por remover espaços desnecessários.
	 * Exemplo: Funcoes::removerEspacosDuplos("O    gato             foi dormir  la        na      rua");
	 * O exemplo acima retornará "O gato foi dormir la na rua"
	 *
	 * @param string $texto Texto a ser limpo.
	 * @return string Retorna o texto devidamento corrigido.
	 * @see http://phpsp.org.br/2010/10/salvem-os-bebes-foca-espacos-duplicados/
	 */
	public static function removerEspacosDuplos($texto)
	{
		return preg_replace('/\s\s+/', ' ', $texto);
	}

	/**
	 * Verifica se o texto informado esta com a codificação UTF-8
	 *
	 * @param string $texto Texto a ser verificado.
	 * @return bool Retorna true caso seja utf-8 e false caso contra.
	 * @see http://queryposts.com/function/seems_utf8/
	 */
	public static function ehUtf8($texto)
	{
		$length = strlen($texto);
		for($i = 0; $i < $length; $i++)
		{
			$c = ord($texto[$i]);
			if ($c < 0x80)
				$n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0)
				$n = 1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0)
				$n = 2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0)
				$n = 3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8)
				$n = 4; # 111110bb
			elseif (($c & 0xFE) == 0xFC)
				$n = 5; # 1111110b
			else
				return false; # Does not match any model
			for ($j = 0; $j < $n; $j++)
			{ # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($texto[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	/**
	 * Verifica se o texto informado esta com a codificação UTF-8
	 *
	 * @param string $texto Texto a ser verificado.
	 * @return int Retorna 1 caso seja utf-8 e 0 caso contra.
	 * @see http://php.net/manual/en/function.mb-detect-encoding.php
	 */
	public static function detectUtf8($string)
	{
		return preg_match('%(?:
			[\xC2-\xDF][\x80-\xBF]				# non-overlong 2-byte
			|\xE0[\xA0-\xBF][\x80-\xBF]			# excluding overlongs
			|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
			|\xED[\x80-\x9F][\x80-\xBF]			# excluding surrogates
			|\xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
			|[\xF1-\xF3][\x80-\xBF]{3}			# planes 4-15
			|\xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
		)+%xs', $string);
	}

	/**
	 * Verifica se o texto informado esta com a codificação UTF-8
	 *
	 * @param string $texto Texto a ser verificado.
	 * @return int Retorna 1 caso seja utf-8 e 0 caso contra.
	 * @see http://php.net/manual/en/function.mb-detect-encoding.php
	 * @see From http://w3.org/International/questions/qa-forms-utf-8.html
	 */
	public static function isUtf8($string) {
		return preg_match('%^(?:
			  [\x09\x0A\x0D\x20-\x7E]            # ASCII
			| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
			|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
			| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
			|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
			|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
			| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
			|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)*$%xs', $string);
	}

	/**
	 * @see http://www.php.net/manual/pt_BR/function.mime-content-type.php
	 * @see http://www.php.net/manual/pt_BR/book.fileinfo.php
	 * @see http://en.wikipedia.org/wiki/Docx
	 * @see http://stackoverflow.com/questions/6595183/docx-file-type-in-php-finfo-file-is-application-zip
	 */
	public static function getMimeType($arquivo)
	{
		$mt['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		$mt['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
		$mt['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
		$mt['sldx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slide';
		$mt['ppsx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
		$mt['potx'] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
		$mt['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		$mt['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
		$extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
		if(array_key_exists($extensao, $mt))
			return $mt[$extensao];
		$mtf = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($mtf, $arquivo);
		finfo_close($mtf);
		return $mimetype;
	}

	/**
	 * Realiza uma validação simples de e-mail.
	 *
	 * @param string $email E-mail a ser validado.
	 * @return bool Retorna true caso e-mail seja válido ou false caso contra.
	 */
	public static function validaEmail($email)
	{
		return (bool) preg_match('/^[a-z0-9]+([+]?[a-z0-9\._-]{1,}|[a-z0-9\._-]{0,})+[@][a-z0-9_-]+(\.[a-z0-9]+)*\.[a-z]{2,3}$/', $email);
	}

	/**
	 * Realiza uma validação de e-mail por máscara e validações de domínio.
	 *
	 * @param string $email E-mail a ser validado.
	 * @return bool Retorna true caso e-mail seja válido ou false caso contra.
	 */
	public static function validaProvedorEmail($email)
	{
		if(!self::validaEmail($email))
			return false;
		list($conta, $dominio) = preg_split('/@/', $email);
		if(!checkdnsrr($dominio, "A"))
			return false;
		if(!getmxrr($dominio, $mx))
			return false;
		return true;
	}

	/**
	 * Realiza um slug na string.
	 *
	 * @param string $string Texto que sofrerá formatação.
	 * @param string $troca_espaco Caracter que será trocado do espaço caso informado.
	 */
	public static function normaliza($string, $troca_espaco = null)
	{
	    $listar = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ' . (!is_null($troca_espaco) ? ' ' : '');
	    $trocar = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr' . (!is_null($troca_espaco) ? $troca_espaco : '');
	    return utf8_encode(strtolower(strtr(utf8_decode($string), utf8_decode($listar), $trocar)));
	}

	/**
	 *
	 */
	public static function processarPorcentagem($valor_fracionado, $valor_total, $mostrar_valor = true, $grafico = true, $pre = true)
	{
		$barra = '';
		$fracao = (($valor_fracionado / $valor_total) * 100);
		if($grafico)
		{
			$valor_barra = number_format($fracao, 0) ;
			$barra = "[";
			for($i = 0; $i < 100; $i ++)
				$barra .= $valor_barra >= $i ? "#" : "&nbsp;";
			$barra .= "]";
		}
		$retorno = '';
		if($grafico && $pre)
			$retorno .= '<pre>';
		$retorno .= $barra . ' ' . number_format($fracao, 2, ',', ' ') . '%';
		if($mostrar_valor)
			$retorno .= ' (' . $valor_fracionado . ' de ' . $valor_total . ')';
		if($grafico && $pre)
			$retorno .= '</pre>';
		return  $retorno;
	}

	/**
	 * Verifica se o link informado é um link válido do youtube.
	 *
	 * @param srtring $url
	 * @return string|bool Retorna id ou false.
	 * @see http://stackoverflow.com/questions/11438544/php-regex-for-youtube-video-id
	 */
	function validarLinkYoutube($url)
	{
		$regex = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($regex, $url, $pedacos);
		return (isset($pedacos[1])) ? $pedacos[1] : false;
	}

	/**
	 * Faz um dump da variável na tela usando as cores padrão do PHP.
	 *
	 * @param mixed $var Variável a ser dumpada.
	 * @return void
	 */
	function var_debug($var){ ob_start(); var_dump($var); echo highlight_string("<?php\n " . ob_get_clean() . "?>", true); }

	/**
	 * Converte entitie codes para UTF-8.
	 *
	 * @param string $string Texto a ser convertido.
	 * @param string Retorna o string convertido.
	 */
	function eslateia($string)
	{
		return preg_replace_callback("/(&#[0-9]+;)/", function($caracter) { return mb_convert_encoding($caracter[0], "UTF-8", "HTML-ENTITIES"); }, $string);
	}

	/**
	 * A função number_format() é boa. Mas, no sistema em q estava desenvolvendo, percebi q caso o usuário "fuja" dos padrões
	 * (999.999,99 ou 9999.99 e variantes), a função retorna um erro ou "deixa passar" o valor q o usuário inseriu.
	 *
	 * echo formatoReal('9'); //false
	 * echo formatoReal('99'); //false
	 * echo formatoReal('999'); //false
	 * echo formatoReal('9,99'); //true
	 * echo formatoReal('99,99'); //true
	 * echo formatoReal('99,999'); //false
	 * echo formatoReal('9.999'); //false
	 * echo formatoReal('9999,99'); //false
	 * echo formatoReal('99.,.99'); //false
	 * echo formatoReal('99,.99'); //false
	 * echo formatoReal('999.999.999,99'); //true
	 *
	 * @author Bonus <anderson_rockandroll@hotmail.com>
	 * @param float Valor a ser validado.
	 * @return bool Retorna TRUE se seguir o padrão (brasileiro) e falso caso contrário.
	 * @see https://groups.google.com/forum/?fromgroups=#!topic/listaphp/MS1ralVUIcY
	 */
	public static function formatoReal($valor)
	{
		$valor = (string) $valor;
		$regra = "/^[0-9]{1,3}([.]([0-9]{3}))*[,]([.]{0})[0-9]{0,2}$/";
		return preg_match($regra, $valor);
	}

        /**
         * Extrai somente números da string.
         *
         * @param string $str
         * @return string
         */
        public static function get_number($str) {
                return preg_replace("/[^0-9]/", "", $str);
        }

        /**
         * Remove mascara de telefone.
         * @param string $str
         * @return string
         */
        public static function tel_to_int($str) {
                return self::get_number($str);
        }

        /**
         * Aplica mascara de telefone.
         * @param string $str
         * @return string
         */
        public static function int_to_tel($str) {
                switch (strlen($str)) {
                        case 8:
                                return substr($str, 0, 4) . '-' . substr($str, 4);
                                break;
                        case 9:
                                return substr($str, 0, 5) . '-' . substr($str, 5);
                                break;
                        default:
                                return $str;
                                break;
                }
        }

        /**
         * Remove máscara de CEP.
         *
         * @param string $str
         * @return string
         */
        public static function cep_to_int($str) {
                return self::get_number($str);
        }

        /**
         * Aplica máscara de CEP.
         *
         * @param string $str
         * @return string
         */
	public static function int_to_cep($str) {
		return substr($str, 0, 5) . '-' . substr($str, 5);
	}

        /**
         * Remove máscara de CPF.
         *
         * @param string $str
         * @return string
         */
	public static function cpf_to_int($str) {
		return self::get_number($str);
	}

        /**
         * Aplica máscara de CPF.
         *
         * @param string $str
         * @return string
         */
	public static function int_to_cpf($str) {
		return substr($str, 0, 3) . '.' . substr($str, 3, 3) . '.' . substr($str, 6, 3) . '-' . substr($str, 9, 2);
	}

        /**
         * Remove máscara de CNPJ.
         *
         * @param string $str
         * @return string
         */
	public static function cnpj_to_int($str) {
		return self::get_number($str);
	}

        /**
         * Aplica máscara de CNPJ.
         *
         * @param string $str
         * @return string
         */
	public static function int_to_cnpj($str) {
		return substr($str, 0, 2) . '.' . substr($str, 2, 3) . '.' . substr($str, 5, 3) . '/' . substr($str, 8, 4) . '-' . substr($str, 12, 2);
	}

        public static function convert_date($data, $mascara = 'Y-m-d') {
            if (is_null($data) || $data == '')
                return null;
            return date($mascara, strtotime(str_replace('/', '-', $data)));
        }

        /**
         * Provides a return data in json format.
         * @param array|text $data Data to be returned.
         */
        public static function return_json($data) {
            header('Content-type: application/json');
            exit(json_encode($data));
        }

        /**
         * Erm, I don't know :(
         *
         * @param type $name
         * @param type $arguments
         * @return type
         */
        public static function __callStatic($name, $arguments) {
            switch ($name) {
                case 'forge_url':
                    if(substr($arguments[0], strlen($arguments[0]) - 1) === '/')
                        $arguments[0] = substr($arguments[0], 0, strlen($arguments[0]) - 1);
                    return implode('/', $arguments);
                    break;
                default:
                    exit(__CLASS__." class said: I don't know this method. ".__FILE__.':'.__LINE__);
                    break;
            }
        }
}

class Functions extends Funcoes {}
