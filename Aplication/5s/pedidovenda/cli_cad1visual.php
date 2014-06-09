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
 * @example cli_cad.php
 *
 * pagina para visulização dos dados do cliente
 */

// incluindo a pagina da que pesquisa os dados

include_once ("cliDados.php");

?>
<html>
	<head>
		<meta content="text/html; charset=windows-1252" http-equiv="content-type">
	    <link rel="stylesheet" type="text/css" href="estilo_css/estilomodal.css" />

		<title></title>
	</head>
	<body background="imagens/fundomain.jpeg">
		<table width="100%" align="center">
			<tbody>
				<tr>
					<td><!-- Tabela tipo de cliente -->
					<fieldset>
						<legend>
							<font size="+2" >Dados do Cliente</font>
						</legend>
						<table style="width: 100%" border="1">
							<tbody>
								<tr>
									<td>Cpf</td>
									<td><? echo $cpf; ?></td>
									<td>Endereco</td>
									<td><? echo $endereco; ?></td>
									<td>Profissao</td>
									<td><? echo $profissaocod; ?></td>
								</tr>
								<tr>
									<td>RG/Inscricao</td>
									<td><? echo $inscrg; ?></td>
									<td>Tipo Endereco</td>
									<td><? echo $tiporesid; ?></td>
									<td>Nome da Empresa</td>
									<td><? echo $trabalho; ?></td>
								</tr>
								<tr>
									<td>Orgao emissor</td>
									<td><? echo $rgemissor; ?></td>
									<td>Numero</td>
									<td><? echo $numeroend; ?></td>
									<td>Dt de admissao</td>
									<td><? echo $dtadmtrab; ?></td>
								</tr>
								<tr>
									<td>Data de Emissao</td>
									<td><? echo $rgdtemissao; ?></td>
									<td>Complemeto</td>
									<td><? echo $complemento; ?></td>
									<td>Fome trabalho</td>
									<td><? echo $fonetrab; ?></td>
								</tr>
								<tr>
									<td>Nome Razao Social</td>
									<td><? echo $razao; ?></td>
									<td>Estado</td>
									<td><? echo $estado; ?></td>
									<td>Endereco</td>
									<td><? echo $endtrab; ?></td>
								</tr>
								<tr>
									<td>Sexo</td>
									<td><? echo $sexo; ?></td>
									<td>Cidade</td>
									<td><? echo $cidadecod; ?></td>
									<td>Tipo de endereco</td>
									<td><? echo $tipoendtrab; ?></td>
								</tr>
								<tr>
									<td>Dt. Nascimento</td>
									<td><? echo $dtnasc; ?></td>
									<td>Bairro</td>
									<td><? echo $bairrocod; ?></td>
									<td>Numero</td>
									<td><? echo $numerotrab; ?></td>
								</tr>
								<tr>
									<td>Nacionalidade</td>
									<td><? echo $naturalidade; ?></td>
									<td>Cep</td>
									<td><? echo $cep; ?></td>
									<td>Complemento</td>
									<td><? echo $complementotrab; ?></td>
								</tr>
								<tr>
									<td>Naturalidade</td>
									<td><? echo $nacionalidade; ?></td>
									<td>Tipo de Residencia</td>
									<td><? echo $tipoend; ?></td>
									<td>Estado</td>
									<td><? echo $estadotrab; ?></td>
								</tr>
								<tr>
									<td>Time de Futebol</td>
									<td><? echo $timefutebol; ?></td>
									<td>Reside desde</td>
									<td><? echo $residedesde; ?></td>
									<td>Cidade</td>
									<td><? echo $cidadetrab; ?></td>
								</tr>
								<tr>
									<td>Estado Civel</td>
									<td><? echo $estadocivil; ?></td>
									<td>Ponto de Referencia</td>
									<td><? echo $pontoref; ?></td>
									<td>Bairro</td>
									<td><? echo $bairrotrab; ?></td>
								</tr>
								<tr>
									<td>Conjuge</td>
									<td><? echo $conjuge; ?></td>
									<td>Fone</td>
									<td><? echo $fone; ?></td>
									<td>Cep</td>
									<td><? echo $ceptrab; ?></td>
								</tr>
								<tr>
									<td>Qtd Filhos</td>
									<td><? echo $qtdfilhos; ?></td>
									<td>Celular</td>
									<td><? echo $celular1; ?></td>
									<td>Renda Mensal</td>
									<td><? echo $rendamensal; ?></td>
								</tr>
								<tr>
									<td>Pai</td>
									<td><? echo $pai; ?></td>
									<td>Email</td>
									<td><? echo $email; ?></td>
									<td>Outras Rendas</td>
									<td><? echo $outrasrendas; ?></td>
								</tr>
								<tr>
									<td>Mae</td>
									<td><? echo $mae; ?></td>
									<td>Rede sociais</td>
									<td><? echo $redessociais; ?></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
						<p />
						<center>
						<input type="button" id="gravar_cab" value="Imprimir Dados!" class="estButton" />
						</center>
					</fieldset></td>
				</tr>
			</tbody>
		</table>
	</body>
</html>