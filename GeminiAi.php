<?php

/**
 * Classe para interação com a API Gemini.
 */
class GeminiAi
{
    private $apiKey;

    /**
     * Construtor da classe.
     *
     * @param string $apiKey Chave API da API Gemini.
     */
    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('API key cannot be empty.');
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Gera conteúdo usando a API Gemini.
     *
     * @param string $filePath Caminho para um arquivo a ser anexado à requisição.
     * @param string $text Texto de entrada para gerar conteúdo.
     * @param string $mimeType Tipo MIME do arquivo, se aplicável.
     * @param array $safetySettings Configurações de segurança para o conteúdo gerado.
     * @param array $contents Conteúdo anterior da conversa.
     *
     * @return array Array contendo o conteúdo gerado e a contagem total de tokens.
     *
     * @throws InvalidArgumentException Se o texto de entrada ou o caminho do arquivo forem inválidos.
     * @throws RuntimeException Se ocorrer um erro de conexão ou um erro na resposta da API.
     */
    public function generateContent(string $filePath = '', string $text, string $mimeType = '', array $safetySettings = [], array $contents = []): array
    {
        $model = "gemini-1.5-flash-latest";

        if (empty($text)) {
            throw new InvalidArgumentException('Text cannot be empty.');
        }

        $fileData = $this->getFileData($filePath, $mimeType);

        $contents[] = [
            'parts' => [
                ['text' => $text]
            ],
            'role' => 'user'
        ];

        $i = count($contents) - 1;

        if (!empty($fileData)) {
            $contents[$i]['parts'][] = [
                'inline_data' => $fileData
            ];
        }

        $safety = $this->getSafetySettings($safetySettings);

        $candidates = ["contents" => $contents, "safetySettings" => $safety];

        $candidatesJson = json_encode($candidates);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        $response = $this->makeRequest($url, $candidatesJson);

        $body = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON decode error: ' . json_last_error_msg());
        }

        if (!isset($body['candidates'][0]['content'])) {
            throw new RuntimeException('Invalid API response: ' . json_encode($body));
        }

        // Salva log
        $this->saveLogChat($text, $contents, $body);

        return [$body['candidates'][0]['content']['parts'][0]['text'], $body['usageMetadata']['totalTokenCount']];
    }

    /**
     * Obtem dados de um arquivo para anexar à requisição.
     *
     * @param string $filePath Caminho para o arquivo.
     * @param string $mimeType Tipo MIME do arquivo.
     *
     * @return array Dados do arquivo formatados para a requisição.
     *
     * @throws InvalidArgumentException Se o arquivo não existir ou não for legível.
     */
    private function getFileData(string $filePath, string $mimeType): array
    {
        if (!empty($filePath)) {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new InvalidArgumentException('File does not exist or is not readable: ' . $filePath);
            }
            return [
                'mime_type' => $mimeType,
                'data' => base64_encode(file_get_contents($filePath))
            ];
        }
        return [];
    }

    /**
     * Formata as configurações de segurança para a requisição.
     *
     * @param array $safetySettings Configurações de segurança.
     *
     * @return array Configurações de segurança formatadas.
     */
    private function getSafetySettings(array $safetySettings): array
    {
        $safety = [];
        foreach ($safetySettings as $key => $value) {
            $safety[] = [
                'category' => $key,
                'threshold' => $value
            ];
        }
        return $safety;
    }

    /**
     * Realiza uma requisição HTTP para a API Gemini.
     *
     * @param string $url URL da API.
     * @param string $jsonData Dados da requisição em formato JSON.
     *
     * @return string Resposta da API.
     *
     * @throws RuntimeException Se ocorrer um erro de conexão ou um erro na resposta da API.
     */
    private function makeRequest(string $url, string $jsonData): string
    {
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $jsonData
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new RuntimeException('Error fetching data: ' . error_get_last()['message']);
        }

        $statusCode =  intval(substr($http_response_header[0], 9, 3));

        if ($statusCode != 200) {
            $errorMessage = $this->getHttpErrorMessage($statusCode, $response);
            echo "\e[31m" . $errorMessage . "\e[0m" . PHP_EOL;
            exit(1);
        }

        return $response;
    }

    /**
     * Obtém uma mensagem de erro HTTP.
     *
     * @param int $statusCode Código de status HTTP.
     * @param string $response Resposta da API.
     *
     * @return string Mensagem de erro.
     */
    private function getHttpErrorMessage(int $statusCode, string $response): string
    {
        $responseBody = json_decode($response, true);
        $message = $responseBody['error']['message'] ?? 'Unknown error';

        switch ($statusCode) {
            case 400:
                return 'HTTP 400 Bad Request: ' . $message;
            case 401:
                return 'HTTP 401 Unauthorized: ' . $message;
            case 403:
                return 'HTTP 403 Forbidden: ' . $message;
            case 404:
                return 'HTTP 404 Not Found: ' . $message;
            case 500:
                return 'HTTP 500 Internal Server Error: ' . $message;
            case 502:
                return 'HTTP 502 Bad Gateway: ' . $message;
            case 503:
                return 'HTTP 503 Service Unavailable: ' . $message;
            default:
                return 'HTTP Error ' . $statusCode . ': ' . $message;
        }
    }

    /**
     * Salva o log da conversa em um arquivo JSON.
     *
     * @param string $text Texto de entrada para gerar conteúdo.
     * @param array $contents Conteúdo anterior da conversa.
     * @param array $body Resposta da API.
     */
    private function saveLogChat(string $text, array $contents, array $body): void
    {    
        // Salva contents local. Usado no chat
        $fileContents = '.contents-gemini.json'; 
        array_push($contents, $body['candidates'][0]['content']);
        file_put_contents($fileContents, json_encode($contents), 0);

        // Salva no log
        $fileLog = $this->limpaNomeArquivo($text);
        file_put_contents($fileLog, json_encode($contents), 0);
    }

    /**
     * Remove caracteres proibidos de um nome de arquivo.
     * Remove espaços e  acentuação
     * Limita o tamanho
     * Verifica se já existe
     *
     * @param string $nomeArquivo O nome do arquivo a ser limpo.
     * @return string O nome do arquivo limpo.
    */
   private function limpaNomeArquivo(string $nomeArquivo): string {
       // Lista de caracteres proibidos
       $caracteresProibidos = array('/', '\\', '?', '*', '"', '<', '>', '|', ':', "\t", "\n", "\r", " ", ",", "--");

       // Remove espaços em branco e substitui caracteres proibidos por hífen
       $arquivoLimpo = trim(str_replace($caracteresProibidos, '-', $nomeArquivo));

       // Verificar se o nome do arquivo é válido
       //if (!preg_match('/^[a-zA-Z0-9_\.\-]+$/', $arquivoLimpo))
       //    die("Nome de arquivo inválido: $arquivoLimpo\n");
      
       // Limita e remove os acentos
       $fileName = substr(trim($arquivoLimpo), 0, 100);
       //$fileName = str_replace(" ", "-", $fileName);
       $fileName = iconv('UTF-8', 'ASCII//TRANSLIT', $fileName);

       $fileName = __DIR__ . '/log/' . $fileName . '.json';

       // Verifica se o arquivo já existe
       if (file_exists($fileName)) {
            // Se existir, gera um novo nome com um sufixo numérico
            $newFileName = $fileName;
            $i = 1;
            while (file_exists($newFileName)) {
                $newFileName = str_replace(".json", "-$i.json", $fileName);
                $i++;
            }
            $fileName = $newFileName;
       }

       return $fileName;
   }
}
