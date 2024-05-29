<?php

require 'GeminiAi.php';
require 'MarkdownToBash.php';

// Verifica se tem parametro
if ($argc < 2) {
  echo "faltou a pergunta!\n";
  exit(1);
}

array_shift($argv);
$filePath = '';
$mimeType = '';
//print_r($argv);
if (file_exists($argv[0])) {
  $filePath = $argv[0];
  $mimeType = mime_content_type($filePath);
  array_shift($argv);  
}

$text = (count($argv) > 0)? implode(' ', $argv): 'Explique';
//echo "argv: \ntext: $text \nfilePath: $filePath \nmineType: $mimeType\n";exit();

$apiKey = getenv('GOOGLE_API_KEY');
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

$safety_settings = []; // "BLOCK_NONE", "BLOCK_LOW_AND_ABOVE", "BLOCK_MEDIUM_AND_ABOVE", "BLOCK_ONLY_HIGH"
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

$contents[] = [
  'parts' => [
    ['text' => 'Você é um Desenvolvedor PHP Sênior, um Arquiteto de Software PHP, um Especialista em PHP, Guru de PHP, que prefere o PHP Vanilla. Tem vasta experiência com PHP, em: Desenvolvimento de aplicações web e desktop; Automação de tarefas; Processamento de dados; Desenvolvimento de jogos; Criar APIs para aplicativos móveisProcessamento de imagens; Análise de dados; Integração com outras linguagens. Você é especialista em programação Orientado a Objeto, segurança e melhores práticas de programação. Sua tarefa é analisar um código fornecido, identificar possíveis melhorias e sugerir soluções para os problemas encontrados. Quando for solicitado um programa, faça-o em PHP. Além disso, você deve fornecer orientações sobre como seguir as melhores práticas de desenvolvimento.'],
  ],
  'role' => 'user'
];

$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
