<?php

namespace Wsl\CourseManager;

use Exception;

use Wsl\CourseManager\Course;

class Env
{
    const ABSPATH = '/home/paul/depots/websealevel/log/enseignement/cours';

    static public function init()
    {
        if (php_sapi_name() !== 'cli') {
            throw new Exception("php doit être executé en mode CLI.");
            exit;
        }
    }

    /**
     * Formate un message pour l'afficher dans le terminal
     * @return void
     */
    static public function print(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * Créer le dossier $path s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon
     * @param string $path. Le path du dossier à écrire (relatif à PATH_COURSES)
     * @global PATH_COURSES
     * @return bool
     * @throws Exception -- Si impossible de créer le dossier à cause des droits d'écriture.
     */
    static public function mkdirp(string $path): bool
    {

        if (is_dir(static::ABSPATH . $path)) {
            static::print(sprintf("Le répertoire %s existe déjà. Skip.", $path));
            return false;
        }

        $created = mkdir(static::ABSPATH . $path);

        if (!$created)
            throw new Exception(
                sprintf("Impossible de créer le dossier %s. Véfifier les droits en écriture sur le path. ", $path)
            );

        return $created;
    }
    /* Retourne la liste des Flags détéctés dans l'input user
    * @param int $argc Le nombre d'arguments du script PHP
    * @param array $argv Les arguments du script PHP
    * @return Flag[] Un tableau de flags détectés définis par le système
    */
    static public function parse_flags(int $argc, array $argv): array
    {
        /**
         * Remove script name
         */
        array_shift($argv);

        $flags_found = array();

        foreach ($argv as $arg) {

            //Si des flags
            if (str_contains($arg, '--') || str_contains($arg, '-')) {

                $found = array_filter(FLAGS, function ($flag) use ($arg) {

                    $names = array($flag->name, $flag->short);

                    return in_array(str_replace(array('--', '-'), '', $arg), $names);
                });

                if (!empty($found)) {
                    $flags_found[] = $found[0];
                }
            }
        }

        return $flags_found;
    }

    /**
     * Initialise le répertoire d'un cours nouvellement créee (création de dossier et de fichiers par défaut)
     * @param Course Le cours dont on initialise le répertoire
     * @return void
     */
    static public function prepareDirectory(Course $course)
    {
        $dirs_to_create = array(
            'Bibliographie'
        );
        $files_to_create = array(
            'README.md'
        );

        foreach ($dirs_to_create as $dir) {
            static::mkdirp(
                sprintf("%s/%s", $course->path(), $dir)
            );
        }

        foreach ($files_to_create as $file) {
        }
    }
}
