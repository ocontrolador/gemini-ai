<?php

// Define o caminho do diretório
$diretorio = "log";

// Executa o comando find para listar os arquivos JSON no diretório
$comando = "find $diretorio -type f -name '*.json'";
$saida = shell_exec($comando);

// Divide a saída do comando em linhas
$linhas = explode("\n", $saida);

// Inicializa o array de arquivos JSON
$jsonFiles = [];

// Percorre cada linha e adiciona o caminho completo do arquivo ao array
foreach ($linhas as $linha) {
    if (trim($linha) !== "") {
        $jsonFiles[] = $linha;
    }
}

// Imprime o array de arquivos JSON
print_r($jsonFiles);



$diretorio = 'log';
$arquivosJson = glob("$diretorio/*.json");

// Ordenar os arquivos por data
usort($arquivosJson, fn($a, $b) => filemtime($a) - filemtime($b));
//sort($arquivosJson, fn($a, $b) => filemtime($b) - filemtime($a));



print_r($arquivosJson);



