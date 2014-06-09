<html>
<head>
	<link rel="stylesheet" type="text/css" href="../viewpedidovenda/estilo_css/estili_cad.css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="refresh/refreshAjax.js"></script>
	<script type="text/javascript" src="menu/ajax-menu.files/dmenu.js"></script>
	<style type="text/css">
			#deluxeMenu {
				display: none
			}
	</style>
	<noscript>
			<link type="text/css" href="menu/ajax-menu.files/style.css" rel="stylesheet">
	</noscript>
</head>
<budy onload="habilita();">
<form name="form" id = "form" >
<fieldset>

	<legend align="center">
		<h3>Enviar Proposta Financiamento</h3>
	</legend>

<div>
<center>

<table>

	<hr/>
	<tr>

		<td>Forma Pagamento:<br/><input name="formapag" id="formapag" type="text"  class="campo"></td>
		<td>Numero do Produto:<br/><input name="numprod" id="numprod" type="text"  class="campo"></td>
	    <td>Valor Financiamento:<br/><input name="valfinan" id="valfinan" type="text"  class="campo"></td>
	    
	 </tr><tr>
		
	    <td>Valor Prestacao :<br/><input name="valprest" id="valprest" type="text" class="campo"></td>
		<td>Valor do Bem :<br/><input name="valbem" id="valbem" type="text" class="campo"></td>
		<td>Quantidade Prestacoes:<br/><input name="qtprest" id="qtprest" type="text" class="campo"></td>
	</tr><tr>
	 	
		<td>Codigo Modalidade:<br/><input name="modalidade" id="modalidade" type="text" class="campo"></td>
		<td>isencaoTC:<br/><input name="tc" id="tc" type="text" class="campo"></td>
		<td>isencaoTAB :<br/><input name="tab" id="tab" type="text" class="campo"></td>
	</tr><tr>
		
		<td>Vendedor:<br/><input name="vendedor" id="vendedor" type="text" class="campo"></td>
		<td>Numero Vendedor:<br/><input name="numvendedor" id="numvendedor" type="text" class="campo"></td>
		<td>indicadorTac:<br/><input name="tac" id="tac" type="text" class="campo">	</td>
	</tr><tr>
		<td>Texto Controle Loja:<br/><input name="textoloja" id="textoloja" type="text" class="campo"></td>
		<td>Texto Obs Loja:<br/><input name="textobs" id="textobs" type="text" class="campo"></td>
		<td>Valor Entrada:<br/><input name="valentrada" id="valentrada" type="text" class="campo"></td>
	</tr><tr>
		<td>codigoTipoMoeda:<br/><input name="tpmoeda" id="tpmoeda" type="text"  class="campo"></td>
		<td>CPF/CNPJ cliente:<br/><input name="cpf" id="cpf" type="text"  class="campo"></td>
	</tr>
	<center>
		<td><input type ="submit" value ="Enviar Proposta" style ="border:none; height:36; width:120; color:red " onClick ="submit_action('teste.php');"/></td>
	</center>
	</tr>
</table></center>

<hr/>
<h6>*Tela prototipo</h6>
</fieldset>

</div>
</form>
</budy>
</html>