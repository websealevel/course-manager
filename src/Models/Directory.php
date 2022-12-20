<?php

namespace Wsl\CourseManager\Models;

/**
 * Une classe représentant un répertoire
 */
class Directory
{
    public function __construct(

        public readonly string $name,

        /**
         * Liste des dossiers qui doivent être présents
         * @var Directory[]
         */
        public readonly array $directories = array(),

        /**
         * Liste des fichiers qui doivent être présents
         * @var File[]
         */
        public readonly array $files = array(),

        public readonly string $description = ''
    ) {
    }

    /**
     * Retourne vrai si le repertoire est supposé contenir
     * des fichiers, faux sinon.
     */
    public function hasFiles(): bool
    {
        return 0 !== count($this->files);
    }
}
