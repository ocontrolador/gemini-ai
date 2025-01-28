<?php
// linux, bash, git, github, vscode, vim, mysql

require 'GeminiAi.php';
require 'MarkdownToBash.php';

// Variaveis
$apiKey = getenv('GOOGLE_API_KEY');
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

// Verifica se tem parametro
if ($argc < 2) {
  echo "faltou a pergunta!\n";
  exit(1);
}

// Verifica se tem arquivo anexo
array_shift($argv);
$filePath = '';
$mimeType = '';
if (file_exists($argv[0])) {
  $filePath = $argv[0];
  $mimeType = mime_content_type($filePath);
  array_shift($argv);  
}

// Programa específico
$parametro = (isset($argv[0]))? $argv[0] : '';
if ($parametro[0] == '-') {
  $programa = substr($parametro,1);
  array_shift($argv);
} else {
  $programa = "Linux, Ubuntu, Bash, Shell Script, SSH, PHP, MySQL, Laravel, VsCode, Python, JavaScript, Deno.js, CSS, HTML, Git, GitHub, GitLab e Vim";
}

// Pergunta
$text = (count($argv) > 0)? implode(' ', $argv): 'Explique';

// Seta parametros da API Gemini
$safety_settings = []; //"BLOCK_NONE", "BLOCK_LOW_AND_ABOVE", "BLOCK_MEDIUM_AND_ABOVE", "BLOCK_ONLY_HIGH", 
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

// Conteudo da pergunta
$contents[] = [
  'parts' => [
    ['text' => "Você é um especialista em **{$programa}**. 
    - O Ambiente de trabalhor é o Linux Ubuntu 20.04. Nunca faça referência a outro SO.
    - De preferência para comandos no terminal.
    - De preferência a teclas de atalhos ao invês de acesso ao menu.
    - Infome apenas o necessário.
    - **Não** faça comentários.
    - O código, script, comando ou atalho apropriado para executar tarefa solicitada, deverá, sempre, vir no final da resposta. "],
  ],
  'role' => 'user'
];


// Pergunta a API Gemini
$result = $geminiAi->generateContent($text, $mimeType, $safety_settings, $contents, $filePath);

// Resposta
echo $markdownToBash->convert($result[0]) . "[tokens: " . $result[1] . "]\n";

