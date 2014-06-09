<?php
/**
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter esse comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example function_dados.js
 */

//include_once ("../conexao2.inc.php");
include_once ("connection/conexao_bd.php");

class function_dados {
	/**
	 * variaveis para test
	 */
	public $acelogin = "jalen";
	public $ljcod = "07";

	public $cidade;
	public $estado;
	public $novocad;
	public $permLoja;

	/**
	 * Metodo para pegar a sequencia da loja
	 * @access public
	 * @param $loja
	 * @return sequencia da loja
	 */
	public function pedvend($loja) {
		$venda = "SELECT lj_sigla, lj_pvautomatico, lj_seqpv FROM loja 
				  WHERE lj_cod = '$loja'";
		$exe_venda = mysql_query($venda);
		if (mysql_num_rows($exe_venda) > 0) {
			$linha_venda = mysql_fetch_object($exe_venda);
			echo $linha_venda -> lj_sigla . $linha_venda -> lj_seqpv;
		} else {
			echo "";
		}
	}

	/**
	 * Metodo que pega os dados do usuario e da loja
	 * @access public
	 * @param $acess, $lojcd
	 * @return cidade, estado, novocad, permLoja
	 */
	public function dadosUser($acess, $lojcd) {
		$sql = "SELECT * FROM acessos WHERE ace_login = '$acess'";
		$query = mysql_query($sql);
		if (mysql_num_rows($query) > 0) {
			$linha = mysql_fetch_object($query);
			$this -> permLoja = $linha -> ace_14;
			if ($linha -> ace_14 == 'S') {
				$sql_lj = "SELECT lj_cidade, lj_estado, lj_uf, lj_novocadcli
						   FROM loja WHERE lj_cod = '$lojcd' ";
				$query_lj = mysql_query($sql_lj) or die(mysql_error());
				$linha_lj = mysql_fetch_object($query_lj);
				$this -> cidade = $linha_lj -> lj_cidade;
				$this -> estado = $linha_lj -> lj_estado;
				$this -> novocad = $linha_lj -> lj_novocadcli;
			}
		}
	}

	/**
	 * Metodo que troca o formato da data para o formato br
	 * @access public
	 * @param $data
	 * @return $data
	 */
	public function emissao($data) {
		$aux = explode("-", $data);
		$c = array_reverse($aux);
		$data = implode("/", $c);
		echo $data;
	}

	/**
	 * Metodo que muda data de 2002/00/00 para 2002-00-00; formato americano
	 * @access public
	 * @param $data
	 * @return $data
	 */
	public function muda_data_en($data) {
		$aux = explode("/", $data);
		$c = array_reverse($aux);
		$data = implode("-", $c);
		return $data;
	}

	/**
	 * Metodo que retira o acento das palavras
	 * @access public
	 * @param $string
	 * @return $string;
	 */
	public function any_accentuation($string = "") {
		if ($string != "") {
			$com_acento = "à á â ã ä è é ê ë ì í î ï ò ó ô õ ö ù ú û ü À Á Â Ã Ä È É Ê Ë Ì Í Î Ò Ó Ô Õ Ö Ù Ú Û Ü ç Ç ñ Ñ '";
			$sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I O O O O O U U U U c C n N -";
			$c = explode(' ', $com_acento);
			$sa = explode(' ', $sem_acento);
			$ts = strlen($string);
			$tv = strlen($com_acento);
			$tv = $tv / 2;
			$str = $string;
			$i = 0;
			$cont = $i;
			while ($i < $ts) {
				$cont = 0;
				while ($cont < $tv) {
					if ($string{$i} == $c[$cont]) {
						$string{$i} = $sa[$cont];
					}
					$cont++;
				}
				$i++;
			}
			$string = strtoupper($string);
		}
		return $string;
	}

	/**
	 * Metodo que tira algumas letras que não podem ser gravadas no banco de dados
	 * @access public
	 * @param String $texto
	 * @return String $texto
	 */
	public function LimparTexto($texto) {
		$texto = str_replace(array("<", ">", "\\", "/", "=", "'", "?", "º", "£", "¥", "©", "®", "±", "º", "?", "£", "=", "=", "®", "µ", "$", "%", "&", "!", "(", ")", "*", "+", ",", "-", ".", "/", ";", ":", "@", "{", "}", "¤"), "", $texto);
		return $texto;
	}

	/**
	 * Metodo que converte valor 1.000,00 para 1000.00
	 * @access public
	 * @param String $valor
	 * @return String $valor
	 */
	public function valor_mysql($valor) {
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", ".", $valor);
		return $valor;
	}

	/**
	 * Metodo que mostra todas as operadoras
	 * @access public
	 * @param não tem
	 * @return void
	 */
	public function operadoras() {
		echo "<option value='TIM'>TIM</option>";
		echo "<option value='VIVO'>VIVO</option>";
		echo "<option value='CLARO'>CLARO</option>";
		echo "<option value='OI'>OI</option>";
		echo "<option value='NEXTEL'>NEXTEL</option>";
		echo "<option value='OUTRA'>OUTRA</option>";
	}

