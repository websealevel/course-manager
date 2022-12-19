<?php


namespace Wsl\CourseManager\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wsl\CourseManager\Services\FileManager;
use Wsl\CourseManager\Services\Formatter;

class CreateCourseCommand extends Command
{

    protected static $defaultDescription = 'Create a new course in the current project.';

    protected static $defaultName = 'add:course';

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $courseName = $input->getArgument('course_name');

        $vendorName = $input->getArgument('vendor_name') ?? '';

        $keywords = $input->getOption('keywords');

        $levels = $input->getOption('level');

        var_dump(array(
            $courseName, $vendorName, $keywords, $levels
        ));

        //Check que vendorName/courseName n'existe pas déjà, sinon renvoyer une erreur.

        $dirName = Formatter::pathOfCourse($courseName, $vendorName);

        if (FileManager::dirExists($dirName)) {
            $output->writeln(
                sprintf("Le cours %s existe déjà.", $dirName)
            );
            return COMMAND::FAILURE;
        }

        //Creer le dossier 

        FileManager::createDirectory($dirName);

        //Initialiser le contenu par défaut (dont les métadonnées)

        return COMMAND::SUCCESS;
    }

    public function configure(): void
    {

        //Arguments

        $this
            ->addArgument(
                'course_name',
                InputArgument::REQUIRED,
                'The name of the course you want to add to the current project.'
            );

        $this
            ->addArgument(
                'vendor_name',
                InputArgument::OPTIONAL,
                'The name of the vendor for which the course is attached.'
            );

        //Options

        $this
            ->addOption(
                // this is the name that users must type to pass this option (e.g. --iterations=5)
                'keywords',
                // this is the optional shortcut of the option name, which usually is just a letter
                // (e.g. `i`, so users pass it as `-i`); use it for commonly used options
                // or options with long names
                '-k',
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                // the option description displayed when showing the command help
                'Keywords to describe the course content. Separate each keyword by a comma (e.g --keywords=foo,bar)',
                // the default value of the option (for those which allow to pass values)
                []
            );


        $this
            ->addOption(
                'level',
                '-l',
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                // the option description displayed when showing the command help
                'Labels to describe the level of the course. Separate each level by a comma (e.g --keywords=B1,B2)',
                // the default value of the option (for those which allow to pass values)
                []
            );
    }
}
