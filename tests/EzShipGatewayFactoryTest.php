<?php

namespace PayumTW\Ezship\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\EzshipGatewayFactory;

class EzshipGatewayFactoryTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testCreateConfig()
    {
        $gateway = new EzshipGatewayFactory();
        $config = $gateway->createConfig([
            'payum.api' => false,
            'payum.required_options' => [],
            'payum.http_client' => $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            'httplug.message_factory' => $messageFactory = m::mock('Http\Message\MessageFactory'),
            'payum.api' => false,
            'su_id' => 'foo',
        ]);

        $this->assertInstanceOf(
            'PayumTW\Ezship\Api',
            $config['payum.api'](ArrayObject::ensureArrayObject($config))
        );
    }
}
