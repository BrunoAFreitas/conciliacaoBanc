<?php
include_once "consultaProposta.php";
class ConsultaStatusProposta{

	private $numPropostaAdp;

	public function __construct($numPropostaAdp){
		$this->numPropostaAdp = $numPropostaAdp;
		
	}

	public function getNumProposta(){
		return $this->numPropostaAdp;
	}

	public function consulta(){
		$consulta = new consultaStatus($this->numPropostaAdp);
		
		echo $this->numPropostaAdp;
		$consulta->executa();
		echo "</br>Numero da proposta(". $consulta -> numeroPropostas . ")</br>";
		echo "Status da proposta(" . $consulta -> statusPropostas . ")</br>";
		echo "Descrição do status(". $consulta -> descricaoStatus .")</br>";
		echo "Retorno(" . $consulta -> codigoRetorno . ")</br>";
	}
}

$a = new ConsultaStatusProposta("1001007522");
$a->consulta();

?>