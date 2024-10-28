#!/usr/bin/env php
<?php
/*
  * ATCO Dias
  * 29/05/24
  * release: 26/10/24
  * Visualiza contents.json
*/

require_once 'autoload.php';

//use Diasdlasd\MarkdownToBash;

$parser = new MarkdownToBash();

// [opcional] nome usado para salvar o código
$fileOut = (isset($argv[1]))? $argv[1] : null;

// Procura por arquivos JSON com base no nome
$fileJSON = glob('{.contents-gemini.json, contents.json}', GLOB_BRACE);
// Verifica se encontrou algum arquivo
if (count($fileJSON) > 0) {
    // Caso encontre mais de um arquivo, pega o primeiro
    $fileJSON = $fileJSON[0];
} else {
    die("Aviso: Nenhum arquivo encontrado.\n");
}

$jsonConteudo = file_get_contents($fileJSON);

// Converte o JSON para texto
$arrayConteudo = json_decode($jsonConteudo, true);
// Remove os dois primeiros elementos do array
array_splice($arrayConteudo, 0, 2);
$texto = '';
//var_dump($arrayConteudo,$filename);
foreach ($arrayConteudo as $key => $item) { 
    // Verifica se o elemento é do tipo 'user' ou 'bot'
    $agente = ($item['role'] == 'user')? '# 🤷 - ': '> 🤖 - ';
    // Adiciona o texto formatado à variável $texto
    $texto .= $agente . $item['parts'][0]['text'] . "\n\n";   
}

// Exibe o texto no terminal Linux
echo $parser->convert($texto, $fileOut);


