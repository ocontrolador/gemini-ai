<?php
class GeminiAi
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('API key cannot be empty.');
        }
        $this->apiKey = $apiKey;
    }

    public function generateContent(string $filePath, string $text, string $mimeType = '', array $safetySettings = [], array $contents = []): array
    {
        $model = "gemini-1.5-flash-latest";

        if (empty($text)) {
            throw new InvalidArgumentException('Text cannot be empty.');
        }

        $fileData = '';
        if (!empty($filePath)) {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new InvalidArgumentException('File does not exist or is not readable: ' . $filePath);
            }
            $fileData = base64_encode(file_get_contents($filePath));
        }
        
        $contents[] = [
            'parts' => [
                ['text' => $text]
            ],
            'role' => 'user'
        ];

        $i = count($contents) - 1;

        if (!empty($fileData)) {
            $contents[$i]['parts'][] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $fileData
                ]
            ];
        }

        $safety = [];
        foreach ($safetySettings as $key => $value) {
            $safety[] = [
                'category' => $key,
                'threshold' => $value
            ];
        };

        $candidates = ["contents" => $contents, "safetySettings" => $safety];  
        
        $candidatesJson = json_encode($candidates);
        //file_put_contents('candidates.json', $candidatesJson,0);exit();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($candidates));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('cURL Error: ' . $curlError);
        }

        if ($statusCode != 200) {
            $errorMessage = $this->getHttpErrorMessage($statusCode, $response);
            echo "\e[31m" . $errorMessage . "\e[0m" . PHP_EOL; exit(1);
        }

        $body = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON decode error: ' . json_last_error_msg());
        }

        if (!isset($body['candidates'][0]['content'])) {
            throw new RuntimeException('Invalid API response: ' . json_encode($body));
        }

        // salva historico
        array_push($contents, [$body['candidates'][0]['content']]);
        file_put_contents('contents.json',json_encode($contents),0);

        return [$body['candidates'][0]['content']['parts'][0]['text'], $body['usageMetadata']['totalTokenCount']];
    }

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
}

