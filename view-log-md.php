#!/usr/bin/env php
<?php
/*
	ATCO Dias
	28/05/2024
	Mostra os log de Gemini em markdown puro
*/

require 'dev-helpers.php';

$diretorio = __DIR__ . '/log';

// Validar a existência do diretório
if (!is_dir($diretorio)) 
  die("Diretório '$diretorio' não encontrado.");

// Listar os arquivos JSON no diretório
$jsonFiles = glob("$diretorio/*.json");

// Ordenar os arquivos por data
usort($jsonFiles, fn($a, $b) => filemtime($b) - filemtime($a));

// Vai direto para o Log ou lista os Logs
$direto = (isset($argv[1]))? $argv[1] : '';
if ($direto[0] == '+') {
  $opcao = substr($direto,1);
} else {
// Limitar a lista de arquivos visualizados
$limiteDefault = 20;
$limite = (isset($argv[1]))? $argv[1] : $limiteDefault; 
$limite = is_numeric($limite)? $limite : $limiteDefault;

// Exibe os arquivos JSON disponíveis para seleção
echo "Arquivos JSON na pasta 'log':\n";
foreach ($jsonFiles as $indice => $jsonFile) {
    $tamDir = mb_strlen($diretorio) + 1;
    $nomeJson = mb_substr($jsonFile, $tamDir, mb_strlen($jsonFile) - $tamDir - 5);
    echo "- [" . ($indice + 1). "] " . str_replace('-',' ', $nomeJson) . PHP_EOL;
    if ($indice == $limite -1) break; # para no valor limite
}

// Solicita ao usuário que selecione um arquivo
echo "Selecione um arquivo digitando o número correspondente:\n";
$opcao = readline();

if ($opcao > $limite) die("Opção inválida!\n");
}

// Verifica se a opção é válida
if (is_null($opcao) || !is_numeric($opcao)) die("Não é um numero!\n");
if (!isset($jsonFiles[$opcao - 1])) die("Opção inválida.\n");

// Lê o conteúdo do arquivo selecionado
$jsonSelecionado = $jsonFiles[$opcao - 1];
$jsonConteudo = file_get_contents($jsonSelecionado);

// Converte o JSON para texto
$arrayConteudo = json_decode($jsonConteudo, true);
array_shift($arrayConteudo); // o primeiro
$texto = '';
foreach ($arrayConteudo as $item) {    
    $agente = ($item['role'] == 'user')? '# 🤷 - ': '> 🤖 - ';
    $texto .= $agente . $item['parts'][0]['text'] . "\n\n";
}

// Exibe o texto no terminal Linux
echo $texto;

