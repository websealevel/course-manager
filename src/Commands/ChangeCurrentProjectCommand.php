<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ChangeCurrentProjectCommand extends Command
{

    protected static $defaultDescription = 'Change the current projet in the global configuration (default current project)';


    public function execute(InputInterface $input, OutputInterface $output)
    {
        return COMMAND::FAILURE;
    }


    public function configure(): void
    {
    }
}
