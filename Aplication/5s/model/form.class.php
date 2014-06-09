<?php
/**
 * Este arquivo faz parte do projeto gerador de notas fiscais eletrônicas em TXT - NFeJacauna.
 * No entanto foi desenvolvido com o intuito de ser aplicado em todos os projetos futuros, visto
 * que esses codigos se repetem em outros diversos. A ideia desse arquivo é evitar o maximo possivel os
 * codigos php dentro das paginas em HTML afim de adotar as boas maneiras da programação, economizar tempo
 * e melhorar a manutenção dos codigos.
 *
 * @package    NFeJacauna
 * @subpackage Controller
 * @name       forms
 * @author     Bruno A. Freitas <bruno.araujo@jacauna.net>
 * 
 */

class forms extends crud{
	
	/**
	 * Metodo que cria as opções de um select apartir do banco de dados
	 * nesse metodo buscamos os valores que desejamos exibir fazendo uso do
	 * metodo herdado listaTable e em seguida escrevemos a tag HTML
	 * @access public
	 * @param  $campos, $tabela, $condicao (dados para consulta), $form (viriavel que contem o value do compo)
	 * @return opções do formulario select
	 */
	public function options($campos, $tabela, $condicao, $form){
			
		$camposStr = join(', ',$campos);
		$query = self::listaTable($camposStr, $tabela, $condicao);
				
		while ($linhaOp = mysql_fetch_array($query)){
			
			if($form == $linhaOp[$campos[0]]){
				echo "<option value='" . $linhaOp[$campos[0]] . "'selected>[" . $linhaOp[$campos[0]] . " ] " . $linhaOp[$campos[1]] . "  </option>";
			}else{
				echo "<option value='" . $linhaOp[$campos[0]] . "'>[" . $linhaOp[$campos[0]] . " ] " . $linhaOp[$campos[1]] . "</option>";
			
			}
		}
	}
	
	/**
	 * Metodo que verifica se o campo de data em um formulario retornou vasio, se sim
	 * é atribuido a este a data atual, caso contrario é passado o valor digitado.
	 * Uso de condições ternarias.
	 * @access public	
	 * @param  $data (recebe data a ser checada)
	 * @return Nova data.
	 */
	public function preencheData($data){
			
		$data = (empty($data) ? date('d/m/Y'): $data );
		return $data;
		
	}
	
	/**
	 * Metodo que converte padrão de tada do banco de dados para o padrão
	 * nacional usado na exibição
	 * @return data no padão nacional
	 * @author Jalen F. Barboza<jalen@jacauna.net>
	 * @param  $data ( data a ser modificda)
	 */
    public function muda_data_pt($data) {
		  $aux = explode("-",$data); 
		  $c = array_reverse($aux); 
		  $data = implode("/",$c);
		  return $data;
    }
	
    /**
	 * Metodo que converte do padrão nacional para o  padrão 
	 * internacional usado no banco de dados para o padrão
	 * @return data no padão internacional
	 * @author Jalen F. Barboza<jalen@jacauna.net>
	 * @param  $data ( data a ser modificda)
	 */
    public function muda_data_en($data) {	
		  $aux = explode("/",$data); 
		  $c = array_reverse($aux); 
		  $data = implode("-",$c);
		  return $data;
    }
	
	/**
	 * Diferencia as cores das linhas nas tabelas
	 * @param  recebe contador
	 * @return retorna a cor da <tr>
	 */
    public function diferenciaCores($count, $color1 = '#FFFFC0"', $color2='#FFFFFF'){
    	$color = (($count%2)? $color1 : $color2);
		return $color;
    }
}

?>
