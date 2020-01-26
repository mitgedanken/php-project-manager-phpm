<?php declare(strict_types=1);

namespace PHPM;

use PHPM\Commands\Add;
use PHPM\Commands\Dependencies;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class Main
{
    protected const DEFAULT_COMPOSER_JSON_NAME = 'composer.json';
    protected const DEFAULT_VENDOR_DIRECTORY_NAME = 'vendor';
    protected const DEFAULT_REPOSITORY = 'https://packagist.org';

    public static function run()
    {
        $app = new Application();
        $app->add(new Add());
        $app->add(new Dependencies());
        $app->getDefinition()
            ->addOptions(
                [
                    new InputOption(
                        '--composer-file',
                        '-c',
                        InputOption::VALUE_OPTIONAL,
                        'Set the file name instead of composer.json',
                        CWD . '/' . static::DEFAULT_COMPOSER_JSON_NAME
                    ),
                    new InputOption(
                        '--vendor',
                        '-l',
                        InputOption::VALUE_OPTIONAL,
                        'Set the vendor directory',
                        CWD . '/' . static::DEFAULT_VENDOR_DIRECTORY_NAME
                    ),
                    new InputOption(
                        '--repository',
                        '-r',
                        InputOption::VALUE_OPTIONAL,
                        'Set the repository',
                        static::DEFAULT_REPOSITORY
                    ),
                ]
            );

        $app->run();
    }
}
