<?php
/***
 * Pagina feita para melhoria de qualquer sistema,
 * Está pagina está livre para
 * bom uso e melhoria de sistemas
 * manter esse comentário.
 *
 * @author Akarlos Vasconcelos
 * @version 1.0
 * @access private
 * @package class
 * @example manipulaData.php
 */

/**
 * Class responsavel por fazer alguns comandos no banco de dados
 */

// incluindo conexao.php
include_once ("connection/conexao_bd.php");

class Manipula {

	protected $sql, $table, $fields, $dados, $status, $id, $valueId;
	/**
	 * envia o nome da tabela a ser usada no class
	 */
	public function setTablet($t) {
		$this -> table = $t;
	}

	/**
	 * envia os campos a ser usado na class
	 */
	public function setFields($f) {
		$this -> fields = $f;
	}

	//os dados ou valores a ser usado na class
	/**
	 *
	 */
	public function setDados($d) {
		$this -> dados = $d;
	}

	/**
	 * mostra a mensagem na tela
	 */
	public function getStatus() {
		return $this -> status;
	}

	//
	/**
	 * envia o campo de pesquisa normalmente o codigo
	 */
	public function setId($id) {
		$this -> id = $id;
	}

	/**
	 * envia os dados a ser pesquisados ou cadastrados
	 */
	public function setValueId($valueId) {
		$this -> valueId = $valueId;
	}

	/**
	 * metodo que inseri no banco de dados
	 */
	public function insert() {
		echo $this -> sql = "INSERT INTO $this->table ($this->fields)
					  VALUES($this->dados) ";
		if (mysql_query($this -> sql)) {
			$this -> status = "Cadastrado deu certo";
		}
	}

	/**
	 * metodo para deletar valores no banco de dados
	 */
	public function delete() {
		$this -> sql = "DELETE FROM $this->table
					  WHERE $this->id = '$this->valueId' ";
		if (mysql_query($this -> sql)) {
			$this -> status = "Delete deu certo";
		}
	}

	/**
	 * metodo para atualizar os valores do banco de dados
	 */
	public function update() {
		$this -> sql = "UPDATE $this->table 
					  SET $this->fields
					  WHERE $this->id = '$this->valueId' ";
		if (mysql_query($this -> sql)) {
			$this -> status = "Atualizar deu certo";
		}
	}
	
	/**
	 * metodo para crar um select
	 */
	public function listaTable($campos, $table, $condicao) {
		$msg = "";
		$query = "";
		try {
			if ($condicao == "") {
				$case = "";
			} else {
				$case = " where " . $condicao;
			}
			$sql = "SELECT " . $campos . " from " . $table . " " . $case;
			$query = mysql_query($sql);
		} catch(Exception $ex) {
			echo $this -> getStatus() . $ex -> getMessage() . " " . $ex -> getLine();
		}
		return $query;
	}

}
?>