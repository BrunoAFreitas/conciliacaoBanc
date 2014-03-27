<?php //funcao para checar email e dominio valido
function ValidaEmail($str_mail) {
	if (eregi("^[-_a-z0-9]+(\.[-_a-z0-9]+)*\@([-a-z0-9]+\.)*([a-z]{2,4})$", $str_mail)) {
		$dns_mail = explode("@", $str_mail);
		if (checkdnsrr($dns_mail[1])) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

//funcao para checar
function validaCPF($cpf) {// Verifiva se o número digitado contém todos os digitos
	$cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

	// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
	if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
		return false;
	} else {// Calcula os números para verificar se o CPF é verdadeiro
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}

			$d = ((10 * $d) % 11) % 10;

			if ($cpf{$c} != $d) {
				return false;
			}
		}
		return true;
	}
}

// funcoes aplicadas...
function UltimoDia($ano, $mes) {
	if (((fmod($ano, 4) == 0) and (fmod($ano, 100) != 0)) or (fmod($ano, 400) == 0)) {
		$dias_fevereiro = 29;
	} else {
		$dias_fevereiro = 28;
	}
	switch($mes) {
		case 01 :
			return 31;
			break;
		case 02 :
			return $dias_fevereiro;
			break;
		case 03 :
			return 31;
			break;
		case 04 :
			return 30;
			break;
		case 05 :
			return 31;
			break;
		case 06 :
			return 30;
			break;
		case 07 :
			return 31;
			break;
			echo $mes . 'mes';
		case 08 :
			return 31;
			break;
		case 09 :
			return 30;
			break;
		case 10 :
			return 31;
			break;
		case 11 :
			return 30;
			break;
		case 12 :
			return 31;
			break;
	}
}

function nomeMes($mes) {
	switch ($mes) {
		case "01" :
			$mes = JANEIRO;
			break;
		case "02" :
			$mes = FEVEREIRO;
			break;
		case "03" :
			$mes = MARÇO;
			break;
		case "04" :
			$mes = ABRIL;
			break;
		case "05" :
			$mes = MAIO;
			break;
		case "06" :
			$mes = JUNHO;
			break;
		case "07" :
			$mes = JULHO;
			break;
		case "08" :
			$mes = AGOSTO;
			break;
		case "09" :
			$mes = SETEMBRO;
			break;
		case "10" :
			$mes = OUTUBRO;
			break;
		case "11" :
			$mes = NOVEMBRO;
			break;
		case "12" :
			$mes = DEZEMBRO;
			break;
	}
	return $mes;
}

function muda_data_pt($data) {
	//mudar data de 2002-00-00 para 00/00/2002; formato brasileiro
	$aux = explode("-", $data);
	$c = array_reverse($aux);
	$data = implode("/", $c);
	return $data;
}

function muda_data_en($data) {
	//mudar data de 2002/00/00 para 2002-00-00; formato americano
	$aux = explode("/", $data);
	$c = array_reverse($aux);
	$data = implode("-", $c);
	return $data;
}

// converte valor 1.000,00 para 1000.00
function valor_mysql($valor) {
	$valor = str_replace(".", "", $valor);
	$valor = str_replace(",", ".", $valor);
	return $valor;
}

/*
 function date_diff($from, $to) {
 // Use DD-MM-AA ou DD-MM-AAAA
 list($from_day, $from_month, $from_year) = explode("/", $from);
 list($to_day, $to_month, $to_year) = explode("/", $to);

 $from_date = mktime(0,0,0,$from_month,$from_day,$from_year);
 $to_date = mktime(0,0,0,$to_month,$to_day,$to_year);

 $days = ($to_date - $from_date)/86400;

 return ceil($days);
 }
 */
function any_accentuation($string = "") {

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
}// end function

function LimparTexto($texto) {
	$texto = str_replace(array("<", ">", "\\", "/", "=", "'", "?", "º", "£", "¥", "©", "®", "±", "º", "?", "£", "=", "=", "®", "µ", "$", "%", "&", "!", "(", ")", "*", "+", ",", "-", ".", "/", ";", ":", "@", "{", "}", "¤"), "", $texto);
	return $texto;
}

