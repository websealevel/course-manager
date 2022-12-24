<?php

namespace Wsl\CourseManager\Services;


class DefaultContent
{
    /**
     * Retourne le contenu par défaut d'un deck de slides marp (header, config yaml, style, première slide)
     * @return string
     */
    public static function marpFirstSlide(string $moduleName, string $courseLevel)
    {

        $slideContent = <<<MARPMARKDOWN
        ---
        marp: false
        paginate: true
        headingDivider: 2
        header:
        footer: 
        ---
        <style>
        section {
        font-size: 1.4rem;
        }
        </style>
        
        # {$moduleName}
        
        Durée:
        niveau: {$courseLevel}

        MARPMARKDOWN;

        return $slideContent;
    }

    /**
     * Retourne le contenu du README d'un cours à sa création
     * @return string
     */
    public static function readmeContent(string $title): string
    {

        $titleCapitalFirst = ucfirst($title);

        $content = <<<MARKDOWN

        # ${titleCapitalFirst}

        ## Notes

        ## Ressources

        MARKDOWN;

        return $content;
    }

    /**
     * Retourne le contenu du index.html d'un cours à sa création
     * @return string
     */
    public static function indexHtmlContent(string $title): string
    {

        $titleCapitalFirst = ucfirst($title);

        //Listing des fichiers du projet

        $content = <<<HTML

        <!DOCTYPE html>
        <html lang="fr"> 
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
            <h1>${titleCapitalFirst}</h1>
        </body>
        </html>

        HTML;

        return $content;
    }
}
