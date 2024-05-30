#!/usr/bin/env php
<?php
/*
	ATCO Dias
	28/05/2024
	log.php Mostra os log de Gemini
*/

require 'MarkdownToBash.php';

$parser = new MarkdownToBash();

function dd(array $array):void 
{
  var_dump($array); 
  exit();
}

$diretorio = 'log';

// Validar a existência do diretório
if (!is_dir($diretorio)) {
  throw new Exception("Diretório '$diretorio' não encontrado.");
}

// Listar os arquivos JSON no diretório
$jsonFiles = glob("$diretorio/*.json");

// Ordenar os arquivos por data inversa usando arrow function
usort($jsonFiles, fn($a, $b) => filemtime($b) - filemtime($a));

// Exibe os arquivos JSON disponíveis para seleção
echo "Arquivos JSON na pasta 'log':\n";
foreach ($jsonFiles as $indice => $jsonFile) {
    $tamDir = mb_strlen($diretorio) + 1;
    $nomeJson = mb_substr($jsonFile, $tamDir, mb_strlen($jsonFile) - $tamDir - 5);
    echo "- [" . ($indice + 1). "] " . str_replace('-',' ', $nomeJson) . PHP_EOL;
}

// Solicita ao usuário que selecione um arquivo
echo "Selecione um arquivo digitando o número correspondente:\n";
$opcao = readline();

// Verifica se a opção é válida
if (!isset($jsonFiles[$opcao - 1])) 
    die("Opção inválida.\n");

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
echo $parser->convert($texto);

