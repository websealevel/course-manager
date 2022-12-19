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

        //Récuperer de l'input: vendor, level et name

        // $cours = new Course(Config::create(),);
        //Initialisation du contenu du dossier du cours
        // $course->createCourseDirectory();

        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {
    }
}
