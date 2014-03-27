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
 * @example EnviarEmail.php
 */

/**
 * Essa class e responsavel por enviar email
 */
class EnviarEmail {

	/**
	 * Metod para enviar o email
	 * @access public
	 * @param String $loja, String $remtVend, String $destn, String $msg, String $nomeCli
	 */
	public function sendEmail(String $loja, String $destn, String $nomeCli) {

		$sql_loja = "SELECT lj_fantasia, lj_cod, lj_email FROM loja WHERE lj_cod = '$loja' ";
		$sql_exe_loja = mysql_query($sql_loja);
		if (mysql_num_rows($sql_exe_loja) > 0) {
			$lojaCod = mysql_fetch_object($sql_exe_loja);
			$lojaDadosEmail = $lojaCod -> lj_email;
			$lojaDadosNome = $lojaCod -> lj_fantasia;
			$lojaDadosIni = "sac@jacauna.com.br";
		}

		/* Montando o cabeçalho da mensagem */
		if (PATH_SEPARATOR == ";") {
			$quebra_linha = "\r\n";
		} else {
			$quebra_linha = "\n";
		}

		$headers = $quebra_linha;
		$headers .= "Content-type: text/html;" . $quebra_linha;

		/* Medida preventiva para evitar que outros domínios sejam remetente da sua mensagem. */
		if (eregi('tempsite.ws$|locaweb.com.br$|hospedagemdesites.ws$|websiteseguro.com$', $_SERVER[HTTP_HOST])) {
			$emailsender;
		} else {
			$emailsender;
		}

		// parte que cria o corpo o email
		$data_dia = date('d/m/Y');
		$assunto = "Loja" . lj_fantasia . " Dia " . $data_dia;
		$lj_desc = $lojaDadosNome;
		$nomeremetente = $lojaDadosEmail;
		$emaildestinatario = $destn;
		$mensagem = "Cliente " . $nomeCli . " cadastrado com sucesso como cliente da " . lj_fantasia;
		$mensagem_fim = "Grupo Jacauna";
		// corpo completo do email
		$mensagemHTML = "<hr><P><b>" . $mensagem . "</b></P><hr>
					     <hr><P><b>" . $mensagem_fim . "</b></P><hr>
					     <hr><P>Este E-mail Foi Enviado Pelo Sistema Gercom!</P>";

		// Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
		$headers .= "From: " . $emailsender . $quebra_linha;
		$headers .= "Reply-To: " . $emailsender . $quebra_linha;
		// Note que o e-mail do remetente será usado no campo Reply-To (Responder Para)

		/* Enviando a mensagem */
		//Verificando qual é o MTA que está instalado no servidor e efetuamos o ajuste colocando o paramentro -r caso seja Postfix
		if (!mail($emaildestinatario, $assunto, $mensagemHTML, $headers, "-r" . $emailsender)) {
			$headers .= "Return-Path: " . $emailsender . $quebra_linha;
			mail($emaildestinatario, $assunto, $mensagemHTML, $headers);
		}
	}

}
?>