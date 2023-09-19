<?php

$empresas = array();
$valorBase = 0;
$resultadoPorPagina = 20;

do {

//array que realiza a consulta

$data = array(
    'autenticacao' => array(
        'usuario' => 'USER_HERE',
        'token' => 'TOKEN_HERE'
    ),
    'acao' => 'listar_clientes',
    'cliente_id' => "",
    'nome' => "",
    'pos_registro_inicial' => $valorBase,
);


$response = fazerRequisicao($data);

    if ($response !== false) {
    // Decodifique a resposta JSON em um array
    $responseData = json_decode($response, true);
    
    $empresas[] = $response;
    
    // Atualize o valor base para a próxima página
    $valorBase += $resultadoPorPagina;
} else {
    echo "Erro na solicitação à API.";
    break;
}

} while ($valorBase < $responseData['qtd_total_resultados']);

//faz a requisição na API
function fazerRequisicao($data){

$url = 'URLHERE.COM/pabx/api.php';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url); // Defina a URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Receba a resposta como uma string
curl_setopt($ch, CURLOPT_POST, true); // Usa o método POST
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Envie os dados como JSON

// Executar a solicitação e armazenar a resposta em $response
$response = curl_exec($ch);

if ($response === false) {
    echo "Erro cURL: " . curl_error($ch);
}


curl_close($ch);

return $response;

}

//Tratamento de dados

$empresasCombinadas = [];

foreach ($empresas as $empresaJson) {
    $data = json_decode($empresaJson);

    
    $empresasCombinadas = array_merge($empresasCombinadas, $data->dados);
}

// Cria um novo objeto com os dados combinados e outras informações
$newObj = new stdClass();
//$newObj->http_response_code = $data->http_response_code;
//$newObj->qtd_total_resultados = $data->qtd_total_resultados;
//$newObj->qtd_resultados_retornados = $data->qtd_resultados_retornados;
$newObj->dados = $empresasCombinadas;
//$newObj->mensagem = $data->mensagem;

$filename = "data_" . date("YmdHis") . ".json";

// Set the response headers to indicate a JSON file download
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Codifica o novo objeto em JSON
$newJson = json_encode($newObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);




// Imprime o resultado
//echo "<pre>";
print_r($newJson);
//echo "</pre>";
?>