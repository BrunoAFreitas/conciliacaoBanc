/*
 combo box dinamico  jquery + php + mysql
 */
$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=edtEstado]").change(function() {
		$("select[name=edtCidade]").html('<option value="0">Carregando...</option>');
		$.post("cbox_cidade.php", {
			edtEstado : $(this).val()
		}, function(valor) {
			$("select[name=edtCidade]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	/////////////////////Titulo de Eleitor
	$("select[name=edtCidade]").change(function() {
		$("select[name=edtBairro]").html('<option value="0">Carregando...</option>');
		$.post("cbox_bairro.php", {
			edtCidade : $(this).val()
		}, function(valor) {
			$("select[name=edtBairro]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});
$(document).ready(function() {
	/////////////////////Titulo de Eleitor
	$("select[name=lsttipodoc]").change(function() {
		$("select[name=lstplanfin]").html('<option value="0">Carregando...</option>');
		$.post("cbox_tipodoc.php", {
			lsttipodoc : $(this).val()
		}, function(valor) {
			$("select[name=lstplanfin]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstLoja]").change(function() {
		$("select[name=lstVend]").html('<option value="0">Carregando...</option>');
		$.post("cbox_vend.php", {
			lstLoja : $(this).val()
		}, function(valor) {
			$("select[name=lstVend]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergSimplificada]").change(function() {
		$("select[name=lstRespSimplificada]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergsimplif.php", {
			lstPergSimplificada : $(this).val()
		}, function(valor) {
			$("select[name=lstRespSimplificada]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergComparativa1]").change(function() {
		$("select[name=lstRespComparativa1]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergcomparativa1.php", {
			lstPergComparativa1 : $(this).val()
		}, function(valor) {
			$("select[name=lstRespComparativa1]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergComparativa2]").change(function() {
		$("select[name=lstRespComparativa2]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergcomparativa2.php", {
			lstPergComparativa2 : $(this).val()
		}, function(valor) {
			$("select[name=lstRespComparativa2]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergComparativa3]").change(function() {
		$("select[name=lstRespComparativa3]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergcomparativa3.php", {
			lstPergComparativa3 : $(this).val()
		}, function(valor) {
			$("select[name=lstRespComparativa3]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergComparativa4]").change(function() {
		$("select[name=lstRespComparativa4]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergcomparativa4.php", {
			lstPergComparativa4 : $(this).val()
		}, function(valor) {
			$("select[name=lstRespComparativa4]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstPergComparativa5]").change(function() {
		$("select[name=lstRespComparativa5]").html('<option value="0">Carregando...</option>');
		$.post("cbox_pergcomparativa5.php", {
			lstPergComparativa5 : $(this).val()
		}, function(valor) {
			$("select[name=lstRespComparativa5]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});

$(document).ready(function() {
	////////////////////Dados Pessoais
	$("select[name=lstFab]").change(function() {
		$("select[name=lstProd]").html('<option value="0">Carregando...</option>');
		$.post("cbox_relprodvendidos.php", {
			lstFab : $(this).val()
		}, function(valor) {
			$("select[name=lstProd]").html(valor);
		});
	});
	///////////////////////////////////////////////////////////////////////
});
