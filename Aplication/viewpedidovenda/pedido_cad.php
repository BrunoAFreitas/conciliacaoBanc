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
 * @example test_aut_md.php
 *
 */
set_time_limit(30000);
include_once ("function_dados.php");
include_once ("../view/consultaDom.class.php");
include_once ("../view/AtvEconomica.class.php");
/**
 * Variavel que instancia os metodos da class
 * function_dados();
 */
$dom = new ConsultaDom('0008','rosani'); 
$atv = new ConsultaAtvEconomica('0008','rosani');

$valores = new function_dados();
$date = date("d/m/Y");
$hora = date("H:i:s");

$ljcod = $valores -> ljcod;
$acelogin = $valores -> acelogin;


// variaveis do proprio codigo
$trava_alt = "disabled";
$caixatravado = "N";
$sql_trc = "SELECT trc_loja, trc_motivo FROM travacaixa WHERE trc_loja = '$ljcod' AND trc_excluido = 'N';";
   $query_trc = mysql_query($sql_trc);
   if(mysql_num_rows($query_trc) > 0){
     $linha_trc = mysql_fetch_object($query_trc);	   
	 $caixatravado = "S";
   }else{
	 $caixatravado = "N";	   
   }
   
	if ($caixatravado == "S") {
		include("caixafechado.php" );
	} else {
?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>:: gercom.NET - Pedido de Venda ::</title>
	<!-- Estes sao os arquivos de estilo -->
	<link rel="stylesheet" type="text/css" href="estilo_css/jquery.autocomplete.css" />
	<link rel="stylesheet" type="text/css" href="estilo_css/estili_cad.css" />
	<link rel="stylesheet" type="text/css" href="estilo_css/estilomodal.css" />
	<link rel="stylesheet" href="estilo_css/jquery-ui_datepick.css" />

	<!-- Este e o diretorio do jquery -->
	<!-- 
		Esta pagina vai ficar por questoes de teste mas em todo caso e so comentar
		-->
	<script type="text/javascript" src="jquey_plugins/jquery-ui_1.10.4.min.js"></script>
	<!-- <script type="text/javascript" src="jquey_plugins/jquery-1.9.1.js"></script> -->
	
	<!-- este e outro diretoria do jquery para gravar os dados -->
	<script type="text/javascript" src="jquey_plugins/jquery_grava-1.7.1.js"></script>
	<!-- este diretoria e para poder chamar o calendario -->
	<script type="text/javascript" src="jquey_plugins/jquery-ui_1.9.0.js"></script>
	<!-- este e para chamar o autocomplete -->
	<script type='text/javascript' src='jquey_plugins/jquery.autocomplete.js'></script>
	<!-- este e para chamar as mascaras -->
	<script type="text/javascript" src="jquey_plugins/jquery_mask1.2.2.js"></script>
	<!-- este e para criar hintes nos campos de texto -->
	<script type='text/javascript' src='jquey_plugins/jquery_coolinput.js'></script>

	<script type='text/javascript' src='funcaoJs/functiontest.js'></script>
	<script type="text/javascript" src="funcaoJs/valida.js"></script>
	<script type="text/javascript">
		// Este mascaramento e da pagina cli_cad.php pois ele esta sendo incluida no modal
		// entao pode-se herdar dessa pagina que vai funcionar
		$(function() {
			$("#edtCgcCpf").mask("999.999.999-99");
			$("#edtCgcCnpj").mask("99.999.999/9999-99");
			$("#edtCNPJtrab").mask("99.999.999/9999-99");
			$("#edtDtEm").mask("99/99/9999");
			$("#edtDtNasc").mask("99/99/9999");
			$("#edtQtFi").mask("99");
			$("#edtNumBanco").mask("999");
			$("#edtConta").mask("99999999");
			$("#edtCep").mask("99.999-999");
			$("#edtResDesd").mask("99/99/9999");
			$("#edtFone").mask("(99) 9999-9999");
			$("#edtCelular1").mask("(99) 9999-9999");
			$("#edtCelular2").mask("(99) 9999-9999");
			$("#edtNum").mask("9999");
			$("#edtNumTrabalho").mask("9999");
			$("#edtDtAdmis").mask("99/99/9999");
			$("#edtFoneTrab").mask("(99) 9999-9999");
			$("#edtTelRef1").mask("(99) 9999-9999");
			$("#edtTelRef2").mask("(99) 9999-9999");
			$("#edtFoneAge").mask("(99) 9999-9999");
			$("#edtCepTrab").mask("99.999-999");
			$("#edtDtAbet").mask("99/99/9999");
			$("#edtDesde").mask("99/99/9999");
			$("#edtDataAtiv").mask("99/99/9999");
			$("#edtRenda").mask("99.999,99");
			$("#edtOtRendas").mask("99.999,99");
			$("#edtPatrim").mask("99.999,99");
			$("#edtFatMen").mask("99.999,99");
			$("#edtPatri").mask("99.999,99");
			
			// para colocar o hint nos campos de texto
			$("#edtEndEntrega").coolinput();
			$("#edtRedeSoc").coolinput();
		});
	</script>

</head>
<body background="imagens/fundomain.jpeg">
	<form method="post" id="form_cleinte" name="form_cleinte" action="pedido_cad.php?">
		<center>
			<table width="980" border="0">
				<tr>
					<td><font size="+2" > Pedido de Venda: </font></td>
				</tr>
			</table>
			<table width="980" border="0" >
				<tr>
					<td width="159">Nº Pedido de Venda</td>
					<td width="156">Emissão</td>
					<td width="300" >Cleinte</td>
					<td width="67" ></td>
					<td width="156">Vendedor</td>
					<td width="117">Tipo Venda</td>
				</tr>
				<tr>
					<td>
					<input type="text" id="pedido_venda" size="17" disabled value="<?php $valores->pedvend($ljcod) ?>"
					class="estCampoTexto" />
					</td>
					<td>
					<input type="text" id="emissao" size="17" disabled value="<?php $valores -> emissao($date); ?>"
					class="estCampoTexto" />
					</td>
					<td>
					<input type="text" id="lstCli" onFocus="lookup(form_cleinte.lstCli);" size="60px" class="estCampoTexto" />
					</td>
					<td align="left"><a href="#janela1" rel="modal"> <img src="imagens/mais.jpg" height="25px" width="25px" title="Cadastro de Cliente!"></img> </a></td>
					<td>
					<select id="lstVend" class="estCampoTexto" >
						<?php $valores -> vendedor($ljcod); ?>
					</select></td>
					<td>
					<input type="button" id="gravar_cab" value="Gravar Cabeçalho" class="estButton" onclick="but_gravar();" />
					</td>
				</tr>
			</table>
		</center>

		<!-- Criando a tela de Modal
		que chama a dela de cadastro de cliente
		-->
		<div class="window" id="janela1" >
			<a href="#" class="fechar"><img src="imagens/apagar.gif" /></a>
			<?php
			/**
			 * Este codigo e executado logo na chamada da pagina cli_cad.php
			 * para verificar os dados do usuario e da loja e valida-lo caso tenha
			 * acesso a pagina
			 * */
			$valores -> dadosUser($acelogin, $ljcod);
			if ($valores -> permLoja == 'S') {
				/**
				 * essas variaveis são usadas na pagina cli_cad.php
				 */
				$cidade = $valores -> cidade;
				$estado = $valores -> estado;
				$novocad = $valores -> novocad;
				// tem que consertar o data
				$data = $valores -> muda_data_en($data);
				// incluindo a pagina que cadastra cliente
				include_once ("cli_cad1.2.php");
				//include_once ("cli_cad1_teste.php");
				// caso o usuario não tenha permissões para acesso vai chamar outra pagina
			} else {
				echo "Você não tem Permissão";
				//include("naoautorizado.php");
			}
			?>
		</div>
		<!-- mascara para cobrir o site -->
		<div id="mascara" />
	</form>
</body>
</html>
<?php
}
?>