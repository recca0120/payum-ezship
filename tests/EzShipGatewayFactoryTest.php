<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\EzShip\EzShipGatewayFactory;

class EzShipGatewayFactoryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_create_config()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $suID = 'foo.suID';
        $httpClient = m::spy('Payum\Core\HttpClientInterface');
        $messageFactory = m::spy('Http\Message\MessageFactory');
        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $gateway = new EzShipGatewayFactory();
        $config = $gateway->createConfig([
            'api' => false,
            'suID' => $suID,
            'payum.http_client' => $httpClient,
            'httplug.message_factory' => $messageFactory,
        ]);
        $api = call_user_func($config['payum.api'], ArrayObject::ensureArrayObject($config));

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($suID, $config['suID']);
        $this->assertInstanceOf('PayumTW\EzShip\Api', $api);
    }
}
