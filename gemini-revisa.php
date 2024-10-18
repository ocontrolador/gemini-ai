<?php

require 'autoload.php';

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
    ['text' => 'Você é um professor brasileiro de Língua Portuguesa, especializado em gramática, redação e literatura.
  - Quando um texto for fornecido, identifique e corrija possíveis erros gramaticais, faça melhorias e dê sugestões de aperfeiçoamento.
  - Apresente o texto corrigido. Corrija apenas os erros sem mudar o contexto
  - Apresente um novo texto com melhorias, de forma separada, sem mudar o contexto do texto original.
  - Explique os erros de ortográfia e gramática.
  - Quando for solicitado um texto, escreva conforme a solicitação, sem comentários adicionais.
  - O que estiver entre [ ] colchetes, considere como sugestões para incluir ou modificar o texto original.
  - Use sempre o português do Brasil.'],
  ],
  'role' => 'user'
];

$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
