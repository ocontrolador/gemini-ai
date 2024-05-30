<?php

require 'GeminiAi.php';
require 'MarkdownToBash.php';

// Verifica se tem parametro
if ($argc < 2)
  die("Faça sua pergunta após '$argv[0]'\n'-n' para iniciar uma novo chat");

// Remove o nome 
array_shift($argv);

// Verifica se é um novo chat ou carrega historico
$contents = [];
if ($argv[0] == '-n' || !file_exists('contents.json')) {
  $contents = [];
  array_shift($argv);  
} else {
  $contentsJson = file_get_contents('contents.json');
  $contents = json_decode($contentsJson);
}

// Verifica se tem arquivo em anexo
$filePath = '';
$mimeType = '';
if (file_exists($argv[0])) {
  $filePath = $argv[0];
  $mimeType = mime_content_type($filePath);
  array_shift($argv);  
}

# Pergunta
$text = (count($argv) > 0)? implode(' ', $argv): 'Explique';


// Carrega variavel de ambiente GOOGLE_API_KEY
$apiKey = getenv('GOOGLE_API_KEY'); 
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

// Parametros de seguraça
$safety_settings = []; // "BLOCK_NONE", "BLOCK_LOW_AND_ABOVE", "BLOCK_MEDIUM_[AND_ABOVE", "BLOCK_ONLY_HIGH"
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

// Faz a consulta do Gemini
$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

// Visualiza o resultado, convertido do Markdown, no terminal do Linux
$markdownToBash = new MarkdownToBash();
echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
