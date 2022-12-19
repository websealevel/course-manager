<?php

/**
 * Classe qui gère la création de fichiers et de dossiers
 *
 * @package wsl
 */

namespace Wsl\CourseManager\Services;


class FileManager
{

    /**
     * Retourne le path absolu du dossier home/user
     * @return string
     */
    public static function getHomeDirOfUser(): string
    {
        $home = getenv("HOME");
        return $home;
    }

    /**
     * Retourne le contenu parsé d'un fichier de configuration au format INI
     * @param string $absPathIniFile Le chemin absolu vers le fichier de configuration du projet 
     * @return array|false
     */
    public static function parseIniFile(string $absPathIniFile): array|false
    {
        $size = filesize($absPathIniFile);

        $result = parse_ini_file($absPathIniFile);

        //Un fichier vide n'est pas un fichier INI invalide.
        if (false === $result && 0 !== $size)
            throw new \Exception("Impossible de parser le fichier de configuration INI. Veuillez vérifier sa syntaxe.");
        return $result;
    }

    /**
     * Retourne vrai si le fichier existe, faux sinon
     * @param string $absPathTofile Le path absolu du fichier
     * @return bool
     */
    public static function fileExists(string $absPathToFile): bool
    {
        return file_exists($absPathToFile);
    }

    /**
     * Retourne le chemin absolu du repertoire racine du projet
     * @return string
     */
    public static function absPathToRootDir(): string
    {

        //Si dans un projet de create-manager (présence d'un fichier config.ini avec une clef course-manager pour
        //le distinguer d'autres éventuels fichier .ini) alors le rootDir est le dossier courant.
        //Sinon, récuperer le projet MAIN défini dans le fichier de configuration global

        return '';
    }

    /**
     * Action: Crée le fichier $abspath et écrit le contenu dedans.
     * @param string $abspath Le path absolu du fichier à créée
     * @param string $content . Optionnel. Le contenu du fichier à écrire.
     * @return bool
     * @throws Exception - Si impossible d'ouvrir le fichier en écriture, impossible d'écrire dans le fichier.
     */
    public static function createFile(string $abspath, string|array $content = ''): bool
    {
        $file = fopen(
            $abspath,
            'w'
        );

        if (!$file) {
            throw new \Exception(sprintf("Impossible d'ouvrir le fichier %s", $abspath));
        }

        if (false === fwrite($file, $content)) {
            throw new \Exception(sprintf("Impossible d'écrire dans le fichier %s", $abspath));
        }

        return fclose($file);
    }


    /**
     * Action: supprime le fichier sur le path $abspath. Retourne vrai si la suppression
     * a eu lieu, faux sinon.
     * @param $abspath Le path du fichier à supprimer
     * @return bool
     */
    public static function removeFile(string $abspath): bool
    {
        return unlink($abspath);
    }

    /**
     * Action: supprime le dossier sur le path $abspath. Retourne vrai si la suppression
     * a eu lieu, faux sinon.
     * @param $abspath Le path du fichier à supprimer
     * @return bool
     */
    public static function removeDir(string $abspath): bool
    {
        //Supprimer le dossier et tout son contenu de manière récursive.
        return rmdir($abspath);
    }

    /**
     * Action: crée le repertoire racine du projet de gestion de cours. Retourne
     * vrai si la création du répertoire a réussi, faux sinon
     * @return string Le chemin absolu vers le dossier ROOT du projet
     * @throws Exception @see createDirectory()
     */
    public static function createRootDirectory(string $rootDir): string
    {

        //Récuperer le répertoire courant
        $absPathRootDir = sprintf("%s/%s", getcwd(), $rootDir);

        static::createDirectory($rootDir);

        return $absPathRootDir;
    }

    /**
     * Action: crée le dossier $path s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon
     * @param string $path. Le path du dossier à écrire
     * @throws Exception -- Si impossible de créer le dossier à cause des droits d'écriture, si le dossier existe déjà.
     * @return bool
     */
    public static function createDirectory(string $path): bool
    {

        if (is_dir($path)) {
            throw new \Exception(sprintf("Le projet <%s> existe déjà dans le repertoire courant. Abandon.", $path));
        }

        $created = mkdir($path, recursive: true);

        if (!$created) {
            throw new \Exception(
                sprintf("Impossible de créer le dossier %s. Véfifier les droits en écriture sur le path. ", $path)
            );
        }

        return $created;
    }

    /**
     * Action: crée un ensemble de dossiers
     * @param string[] Des paths de dossiers à créer
     * @return bool Retourne vrai si tous les dossiers ont été crées avec succès
     * @throws Exception - @see createDirectory
     */
    public static function createDirectories(array $dirs): bool
    {
        foreach ($dirs as $dir) {
            static::createDirectory($dir);
        }

        return true;
    }

    /**
     * Action: crée un ensemble de fichiers
     * @param string[] Des paths de fichiers à créer
     * @return bool Retourne vrai si tous les fichiers ont été crées avec succès
     * @throws Exception - @see createFile
     */
    public static function createFiles(array $files): bool
    {
        foreach ($files as $file) {
            static::createFile($file);
        }

        return true;
    }
}
