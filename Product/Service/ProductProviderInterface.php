<?php
namespace Product\Service;

interface ProductProviderInterface
{
    public function getFilteredProducts(string $from, string $to, int $travelers);
}