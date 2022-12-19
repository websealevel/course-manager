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
     * Nom du fichier de configuration global du programme. Maintient la liste des tous les projets de gestion de cours,
     * et un pointeur MAIN sur le projet courant.
     * @var string
     */
    public const HOME_CONFIG_FILE = '.course-manager';
    public const DEFAULT_ROOT_DIR = 'courses';

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
     * @param string $absPathToRootDir Le chemin absolu (et l'identifiant) du nouveau projet.
     * @throws Exception - Si impossible de créer le fichier de configuration s'il n'existe pas déjà.
     */
    public static function createHomeConfigFile(string $absPathToRootDir): void
    {
        $pathHome = static::getHomeDirOfUser();

        $absPathConfigHome = sprintf("%s/%s", $pathHome, FileManager::HOME_CONFIG_FILE);

        //S'il existe déjà on ajoute le projet à la liste des projets maintenus
        if (FileManager::fileExists($absPathConfigHome)) {

            //Recuperer la valeur sous la clefs PROJECTS

            //Ajouter le path aux paths existants

            return;
        }

        //Sinon, on crée le fichier de config global, et on met le pointeur MAIN sur le projet courant
        $success = static::createFile(
            $absPathConfigHome,
            sprintf(
                "MAIN=%s\nPROJECTS=%s",
                $absPathToRootDir,
                $absPathToRootDir
            )
        );

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
    public static function createFile(string $abspath, string $content = ''): bool
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
