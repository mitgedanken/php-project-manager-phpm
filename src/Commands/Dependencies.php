<?php declare(strict_types=1);

namespace PHPM\Commands;

use PHPM\Composer\Composer;
use PHPM\Core\Dependency;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Dependencies extends Command
{
    protected function configure()
    {
        $this
            ->setName('dependencies')
            ->setDescription('Show package dependencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = Composer::factory(
            $input->getOption(
                'composer-file'
            ),
            $input,
            $output
        );

        $composer->wait();
//
//        $dependencyPackages = (new Dependency($composer))
//            ->resolve();
//
//        foreach ($dependencyPackages as $dependencyPackage) {
//            $composer->getOutput()
//                ->writeln(
//                    $dependencyPackage,
//                    OutputInterface::OUTPUT_NORMAL
//                );
//        }

        return 0;
    }

}