	/**
	 * Metodo que mostra os bairros da cidade
	 * @access public
	 * @param String $cidade, $lstBairro
	 * @return Nome dos vendedores em option
	 */
	public function vendedor($loja) {
		$sql_vend = "SELECT ven_cod,ven_nome FROM vendedor 
					 WHERE ven_loja = '$loja' AND ven_ativo = 'S'
                     ORDER BY ven_nome";
		$query_vend = mysql_query($sql_vend);
		if (mysql_num_rows($query_vend) > 0) {
			while ($linha_vend = mysql_fetch_object($query_vend)) {
				if ($lstVend == $linha_vend -> ven_cod) {
					echo "<option value='" . $linha_vend -> ven_cod . "'selected>" . $linha_vend -> ven_nome . "
						  </option>";
				} else {
					echo "<option value='" . $linha_vend -> ven_cod . "'>" . $linha_vend -> ven_nome . "</option>";
				}
			}
		}
	}

	/**
	 * Metodo que mostra todas as profissões do banco de dados
	 * @access public
	 * @param não tem
	 * @return void
	 */
	public function profissao($edtProfissao) {
		$sql_prof = "SELECT clip_cod, clip_desc FROM clientes_prof 
					 WHERE clip_ativo = 'S' ORDER BY clip_desc";
		$query_prof = mysql_query($sql_prof);
		if (mysql_num_rows($query_prof) > 0) {
			while ($linha_prof = mysql_fetch_object($query_prof)) {
				if ($linha_prof -> clip_cod == $edtProfissao) {
					echo "<option value='" . $linha_prof -> clip_cod . "'selected>" . $linha_prof -> clip_desc . "
						  </option>";
				} else {
					echo "<option value='" . $linha_prof -> clip_cod . '|' . $linha_prof -> clip_desc . "'>" . $linha_prof -> clip_desc . "</option>";
				}
			}
		}
	}

	/**
	 * Metodo que mostra os Estados
	 * @access public
	 * @param String $edtEstado
	 * @return Estados em option
	 */
	public function estados($edtEstado) {
		$sql_uf = "SELECT fmun_uf FROM fmunicipios group by fmun_uf order by fmun_uf;";
		$query_uf = mysql_query($sql_uf);
		if (mysql_num_rows($query_uf) > 0) {
			while ($linha_uf = mysql_fetch_object($query_uf)) {
				if ($linha_uf -> fmun_uf == $edtEstado) {
					echo "<option value='" . $linha_uf -> fmun_uf . "'selected>" . $linha_uf -> fmun_uf . "</option>";
				} else {
					echo "<option value='" . $linha_uf -> fmun_uf . "'>" . $linha_uf -> fmun_uf . "</option>";
				}
			}
		}

	}

	/**
	 * Metodo que mostra as cidade do estado
	 * @access public
	 * @param String $estado, $edtCidade
	 * @return cidades em option
	 */
	public function cidades($estado, $edtCidade) {
		$sql_cidade = "SELECT fmun_desc,fmun_cod FROM fmunicipios where fmun_uf = '$estado'
					   order by fmun_desc";
		$query_cidade = mysql_query($sql_cidade);
		if (mysql_num_rows($query_cidade) > 0) {
			echo "<option value='0' selected>Escolha Cidade</option>";
			while ($linha_cidade = mysql_fetch_object($query_cidade)) {
				if ($linha_cidade -> fmun_cod == $edtCidade) {
					echo "<option value='" . $linha_cidade -> fmun_cod . "'selected>" . any_accentuation($linha_cidade -> fmun_desc) . " </option>";
				} else {
					echo "<option value='" . $linha_cidade -> fmun_cod  . "'>" . $linha_cidade -> fmun_desc . "</option>";
				}
			}
		}
	}

	/**
	 * Metodo que mostra os bairros da cidade
	 * @access public
	 * @param String $cidade, $lstBairro
	 * @return Bairros em option
	 */
	public function bairros($cidade1, $lstBairro) {
		$sql_bair = "SELECT bair_cod,bair_desc FROM bairros
					 WHERE bair_cidade = '$cidade1'
					 ORDER BY bair_desc";
		$query_bair = mysql_query($sql_bair);
		if (mysql_num_rows($query_bair) > 0) {
			echo "<option value='0' selected>Escolha Bairro</option>";
			while ($linha_bair = mysql_fetch_object($query_bair)) {
				$bairrodesc = $linha_bair -> bair_desc;
				if ($linha_bair -> bair_cod == $lstBairro) {
					echo "<option value='" . $linha_bair -> bair_cod . "'selected>" . any_accentuation($bairrodesc) . "	</option>";
				} else {
					echo "<option value='" . $linha_bair -> bair_cod . "'>" . $bairrodesc . "</option>";
				}
			}
		}
	}

