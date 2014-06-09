<?php
/***
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cadastrar_cliente.css
 *
 * pagina para receber os dados do cliente
 */

include_once ("function_dados.php");
include_once ("funcdata.php");
include_once ("connection/EnviarEmail.php");

/**
 * Variaveis de instanciação das class
 */
$valores = new function_dados();
$cadastra = new Manipula();
$sendEmail = new EnviarEmail();
/**
 * Variaveis que pega a loja e o login do vendedor
 */
$ljcod = $valores -> ljcod;
$acelogin = $valores -> acelogin;
// neste caso e executa para pegar saber o valor do novo cad
$valores -> dadosUser($acelogin, $ljcod);
$novocad1 = $valores -> novocad;
// pegando o nome da tabela
$cadastra -> setTablet("clientes");
// variaiveis
$date = $valores -> muda_data_en(date("d/m/Y"));
$hora = date("H:i:s");
$tipoCli = $_POST['tipoCli'];
$edtCgcCpf = $_POST['edtCgcCpf'];
$edtInscr = $_POST['edtInscr'];
$edtCgcCnpj = $_POST['edtCgcCnpj'];
$edtOrgEm = $_POST['edtOrgEm'];
$edtDtEm = $_POST['edtDtEm'];
$edtRazao = $_POST['edtRazao'];
$selSex = $_POST['selSex'];
$edtDtNasc = $_POST['edtDtNasc'];
$edtFantasia = $_POST['edtFantasia'];
$selNatu = $_POST['selNatu'];
$selNacio = $_POST['selNacio'];
$selTime = $_POST['selTime'];
$selEstCiv = $_POST['selEstCiv'];
$edtConjuge = $_POST['edtConjuge'];
$edtPai = $_POST['edtPai'];
$edtMae = $_POST['edtMae'];
$edtQtFi = $_POST['edtQtFi'];
$edtTipoEnde = $_POST['edtTipoEnde'];
$edtEnd = $_POST['edtEnd'];
$edtNum = $_POST['edtNum'];
$edtCompt = $_POST['edtCompt'];
$edtEstado = $_POST['edtEstado'];
$edtCidade = $_POST['edtCidade'];
$edtBairro = $_POST['edtBairro'];
$edtCep = $_POST['edtCep'];
$edtTipRes = $_POST['edtTipRes'];
$edtResDesd = $_POST['edtResDesd'];
$edtPontRef = $_POST['edtPontRef'];
$edtEndEntrega = $_POST['edtEndEntrega'];
$edtEmail = $_POST['edtEmail'];
$edtFone = $_POST['edtFone'];
$edtCelular1 = $_POST['edtCelular1'];
$edtOperadora1 = $_POST['edtOperadora1'];
$edtCelular2 = $_POST['edtCelular2'];
$edtOperadora2 = $_POST['edtOperadora2'];
$selRedeSoc = $_POST['selRedeSoc'];
$edtRedeSoc = $_POST['edtRedeSoc'];
$edtProfissao = $_POST['edtProfissao'];
$edtTrabalho = $_POST['edtTrabalho'];
$edtDtAdmis = $_POST['edtDtAdmis'];
$edtFoneTrab = $_POST['edtFoneTrab'];
$edtTipoEndTrabalho = $_POST['edtTipoEndTrabalho'];
$edtEndTrabalho = $_POST['edtEndTrabalho'];
$edtNumTrabalho = $_POST['edtNumTrabalho'];
$edtComTrabalho = $_POST['edtComTrabalho'];
$selEstadoTrab = $_POST['selEstadoTrab'];
$selCidadeTrab = $_POST['selCidadeTrab'];
$selBairroTrab = $_POST['selBairroTrab'];
$edtCepTrab = $_POST['edtCepTrab'];
$edtRenda = $_POST['edtRenda'];
$edtOtRendas = $_POST['edtOtRendas'];
$selBanco = $_POST['selBanco'];
$edtAgencia = $_POST['edtAgencia'];
$edtConta = $_POST['edtConta'];
$edtTipoConta = $_POST['edtTipoConta'];
$edtDtAbet = $_POST['edtDtAbet'];
$edtExtras = $_POST['edtExtras'];
/**
 * novos campos
 */
