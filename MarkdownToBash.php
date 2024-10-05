<?php

require_once 'autoload.php';

//namespace Diaslasd;

// Classe para converter Markdown para texto formatado para o terminal
class MarkdownToBash
{
    /**
     * @var AnsiColors
     */
    private $colors;

    public function __construct()
    {
        $this->colors = new AnsiColors();
    }

    /**
     * Converte uma string Markdown para texto formatado para o terminal.
     *
     * @param string $markdown O texto Markdown a ser convertido.
     * @return string O texto formatado para o terminal.
     */
    private function formatMarkdownString(string $markdown): string
    {
        // Converter cabeçalhos
        $markdown = preg_replace_callback(
            '/^(#{1,6})\s+(.*?)\s*$/m',
            function ($matches) {
                $level = strlen($matches[1]);
                return str_repeat('»', $level) . ' ' . $this->colors::BG_CYAN . $this->colors::BOLD . $matches[2] . $this->colors::RESET . PHP_EOL;
            },
            $markdown
        );

        // Converter itens de listas
        $markdown = preg_replace_callback(
            '/^(\s*[-*+])\s+(.*?)$/m',
            function ($matches) {
                return $this->colors::YELLOW . $matches[1] . ' ' . $matches[2] . $this->colors::RESET;
            },
            $markdown
        );

        // Converter negrito e itálico
        $markdown = preg_replace('/\*\*(.*?)\*\*/', $this->colors::BOLD . '$1' . $this->colors::RESET, $markdown);
        $markdown = preg_replace('/\*(.*?)\*/', $this->colors::UNDERLINE . '$1' . $this->colors::RESET, $markdown);

        // Converter links
        $markdown = preg_replace_callback(
            '/\[(.*?)\]\((.*?)\)/',
            function ($matches) {
                return $this->colors::UNDERLINE . $matches[1] . $this->colors::RESET . ' (' . $this->colors::BLUE . $matches[2] . $this->colors::RESET . ')';
            },
            $markdown
        );

        // Converter tabelas
        $markdown = preg_replace_callback(
            '/^\|(.+)\|\s*$/m',
            function ($matches) {
                $rows = array_map('trim', explode('|', $matches[1]));
                $output = '';
                foreach ($rows as $row) {
                    $output .= $this->colors::CYAN . str_pad($row, 15) . $this->colors::RESET;
                }
                return $output;
            },
            $markdown
        );

        // Converter linhas horizontais de tabelas
        $markdown = preg_replace(
            '/^\|(?:\s*:?-+:?\s*\|)+\s*$/m',
            str_repeat('-', 15 * 5) . PHP_EOL,
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
    public function convert(string $markdown, string $filename = null): string
    {
        if (strpos($markdown, '```') === false) {
            return $this->formatMarkdownString($markdown);
        }

        $markdownArray = explode('```', $markdown);
        $bash = '';

        foreach ($markdownArray as $key => $value) {
            if ($key % 2 == 1) {
              $bash .= "\n" . $this->colors::BG_BLACK . $this->colors::WHITE . '👾 ' . $value . $this->colors::RESET . "\n";
              if (!is_null($filename)) file_put_contents($filename, $value, FILE_APPEND);
            } else {
                $bash .= $this->formatMarkdownString($value);
            }
        }

        return $bash;
    }
    


}


