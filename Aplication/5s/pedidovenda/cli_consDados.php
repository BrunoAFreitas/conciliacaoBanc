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
 * @example cli_consDados.php
 * 
 *  Essa parte do codigo foi separada da pagina para melhor
 *  manutenção do codigo
 *  Essa pagina e responsavel pela pesquisa dos clientes dando um 
 *  retorno de seus dados
 */

// caso a pesquisa seja feita vai enviar um valor get 
if ($functionDados == "pes") {
	// verificando se a pesquisa e por cretido do cleinte
	if ($credito == "s") {
		$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_loja = '$ljcod' AND cli_credito > 0";
	} else {
		// caso nao seja e por estes dados
		if ($lstTipo == "cpf") {
			$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_cgccpf = '$cpf'";
		}
		if ($lstTipo == "nomeraz") {
			$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_razao LIKE '%" . $edtNomeCgc . "%'";
		}
		if ($lstTipo == "email") {
			$sql_cons = "SELECT DISTINCT * FROM clientes, loja WHERE cli_loja = lj_cod AND cli_email LIKE '%" . $email . "%'";
		}
	}

	// informando os valores
	$query_cons = mysql_query($sql_cons);
	echo $tabelaHedar = "<table table width='980' border='0' align='center'>
				<tbody>
					<tr>
						<td>Loja que Cadastrou</td>
						<td>Cnpj/Cpf</td>
						<td>Nome</td>";
	if ($credito == "s") {
		echo $tabelaHedar1 = "<td>Credito</td>";
	}
	echo $tabelaHedar2 = "<td align='right'>Visualizar</td>
						  <td align='right'>Alterar</td>
					</tr>";
	while ($linha_cons = mysql_fetch_object($query_cons)) {
		echo $tabelaBody = " <tr>
						      	<td>" . $linha_cons -> lj_fantasia . "</td>
			   			 		<td>" . $linha_cons -> cli_cgccpf . "</td>
			   			 		<td>" . $linha_cons -> cli_razao . "</td>";
		$idCli = $linha_cons -> cli_seq;
		$tipo = $linha_cons -> cli_fisica;
		if ($credito == "s") {
			echo $tabelaBody1 = "<td>" . number_format($linha_cons -> cli_credito, '2', ',', '.') . "</td>";
		}
		echo $tabelaBody2 = "	 <td align='right'><a href='cli_cad1visual.php?id_cli=$idCli&tipo=$tipo'> <img src='imagens/detalhe.bmp' border='no'></a><td>
			   			 	 	 <td align='right'><a href='cli_cad1edit.php?id_cli=$idCli&tipo=$tipo'> <img src='imagens/alt.gif' border='no'></a></td>
			   			     </tr>";
	}

	echo $tabelaFoote = "</tbody></table>";
}
?>
