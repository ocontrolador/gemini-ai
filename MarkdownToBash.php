<?php

class MarkdownToBash
{
    // Códigos de cores ANSI
    private $colors = array(
        'black' => "\e[30m",
        'red' => "\e[31m",
        'green' => "\e[32m",
        'yellow' => "\e[33m",
        'blue' => "\e[34m",
        'magenta' => "\e[35m",
        'cyan' => "\e[36m",
        'white' => "\e[37m",
        'bg_black' => "\e[40m",
        'bg_red' => "\e[41m",
        'bg_green' => "\e[42m",
        'bg_yellow' => "\e[43m",
        'bg_blue' => "\e[44m",
        'bg_magenta' => "\e[45m",
        'bg_cyan' => "\e[46m",
        'bg_white' => "\e[47m",
        'reset' => "\e[0m",
        'bold' => "\e[1m",
        'underline' => "\e[4m",
        'blink' => "\e[5m",
        'reverse' => "\e[7m",
        'hidden' => "\e[8m"
    );


    /**
     * Converte uma string Markdown para texto formatado para o terminal.
     *
     * @param string $markdown O texto Markdown a ser convertido.
     * @return string O texto formatado para o terminal.
     */
    private function convertString(string $markdown): string
    {
        // Converter cabeçalhos
        $markdown = preg_replace_callback(
            '/^(#{1,6})\s+(.*?)\s*$/m',
            function ($matches) {
                $level = strlen($matches[1]);
                $color = $this->colors['bg_cyan'] . $this->colors['bold'];
                return str_repeat('»', $level) . ' ' . $color . $matches[2] . $this->colors['reset'] . PHP_EOL;
            },
            $markdown
        );

        // Converter itens de listas
        $markdown = preg_replace_callback(
            '/^(\s*[-*+])\s+(.*?)$/m',
            function ($matches) {
                return $this->colors['yellow'] . $matches[1] . ' ' . $matches[2] . $this->colors['reset'];
            },
            $markdown
        );

        $markdown = preg_replace_callback(
            '/^(\d+\.)\s+(.*?)$/m',
            function ($matches) {
                return $this->colors['yellow'] . $matches[1] . ' ' . $matches[2] . $this->colors['reset'];
            },
            $markdown
        );

        // Converter negrito e itálico
        $markdown = preg_replace('/\*\*(.*?)\*\*/', $this->colors['bold'] . '$1' . $this->colors['reset'], $markdown);
        $markdown = preg_replace('/\*(.*?)\*/', $this->colors['underline'] . '$1' . $this->colors['reset'], $markdown);

        // Converter links
        $markdown = preg_replace_callback(
            '/\[(.*?)\]\((.*?)\)/',
            function ($matches) {
                return $this->colors['underline'] . $matches[1] . $this->colors['reset'] . ' (' . $this->colors['blue'] . $matches[2] . $this->colors['reset'] . ')';
            },
            $markdown
        );

        return $markdown;
    }

    /**
     * Converte o texto Markdown dado em texto formatado para o terminal com blocos de código formatados.
     *
     * @param string $markdown O texto Markdown a ser convertido.
     * @return string O texto formatado para o terminal.
     */
    public function convert(string $markdown): string
    {
        if (strpos($markdown, '```') === false) {
            return $this->convertString($markdown);
        }

        $markdownArray = explode('```', $markdown);
        $bash = '';

        foreach ($markdownArray as $key => $value) {
            if ($key % 2 == 1) {
                $bash .=  "\n" . $this->colors['bg_black'] . $this->colors['white'] . '👾 ' . $value . $this->colors['reset'] . "\n";
            } else {
                $bash .= $this->convertString($value);
            }
        }

        return $bash;
    }
}

/*
// Exemplo de uso
$markdownText = file_get_contents('resposta.md');

$converter = new MarkdownToBash();
echo $converter->convert($markdownText) . PHP_EOL;
*/
