<?php declare(strict_types=1);

namespace PHPM\Composer;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;

class Package
{
    protected $vendorName;
    protected $extensionName;
    protected $requireVersion;
    protected $dependencies = [];
    protected $guzzleClient;
    protected $composer;
    protected $promise;

    public function __construct(Composer $composer, string $vendorName, string $extensionName, string $requireVersion = '*')
    {
        $this->composer = $composer;
        $this->vendorName = $vendorName;
        $this->extensionName = $extensionName;
        $this->requireVersion = $requireVersion;

        $this->guzzleClient = new Client(
            [
                'base_uri' => $this->composer->getInput()->getOption(
                    'repository'
                ),
            ]
        );
    }

    public function __toString(): string
    {
        return $this->vendorName . '/' . $this->extensionName . ':' . $this->requireVersion;
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
        return $this->promise = $this->promise ?? $this->guzzleClient
            ->requestAsync(
                'GET',
                sprintf(
                    '/packages/%s/%s.json',
                    $this->vendorName,
                    $this->extensionName,
                )
            )
            ->then(function (Response $response) {
                $json = json_decode(
                    (string) $response->getBody(),
                    true
                );

                $composers = [];
                $versions = $json['package']['versions'];
                foreach ($versions as $version => $details) {
                    $composers[$version] = new Composer(
                        $details,
                        $this->composer->getDispatcher(),
                        $this->composer->getInput(),
                        $this->composer->getOutput(),
                        $this->composer
                    );
                }
            });
    }
}
