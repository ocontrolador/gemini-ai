<?php
/* 
 * ATCO Dias
 * 25/10/2024
 * gemini.corrige.php
 * Corrige a ortografia
*/


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
$text = 'Texto:
"""
' . $text . '
"""';
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
    ['text' => 'Você é um professor brasileiro de Língua Portuguesa. Sua função é apenas corrigir erros de concordância, gramática e ortografia do texto a seguir. 🚨 ATENÇÃO: Corrija o texto **sem perguntas ou comentários adicionais**.
Texto:
"""
[Insira o texto aqui]
"""'],
  ],
  'role' => 'user'
];

//* Funciona, mas não vi alteração na resposta
$contents[] = [
  'parts' => [
    ['text' => 'Ok, compreendi. Pode me enviar o texto. 😄'],
  ],
  'role' => 'model'
];


$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

echo $markdownToBash->convert($result[0]) . "[{$result[1]} tokens]" . PHP_EOL;

