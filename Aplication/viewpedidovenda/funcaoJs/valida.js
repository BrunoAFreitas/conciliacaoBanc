/**
 * @author akarlos
 *
 * Pagina feita para melhoria do sistema Gercom
 * do grupo Jacauna, Está pagina está livre para
 * bom uso e melhoria do sistema manter este comentário.
 *
 * Esta pagina e para validar os campos das tabelas
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @copyright Ruah Industria
 * @access private
 * @package pedido_de_venda
 * @example functiontest.js
 */

/**
 * Metodo para validar cpf para saber se o
 * Cpf digitado e valido
 *
 * @param {Object} Objcpf
 */
function validarCPF(Objcpf) {
	var cpf = Objcpf.value;
	exp = /\.|\-/g;
	cpf = cpf.toString().replace(exp, "");
	var digitoDigitado = eval(cpf.charAt(9) + cpf.charAt(10));
	var soma1 = 0, soma2 = 0;
	var vlr = 11;

	for ( i = 0; i < 9; i++) {
		soma1 += eval(cpf.charAt(i) * (vlr - 1));
		soma2 += eval(cpf.charAt(i) * vlr);
		vlr--;
	}
	soma1 = (((soma1 * 10) % 11) == 10 ? 0 : ((soma1 * 10) % 11));
	soma2 = (((soma2 + (2 * soma1)) * 10) % 11);

	var digitoGerado = (soma1 * 10) + soma2;
	if (digitoGerado != digitoDigitado) {
		alert("CPF Invalido!");
		limparDados(Objcpf);
	} else {
		verificarCpf();
	}
}

/**
 * Metodo para validar email
 *
 * @param {Object} email
 */
function validaEmail(email) {
	var txt = email.value;
	if ((txt.length != 0) && ((txt.indexOf("@") < 1) || (txt.indexOf('.') < 7))) {
		alert("Email incorreto");
		limparDados(email);
	}
}

/**
 * Metodo para validat Cnpj
 *
 * @param {Object} email
 */
function validaCnpj(ObjCnpj) {
	var cnpj = ObjCnpj.value;
	var valida = new Array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
	var dig1 = new Number;
	var dig2 = new Number;

	exp = /\.|\-|\//g;
	cnpj = cnpj.toString().replace(exp, "");
	var digito = new Number(eval(cnpj.charAt(12) + cnpj.charAt(13)));

	for ( i = 0; i < valida.length; i++) {
		dig1 += (i > 0 ? (cnpj.charAt(i - 1) * valida[i]) : 0);
		dig2 += cnpj.charAt(i) * valida[i];
	}
	dig1 = (((dig1 % 11) < 2) ? 0 : (11 - (dig1 % 11)));
	dig2 = (((dig2 % 11) < 2) ? 0 : (11 - (dig2 % 11)));

	if (((dig1 * 10) + dig2) != digito) {
		alert("CNPJ Invalido!");
		limparDados(ObjCnpj);
	} else {
		verificarCnpj();
	}
}

/**
 * Metodo para validar cep
 *
 * @param {Object} cep
 */
function validaCep(cep) {
	exp = /\d{2}\.\d{3}\-\d{3}/;
	if (!exp.test(cep.value)) {
		alert("Numero de Cep Invalido!");
		limparDados(cep);
	}
}

/**
 * Metodo para verificar se o valor digitado esta gravado no
 * banco de dados usando o ajax
 */
function verificarCpf() {
	var nomeUsuario = $('#edtCgcCpf').val();
	$.post('verificarDados.php', {
		nomeUsuario : nomeUsuario
	}, function(data) {
		if (data == "1") {
			alert("Cpf já cadastrado");
			limparDados('#edtCgcCpf');
		}
	});
};

/**
 * Metodo para verificar se o valor digitado esta gravado no
 * banco de dados usando o ajax
 */
function verificarCnpj() {
	var nomeCnpj = $('#edtCgcCnpj').val();
	$.post('verificarDados.php', {
		nomeCnpj : nomeCnpj
	}, function(data) {
		if (data == "1") {
			alert("Cnpj já cadastrado");
			limparDados('#edtCgcCnpj');
		}
	});
};

/**
 * Metodo para limpar um campo de texto e passar
 * o nome entre '#campo' que vai limpar
 *
 * @param {Object} campo
 */
function limparDados(campo) {
	$(campo).attr('value', '');
}
