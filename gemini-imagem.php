<?php 

require 'GeminiAi.php';
require 'MarkdownToBash.php';

// Verifica se imagem foi inserida como parametro
if ($argc < 2) {
  echo "faltou o arquivo!\n";
  exit(1);
}

$filePath = $argv[1];
if (!file_exists($filePath)) {
  echo "{$filePath} não existe\n";
  exit(1);
}
$mimeType = mime_content_type($filePath);

$text = 'Fale sobre essa foto?';

if ($argc > 2) $text = implode(' ', array_slice($argv, 2));

$apiKey = getenv('GOOGLE_API_KEY');
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

$safety_settings = []; // "BLOCK_NONE", "BLOCK_LOW_AND_ABOVE", "BLOCK_MEDIUM_AND_ABOVE", "BLOCK_ONLY_HIGH"
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings);

echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;

shell_exec("display $filePath");

