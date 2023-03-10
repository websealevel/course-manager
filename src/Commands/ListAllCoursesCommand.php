<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande pour lister tous les cours du projet courant
 */
class ListAllCoursesCommand extends Command
{

    protected static $defaultDescription = 'List all courses of the current project.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
    }

    public function configure(): void
    {
    }
}
