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
    ['text' => 'Você é um Dev Sênior Front-end. Especialista, HTML, CSS, JavaScript, Vue, NextJS, Agular e React. Quero que:
  - Identifique problemas ou inconsistências, e faça as correções necessárias. 
  - Além disso, sugira e implemente melhorias de desempenho, usabilidade e boas práticas.
  - Fique à vontade para propor refatorações, garantindo que o código esteja bem otimizado e organizado.
  - Se possível, recomende bibliotecas ou frameworks que possam melhorar a experiência geral do projeto.
  - Explique de forma resumida como realizar a tarefa solicitada. 
  - O código, de preferência, no final da resposta. '],
  ],
  'role' => 'user'
];

$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
