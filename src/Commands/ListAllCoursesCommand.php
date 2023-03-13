<?php

namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wsl\CourseManager\Config;
use Wsl\CourseManager\Services\Loader;

/**
 * Commande pour lister et inspecter tous les cours du projet courant
 */
class ListAllCoursesCommand extends Command
{
    protected static $defaultDescription = 'List all courses (sources) of the current project.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $project = Config::getCurrentProjectDefinedInGlobalConfiguration();

        //Recuperer tous les cours (dossiers) et scanner méta données

        $path_sources = sprintf("%s/%s", $project, 'sources');

        // var_dump($path_sources);

        $courses = Loader::coursesUnderProject($project);

        // //Afficher les cours

        return COMMAND::FAILURE;
    }

    public function configure(): void
    {
    }
}
