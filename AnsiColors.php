<?php

// Classe para gerenciar cores ANSI
class AnsiColors
{
    const BLACK = "\e[30m";
    const RED = "\e[31m";
    const GREEN = "\e[32m";
    const YELLOW = "\e[33m";
    const BLUE = "\e[34m";
    const MAGENTA = "\e[35m";
    const CYAN = "\e[36m";
    const WHITE = "\e[37m";

    const BG_BLACK = "\e[40m";
    const BG_RED = "\e[41m";
    const BG_GREEN = "\e[42m";
    const BG_YELLOW = "\e[43m";
    const BG_BLUE = "\e[44m";
    const BG_MAGENTA = "\e[45m";
    const BG_CYAN = "\e[46m";
    const BG_WHITE = "\e[47m";

    const RESET = "\e[0m";
    const BOLD = "\e[1m";
    const UNDERLINE = "\e[4m";
    const BLINK = "\e[5m";
    const REVERSE = "\e[7m";
    const HIDDEN = "\e[8m";
}