function EntreDatas($inicio, $fim) {
	$aInicio = Explode("/", $inicio);
	$aFim = Explode("/", $fim);
	$nTempo = mktime(0, 0, 0, $aFim[1], $aFim[0], $aFim[2]);
	$nTempo1 = mktime(0, 0, 0, $aInicio[1], $aInicio[0], $aInicio[2]);
	return round(($nTempo - $nTempo1) / 86400) + 1;
}

function somadata2($data, $nDias) {
	$begin_raw = '2012-01-24';
	$begin = strtotime($begin_raw);
	$course_duration = '3';
	$end = mktime(0, 0, 0, date('m', $begin) + $course_duration, date('d', $begin), date('Y', $begin));
}

function somadata($data, $nDias) {
	if (!isset($nDias)) {
		$nDias = 1;
	}
	$aVet = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $aVet[1], $aVet[0] + $nDias, $aVet[2]));
}

// SUBTRAIR DATA
function subtraidata($date, $days) {
	$thisyear = substr($date, 0, 4);
	$thismonth = substr($date, 4, 2);
	$thisday = substr($date, 6, 2);
	$nextdate = mktime(0, 0, 0, $thismonth, $thisday - $days, $thisyear);
	return strftime("%d/%m/%Y", $nextdate);
}

function difdata($inicio, $fim) {
	$aInicio = Explode("/", $inicio);
	$aFinal = Explode("/", $fim);

	date("d", mktime(0, 0, 0, $aFinal[0] - $aInicio[0], $aFinal[1] - $aInicio[1], $aFinal[2] - $aInicio[2]));
}

function semana($data) {
	$aVet = Explode("/", $data);
	$nDia = date("w", mktime(0, 0, 0, $aVet[1], $aVet[0], $aVet[2]));
	return substr("domsegterquaquisexsab", ($nDia + 1) * 3 - 3, 3);
}

function databr($data) {
	$qual = strchr("/", $data);
	if ($qual == "") { $qual = "-";
	}
	$aVet = Explode($qual, $data);
	$ano = $aVet[0];
	$mes = $aVet[1];
	$dia = $aVet[2];
	return date("d" . $qual . "m" . $qual . "Y", mktime(0, 0, 0, $mes, $dia, $ano));
}

function dataen($data) {
	$qual = strpos($data, "/");
	if ($qual > 0) {
		$qual = "/";
	} else {
		$qual = "-";
	}
	$aVet = Explode($qual, $data);
	$ano = $aVet[2];
	$mes = $aVet[1];
	$dia = $aVet[0];
	return date("Y" . $qual . "m" . $qual . "d", mktime(0, 0, 0, $mes, $dia, $ano));
}

function datadehoje() {
	$dia = date("d");
	$mes = date("m");
	$ano = date("Y");
	$aux = mktime(0, 0, 0, $mes, $dia, $ano);
	return date("d/m/Y", $aux);
}

// inicio - leandra - contas a pagar 14-06-2012
function dataAnt($data) {
	$data_hoje = date('d/m/Y');
	$aVet = Explode("/", $data_hoje);
	return date("d/m/Y", mktime(0, 0, 0, $aVet[1], $aVet[0] - 1, $aVet[2]));
}

function dataVenci($data, $diaVenc) {
	if (!isset($diaVenc)) {
		$diaVenc = 1;
	}
	$dia = $diaVenc;
	$ano = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $ano[1] + 1, $dia, $ano[2]));
}

function muda_data_in($data) {
	//mudar data de 2002/00/00 para 2002-00-00; formato americano
	$aux = explode("-", $data);
	$c = array_reverse($aux);
	$data = implode("-", $c);
	return $data;
}

function valor_usaBr($valor_rec) {
	$num_br = number_format($valor_rec, 2, ',', '.');
	return $num_br;
}

// fim - leandra - contas a pagar 14-06-2012

/*====================== LEANDRA 11/07/2012 - PROJETO MODULO DE ENTREGAS=====================*/
//retorna a data de uma semana atras
function UltSemana($data) {
	$ano = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $ano[1], $ano[0] - 7, $ano[2]));
}

//retorna a data de um mes atras
function UltMes($data) {
	$ano = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $ano[1] - 1, $ano[0], $ano[2]));
}