	/**
	 * Metodo que mostra todos os paises
	 * @access public
	 * @return Nacionalidade em option
	 */
	public function nacionalidade() {
		$sql_pais = "SELECT pai_cod,pai_nome FROM paises
					 ORDER BY pai_nome";
		$query_pais = mysql_query($sql_pais);
		if (mysql_num_rows($query_pais) > 0) {
			echo "<option value='0' selected>Escolha o País</option>";
			while ($linha_pais = mysql_fetch_object($query_pais)) {
				$paisdesc = $linha_pais -> pai_nome;
				echo "<option value='" . $linha_pais -> pai_cod . "'>" . $paisdesc . "</option>";
			}
		}
	}
	
	/**
	 * Metodo que mostra a naturalidade
	 * @access public
	 * @return naturalidade em option
	 */
	public function naturalidade() {
		$sql_nat = "SELECT fmun_cod,fmun_desc FROM fmunicipios";
		$query_nat = mysql_query($sql_nat);
		if (mysql_num_rows($query_nat) > 0) {
			echo "<option value='00' selected>Natural de</option>";
			while ($linha_nat = mysql_fetch_object($query_nat)) {
				$natdesc = $linha_nat -> fmun_desc;
				echo "<option value='" . $natdesc . "'>" . $natdesc . "</option>";
			}
		}
	}
	
	/**
	 * Metodo que mostra todos os times de futebol
	 * @access public
	 * @return Times em option
	 */
	public function times() {
		$sql_time = "SELECT tim_cod,tim_nome FROM timesfutebol
					 ORDER BY tim_nome";
		$query_time = mysql_query($sql_time);
		if (mysql_num_rows($query_time) > 0) {
			echo "<option value='0' selected>Escolha um Time</option>";
			while ($linha_time = mysql_fetch_object($query_time)) {
				$timedesc = $linha_time -> tim_nome;
				echo "<option value='" . $linha_time -> tim_cod . "'>" . $timedesc . "</option>";
			}
		}
	}
	
	/**
	 * Metodo que mostra os tipos de residencia
	 * @access public
	 * @return Tipo de residencia em option
	 */
	public function tresidencia() {
		$sql_tresid = "SELECT tresid_cod,tresid_nome FROM tiporesidencia";
		$query_tresid = mysql_query($sql_tresid);
		if (mysql_num_rows($query_tresid) > 0) {
			while ($linha_tresid = mysql_fetch_object($query_tresid)) {
				$tresiddesc = $linha_tresid -> tresid_nome;
				echo "<option value='" . $linha_tresid -> tresid_cod . "'>" . $tresiddesc . "</option>";
			}
		}
	}
	
	/**
	 * Metodo que mostra os tipos de residencia
	 * @access public
	 * @return Tipo de residencia em option
	 */
	public function tendereco() {
		$sql_te = "SELECT te_cod,te_nome FROM tipoend";
		$query_te = mysql_query($sql_te);
		if (mysql_num_rows($query_te) > 0) {
			while ($linha_te = mysql_fetch_object($query_te)) {
				$tedesc = $linha_te -> te_nome;
				echo "<option value='" . $linha_te -> te_cod . "'>" . $tedesc . "</option>";
			}
		}
	}
	
	
	/**
	 * Metodo que mostra os bancos
	 * @access public
	 * @return Bancos em option
	 */
	public function banco() {
		$sql_banco = "SELECT bco_cod,bco_desc FROM banco";
		$query_banco = mysql_query($sql_banco);
		if (mysql_num_rows($query_banco) > 0) {
			while ($linha_banco = mysql_fetch_object($query_banco)) {
				$bancodesc = $linha_banco -> bco_desc;
				echo "<option value='" . $linha_banco -> bco_cod . "'>" . $bancodesc . "</option>";
			}
			echo "<option value='00000'>SEM CONTA</option>";
		}
	}
	
	/**
	 * Metodo que verifica se cidade esta cadastrada
	 * @access public
	 * @param String $edtCidadea
	 * @return String $edtCidadedesc
	 */
	public function procuraCidade($edtCidade) {
		$sql_cidade = "SELECT fmun_desc FROM fmunicipios WHERE fmun_cod = '$edtCidade'";
		$query_cidade = mysql_query($sql_cidade);
		if (mysql_num_rows($query_cidade) > 0) {
			$linha_cidade = mysql_fetch_object($query_cidade);
			//return $edtCidadedesc = $linha_cidade -> fmun_desc;
			return $linha_cidade -> fmun_desc;
		} else {
			//return $edtCidadedesc = "SEM CIDADE";
			return "SEM CIDADE";
		}
	}

	/**
	 * Metodo que verifica se o bairro esta cadastrado
	 * @access public
	 * @param String $edtBairro
	 * @return Stirng $edtBairrodesc
	 */
	public function procuraBairro($edtBairro) {
		$sql_bairro = "SELECT bair_desc FROM bairros WHERE bair_cod = '$edtBairro';";
		$query_bairro = mysql_query($sql_bairro);
		if (mysql_num_rows($query_bairro) > 0) {
			$linha_bairro = mysql_fetch_object($query_bairro);
			//return $edtBairrodesc = $linha_bairro -> bair_desc;
			return $linha_bairro -> bair_desc;
		} else {
			//return $edtBairrodesc = "SEM BAIRRO";
			return "SEM BAIRRO";
		}
	}
}
?>