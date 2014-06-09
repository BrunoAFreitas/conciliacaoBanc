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
 * @example functiontest.js
 */

/**
 * Este codido vai ser chamado pelo link
 * ele criar o medal da pagina na parte da janela
 *
 * foi alterado a altura do modal a parte que escurece atraz da jenela
 * para voltar ao normal descomentar em baixo
 **/
$(document).ready(function() {
	$("a[rel=modal]").click(function(ev) {
		ev.preventDefault();

		var id = $(this).attr("href");

		//var alturaTela = $(document).height();
		var larguraTela = $(window).width();
		var alturaTela = 1390;

		//colocando o fundo preto
		$('#mascara').css({
			'width' : larguraTela,
			'height' : alturaTela
		});
		$('#mascara').fadeIn(1000);
		$('#mascara').fadeTo("slow", 0.8);

		var left = ($(window).width() / 2) - ($(id).width() / 2 );
		var top = ($(window).height() / 2) - ($(id).height() / 2 );

		$(id).css({
			'top' : top,
			'left' : left
		});
		$(id).show();
	});

	$("#mascara").click(function() {
		$(this).hide();
		$(".window").hide();
	});

	$('.fechar').click(function(ev) {
		ev.preventDefault();
		$("#mascara").hide();
		$(".window").hide();
	});
});

/**
 * este comando e para autocomplete vai sempre ser executado com a pagina
 * e ter ser executa no campo de texto que receba o metodo
 */
function lookup(q) {
	$(q).autocomplete("autocomplete.php", {
		width : 360,
		matchContains : true,
		selectFirst : false
	});
};

/**
 * Este metedo e para validar os campos caso algum esteja
 * vazio caso o cadastro seja pessoa fisica
 */
function camposVerificar() {
	// para pegar a variavel com ajax
	var cpf = $('#edtCgcCpf').val();
	var nome = $('#edtRazao').val();
	var rg_ins = $('#edtInscr').val();
	var email = $('#edtEmail').val();
	var dataNascimento = $('#edtDtNasc').val();
	
	if (cpf == "") {
		alert("Informe o CPF");
	} else if (rg_ins == "") {
		alert("Informe o RG");
	} else if (nome == "") {
		alert("Informe o Nome");
	} else if (email == "") {
		alert("Informe o Email");
	} else if(dataNascimento == "") {
		alert("Informe a Data de Nascimento!");
	} else {
		gravar_dados();
	}
}

/**
 * Este metedo e para validar os campos caso algum esteja
 * vazio caso o cadastro seja pessoa juridica
 */
function camposVerificar1() {
	// para pegar a variavel com ajax
	var cnpj = $('#edtCgcCnpj').val();
	var nome1 = $('#edtRazao').val();
	var rg_ins1 = $('#edtInscr').val();
	var email1 = $('#edtEmail').val();

	if (cnpj == "") {
		alert("Informe o CNPJ");
	} else if (rg_ins1 == "") {
		alert("Informe a Inscrição");
	} else if (nome1 == "") {
		alert("Informe o Nome");
	} else if (email1 == "") {
		alert("Informe o Email");
	} else {
		gravar_dados();
	}
}

/***
 * Este codigo e responsavel por passar os dados dos campos do formulario
 * e gravar no banco de dados
 */
function gravar_dados() {
	// vai pegar todos os campos com o metodo post no caos so basta chamar
	var dados_form = $('#form_cleinte').serialize();
	$.ajax({
		url : 'cadastroCliente2.php',
		type : 'POST',
		data : dados_form,
		success : function(data) {
			alert(data);
			// retirar quando passar para o servidor
			//location.reload();
		}
	});
}

/***
 * Metodo executado quando se apertar o button
 * gravar cabeçalho
 */
function but_gravar() {
	var cliente = $('#lstCli').val();
	if (cliente == "") {
		alert("Campo em Branco!");
	} else {
		$.post('verificarDados.php', {
			cliente : cliente
		}, function(data) {
			if (data == 0) {
				alert("Cliente não cadastrado!");
			} else {
				location.href = "pedido_alt.php";
			}
		});
	}
}
