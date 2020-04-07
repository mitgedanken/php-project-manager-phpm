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
        $items = $this->composer
            ->getDispatcher()
            ->getItems();

        // Wait for promises.
        \GuzzleHttp\Promise\all(
            array_map(
                static function (Package $item) {
                    return $item->fetchDependencies();
                },
                $items,
            )
        )->wait();

        return array_reduce(
            $items,
            static function (array $carry, Package $item) {
                [$name, $version] = explode(
                    ':',
                    (string) $item,
                );
                if (in_array($name, $carry, true)) {
                    return $carry;
                }
                $carry[] = $name;
                return $carry;
            },
            [],
        );
    }

}
