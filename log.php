#!/usr/bin/env php
<?php
/*
	ATCO Dias
	12/05/2024
	chat-historico.php Mostra os históricos
*/

require 'vendor/autoload.php';

use PhpPkg\CliMarkdown\CliMarkdown;

$parser = new CliMarkdown;

// Lista os arquivos JSON na pasta "historico"
$arquivos = scandir('historico');
$jsonFiles = array_filter($arquivos, function ($arquivo) {
    return pathinfo($arquivo, PATHINFO_EXTENSION) === 'json';
});

// Exibe os arquivos JSON disponíveis para seleção
echo "Arquivos JSON na pasta 'historico':\n";
foreach ($jsonFiles as $indice => $jsonFile) {
    echo "- [$indice] $jsonFile\n";
}

// Solicita ao usuário que selecione um arquivo
echo "Selecione um arquivo digitando o número correspondente:\n";
$opcao = readline();

// Verifica se a opção é válida
if (!isset($jsonFiles[$opcao])) {
    echo "Opção inválida.\n";
    exit;
}

// Lê o conteúdo do arquivo selecionado
$arquivoSelecionado = 'historico/' . $jsonFiles[$opcao];
$jsonConteudo = file_get_contents($arquivoSelecionado);

// Converte o JSON para texto
$arrayConteudo = json_decode($jsonConteudo, true);
array_splice($arrayConteudo,0,2); // remover os dois primeiro
$texto = '';
foreach ($arrayConteudo as $item) {
    $agente = ($item['role'] == 'user')? '# 🤷 - ': '> 🤖 - ';
    $texto .= $agente . $item['text'] . "\n\n";
}

// Exibe o texto no terminal Linux
echo $parser->render($texto);

