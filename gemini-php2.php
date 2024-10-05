<?php

require 'GeminiAi.php';
require 'MarkdownToBash.php';

// Define a chave API
$apiKey = getenv('GOOGLE_API_KEY');

// Define as configurações de segurança
$safetySettings = [
    "HARM_CATEGORY_HARASSMENT" => "BLOCK_NONE",
    "HARM_CATEGORY_HATE_SPEECH" => "BLOCK_NONE",
    "HARM_CATEGORY_SEXUALLY_EXPLICIT" => "BLOCK_NONE",
    "HARM_CATEGORY_DANGEROUS_CONTENT" => "BLOCK_NONE",
];

// Define a persona do usuário
$userPersona = [
    'parts' => [
        ['text' => 'Você é um Desenvolvedor PHP Sênior, um Arquiteto de Software PHP, um Especialista em PHP, Guru de PHP, que prefere o PHP Vanilla. Tem vasta experiência com PHP, em: Desenvolvimento de aplicações web e desktop; Automação de tarefas; Processamento de dados; Desenvolvimento de jogos; Criar APIs para aplicativos móveisProcessamento de imagens; Análise de dados; Integração com outras linguagens. Você é especialista em programação Orientado a Objeto, segurança e melhores práticas de programação. Sua tarefa é analisar um código fornecido, identificar possíveis melhorias e sugerir soluções para os problemas encontrados. Quando for solicitado um programa, faça-o em PHP. Além disso, você deve fornecer orientações sobre como seguir as melhores práticas de desenvolvimento.'],
    ],
    'role' => 'user'
];

try {
    // Valida a chave API
    if (empty($apiKey)) {
        throw new Exception("Chave API não definida.");
    }

    // Instancia as classes
    $geminiAi = new GeminiAi($apiKey);
    $markdownToBash = new MarkdownToBash();

    // Obtém a pergunta do usuário
    $question = isset($argv[1]) ? $argv[1] : "Explique";

    // Verifica se foi passado um arquivo
    $filePath = isset($argv[2]) && file_exists($argv[2]) ? $argv[2] : '';
    $mimeType = $filePath ? mime_content_type($filePath) : '';

    // Chama a API do Gemini AI
    $result = $geminiAi->generateContent($filePath, $question, $mimeType, $safetySettings, [$userPersona]);

    // Converte o Markdown para Bash
    $bashOutput = $markdownToBash->convert($result[0]);

    // Exibe o resultado
    echo $bashOutput . PHP_EOL . "Total de tokens: " . $result[1] . PHP_EOL;

} catch (Exception $e) {
    // Exibe a mensagem de erro
    echo "Erro: " . $e->getMessage() . PHP_EOL;
}

