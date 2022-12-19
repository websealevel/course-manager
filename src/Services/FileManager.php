<?php

/**
 * Classe qui gère la création de fichiers et de dossiers
 *
 * @package wsl
 */

namespace Wsl\CourseManager\Services;


class FileManager
{

    public const HOME_CONFIG_FILE = '.create-manager';

    public const DEFAULT_ROOT_DIR = 'cours';


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
     * Action: Crée un fichier de configuration .create-manager dans le répertoire home de l'utilisateur courant
     * s'il n'existe pas déjà
     * @param string $content Le contenu du fichier de configuration
     * @throws Exception - Si impossible de créer le fichier de configuration s'il n'existe pas déjà.
     */
    public static function createHomeConfigFile(string $content): void
    {
        $pathHome = static::getHomeDirOfUser();

        $absPathConfigHome = sprintf("%s/%s", $pathHome, FileManager::HOME_CONFIG_FILE);

        //S'il existe déjà on ne fait rien

        if (FileManager::fileExists($absPathConfigHome))
            return;

        $success = static::createFile($absPathConfigHome, $content);

        if (!$success)
            throw new \Exception("Impossible de créer le fichier de configuration %s.", $absPathConfigHome);

        return;
    }

    /**
     * Retourne le contenu parsé d'un fichier de configuration au format INI
     * @param string $absPathIniFile Le chemin absolu vers le fichier de configuration du projet 
     * @return array|false
     */
    public static function parseIniFile(string $absPathIniFile): array|false
    {

        $result = parse_ini_file($absPathIniFile);
        if (!$result)
            throw new \Exception("Impossible de parser le fichier de configuration. Veuillez vérifier sa syntaxe.");
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

    public static function absPathToRootDir()
    {
    }

    /**
     * Action: Crée le fichier $abspath et écrit le contenu dedans.
     * @param string $abspath Le path absolu du fichier à créée
     * @param string $content . Optionnel. Le contenu du fichier à écrire.
     * @return bool
     */
    public static function createFile(string $abspath, string $content = ''): bool
    {
        $file = fopen(
            $abspath,
            'w'
        );
        fwrite($file, $content);
        return fclose($file);
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
}
