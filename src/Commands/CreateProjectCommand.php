<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Wsl\CourseManager\Config;
use Wsl\CourseManager\Services\FileManager;

class CreateProjectCommand extends Command
{

    protected static $defaultDescription = 'Create a new courses managment system in the current directory.';

    public const CONF_FILE = 'config.ini';


    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Initialisation d'un nouveau système de gestion de cours
        $rootDir = $input->getArgument('root_dir') ?? Config::DEFAULT_ROOT_DIR;

        $output->writeln([
            sprintf("Creating a new courses managment project %s", $rootDir),
            '============',
            '',
        ]);

        try {
            $absPathOfRootDir = FileManager::createRootDirectory($rootDir);
        } catch (\Exception $e) {
            $output->writeln([
                $e->getMessage(),
            ]);
        }

        //Initialiser le fichier de configuration global dans le repertoire $HOME/.course-manager
        try {
            Config::registerProject($absPathOfRootDir);
        } catch (\Exception $e) {

            //Rollback: supprimer le dossier $rootDir
            FileManager::removeDir($rootDir);

            $output->writeln([
                $e->getMessage()
            ]);

            return COMMAND::FAILURE;
        }


        //Peupler le projet avec la structure de dossiers/fichiers par défaut.

        $dirs = Config::projectDefaultDirectories();
        $files = Config::projectDefaultFiles();


        $absPathDirs = array_map(function ($dir) use ($absPathOfRootDir) {
            return sprintf("%s/%s", $absPathOfRootDir, $dir);
        }, $dirs);

        $absPathFiles = array_map(function ($file) use ($absPathOfRootDir) {
            return sprintf("%s/%s", $absPathOfRootDir, $file);
        }, $files);

        try {
            //Creer les dossiers sources, templates, public
            FileManager::createDirectories($absPathDirs);
            //Creer les fichiers index.html, config.ini
            FileManager::createFiles($absPathFiles);
        } catch (\Exception $e) {
            $output->writeln([
                $e->getMessage(),
            ]);

            //Rollback: suppresion du $rootDir et du projet dans le fichier de configuration global
            FileManager::removeDir($rootDir);
            //Rollback à tester
            Config::removeFromConfigFile($rootDir);

            return COMMAND::FAILURE;
        }


        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {
        $this
            ->addArgument('root_dir', InputArgument::OPTIONAL, 'The name of your root course managment folder.');
    }
}
