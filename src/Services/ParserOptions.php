<?php


namespace Wsl\CourseManager\Services;


/**
 * Classe en charge de parser les options d'une commande
 */
class ParserOptions
{

    /**
     * Retourne les options d'une commande dans une chaine de caractères
     * @param string[] $options Les options récupérées par une Commande
     * @return string
     */
    public static function flatten(array $options, string $separator = " "): string
    {
        $str = implode($separator, $options);
        return str_replace(',', $separator, $str);
    }
}
