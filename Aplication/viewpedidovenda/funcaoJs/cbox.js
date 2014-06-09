/**
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example cbox.js
 */

/**
 * função para pesquisa no banco de dados, essa pesquisa e de estados para retornar
 * as cidades em um select
 * para infomar os dados do cliente
 */
$(document).ready(function() {
	$("select[name=edtEstado]").change(function() {
		$("select[name=edtCidade]").html('<option value="0">Carregando...</option>');
		$.post("cbox_dados.php", {
			edtEstado : $(this).val()
		}, function(valor) {
			$("select[name=edtCidade]").html(valor);
		});
	});
});

/**
 * função para pesquisa no banco de dados, essa pesquisa e de cidade para retornar
 * os bairros em um select
 * para infomar os dados do cliente
 */
$(document).ready(function() {
	$("select[name=edtCidade]").change(function() {
		$("select[name=edtBairro]").html('<option value="0">Carregando...</option>');
		$.post("cbox_dados.php", {
			edtCidade : $(this).val()
		}, function(valor) {
			$("select[name=edtBairro]").html(valor);
		});
	});
});

/**
 * função para pesquisa no banco de dados, essa pesquisa e de estados para retornar
 * as cidades em um select
 * para infomar os dados do local de trabalho do cliente
 */
$(document).ready(function() {
	$("select[name=selEstadoTrab]").change(function() {
		$("select[name=selCidadeTrab]").html('<option value="0">Carregando...</option>');
		$.post("cbox_dados.php", {
			edtEstado : $(this).val()
		}, function(valor) {
			$("select[name=selCidadeTrab]").html(valor);
		});
	});
});

/**
 * função para pesquisa no banco de dados, essa pesquisa e de cidade para retornar
 * os bairros em um select
 * para infomar os dados do local de trabalho do cliente
 */
$(document).ready(function() {
	$("select[name=selCidadeTrab]").change(function() {
		$("select[name=selBairroTrab]").html('<option value="0">Carregando...</option>');
		$.post("cbox_dados.php", {
			edtCidade : $(this).val()
		}, function(valor) {
			$("select[name=selBairroTrab]").html(valor);
		});
	});
});

function data(data) {
	$(data).datepicker({
		dateFormat : 'dd/mm/yy',
		dayNames : ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
		dayNamesMin : ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
		dayNamesShort : ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
		monthNames : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
		monthNamesShort : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
		changeMonth : true,
		changeYear : true
	});
}