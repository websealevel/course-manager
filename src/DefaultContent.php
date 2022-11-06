<?php


namespace Wsl\CourseManager;

class DefaultContent{

    /**
     * Retourne le contenu par défaut d'un deck de slides marp (header, config yaml, style, première slide)
     * @return string
     */
    public static function marpFirstSlide(string $moduleName, string $courseLevel){

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
}