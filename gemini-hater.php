<?php

require_once __DIR__ . '/GeminiAi.php';
require_once __DIR__ . '/MarkdownToBash.php';

// Verifica se tem parametro
if ($argc < 2) {
  echo "faltou a pergunta!\n";
  exit(1);
}

array_shift($argv);
$filePath = '';
$mimeType = '';
if (file_exists($argv[0])) {
  $filePath = $argv[0];
  $mimeType = mime_content_type($filePath);
  array_shift($argv);  
}

$text = (count($argv) > 0)? implode(' ', $argv): 'Explique';
//echo "argv: " . $text. $filePath. $mimeType . PHP_EOL;exit();

$apiKey = getenv('GOOGLE_API_KEY');
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

$safety_settings = []; //"BLOCK_NONE", "BLOCK_LOW_AND_ABOVE", "BLOCK_MEDIUM_AND_ABOVE", "BLOCK_ONLY_HIGH", 
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

$contents[] = [
  'parts' => [
    ['text' => 'Você é um programador PHP sênior, com mais de 40 anos de experiência, que odeia a linguagem de programação Python. Não importa o que eu perguntar, você sempre responderá com uma crítica ao Python. Seja sarcástico e engraçado nas suas considerações. Use um humor ácido. Seja impaciente com programadores Python. Use emoji. Sempre enalteça o PHP. Imortante! Quando te pedirem um código em Python, responda com um código em PHP.'],
  ],
  'role' => 'user'
];

// Gera o conteúdo usando a API Gemini
echo "Processando...\n";
$result = $geminiAi->generateContent($text, $mimeType, $safety_settings, $contents, $filePath);

// Resposta da API Gemini
$resposta = $result[0] . "\n[{$result[1]}] **Tokens**\n";
echo $markdownToBash->convert($resposta);

