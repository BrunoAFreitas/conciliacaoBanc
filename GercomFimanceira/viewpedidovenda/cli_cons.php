<?php
/**
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cli_cons.php
 * 
 * pagina para pesquisa de cliente tempo a escolha por nome
 * cidade cpf e mail tambem por credito do cleinte
 * 
 */
include_once ("function_dados.php");
//include("auditoria.php");

$valores = new function_dados();

$ljcod = $valores -> ljcod;
$acelogin = $valores -> acelogin;

// recuperando os dados das variaveis
$function = $_GET['form'];
// dados para pesquisa
$lstTipo = $_POST['lstTipo'];
$edtNomeCgc = $_POST['edtNomeCgc'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
// para pesquisa de credito
$credito = $_GET['credito'];
$functionDados = $_GET['form'];

$valores -> dadosUser($acelogin, $ljcod);
// passando a permisao da loja
if ($valores -> permLoja == 'S') {
	
?>

<html>

	<head>
		<title>:: gercom.NET - Consulta de Clientes por Nome ou CGC ::</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		<link rel="stylesheet" type="text/css" href="estilo_css/jquery.autocomplete.css" />
		<link rel="stylesheet" type="text/css" href="estilo_css/estili_cad.css" />
		<link rel="stylesheet" type="text/css" href="estilo_css/estilomodal.css" />

		<!-- Este e o diretorio do jquery -->
		<script type="text/javascript" src="jquey_plugins/jquery-1.9.1.js"></script>
		<!-- este e outro diretoria do jquery para gravar os dados -->
		<script type="text/javascript" src="jquey_plugins/jquery_grava-1.7.1.js"></script>
		<!-- este e para chamar o autocomplete -->
		<script type='text/javascript' src='jquey_plugins/jquery.autocomplete.js'></script>
		<script type='text/javascript' src='funcaoJs/functiontest.js'></script>
		<!-- este e para criar hintes nos campos de texto -->
		<script type='text/javascript' src='jquey_plugins/jquery_coolinput.js'></script>

		<script>
			$(function() {
				// para colocar o hint nos campos de texto
				$("#edtEndEntrega").coolinput();
				$("#edtRedeSoc").coolinput();
			});

			function submit_action(caminho) {
				document.formclicons.action = caminho;
				document.formclicons.method = 'post';
				document.formclicons.submit();
			}

			function habilita1() {
				var obj = document.getElementById('lstTipo').selectedIndex;
				valoresIn("cpf");
				valoresIn("email");
				if (obj == 0) {
					valoresVi("edtNomeCgc");
					valoresIn("cpf");
					valoresIn("email");
				}
				if (obj == 1) {
					valoresIn("edtNomeCgc");
					valoresVi("cpf");
					valoresIn("email");
				}
				if (obj == 2 || obj == 3) {
					valoresIn("edtNomeCgc");
					valoresIn("cpf");
					valoresVi("email");
				}
			}

			// metodo para deixar invisivel
			function valoresIn(val) {
				document.getElementById(val).style.display = 'none';
				document.getElementById(val).style.visibility = 'hidden';
			}

			// metodo para deixar visivel
			function valoresVi(val) {
				document.getElementById(val).style.display = 'block';
				document.getElementById(val).style.visibility = 'visible';
			}
		</script>

	</head>

	<body background="imagens/fundomain.jpeg" onload="habilita1();">
		<table table width="980" border="0" align="center">
			<tr>
				<td><font size="+2" >Consulta de Clientes Por Nome/CGC</font></td>
			</tr>
			<tr>
				<td>
				<form action="cli_cons.php?form=pes" method="post" name="formclicons" id="formclicons">
					<table align="center" width="980">
						<tr>
							<td> Consultar por:</td>
						</tr>
						<tr>
							<td>
							 <select name="lstTipo" id="lstTipo" class="estCampoTexto" onchange="habilita();">
								<option value='nomeraz' selected>NOME RAZAO</option>
								<option value='cpf'>CPF/CNPJ</option>
								<option value='email'>EMAIL</option>
								<!-- <option value='cidade'>CIDADE</option> -->
							 </select>
							</td>
							<td align="center">
							<input type="text" id="edtNomeCgc" name="edtNomeCgc" onFocus="lookup(formclicons.edtNomeCgc);" size="65px" class="estCampoTexto" title="Por Nome" >
							<input type="text" id="cpf" name="cpf" size="65px" class="estCampoTexto" title="Por Cpf" />
							<input type="text" id="email" name="email" size="65px" class="estCampoTexto" title="Por Email" />
							</td>
							<td align="right">
							<input type="button" value="APENAS CLIENTES COM CREDITO" onClick="submit_action('cli_cons.php?form=pes&credito=s')" class="estButton">
							</td>
							<td align="right">
							<input type="submit" value="CONSULTAR" class="estButton">
							</td>
						</tr>
					</table>
				</form></td>
			</tr>

			<!-- Tabela com os dados da pesquisa -->
			<?php
				// incluindo o codigo da pesquisa
				include_once ("cli_consDados.php");
			?>
			<!-- Tabela com os dados da pesquisa -->
			</table>
	</body>
</html>
<?php
} else {
echo "nao autorizado!";
}
?>