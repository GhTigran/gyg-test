<?php

namespace Test\Service;

use PHPUnit\Framework\TestCase;
use Product\Exception\ServiceProviderException;
use Product\Service\ProductProviderMocky;

final class ProductProviderMockyTest extends TestCase
{
    public function testGetFilteredProducts()
    {
        $productProviderMockyMock = $this->getMockBuilder(ProductProviderMocky::class)
             ->setMethods([
                 'getAllProducts',
             ])
             ->disableOriginalConstructor()
             ->getMock()
        ;

        $productProviderMockyMock->method('getAllProducts')->willReturn([
            [
                ProductProviderMocky::PRODUCT_PROPERTY_ID       => 1,
                ProductProviderMocky::PRODUCT_PROPERTY_START    => '2017-08-03T12:45',
                ProductProviderMocky::PRODUCT_PROPERTY_DURATION => 120,
                ProductProviderMocky::PRODUCT_PROPERTY_PLACES   => 50,
            ],
            [
                ProductProviderMocky::PRODUCT_PROPERTY_ID       => 2,
                ProductProviderMocky::PRODUCT_PROPERTY_START    => '2017-08-04T15:00',
                ProductProviderMocky::PRODUCT_PROPERTY_DURATION => 180,
                ProductProviderMocky::PRODUCT_PROPERTY_PLACES   => 70,
            ],
        ]);

        $filteredProducts = $productProviderMockyMock->getFilteredProducts(
            '2017-08-04T09:00', '2017-08-05T09:00', 4
        );

        $this->assertInternalType('array', $filteredProducts);
        $this->assertCount(1, $filteredProducts);
    }

    public function testValidateImportedProductsFail()
    {
        $productProviderMockyMock = $this->getMockBuilder(ProductProviderMocky::class)
             ->disableOriginalConstructor()
             ->getMock()
        ;

        $this->expectException(ServiceProviderException::class);

        $this->invokeMethod($productProviderMockyMock, 'validateImportedProducts',
            [
                [
                    [
                        ProductProviderMocky::PRODUCT_PROPERTY_ID       => 2,
                        ProductProviderMocky::PRODUCT_PROPERTY_START    => '2017-08-04 15:00',
                        ProductProviderMocky::PRODUCT_PROPERTY_DURATION => 180,
                        ProductProviderMocky::PRODUCT_PROPERTY_PLACES   => 70.8,
                    ],
                ],
            ]
        );
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}