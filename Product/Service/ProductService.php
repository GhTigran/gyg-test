<?php

namespace Product\Service;

use Product\Exception\ServiceProviderException;

class ProductService
{
    private $providerService;

    public function __construct(
        ProductProviderInterface $providerService
    )
    {
        $this->providerService = $providerService;
    }

    /**
     * @param string $from
     * @param string $to
     * @param int    $travelers
     * @return array
     * @throws ServiceProviderException
     */
    public function getAvailableProducts(string $from, string $to, int $travelers): array
    {
        $filteredProducts   = $this->providerService->getFilteredProducts($from, $to, $travelers);

        if (!$filteredProducts) {
            return [];
        }

        $sortedProducts     = $this->sortProducts($filteredProducts);
        $reformattedProducts = $this->reformatProducts($sortedProducts);

        return $reformattedProducts;
    }

    private function sortProducts(array $products): array
    {
        usort($products, function ($product1, $product2) {
            if ($product1['product_id'] == $product2['product_id']) {
                return $product1['activity_start_datetime'] <=> $product2['activity_start_datetime'];
            }

            return $product1['product_id'] <=> $product2['product_id'];
        });

        return $products;
    }

    private function reformatProducts(array $products) : array
    {
        $reformatted = [];

        foreach ($products as $product) {
            if (isset($reformatted[$product['product_id']])) {
                $reformatted[$product['product_id']]['available_starttimes'][] = $product['activity_start_datetime'];
            } else {
                $reformatted[$product['product_id']] = [
                    'product_id'           => $product['product_id'],
                    'available_starttimes' => [
                        $product['activity_start_datetime'],
                    ],
                ];
            }
        }

        return $reformatted;
    }
}