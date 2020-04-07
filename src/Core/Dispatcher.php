<?php declare(strict_types=1);
namespace PHPM\Core;

class Dispatcher
{
    protected $items = [];

    public function dispatch($item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function findBy($from, callable $compareFunction): bool
    {
        foreach ($this->items as $item) {
            if ($compareFunction($item, $from)) {
                return true;
            }
        }
        return false;
    }
}
