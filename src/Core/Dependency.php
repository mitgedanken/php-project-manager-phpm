<?php declare(strict_types=1);
namespace PHPM\Core;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use PHPM\Composer\Composer;
use PHPM\Composer\Package;

class Dependency
{
    protected $fetched = [];
    protected $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    public function resolve(): array
    {
        $responses = \GuzzleHttp\Promise\all($this->fetch())
            ->wait();

        var_dump($this->composer->getDispatcher()->getItems());
        return [];
    }

    public function fetch()
    {
        $promises = [];
        foreach ($this->composer->getRequires() as $package) {
            /**
             * @var Promise $promise
             */
            $promise = $package->fetchDependencies()
                ->current();

            if ($promise === null) {
                continue;
            }

            $promises[] = $promise->then(function(Response $onFulfilled) use ($package) {
                $jsonData = json_decode(
                    (string) $onFulfilled->getBody(),
                    true
                );

                $selectedVersion = array_filter($jsonData['package']['versions'] ?? [], function ($detail, $key) use ($package) {
                    return !!preg_match('/' . $package->getRequireVersion() . '/', $key);
                }, ARRAY_FILTER_USE_BOTH);

                $selectedVersion = current($selectedVersion);

                $this->composer->getDispatcher()->dispatch(
                    new Composer(
                        $selectedVersion,
                        $this->composer->getDispatcher(),
                        $this->composer->getInput(),
                        $this->composer->getOutput(),
                        $this->composer,
                    )
                );

            });
            $package->fetchDependencies()
                ->next();
        }
        return $promises;
    }
}