<?php

$globalUsuario = "madeireira"; // Disponibilizado pelo Santander
$globalKey =  "89564B999337A671"; // Disponibilizado pelo Santander
$globalCnpj = "03875890000173"; // CNPJ (somente n�meros)
$globalCodigoGrupoCanal = "0008.001"; // Disponibilizado pelo Santander
$globalNumeroIntermediario = "198710"; // Disponibilizado pelo Santander

$globalEnderecoEndPointProposta = "https://afc.santanderfinanciamentos.com.br:443/afc-services/FinanciamentosOnlineEndpointService";//producao
//$globalEnderecoEndPointProposta = "https://afc.santanderfinanciamentos.com.br/afc-services/FinanciamentosOnlineEndpointService/FinanciamentosOnlineEndpointService.wsdl";//EndPoint Producao
$globalEnderecoEndPointDominio =  "https://afc.santanderfinanciamentos.com.br:443/afc-services/DominiosEndpointService";//dominio Producao

//$globalEnderecoEndPointProposta = "https://aceiteparcerias.santanderfinanciamentos.com.br/afc-services/FinanciamentosOnlineEndpointService";//EndPoint Homologacao
//$globalEnderecoEndPointDominio  = "https://aceiteparcerias.santanderfinanciamentos.com.br:443/afc-services/DominiosEndpointService";//dominio Homologacao

@define(usuario, $globalUsuario);
@define(Key , $globalKey);
@define(cnpj, $globalCnpj);
@define(codigoGrupoCanal, $globalCodigoGrupoCanal);
@define(numeroIntermediario, $globalNumeroIntermediario);
@define(enderecoEndPointProposta, $globalEnderecoEndPointProposta);
@define(enderecoEndPointDominio, $globalEnderecoEndPointDominio);

?>