<?php

namespace Product\Service;

use GuzzleHttp\ClientInterface;
use Product\Exception\ServiceProviderException;

class ProductProviderMocky implements ProductProviderInterface
{
    const URL = 'http://www.mocky.io/v2/58ff37f2110000070cf5ff16';
    private $client;

    public function __construct(
        ClientInterface $client
    )
    {
        $this->client = $client;
    }

    /**
     * @param string $from
     * @param string $to
     * @param int    $travelers
     * @return array
     * @throws ServiceProviderException
     */
    public function getFilteredProducts(string $from, string $to, int $travelers): array
    {
        $allProducts = $this->getAllProducts();

        $filteredProducts = array_filter($allProducts, function ($product) use ($from, $to, $travelers) {
            return (
                $product['places_available'] >= $travelers
                &&
                $product['activity_start_datetime'] >= $from
                &&
                strtotime($product['activity_start_datetime']) + 60 * $product['activity_duration_in_minutes'] <= strtotime($to)
            );
        });

        return $filteredProducts;
    }

    /**
     * @return array
     * @throws ServiceProviderException
     */
    public function getAllProducts(): array
    {
        $res = $this->client->request('GET', self::URL);

        if ($res->getStatusCode() != 200) {
            throw new ServiceProviderException('Failed to retrieve data from service provider');
        }

        $products = json_decode($res->getBody(), true);

        if (json_last_error() != JSON_ERROR_NONE || !isset($products['product_availabilities'])) {
            throw new ServiceProviderException('Data provided by service is malformed');
        }

        $products = $products['product_availabilities'];

        $this->validateImportedProducts($products);

        return $products;
    }

    /**
     * @param array $products
     * @return void
     * @throws ServiceProviderException
     */
    private function validateImportedProducts(array $products)
    {
        if (!$products) {
            return;
        }

        foreach ($products as $product) {
            if (!(
                isset($product['places_available']) && is_numeric($product['places_available'])
                &&
                isset($product['activity_duration_in_minutes']) && is_numeric($product['activity_duration_in_minutes'])
                &&
                isset($product['product_id']) && is_numeric($product['product_id'])
                &&
                isset($product['activity_start_datetime']) && Validator::isValidDate($product['activity_start_datetime'])
            )) {
                throw new ServiceProviderException('Data provided by service is malformed');
            }
        }
    }
}