//retorna a data de 2 mes atras
function UltdoisMes($data) {
	$ano = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $ano[1] - 2, $ano[0], $ano[2]));
}

//retorna a data de 3 mes atras
function UlttresMes($data) {
	$ano = Explode("/", $data);
	return date("d/m/Y", mktime(0, 0, 0, $ano[1] - 3, $ano[0], $ano[2]));
}

/*====================== LEANDRA 20/07/2012 - PROJETO MODULO DE ENTREGAS=====================*/
//soma dias a data
function DataDias($dataPv, $dias) {
	$ano = explode("-", $dataPv);
	$anoSomado = date("d/m/Y", mktime(0, 0, 0, $ano[1], $ano[2] + $dias, $ano[0]));
	$data = explode("/", $anoSomado);
	$dataFormato = array_reverse($data);
	$Formacao = implode("", $dataFormato);
	return $Formacao;
}

/*====================== LEANDRA 20/07/2012 - PROJETO MODULO DE ENTREGAS=====================*/
//retorna diferença entre duas datas
function dif_data($dataPv, $data2) {
	$ano = Explode("/", $dataPv);
	$data_reverse = array_reverse($ano);
	$data1 = implode("", $data_reverse);
	return $data1;
}

/*====================== LEANDRA 20/07/2012 - PROJETO MODULO DE ENTREGAS=====================*/
//Transforma Data em numero
function DataNum($dataPv) {
	$ano = Explode("-", $dataPv);
	$data1 = implode("", $ano);
	return $data1;
}

//====================== JALEN 09/08/2012 - PROJETO MODULO DE ENTREGAS ========================
function somar_dias_uteis($str_data, $int_qtd_dias_somar = 7) {
	$str_data = substr($str_data, 0, 10);

	if (preg_match("@/@", $str_data) == 1) {
		$str_data = implode("-", array_reverse(explode("/", $str_data)));
	}

	$array_data = explode('-', $str_data);
	$count_days = 0;
	$int_qtd_dias_uteis = 0;

	while ($int_qtd_dias_uteis < $int_qtd_dias_somar) {
		$count_days++;
		if (($dias_da_semana = gmdate('w', strtotime('+' . $count_days . ' day', mktime(0, 0, 0, $array_data[1], $array_data[2], $array_data[0])))) != '0' && $dias_da_semana != '6') {
			$int_qtd_dias_uteis++;
		}
	}

	return gmdate('d/m/Y', strtotime('+' . $count_days . ' day', strtotime($str_data)));
}

/*=======================LEANDRA 07/11/2012 - PROJETO FLUXO DE CAIXA====================*/
function Data_semana($datainicial, $dias) {
	$ano = explode("-", $datainicial);
	$anoSomado = date("d/m/Y", mktime(0, 0, 0, $ano[1], $ano[2] + $dias, $ano[0]));
	$data1 = explode("/", $anoSomado);
	$data_reverse = array_reverse($data1);
	$datafinal = $data_reverse[0] . "-" . $data_reverse[1] . "-" . $data_reverse[2];
	return $datafinal;
}

/*=======================LEANDRA 07/11/2012 - PROJETO FLUXO DE CAIXA====================*/
function data_numerica($data) {
	$ano = Explode("/", $data);
	$data_reverse = array_reverse($ano);
	$data1 = implode("", $data_reverse);
	return $data1;
}

/**-------------------------LEANDRA 22/11/2012 - PROJETO FLUXO DE CAIXA ---------------*/
function diasemana($data) {
	$ano = substr("$data", 0, 4);
	$mes = substr("$data", 5, -3);
	$dia = substr("$data", 8, 9);

	$diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));

	switch($diasemana) {
		case"0" :
			$diasemana = "Domingo";
			break;
		case"1" :
			$diasemana = "Segunda-Feira";
			break;
		case"2" :
			$diasemana = "Terça-Feira";
			break;
		case"3" :
			$diasemana = "Quarta-Feira";
			break;
		case"4" :
			$diasemana = "Quinta-Feira";
			break;
		case"5" :
			$diasemana = "Sexta-Feira";
			break;
		case"6" :
			$diasemana = "Sábado";
			break;
	}
	return $diasemana;
}
?>
