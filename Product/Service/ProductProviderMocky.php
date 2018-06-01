<?php

namespace Product\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Product\Exception\ServiceProviderException;

class ProductProviderMocky implements ProductProviderInterface
{
    const URL = 'http://www.mocky.io/v2/58ff37f2110000070cf5ff16';

    const COLLECTION_KEY            = 'product_availabilities';
    const PRODUCT_PROPERTY_ID       = 'product_id';
    const PRODUCT_PROPERTY_START    = 'activity_start_datetime';
    const PRODUCT_PROPERTY_DURATION = 'activity_duration_in_minutes';
    const PRODUCT_PROPERTY_PLACES   = 'places_available';

    /**
     * @var ClientInterface
     */
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
                $product[self::PRODUCT_PROPERTY_PLACES] >= $travelers
                &&
                $product[self::PRODUCT_PROPERTY_START] >= $from
                &&
                strtotime($product[self::PRODUCT_PROPERTY_START]) + 60 * $product[self::PRODUCT_PROPERTY_DURATION] <= strtotime($to)
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
        try {
            $response = $this->client->request('GET', self::URL);

            if ($response->getStatusCode() != 200) {
                throw new ServiceProviderException('Failed to retrieve data from service provider');
            }

            $products = json_decode($response->getBody(), true);

            if (json_last_error() != JSON_ERROR_NONE || !isset($products['product_availabilities'])) {
                throw new ServiceProviderException('Data provided by service is malformed');
            }

            $products = $products[self::COLLECTION_KEY];

            $this->validateImportedProducts($products);

            return $products;
        } catch (GuzzleException $e) {
            throw new ServiceProviderException('Failed to retrieve data from provider.');
        }
    }

    /**
     * @param array $products
     * @return void
     * @throws ServiceProviderException
     */
    private function validateImportedProducts(array $products)
    {
        foreach ($products as $product) {
            if (!(
                isset($product[self::PRODUCT_PROPERTY_PLACES]) && is_numeric($product[self::PRODUCT_PROPERTY_PLACES])
                &&
                isset($product[self::PRODUCT_PROPERTY_DURATION]) && is_numeric($product[self::PRODUCT_PROPERTY_DURATION])
                &&
                isset($product[self::PRODUCT_PROPERTY_ID]) && is_numeric($product[self::PRODUCT_PROPERTY_ID])
                &&
                isset($product[self::PRODUCT_PROPERTY_START]) && Validator::isValidDate($product[self::PRODUCT_PROPERTY_START])
            )) {
                throw new ServiceProviderException('Data provided by service is malformed');
            }
        }
    }
}