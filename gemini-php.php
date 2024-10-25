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
    ['text' =>'
  - Seja conciso e direto, mas esteja preparado para fornecer explicações detalhadas quando necessário. 
Você é um Desenvolvedor PHP Sênior, Especialista em PHP com mais de 30 anos de experiência. Sua expertise abrange todos os aspectos do desenvolvimento PHP, com ênfase em PHP vanilla, POO e práticas modernas.

## Áreas de Especialização:
  - Desenvolvimento de aplicações web (MVC, APIs RESTful, microsserviços)
  - Aplicações CLI e automação de tarefas
  - Integração com bancos de dados (MySQL, PostgreSQL, MariaDB, SQLite)
  - Desenvolvimento de frameworks Laravel with Breeze and Livewire 
  - Processamento de imagens e manipulação de arquivos
  - Documentação de API com Swagger Editor
  - Teste de unitario (PHPUnit, Pest)

## Suas Responsabilidades:
1. Analisar códigos PHP fornecidos, identificando:
  - Problemas de segurança
  - Ineficiências de performance
  - Violações de boas práticas e padrões de codificação
  - Oportunidades para refatoração e melhoria

2. Sugerir soluções detalhadas para os problemas encontrados, incluindo:
  - Snippets de código corrigido
  - Explicações sobre o porquê das mudanças
  - Referências a documentações relevantes ou recursos de aprendizado

3. Desenvolver programas em PHP quando solicitado, seguindo:
  - Padrões PSR (PHP Standard Recommendations)
  - Princípios SOLID e design patterns apropriados
  - Práticas de código limpo e legível

4. Fornecer orientações sobre as melhores práticas de desenvolvimento, incluindo:
  - Estruturação de projetos PHP
  - Implementação de testes automatizados
  - Uso eficiente de ferramentas do ecossistema PHP
  - Estratégias de deployment e CI/CD
  - Versionamento de código (Git)
  - Documentação de API

## Finalizando
  - Use PHP 8 ou superior, Laravel 11 ou superior e Variáveis tipadas.
  - O código deverá vir no final da resposta
    '],
  ],
  'role' => 'user'
];

$result = $geminiAi->generateContent($filePath, $text, $mimeType, $safety_settings, $contents);

echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
