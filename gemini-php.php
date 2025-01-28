<?php
/*
 * Arquivo: gemini-php.php
 * Descrição: Interage com a API Gemini para obter respostas e convertê-las para código bash.
 * Autor: ATCO Dias
 * Data: 26/10/24
 */

require_once __DIR__ . '/GeminiAi.php';
require_once __DIR__ . '/MarkdownToBash.php';

// Verifica se foi fornecida uma pergunta como argumento
if ($argc < 2) {
  echo "Falta a pergunta!\n";
  exit(1);
}

// Remove o nome do arquivo do array de argumentos
array_shift($argv);

// Inicializa as variáveis para o caminho do arquivo e tipo MIME
$filePath = '';
$mimeType = '';

// Verifica se o primeiro argumento é um caminho válido para um arquivo
if (file_exists($argv[0])) {
  $filePath = $argv[0];
  $mimeType = mime_content_type($filePath);
  // Remove o caminho do arquivo do array de argumentos
  array_shift($argv);  
}

// Junta os argumentos restantes em uma string de texto
$text = (count($argv) > 0)? implode(' ', $argv): 'Explique';

// Obtém a chave de API do Google do ambiente
$apiKey = getenv('GOOGLE_API_KEY');

// Cria instâncias das classes GeminiAi e MarkdownToBash
$geminiAi = new GeminiAi($apiKey);
$markdownToBash = new MarkdownToBash();

// Define as configurações de segurança para a API Gemini
$safety_settings = [];
$safety_settings["HARM_CATEGORY_HARASSMENT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_HATE_SPEECH"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "BLOCK_NONE";
$safety_settings["HARM_CATEGORY_DANGEROUS_CONTENT"] = "BLOCK_NONE";

// Define o prompt para a API Gemini, incluindo as responsabilidades do desenvolvedor PHP
$contents[] = [
  'parts' => [
    ['text' =>'
Vou atuar como um Desenvolvedor PHP Sênior com vasta experiência em PHP, especializado em desenvolvimento web, APIs, automação, bancos de dados e Laravel.

## Responsabilidades:
1. Revisar código PHP em busca de:
   - Problemas de segurança
   - Ineficiências
   - Violações de padrões
   - Oportunidades de melhoria

2. Propor soluções detalhadas com:
   - Código corrigido
   - Explicações das alterações
   - Referências úteis

3. Desenvolver código seguindo:
   - Padrões PSR
   - Princípios SOLID
   - Boas práticas de codificação

4. Oferecer orientações sobre:
   - Estrutura de projetos
   - Testes automatizados
   - Ferramentas do ecossistema PHP
   - CI/CD e Git

## Finalizando
- Será usado **PHP 8+** e **Laravel 11+**. 
- **O código sempre deve ser apresentado ao final da resposta. Não use a frase `Código final está apresentado acima`.**


## A resposta de saída será seguirá a **seguite formatação**:
  - [ Explicação resumida]
  - [ Problemas encontrados ] (quando cabível)
  - [ Melhorias e soluções sugeridas ] (quando cabível)
  - [ Nome do código final ]
  - [Código final]
    '],
  ],
  'role' => 'model'
];

// Gera o conteúdo usando a API Gemini
echo "Processando...\n\n";
$result = $geminiAi->generateContent($text, $mimeType, $safety_settings, $contents, $filePath);

// Imprime a resposta convertida para código bash e o número total de tokens usados
echo $markdownToBash->convert($result[0]) . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;
