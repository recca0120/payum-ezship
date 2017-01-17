<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\EzshipGatewayFactory;

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

        $suId = 'foo.su_id';
        $httpClient = m::spy('Payum\Core\HttpClientInterface');
        $messageFactory = m::spy('Http\Message\MessageFactory');
        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $gateway = new EzshipGatewayFactory();
        $config = $gateway->createConfig([
            'api' => false,
            'su_id' => $suId,
            'payum.http_client' => $httpClient,
            'httplug.message_factory' => $messageFactory,
        ]);
        $api = call_user_func($config['payum.api'], ArrayObject::ensureArrayObject($config));

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($suId, $config['su_id']);
        $this->assertInstanceOf('PayumTW\Ezship\Api', $api);
    }
}
