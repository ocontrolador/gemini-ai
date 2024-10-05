
var1 é NULL
var2 não é NULL
php
file_put_contents("nome_do_arquivo.txt", $texto_a_ser_adicionado, FILE_APPEND);
php
// Define o texto a ser adicionado
$texto = "Este é o texto que será adicionado ao arquivo.\n";

// Adiciona o texto ao arquivo existente "arquivo.txt"
file_put_contents("arquivo.txt", $texto, FILE_APPEND);

// Exibe uma mensagem de sucesso
echo "Texto adicionado ao arquivo com sucesso!";
