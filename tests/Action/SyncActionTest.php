<?php

use Mockery as m;
use PayumTW\Ezship\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;

class SyncActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_sync()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('Payum\Core\Request\Sync');
        $gateway = m::spy('Payum\Core\GatewayInterface');
        $token = m::spy('Payum\Core\Model\TokenInterface');

        $details = new ArrayObject([
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ]);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $action = new SyncAction();
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('PayumTW\Ezship\Request\Api\GetTransactionData'))->once();
    }
}
