<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wsl\CourseManager\Models\Course;

class InitCommand extends Command
{

    protected static $defaultDescription = 'Initialize a new courses managment system';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Initialisation d'un nouveau système de gestion de cours

        //Creer le dossier racine
        //Creer les dossiers sources, templates, public
        //Creer les fichiers index.html, .env

        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {
    }
}
