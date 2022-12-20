<?php


namespace Wsl\CourseManager\Models;


/**
 * Interface d'un noeud (projet, cours, module)
 */
interface INode
{
    /**
     * Retourne le chemin absolu du repertoire dans lequel le noeud existe
     * @return string
     */
    public function getAbsPathOfParentDirectory(): string;

    /**
     * Retourne la liste des repertoires par défaut dans le noeud
     * @return Directory[]
     */
    public function getDefaultDirectories(): array;

    /**
     * Retourne la liste des fichiers et de leur contenu par défaut
     * présents dans le noeud
     * @return File[]
     */
    public function getDefaultFiles(): array;
}