$selTipoFone = $_POST['selTipoFone'];
$selSede = $_POST['selSede'];//cli_sedepropria
$selTipoDoc = $_POST['selTipoDoc'];//cli_tipodoc
$edtUf = $_POST['edtUf'];//cli_estadorgemissor
$selPaisDoc = $_POST['selPaisDoc'];//cli_paisdocumento
$selTipoComp = $_POST['selTipoComp'];//cli_tbcomprenda
$selTipoRenda = $_POST['selTipoRenda'];//cli_numoutrasrendas
$edtFonaAge = $_POST['edtFoneAge'];//cli_telbanco
$edtNumBanco = $_POST['edtNumBanco'];//cli_numerobanco
$edtAgencia = $_POST['edtAgencia'];//cli_numeroagenc
$edtTelRef1 = $_POST['edtTelRef1'];//cli_telref1
$edtTelRef2 = $_POST['edtTelRef2'];//cli_telref2
$edtNomeRef1 = $_POST['edtNomeRef1'];//cli_nomeref1
$edtNomeRef2 = $_POST['edtNomeRef2'];//cli_nomeref2
$edtPatrim = $_POST['edtPatrim'];
$edtPatrimJ = $_POST['edtPatri'];// pessoa juridica
$selGruAtEco = $_POST['selGruAtEco'];
$selAtEco = $_POST['selAtEco'];
$edtFatMen = $_POST['edtFatMen'];
$selOcupacao = $_POST['selOcupacao'];

$profissao = explode('|', $edtProfissao);
$profissaoDes = $profissao[1];
$profissaoCod = $profissao[0];



/*
 * trava de idade
 */ 
$edtDtNascL = str_replace("/", "", $edtDtNasc);
$dateL = str_replace("/", "", $dateL);
$idade = $dateL - 18;
$keyperm = "";
if ($edtDtNascL == "01011900") {
} else if ($edtDtNascL == $dateL) {
} else if ($edtDtNascL >= $idade) {
} else {
	$keyperm = "S";
}

$keyperm = "S";
// convertendo algumas variaveis
if ($tipoCli == "fisica") {
	$edtCgcCpf = $valores -> LimparTexto($edtCgcCpf);
	$edtDtNasc = $valores -> muda_data_en($edtDtNasc);
	$edtDtEm = $valores -> muda_data_en($edtDtEm);
	$edtResDesd = $valores -> muda_data_en($edtResDesd);
	$edtDtAdmis = $valores -> muda_data_en($edtDtAdmis);
	$edtDtAbet = $valores -> muda_data_en($edtDtAbet);
	$edtLimite = $valores -> valor_mysql($edtLimite);
}
if ($tipoCli == "juridica") {
	$edtCgcCnpj = $valores -> LimparTexto($edtCgcCnpj);
	$edtDtAbet = $valores -> muda_data_en($edtDtAbet);
}
$edtEmail = strtolower($edtEmail);
/**
 * Aqui é a parte das verificações
 */
//if ($novocad1 == "S") {
	if ($edtCidade == "0") {
		echo "Não pode cadastrar sem cidade!";
	}
	if ($edtBairro == "0") {
		echo "Não pode cadastrar sem bairro";
	}
	// aqui e procurando os dados para cadastro
	$edtCidadedesc = $valores -> procuraCidade($edtCidade);
	$edtBairrodesc = $valores -> procuraBairro($edtBairro);

	$selCidadeTrabDes = $valores -> procuraCidade($selCidadeTrab);
	$selBairroTrabDes = $valores -> procuraBairro($selBairroTrab);

//}
/**
 * Fazer uma verificação se e pessoa fisica ou juridica
 */
