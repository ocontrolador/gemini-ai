> 🤖 - ## Gemini AI PHP Library

This library provides a simple interface for interacting with the Gemini API.

### Features

* Generates content using the Gemini API.
* Supports attaching files to requests.
* Handles safety settings for generated content.
* Logs chat conversations for debugging and analysis.

### Installation

1. Copy the `GeminiAi.php` file to your project directory.
2. Include the file in your PHP scripts:

```php
require_once 'GeminiAi.php';
```

### Usage

1. Create a new instance of the `GeminiAi` class, providing your API key:

```php
$gemini = new GeminiAi('YOUR_API_KEY');
```

2. Use the `generateContent` method to generate content:

```php
$text = "Write a short story about a cat who goes on an adventure.";
$response = $gemini->generateContent($text);

// $response[0] contains the generated content
// $response[1] contains the total token count used
echo $response[0];
```

3. To attach a file to the request, provide the file path and MIME type:

```php
$filePath = 'path/to/your/file.txt';
$mimeType = 'text/plain';
$response = $gemini->generateContent($filePath, $text, $mimeType);
```

4. You can specify safety settings using an associative array:

```php
$safetySettings = [
    'hate_speech' => 0.9,
    'self_harm' => 0.8,
];

$response = $gemini->generateContent($text, $safetySettings: $safetySettings);
```

5. To provide previous conversation context, pass an array of `contents` to the `generateContent` method. Each element in the array should be an associative array with `parts` and `role` keys.

```php
$contents = [
    [
        'parts' => [
            ['text' => 'Hello!']
        ],
        'role' => 'user'
    ],
    [
        'parts' => [
            ['text' => 'Hi there!']
        ],
        'role' => 'assistant'
    ]
];

$response = $gemini->generateContent($text, $contents: $contents);
```

### Best Practices

* **Error Handling:** The library includes robust error handling to catch API errors and exceptions.
* **Logging:** The `saveLogChat` method provides a simple way to log chat conversations for debugging and analysis.
* **Security:** The library uses secure HTTPS communication with the Gemini API.
* **API Key Management:** Store your API key securely and do not expose it in your code. Use environment variables or configuration files to store your API key.

### README.md

This README.md file provides a comprehensive overview of the library, including installation instructions, usage examples, best practices, and other relevant information.

### Contributing

Contributions are welcome! Please submit pull requests with clear descriptions and tests.

### License

This library is licensed under the MIT License.


