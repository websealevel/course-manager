<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class InitCommand extends Command
{

    protected static $defaultDescription = 'Initialize a new courses managment system';

    public function execute(InputInterface $input, OutputInterface $output)
    {

        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {
    }
}
