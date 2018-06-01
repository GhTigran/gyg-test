<?php

namespace Product\Service;

use Product\Exception\ServiceProviderException;

class ProductService
{
    const PRODUCT_PROPERTY_ID          = 'product_id';
    const PRODUCT_PROPERTY_START       = 'activity_start_datetime';
    const PRODUCT_PROPERTY_START_TIMES = 'available_starttimes';

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
        $filteredProducts = $this->providerService->getFilteredProducts($from, $to, $travelers);

        if (!$filteredProducts) {
            return [];
        }

        $sortedProducts      = $this->sortProducts($filteredProducts);
        $reformattedProducts = $this->reformatProducts($sortedProducts);

        return $reformattedProducts;
    }

    private function sortProducts(array $products): array
    {
        usort($products, function ($product1, $product2) {
            if ($product1[self::PRODUCT_PROPERTY_ID] == $product2[self::PRODUCT_PROPERTY_ID]) {
                return $product1[self::PRODUCT_PROPERTY_START] <=> $product2[self::PRODUCT_PROPERTY_START];
            }

            return $product1[self::PRODUCT_PROPERTY_ID] <=> $product2[self::PRODUCT_PROPERTY_ID];
        });

        return $products;
    }

    private function reformatProducts(array $products): array
    {
        $reformatted = [];

        foreach ($products as $product) {
            if (isset($reformatted[$product[self::PRODUCT_PROPERTY_ID]])) {
                $reformatted[$product[self::PRODUCT_PROPERTY_ID]][self::PRODUCT_PROPERTY_START_TIMES][] = $product[self::PRODUCT_PROPERTY_START];
            } else {
                $reformatted[$product[self::PRODUCT_PROPERTY_ID]] = [
                    self::PRODUCT_PROPERTY_ID          => $product[self::PRODUCT_PROPERTY_ID],
                    self::PRODUCT_PROPERTY_START_TIMES => [
                        $product[self::PRODUCT_PROPERTY_START],
                    ],
                ];
            }
        }

        return $reformatted;
    }
}