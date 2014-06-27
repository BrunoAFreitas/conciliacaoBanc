<?php
/**
 * Classe que realiza operações no banco, efetuando inserts, selects, updates, deletes etc
 * @package Controller
 * @access  Abstract
 */

include_once('con_bd_ruah.php');

class crud{
	
	
	private $sql;//Comando sql
	private $table;//tabela
	private $fields;//campos
	private $dados;//valores
	private $status;//mensagem de retorna sucesso ou insucesso
	private $id;//id da tabela ou qualquer campo similar
	private $valueId;// 
	private $conn;//
	
	//envia o nome da tabela a ser usada no class
	public function setConn($conn){
		$this->conn = $conn;
	}
	//envia os campos a ser usado na class
	public function getConn(){
		return $this->conn;
	}
	
	public function setSql($sql){
		$this->sql = $sql;
	}
	public function getSql(){
		return $this->sql;
	}
	
	public function setTablet($t){
		$this->table = $t;
	}
	//envia os campos a ser usado na class
	public function setFields($f){
		$this->fields = $f;
	}
	//os dados ou valores a ser usado na class
	public function setDados($d){
		$this->dados = $d;
	}
	
	public function setStatus($s){
		$this->status = $s;
	}
	//mostra a mensagem na tela
	public function getStatus(){
		return $this->status;
	}
	//envia o campo de pesquisa normalmente o codigo
	public function setId($id){
		$this->id = $id;
	}
    
	public function setValueId($id){
		$this->valueId = $id;
	}
	public function getValueId(){
		return $this->valueId;
	}
	
    //metodo para executar a lista do banco de dados
	public function listQr($qr){
		//echo "aki";
		$linha = mysql_fetch_assoc($qr);
		//echo $linha['pro_ncm'];
		return $linha;	
	}
	//metodo para listar a quantidade de dados do banco de dados
	public function countData($qr){
		$totalRow = mysql_num_rows($qr);
		return $totalRow;
	}
	//testar urgente 	
	public function exeSQL($sql){
		//echo $sql;
		$query="";
		try{
		  $query=mysql_query($sql);
		}
		catch(Exception $ex){
		  $this->setStatus($ex->getMessage()." ".$ex->getLine()); //exibe a mensagem de erro
		}
		return $query;
	}
	//metodo que inseri no banco de dados
	public function insert($fildes,$table,$data){
		$msg="";
		try{
		  $sql = "INSERT INTO $table ($fildes) VALUES($data)";
		  $query=$this->exeSQL($sql);
		  if($query){
			$msg= "Cadastrado deu certo";
	      }
		}catch(Exception $e){ //aqui ele recupera a mensagem de erro gerada no if anterior
		  $msg=$e->getMessage()." ".$e->getLine(); //exibe a mensagem de erro
	    }
		return $msg;
	}
	//metodo para deletar valores no banco de dados
	public function delete($id,$table,$valueId){
		$msg="";
		try{
	     	$sql = "DELETE FROM $table WHERE $valueId";
		    $query= $this->exeSQL($sql);
		   if($query){
			 $msg = "Delete deu certo";
	       }
		}catch(Exception $ex){
			$msg=$this->getStatus().$e->getMessage()." ".$e->getLine(); //exibe a mensagem de erro
		}
		return $msg;
	}
	//metodo para atualizar os valores do banco de dados
	public function update($table,$value,$valueId){
		$msg="";
		try{
		  $sql = "UPDATE $table SET $value WHERE $valueId ";
		  $query=$this->exeSQL($sql);
		  if($query){
			 $msg = "Atualizar deu certo";
	 	  }
		}catch(Exception $ex){
			$msg=$this->getStatus().$e->getMessage()." ".$e->getLine(); //exibe a mensagem de erro
		}
		return $msg;
	}
	//metodo para pegar o ultimo nome do campo do banco de dados	
	public function listaTable($campos,$table,$condicao){
		$msg="";
		$query="";
		//echo $this->getConn()."aki";
		try{
			if($condicao==""){
				$case = "";
			}
			else{
				$case=" where ".$condicao;
			}
		   $sql = "SELECT ".$campos." from ".$table." ". $case; //echo $sql."<br>";
		   $query = $this->exeSQL($sql,$this->getConn());
		}catch(Exception $ex){
		   echo $this->getStatus().$ex->getMessage()." ".$ex->getLine(); //exibe a mensagem de erro
		}
		return $query;
	}  	  
}
?>