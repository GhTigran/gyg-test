<?php
require_once './vendor/autoload.php';

try {
    validateParameters($argv);

    $providerFactory = new \Product\Service\Factory\ProductProviderFactory();
    $providerService = $providerFactory->getService('mocky');
    $productService  = new \Product\Service\ProductService($providerService);

    unset($argv[0]);

    $products = $productService->getAvailableProducts(...$argv);

    echo json_encode(array_values($products), JSON_PRETTY_PRINT);
} catch (\Product\Exception\ValidationException | \Product\Exception\ServiceProviderException $e) {
    outputMessage($e->getMessage());
} catch (\Throwable $t) {
    outputMessage('Not sure what you did there, but you broke the system.');
}


function validateParameters(array $parameters): void
{
    if (count($parameters) !== 4) {
        throw new \Product\Exception\ValidationException('Bad Request. Invalid number of parameters.');
    }

    if (!\Product\Service\Validator::isValidDate($parameters[1])) {
        throw new \Product\Exception\ValidationException('Bad Request. Invalid start date format.');
    }

    if (!\Product\Service\Validator::isValidDate($parameters[2])) {
        throw new \Product\Exception\ValidationException('Bad Request. Invalid end date format.');
    }

    if (!ctype_digit($parameters[3])) {
        throw new \Product\Exception\ValidationException('Bad Request. Invalid guest count format.');
    }
}

function outputMessage($message, $endLine = true)
{
    echo $message . ($endLine ? PHP_EOL : '');
}