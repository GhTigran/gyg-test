<?php

namespace Product\Service\Factory;


use Product\Service\ProductProviderInterface;
use Product\Service\ProductProviderMocky;

class ProductProviderFactory
{
    const PROVIDERS = [
        'mocky' => ProductProviderMocky::class,
    ];

    public function getService(string $type) : ProductProviderInterface
    {
        if (!array_key_exists($type, self::PROVIDERS)) {
            throw new \InvalidArgumentException("$type is not a valid product provider service type");
        }

        $service = self::PROVIDERS[$type];
        return new $service;
    }
}