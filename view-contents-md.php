#!/usr/bin/env php
<?php
/*
	ATCO Dias
	29/05/2024
	Visualiza contents.json em markdown puro
*/


$jsonFile = 'contents.json';

// Verifica se exite e carrega
if (!file_exists($jsonFile)) 
    die("'$jsonFile' não existe nesta pasta!\n");
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
echo $texto;

