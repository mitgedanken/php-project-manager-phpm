<?php declare(strict_types=1);

namespace PHPM\Composer;

use PHPM\Core\Dispatcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Composer
{
    protected $json;
    protected $requires = [];
    protected $input;
    protected $output;
    protected $parentComposer;
    protected $dispatcher;

    public function __construct(array $json, Dispatcher $dispatcher, InputInterface $input, OutputInterface $output, ?Composer $parentComposer = null)
    {
        $this->json = $json;
        $this->input = $input;
        $this->output = $output;
        $this->dispatcher = $dispatcher;
        $this->parentComposer = $parentComposer;
        foreach ($json['require'] ?? [] as $extension => $requireVersion) {
            if (preg_match('/\A([^\/]+)\/(.+)\z/', $extension, $matches)) {
                $package = new Package(
                    $this,
                    $matches[1],
                    $matches[2],
                    $requireVersion
                );
                if ($this->getDispatcher()
                    ->findBy(
                        $package,
                        static function (Package $a, Package $b) {
                            return (string) $a === (string) $b;
                        }
                    )
                ) {
                    continue;
                }

                $this->requires[] = $package;

                $this->getDispatcher()
                    ->dispatch($package);

                $this->getOutput()
                    ->writeln(
                        "Load a package speculation: " . $package,
                    );

                $package->fetchDependencies();
            }
        }
    }

    public function wait(): self
    {
        \GuzzleHttp\Promise\all(
            array_map(
                static function (Package $item) {
                    return $item->fetchDependencies();
                },
                $this->getDispatcher()->getItems(),
            )
        )->wait();
        return $this;
    }

    public static function factory(string $path, InputInterface $input, OutputInterface $output): self
    {
        // TODO: Check to exists composer.json
        return new static(
            json_decode(
                file_get_contents(
                    $path
                ),
                true
            ),
            new Dispatcher(),
            $input,
            $output
        );
    }

    /**
     * @return Package[]
     */
    public function getRequires(): array
    {
        return $this->requires;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function getResolvedRequires(): array
    {
        $requires = [];
        $current = $this;
        do {
            $requires = array_merge(
                $requires,
                $current->getRequires(),
            );
        } while (($current = $current->parentComposer) !== null);

        return $requires;
    }
}