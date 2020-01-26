<?php declare(strict_types=1);

namespace PHPM\Composer;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

class Package
{
    protected $vendorName;
    protected $extensionName;
    protected $requireVersion;
    protected $dependencies = [];
    protected $guzzleClient;
    protected $composer;

    public function __construct(Composer $composer, string $vendorName, string $extensionName, string $requireVersion = '*')
    {
        $this->composer = $composer;
        $this->vendorName = $vendorName;
        $this->extensionName = $extensionName;
        $this->requireVersion = $requireVersion;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function getRequireVersion(): string
    {
        return $this->requireVersion;
    }

    /**
     * @param Package[] $dependencies
     * @return $this
     */
    public function setDependencies(array $dependencies): self
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    public function getDependencies(): array
    {
        if (!empty($this->dependencies)) {
            return $this->dependencies;
        }
        return [];
    }

    public function fetchDependencies()
    {
        $client = $this->guzzleClient ?? new Client(
            [
                'base_uri' => $this->composer->getInput()->getOption(
                    'repository'
                ),
            ]
        );

        $package = $client->requestAsync(
            'GET',
            sprintf(
                '/packages/%s.json',
                $this->vendorName . '/' . $this->extensionName
            )
        );

        yield $package;

        $promises = [];

        yield $promises;

    }
}