if ($tipoCli == "fisica") {
	$tipoCli = "S";
	if ($keyperm == "S") {
		$cadastra -> setFields("cli_cgccpf, cli_emp, cli_razao, cli_fisica,
								cli_inscrg, cli_rgemissor, cli_rgdtemissao, cli_sexo, 
								cli_dtnasc, cli_naturalidade, cli_nacionalidade, cli_timefutebol, 
								cli_estadocivil, cli_conjuge, cli_pai, cli_mae, 
								cli_qtdfilhos,cli_tipoend, cli_end, cli_numeroend, 
								cli_complemento, cli_tiporesid, cli_residedesde, cli_estado, 
								cli_cidadecod, cli_cidade, cli_bairrocod, cli_cep, 
								cli_pontoref, cli_endentrega, cli_email, cli_fone,
								cli_celular1, cli_celular2, cli_operadora1, cli_operadora2, 
								cli_tipofone, cli_usaredesocial, cli_redessociais, cli_profissaocod, 
								cli_profissao, cli_ocupcod, cli_trabalho, cli_dtadmtrab, 
								cli_fonetrab, cli_tipoendtrab, cli_endtrab, cli_numerotrab, 
								cli_complementotrab, cli_estadotrab, cli_cidadetrab, cli_cidadetrabdesc, 
								cli_bairrotrab, cli_ceptrab, cli_rendamensal, cli_outrasrendas,
								cli_banco, cli_agencia, cli_conta, cli_tipoconta, 
								cli_dtaberturaconta, cli_obs, cli_incluido, cli_alterado, 
								cli_dtincluido, cli_dtalterado, cli_hora, cli_loja, 
								cli_login, cli_tipodoc, cli_estadorgemissor, cli_paisdocumento, 
								cli_tpcomprenda, cli_numoutrasrendas, cli_telbanco, cli_numerobanco, 
								cli_numeroagenc, cli_telref1, cli_telref2, cli_nomeref1,
								cli_nomeref2, cli_patrimonio, cli_bairro");

		$cadastra -> setDados(" '$edtCgcCpf', '', '$edtRazao', '$tipoCli',
		 						'$edtInscr', '$edtOrgEm', '$edtDtEm','$selSex', 
		 						'$edtDtNasc', '$selNatu', '$selNacio', '$selTime', 
		 						'$selEstCiv', '$edtConjuge', '$edtPai', '$edtMae', 
		 						'$edtQtFi', '$edtEnd', '$edtTipoEnde', '$edtNum', 
		 						'$edtCompt', '$edtTipRes', '$edtResDesd', '$edtEstado', 
		 						'$edtCidade','$edtCidadedesc', '$edtBairro', '$edtCep',
		 						'$edtPontRef', '$edtEndEntrega', '$edtEmail', '$edtFone', 
		 						'$edtCelular1', '$edtCelular2', '$edtOperadora1', '$edtOperadora2',
		 						'$selTipoFone', '$selRedeSoc', '$edtRedeSoc', '$profissaoCod',
		 						'$profissaoDes', '$selOcupacao','$edtTrabalho', '$edtDtAdmis', 
		 						'$edtFoneTrab', '$edtEndTrabalho', '$edtTipoEndTrabalho', '$edtNumTrabalho', 
		 						'$edtComTrabalho', '$selEstadoTrab', '$selCidadeTrabDes','$selCidadeTrabDes', 
		 						'$selBairroTrabDes', '$edtCepTrab', '$edtRenda', '$edtOtRendas',
								'$selBanco', '$edtAgencia', '$edtConta', '$edtTipoConta', 
								'$edtDtAbet', '$edtExtras', 'S', 'N', 
								'$date', '', '$hora', '$ljcod',
								'$acelogin', '$selTipoDoc', '$edtUf', '$selPaisDoc', 
								'$selTipoComp', '$selTipoRenda', '$edtFonaAge','$edtNumBanco',
								'$edtAgencia', '$edtTelRef1', '$edtTelRef2','$edtNomeRef1',
								'$edtNomeRef2', '$edtPatrim', '$edtBairrodesc' ");
		$cadastra -> insert();
		// mandando um email
		//$sendEmail -> sendEmail($ljcod, $edtEmail, $edtRazao);
		echo "Cadastrado com Sucesso Pessoa Fisica!";
	} else {
		echo "Data de nascimento invalida!";
	}
}
if ($tipoCli == "juridica") {
	$tipoCli = "N";
	$cadastra -> setFields("cli_cgccpf, cli_emp, cli_razao, cli_fantasia, cli_fisica, cli_inscrg,
							cli_tipoend, cli_end, cli_numeroend, cli_complemento,
							cli_estado, cli_cidadecod, cli_bairrocod, cli_cep, cli_pontoref, cli_endentrega, cli_email, 
							cli_fone, cli_celular1, cli_celular2, cli_operadora1, cli_operadora2, cli_tipofone, cli_usaredesocial, 
							cli_redessociais, cli_banco, cli_agencia, cli_conta, cli_tipoconta, cli_dtaberturaconta, cli_obs,
							cli_incluido, cli_alterado, cli_dtincluido, cli_dtalterado, cli_hora, cli_loja, cli_login,
							cli_sedepropria, cli_patrimonio, cli_telbanco, cli_numerobanco, cli_numeroagenc, cli_grupoeconomico, cli_ativeconomico, cli_rendamensal" );

	$cadastra -> setDados(" '$edtCgcCnpj', '', '$edtRazao', '$edtFantasia', '$tipoCli', '$edtInscr',
							'$edtEnd', '$edtTipoEnde', '$edtNum', '$edtCompt', 
							'$edtEstado', '$edtCidade', '$edtBairro', '$edtCep', '$edtPontRef', '$edtEndEntrega', '$edtEmail', 
							'$edtFone', '$edtCelular1', '$edtCelular2', '$edtOperadora1', '$edtOperadora2', '$selTipoFone', '$selRedeSoc', 
							'$edtRedeSoc', '$selBanco', '$edtAgencia', '$edtConta', '$edtTipoConta', '$edtDtAbet', '$edtExtras', 
							'S', 'N', '$date', '', '$hora', '$ljcod', '$acelogin' ,
							'$selSede', '$edtPatrimJ', '$edtFonaAge','$edtNumBanco','$edtAgencia', '$selGruAtEco', '$selAtEco', '$edtFatMen' ");
	$cadastra -> insert();
	echo "Cadastrado com Sucesso Pessoa Juridica!";
}

?>