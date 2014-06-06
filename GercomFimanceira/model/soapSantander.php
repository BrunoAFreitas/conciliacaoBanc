<?php
class clsSantanderSoap {

	protected $parametro = array();
	protected $_xml, $username, $key, $cnpj, $codigoGrupoCanal, $numeroIntermediario;
	protected $implementacao, $_xmlResposta = "";

	public function __construct($username, $key, $cnpj, $codigoGrupoCanal, $numeroIntermediario) {

		$this -> username = $username;
		$this -> key = $key;
		$this -> cnpj = $cnpj;
		$this -> codigoGrupoCanal = $codigoGrupoCanal;
		$this -> numeroIntermediario = $numeroIntermediario;

	}

	public function setStrImplementacao($strImplementacao) {
		$this -> implementacao = $strImplementacao;
	}

	public function setParametro($campo, $valor) {
		$this -> parametro[$campo] = $valor;
	}

	// medoto para chamar o ws das paginas
	public function consomeWebService($endereco_wsdl) {
		$ch;
		$this -> criaXML();

		$cabecalho = array('User-Agent: Curl-PHP/', 'Content-Type: text/xml; charset=utf-8', 'Content-Length: ' . strlen($this -> _xml), 'Accept-Encoding: GZIP');

		$ch = curl_init();
		// Iniciar o Curl
		curl_setopt($ch, CURLOPT_URL, $endereco_wsdl);
		// O Endereço que irá acessar
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Para Retornar o resultado
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// Modo Verbose, para exibir o processo na tela
		curl_setopt($ch, CURLOPT_HEADER, false);
		// Se precisar de retorno dos cabeçalhos
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		// Tempo máximo em segundos que deve esperar responder
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cabecalho);
		// Cabecalho para ser enviado
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// Seguir redirecionamentos
		curl_setopt($ch, CURLOPT_POST, true);
		// Usará metodo post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this -> _xml);
		// Dados para serem processados
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Caso precise verificar certificado
		curl_setopt($ch, CURLOPT_ENCODING, 'GZIP');
		// Usar compressao

		// Executa a requisição
		$this -> _xmlResposta = curl_exec($ch);
		//$this->_xmlResposta = curl_exec($ch);



		if (curl_errno($ch)) {
			echo "Error: ",  curl_error($ch);
			exit();
		}

	}

	public function criaXML() {

		$xml = new SimpleXMLElement('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:impl="http://impl.webservice.afc.app.bsbr.altec.com/"></soapenv:Envelope>');
		$header = $xml -> addChild("Header");
		$token = $header -> addChild('token:security', "", "http://santander-fo");
		$token -> addChild("username", $this -> username, "");
		$token -> addChild("key", $this -> key, "");
		$token -> addChild("cnpj", $this -> cnpj, "");
		$token -> addChild("codigoGrupoCanal", $this -> codigoGrupoCanal, "");
		$token -> addChild("numeroIntermediario", $this -> numeroIntermediario, "");

		$body = $xml -> addChild("Body");
		$implement = $body -> addChild('impl:' . $this -> implementacao, null, "http://santander-fo");
		$chamada = $implement -> addChild('chamada', "", "");

		while (list($key, $val) = each($this -> parametro)) :
			$chamada -> addChild($key, $val, "");
		endwhile;
 
		//echo $this -> _xml = $xml -> asXML();
		$this->_xml = $xml->asXML();

	}
	public function xmlToObject() {

		$xml = simplexml_load_string($this -> _xmlResposta);
		$xml -> registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');

		return $xml;
	}
	public function toArray($buscar = null, $xml = null) {

		$XML = $xml ? $xml : $this -> _xmlResposta;
		$buscar = $buscar ? $buscar : "*";
		$retorno = array();
		$xml = simplexml_load_string($XML);

		$xml -> registerXPathNamespace('SOAPENV', 'http://schemas.xmlsoap.org/soap/envelope/');
		foreach ($xml->xpath('//' . $buscar) as $item) {
			$retorno[] = $item;
		}

		return $retorno;
	}

	public function XMLtoArray($xml = null) {

		$XML = $xml ? $xml : $this -> _xmlResposta;

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $XML, $vals);
		xml_parser_free($xml_parser);
		// wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
		$_tmp = '';
		foreach ($vals as $xml_elem) {
			$x_tag = $xml_elem['tag'];
			$x_level = $xml_elem['level'];
			$x_type = $xml_elem['type'];
			if ($x_level != 1 && $x_type == 'close') {
				if (isset($multi_key[$x_tag][$x_level]))
					$multi_key[$x_tag][$x_level] = 1;
				else
					$multi_key[$x_tag][$x_level] = 0;
			}
			if ($x_level != 1 && $x_type == 'complete') {
				if ($_tmp == $x_tag)
					$multi_key[$x_tag][$x_level] = 1;
				$_tmp = $x_tag;
			}
		}
		// jedziemy po tablicy
		foreach ($vals as $xml_elem) {
			$x_tag = $xml_elem['tag'];
			$x_level = $xml_elem['level'];
			$x_type = $xml_elem['type'];
			if ($x_type == 'open')
				$level[$x_level] = $x_tag;
			$start_level = 1;
			$php_stmt = '$xml_array';
			if ($x_type == 'close' && $x_level != 1)
				$multi_key[$x_tag][$x_level]++;
			while ($start_level < $x_level) {
				$php_stmt .= '[$level[' . $start_level . ']]';
				if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
					$php_stmt .= '[' . ($multi_key[$level[$start_level]][$start_level] - 1) . ']';
				$start_level++;
			}
			$add = '';
			if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type == 'open' || $x_type == 'complete')) {
				if (!isset($multi_key2[$x_tag][$x_level]))
					$multi_key2[$x_tag][$x_level] = 0;
				else
					$multi_key2[$x_tag][$x_level]++;
				$add = '[' . $multi_key2[$x_tag][$x_level] . ']';
			}
			if (isset($xml_elem['value']) && trim($xml_elem['value']) != '' && !array_key_exists('attributes', $xml_elem)) {
				if ($x_type == 'open')
					$php_stmt_main = $php_stmt . '[$x_type]' . $add . '[\'content\'] = $xml_elem[\'value\'];';
				else
					$php_stmt_main = $php_stmt . '[$x_tag]' . $add . ' = $xml_elem[\'value\'];';
				eval($php_stmt_main);
			}
			if (array_key_exists('attributes', $xml_elem)) {
				if (isset($xml_elem['value'])) {
					$php_stmt_main = $php_stmt . '[$x_tag]' . $add . '[\'content\'] = $xml_elem[\'value\'];';
					eval($php_stmt_main);
				}
				foreach ($xml_elem['attributes'] as $key => $value) {
					$php_stmt_att = $php_stmt . '[$x_tag]' . $add . '[$key] = $value;';
					eval($php_stmt_att);
				}
			}
		}
		return $xml_array;
	}

}
?>