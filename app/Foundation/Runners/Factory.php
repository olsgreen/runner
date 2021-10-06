<?php

namespace App\Foundation\Runners;

use Illuminate\Contracts\Container\Container;

class Factory
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public static function getRunnerTypeMap(): array
    {
        return [
            'local' => Local::class
        ];
    }

    public function make(string $type): Runner
    {
        $map = static::getRunnerTypeMap();

        if ($abstract = $map[$type]) {
            return $this->container->make($abstract);
        }

        throw new \InvalidArgumentException(
            'Invalid runner type specified.'
        );
    }
}
