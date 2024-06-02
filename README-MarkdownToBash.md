## Markdown to Bash

Esta classe PHP converte texto Markdown em texto formatado para o terminal. Ela usa códigos de cores ANSI para destacar elementos Markdown, como cabeçalhos, listas, negrito, itálico e links.

### Instalação

```bash
composer require your-vendor/markdown-to-bash
```

### Uso

```php
<?php

use MarkdownToBash\MarkdownToBash;

// Carregar o texto Markdown
$markdownText = file_get_contents('README.md');

// Criar uma instância da classe MarkdownToBash
$converter = new MarkdownToBash();

// Converter o texto Markdown para texto formatado para o terminal
$bashText = $converter->formatMarkdown($markdownText);

// Imprimir o texto formatado
echo $bashText;

?>
```

### Recursos

* Suporte a cabeçalhos, listas, negrito, itálico e links.
* Formatação de blocos de código.
* Códigos de cores ANSI.

### Limitações

* Não há suporte a tabelas.
* Não há suporte a destaque de sintaxe para blocos de código.

### Contribuições

Contribuições são bem-vindas! Abra um problema ou um pedido de pull request.
