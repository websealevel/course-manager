<?php

namespace Wsl\CourseManager;

use Exception;

use Wsl\CourseManager\Course;
use Wsl\CourseManager\FileManager;
use Wsl\CourseManager\Module;

class Env
{

    const ABS_PATH_KEY = 'path_courses';

    private function __construct(
        readonly public array $envVariables
    ) {
    }

    /**
     * Factory qui retourne un environnement avec validation préalable
     * @return Env
     */
    static public function create(): Env
    {

        try {

            if (php_sapi_name() !== 'cli') {
                throw new Exception("php doit être executé en mode CLI.");
            }

            $env_variables = static::readEnvVariables();
            $env = new Env($env_variables);
            return $env;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit;
        }
    }

    /**
     * Retourne un tableau contenant les variables d'environnemnt
     * @throws Exception - Si le fichier conf.ini n'est pas trouvé
     * @throws Exception - Si le fichier conf.ini ne contient pas les variables obligatoires
     * @return array
     */
    static public function readEnvVariables(): array
    {
        $conf_file = __DIR__ . '/../conf.ini';

        if (!file_exists($conf_file)) {
            throw new Exception("Fichier de configuration conf.ini manquant à la racine du projet.");
        }

        $env_variables = parse_ini_file($conf_file);

        if (false === $env_variables) {
            throw new Exception("Impossible de lire le fichier de configuration. Fichier mal formatté.");
        }

        $mandatory_options = array('path_courses');

        $diff = array_diff($mandatory_options, array_keys($env_variables));

        if (!empty($diff)) {
            throw new Exception(sprintf("Variables d'environnement non initialisées: %s", implode(",", $diff)));
        }

        return $env_variables;
    }

    /**
     * Formate un message pour l'afficher dans le terminal
     * @return void
     */
    public function print(string $message, ...$params): void
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }
        echo $message . PHP_EOL;
    }


    /**
     * Retourne le path absolu de la racine du projet (avec trailing slash)
     * @throws Exception - Si la variable ABS_PATH_KEY n'existe pas dans les variables d'environnement
     * @return string
     */
    public function abspath(): string
    {
        if (!isset($this->envVariables[self::ABS_PATH_KEY])) {
            throw new Exception("La variable d'environnement " . self::ABS_PATH_KEY . "n'est pas définie.");
        }
        return $this->envVariables[self::ABS_PATH_KEY] . '/';
    }

    public function fullPath(string $relativePath, string $trailing = '')
    {
        return $this->abspath() . $relativePath . $trailing;
    }

    /**
     * Créer le dossier $path à la racine du projet s'il n'existe pas déjà. Retourne vrai si la création du dossier
     * a réussi, faux sinon
     * @param string $path. Le path du dossier à écrire (relatif à abspath)
     * @throws Exception -- Si impossible de créer le dossier à cause des droits d'écriture.
     * @return bool
     */
    public function mkdirp(string $path): bool
    {

        $full_path = $this->fullPath($path);

        if (is_dir($full_path)) {
            $this->print(sprintf("Le répertoire %s existe déjà. Skip.", $path));
            return false;
        }

        $created = mkdir($full_path, recursive: true);

        if (!$created)
            throw new Exception(
                sprintf("Impossible de créer le dossier %s. Véfifier les droits en écriture sur le path. ", $path)
            );

        return $created;
    }


    /**
     * Retourne le contenu du README d'un cours à sa création
     * @return string
     */
    public function readmeContent(string $title): string
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
    public function indexHtmlContent(string $title): string
    {

        $titleCapitalFirst = ucfirst($title);

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

    /**
     * Initialise le répertoire d'un cours nouvellement créee (création de dossiers et de fichiers par défaut)
     * @param Course Le cours dont on initialise le répertoire
     * @return void
     */
    public function prepareDirectory(Course $course)
    {
        $dirsToCreate = array(
            'Bibliographie',
        );
        $filesToCreate = array(
            'README.md' => $this->readmeContent($course->name),
            'index.html' => $this->indexHtmlContent($course->name)
        );

        foreach ($dirsToCreate as $dir) {
            $this->mkdirp(
                sprintf("%s/%s", $course->path(), $dir)
            );
        }

        foreach ($filesToCreate as $file => $content) {
            $file = fopen(
                sprintf("%s/%s", $this->fullPath($course->path()), $file),
                'w'
            );
            fwrite($file, $content);
            fclose($file);
        }

        //Creation du module de presentation
        $module = new Module(
            $course,
            0,
            'presentation'
        );

        //Creation des sous directory du module
        foreach ($module->directories as $dir) {
            $this->mkdirp(
                sprintf("%s/%s", $module->path(), $dir)
            );
        }

        //Creation du fichier de cours
        $file = fopen(
            sprintf(
                "%s/%s/cours/%s",
                $this->fullPath($module->course->path()),
                $module->fullName(),
                $module->courseMarkdownFile()
            ),
            'w'
        );

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
        
        # {$module->name}
        
        Durée:
        niveau: {$module->course->level}

        MARPMARKDOWN;

        fwrite($file, $slideContent);
        fclose($file);
    }
}
