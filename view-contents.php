#!/usr/bin/env php
<?php
/*
	ATCO Dias
	29/05/2024
	Visualiza contents.json
*/

require_once 'autoload.php';

//use Diasdlasd\MarkdownToBash;

$parser = new MarkdownToBash();

// [opcional] salva código
$filename = (isset($argv[1]))? $argv[1] : null;

// Verifica se exite e carrega
if (file_exists('.contents-gemini.json')) 
    $jsonFile = '.contents-gemini.json';
elseif (file_exists('contents.json')) 
    $jsonFile = 'contents.json';
else 
    die("Aviso: Nenhum arquivo encontrado.\n");
    
$jsonConteudo = file_get_contents($jsonFile);

// Converte o JSON para texto
$arrayConteudo = json_decode($jsonConteudo, true);
//array_shift($arrayConteudo); // o primeiro
$texto = '';
foreach ($arrayConteudo as $item) {    
    $agente = ($item['role'] == 'user')? '# 🤷 - ': '> 🤖 - ';
    $texto .= $agente . $item['parts'][0]['text'] . "\n\n";
}

// Exibe o texto no terminal Linux
echo $parser->convert($texto, $filename);

