<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\PayPal;

use Omnipay\GatewayTestCase;
use Omnipay\Common\CreditCard;

class ProGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ProGateway($this->httpClient, $this->httpRequest);

        $this->options = array(
            'amount' => 1000,
            'card' => new CreditCard(array(
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2016',
                'cvv' => '123',
            )),
        );
    }

    public function testAuthorize()
    {
        $this->setMockResponse($this->httpClient, 'ProPurchaseSuccess.txt');

        $response = $this->gateway->authorize($this->options);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('96U93778BD657313D', $response->getGatewayReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchase()
    {
        $this->setMockResponse($this->httpClient, 'ProPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('96U93778BD657313D', $response->getGatewayReference());
        $this->assertNull($response->getMessage());
    }
}
