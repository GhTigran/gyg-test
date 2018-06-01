<?php

namespace Product\Service\Factory;


use GuzzleHttp\Client;
use Product\Service\ProductProviderInterface;
use Product\Service\ProductProviderMocky;

class ProductProviderFactory
{
    public function getService(string $type) : ProductProviderInterface
    {
        switch ($type) {
            case 'mocky':
                return new ProductProviderMocky(
                    new Client()
                );
            default:
                throw new \InvalidArgumentException("$type is not a valid product provider service type");
        }
    }
}